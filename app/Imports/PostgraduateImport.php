<?php

namespace App\Imports;

use App\Models\HealthProfessional;
use App\Models\PostgraduateRegistration;
use App\Models\ProfessionalQualification;
use App\Models\MedicalMovement;
use App\Models\Facility;
use App\Models\District;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class PostgraduateImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            DB::transaction(function () use ($row) {

                // تحويل الصف إلى مصفوفة مفهرسة رقمياً للوصول المباشر والثابت للأعمدة حسب الترتيب
                $rowArray = array_values($row->toArray());

                // دالة مساعدة لجلب القيمة حسب ترتيب العمود (مبتدئاً من الصفر 0)
                $val = function($index) use ($rowArray) {
                    return isset($rowArray[$index]) ? trim($rowArray[$index]) : null;
                };

                // دالة محسنة لمعالجة التواريخ والسنوات لمنع أخطاء قاعدة البيانات
                $parseExcelDate = function($value, $isYearOnly = false) {
                    if (empty($value)) return null;
                    try {
                        if (is_numeric($value)) {
                            // إذا كان رقماً تسلسلياً لإكسيل
                            if ($value > 1000 && $value < 50000) {
                                return Carbon::instance(ExcelDate::excelToDateTimeObject($value))->format('Y-m-d');
                            }
                            // إذا كان مجرد سنة (مثل 2022)
                            if ($isYearOnly && strlen((string)$value) == 4) {
                                return $value . '-01-01';
                            }
                        }

                        $stringval = trim((string)$value);
                        // إذا كان النص عبارة عن 4 أرقام (سنة فقط)
                        if ($isYearOnly && preg_match('/^\d{4}$/', $stringval)) {
                            return $stringval . '-01-01';
                        }

                        return Carbon::parse($stringval)->format('Y-m-d');
                    } catch (\Exception $e) {
                        return null;
                    }
                };

                // العمود 2: الرقم القومي (الترتيب الثالث في الشيت)
                $rawNationalId = $val(2);
                if (empty($rawNationalId)) {
                    return;
                }

                if (is_float($rawNationalId) || stripos((string)$rawNationalId, 'e') !== false) {
                    $nationalId = number_format($rawNationalId, 0, '', '');
                } else {
                    $nationalId = trim((string)$rawNationalId);
                }

                if (strlen($nationalId) < 14) {
                    $nationalId = str_pad($nationalId, 14, '0', STR_PAD_LEFT);
                }

                // 1. المركز (الترتيب 5)
                $districtName = $val(5);
                $district = null;
                if (!empty($districtName)) {
                    $district = District::firstOrCreate(['name' => $districtName]);
                }

                // 2. جهة العمل الاصلية (الترتيب 6)
                $facilityName = $val(6);
                $facility = null;
                if (!empty($facilityName)) {
                    $facility = Facility::firstOrCreate(
                        ['name' => $facilityName],
                        ['district_id' => $district?->id, 'sector_id' => 1, 'type' => 'مستشفى عام']
                    );
                }

                // 3. الطبيب (الاسم: 1, التليفون: 3, الوظيفة: 4, الجهة المنتدبة: 7)
                $healthProfessional = HealthProfessional::updateOrCreate(
                    ['national_id' => $nationalId],
                    [
                        'name' => $val(1),
                        'phone' => $val(3),
                        'profession' => $val(4),
                        'facility_id' => $facility?->id,
                        'secondment_facility' => $val(7),
                    ]
                );

                // 4. المؤهل (جامعة: 14, كلية: 15, دفعة: 16, تقدير عام: 17, تقدير مادة: 18, مجموع: 19)
                ProfessionalQualification::updateOrCreate(
                    ['health_professional_id' => $healthProfessional->id],
                    [
                        'university' => $val(14),
                        'qualification' => $val(15),
                        'graduation_batch' => $val(16),
                        'general_grade' => $val(17),
                        'subject_grade' => $val(18),
                        'total_marks' => $val(19),
                    ]
                );

                // 5. حركة النيابة (التخصص: 12, تاريخ الحركة: 13)
                $movementSpecialty = $val(12);
                $movementDate = $parseExcelDate($val(13));
                if (!empty($movementSpecialty) || !empty($movementDate)) {
                    MedicalMovement::updateOrCreate(
                        ['health_professional_id' => $healthProfessional->id],
                        [
                            'specialty' => $movementSpecialty,
                            'movement_date' => $movementDate,
                        ]
                    );
                }

                // 6. تسجيلات الدراسات العليا (موافقة للترتيب الجديد تماماً في التصدير)
                $requiredDegree = $val(9);   // نوع الدراسة المطلوبة
                $requiredSpecialty = $val(10); // تخصص الدراسة المطلوبة
                $rawRegDate = $val(23);       // تاريخ / سنة القيد
                $registrationDate = $parseExcelDate($rawRegDate, true);

                PostgraduateRegistration::updateOrCreate(
                    [
                        'health_professional_id' => $healthProfessional->id,
                        'required_degree' => $requiredDegree,
                        'required_specialty' => $requiredSpecialty,
                    ],
                    [
                        'sponsorship_type'        => $val(8),  // نوع الترشيح للدراسة
                        'required_university'     => $val(11), // الجامعة المطلوبة للدراسة
                        'prior_registration_status' => $val(20), // هل سبق القيد بالدراسات العليا
                        'prior_registration_study'  => $val(21), // الدراسة السابقة
                        'prior_registration_year'   => $val(22), // سنة القيد السابقة
                        'registration_date'       => $registrationDate,
                        'cancellation_reason'     => $val(23), // سبب إلغاء الدراسة
                        'study_leave_type'        => $val(24), // إجازة التفرغ الدراسي
                        'leaves_history'          => $val(25), // الأجازات المسجلة
                        'application_date'        => $parseExcelDate($val(26)), // تاريخ تسجيل الطلب
                        'nominated_degree_status' => $val(27), // موقف الحصول على الدرجة المرشح لها
                        'degree_grade'            => $val(28), // تقدير الدرجة
                        'master_degree_date'      => $parseExcelDate($val(29)), // تاريخ الحصول على الماجستير
                        'nominated_degree_date'   => $parseExcelDate($val(30)), // تاريخ الحصول على الدرجة
                        'years_from_registration' => $val(31), // عدد سنوات الدراسة من تاريخ القيد
                        'study_status'            => $val(32) ?: 'جاري فحص الطلب', // موقف القيد بالدراسة
                        'apology_date'            => $parseExcelDate($val(33)), // تاريخ الاعتذار
                        'apology_reason'          => $val(34), // سبب الاعتذار
                        'rejection_date'          => $parseExcelDate($val(35)), // تاريخ الرفض
                        'rejection_reason'        => $val(36), // سبب الرفض
                        'execution_date'          => $parseExcelDate($val(37)), // تاريخ تنفيذ الدراسة
                    ]
                );
            });
        }
    }
}
