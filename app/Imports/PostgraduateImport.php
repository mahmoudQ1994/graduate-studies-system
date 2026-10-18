<?php

namespace App\Imports;

use App\Models\HealthProfessional;
use App\Models\PostgraduateRegistration;
use App\Models\ProfessionalQualification;
use App\Models\MedicalMovement;
use App\Models\Facility;
use App\Models\District;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class PostgraduateImport
{
    private function parseExcelDate($value, $isYearOnly = false)
    {
        if (empty($value)) return null;
        try {
            if (is_numeric($value)) {
                if ($value > 1000 && $value < 50000) {
                    return Carbon::instance(ExcelDate::excelToDateTimeObject($value))->format('Y-m-d');
                }
                if ($isYearOnly && strlen((string)$value) == 4) {
                    return $value . '-01-01';
                }
            }
            $stringval = trim((string)$value);
            if ($isYearOnly && preg_match('/^\d{4}$/', $stringval)) {
                return $stringval . '-01-01';
            }
            return Carbon::parse($stringval)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function preview($filePath): array
    {
        $data = Excel::toArray([], $filePath)[0];
        $rows = array_slice($data, 1);

        $report = [
            'errors' => 0,
            'existing_trainees' => 0,
            'brand_new_trainees' => 0,
            'details' => []
        ];

        // 1. فحص فريدية الرقم القومي لعدم نسبته لأشخاص مختلفين في الشيت
        $nationalIdNamesMap = [];
        foreach ($rows as $rowArray) {
            $nationalId = trim($rowArray[2] ?? '');
            if (empty($nationalId)) continue;
            if (strlen($nationalId) < 14) {
                $nationalId = str_pad($nationalId, 14, '0', STR_PAD_LEFT);
            }
            $name = trim($rowArray[1] ?? '');

            if (!isset($nationalIdNamesMap[$nationalId])) {
                $nationalIdNamesMap[$nationalId] = [];
            }
            if (!in_array($name, $nationalIdNamesMap[$nationalId])) {
                $nationalIdNamesMap[$nationalId][] = $name;
            }
        }

        $conflictedNationalIds = array_filter($nationalIdNamesMap, function($names) {
            return count($names) > 1;
        });

        // لتتبع الطلبات داخل الملف نفسه
        $fileRequestsTracker = [];

        foreach ($rows as $index => $rowArray) {
            $name = trim($rowArray[1] ?? '');
            $nationalId = trim($rowArray[2] ?? '');
            $phone = trim($rowArray[3] ?? '');
            $profession = trim($rowArray[4] ?? '');
            $facility = trim($rowArray[6] ?? '');
            $requiredDegree = trim($rowArray[9] ?? ''); // نوع الدراسة المطلوبة
            $requiredSpecialty = trim($rowArray[10] ?? ''); // التخصص المطلوب
            $university = trim($rowArray[11] ?? '');
            $rawAppDate = trim($rowArray[26] ?? '');
            $rawRegDate = trim($rowArray[27] ?? '');
            $studyStatus = trim($rowArray[33] ?? '') ?: 'جاري فحص الطلب';

            if (empty($nationalId)) continue;
            if (strlen($nationalId) < 14) {
                $nationalId = str_pad($nationalId, 14, '0', STR_PAD_LEFT);
            }

            // تحديد ما إذا كان الطلب الجديد "مفتوحاً / سارياً" أم "مغلقاً"
            $openStatuses = ['جاري فحص الطلب', 'قيد الدراسة', 'مقبول', 'مستمر بالدراسة'];
            $isOpenRequest = in_array($studyStatus, $openStatuses);

            $parsedDate = $this->parseExcelDate($rawRegDate, true) ?? $this->parseExcelDate($rawAppDate);
            $requestYear = $parsedDate ? date('Y', strtotime($parsedDate)) : date('Y');

            $status = 'brand_new';
            $notes = 'دارس جديد يتم إضافة بياناته لأول مرة';
            $conflictDetails = null;
            $isApproved = true;

            $requestSignature = $nationalId . '_' . mb_strtolower($requiredDegree) . '_' . mb_strtolower($requiredSpecialty);

            // القاعدة 1: فريدية الرقم القومي
            if (array_key_exists($nationalId, $conflictedNationalIds)) {
                $status = 'conflicted';
                $otherNames = implode(' و ', $conflictedNationalIds[$nationalId]);
                $notes = 'خطأ فريدية الرقم القومي: مستخدم لأشخاص مختلفة (' . $otherNames . ')';
                $conflictDetails = "الرقم القومي فريد ولا يجوز أن يخص شخصين مختلفين في الشيت!";
                $isApproved = false;
                $report['errors']++;
            }
            // القاعدة 2: إذا كان الطلب الجديد "مفتوحاً"، ونفس الطلب (نوع الدراسة + التخصص) مكرر ومفتوح في نفس الملف أو نفس السنة
            else if ($isOpenRequest && isset($fileRequestsTracker[$requestSignature])) {
                $status = 'conflicted';
                $notes = 'خطأ تعارض زمني أو تكرار: يوجد طلب مفتوح لنفس الدراسة والتخصص في نفس الفترة داخل الملف!';
                $conflictDetails = "يوجد طلب نشط مطابِق تماماً في الشيت لنفس المتدرب في نفس الفترة الزمنية.";
                $isApproved = false;
                $report['errors']++;
            }

            if ($isOpenRequest && $status !== 'conflicted') {
                $fileRequestsTracker[$requestSignature] = $requestYear;
            }

            // القاعدة 3: فحص قاعدة البيانات للتأكد من عدم وجود تعارض أو قيد ساري لنفس التخصص والدراسة
            if ($status !== 'conflicted') {
                $existingProf = HealthProfessional::with(['postgraduateRegistrations'])->where('national_id', $nationalId)->first();

                if ($existingProf) {
                    if ($existingProf->name !== $name) {
                        $status = 'conflicted';
                        $notes = 'خطأ تعارض: الرقم القومي مسجل بالنظام باسم شخص آخر (' . $existingProf->name . ')';
                        $conflictDetails = "المسجل بالقاعدة: {$existingProf->name} - هاتفه: {$existingProf->phone}";
                        $isApproved = false;
                        $report['errors']++;
                    } else {
                        $hasConflictInDB = false;

                        foreach ($existingProf->postgraduateRegistrations as $reg) {
                            $isSameDegreeAndSpecialty = (mb_strtolower($reg->required_degree) === mb_strtolower($requiredDegree) &&
                                                         mb_strtolower($reg->required_specialty) === mb_strtolower($requiredSpecialty));

                            $regDate = $reg->registration_date ?? $reg->application_date;
                            $regYear = $regDate ? date('Y', strtotime($regDate)) : null;

                            // الشرط الدقيق: يعتبر تعارضاً فقط إذا كان الطلب القديم "مفتوحاً وسارياً" (وليس مغلقاً مثل حصل على الدرجة أو مرفوض)
                            // وكان في نفس سنة القيد أو الفترة الزمنية للطلب الجديد
                            $isOldRequestOpen = in_array($reg->study_status, $openStatuses);

                            if ($isSameDegreeAndSpecialty && $isOldRequestOpen && $regYear == $requestYear) {
                                $hasConflictInDB = true;
                                $status = 'conflicted';
                                $notes = 'خطأ تعارض زمني: يوجد قيد ساري ومفتوح لنفس الدراسة والتخصص في نفس العام (' . $regYear . ')!';
                                $conflictDetails = "القيد الساري والمفتوح بالقاعدة: {$reg->required_degree} - {$reg->required_specialty} (الحالة: {$reg->study_status})";
                                $isApproved = false;
                                $report['errors']++;
                                break;
                            }
                        }

                        if (!$hasConflictInDB) {
                            $status = 'existing';
                            $notes = 'دارس مسجل مسبقاً (مسموح بالرفع لعدم وجود قيد ساري متعارض)';
                            $report['existing_trainees']++;
                        }
                    }
                } else {
                    $report['brand_new_trainees']++;
                }
            } else {
                $existingProf = HealthProfessional::where('national_id', $nationalId)->first();
                if ($existingProf) {
                    $report['existing_trainees']++;
                } else {
                    $report['brand_new_trainees']++;
                }
            }

            $report['details'][] = [
                'row_index' => $index,
                'row_num' => $index + 2,
                'name' => $name,
                'national_id' => $nationalId,
                'phone' => $phone,
                'profession' => $profession,
                'facility' => $facility,
                'degree' => $requiredDegree,
                'specialty' => $requiredSpecialty,
                'university' => $university,
                'study_status' => $studyStatus,
                'status' => $status,
                'notes' => $notes,
                'conflict_details' => $conflictDetails,
                'approved' => $isApproved
            ];
        }

        return $report;
    }

    public function executeImportSelected($filePath, array $approvedRowIndices)
    {
        $data = Excel::toArray([], $filePath)[0];
        $rows = array_slice($data, 1);

        foreach ($rows as $index => $rowArray) {
            if (!in_array($index, $approvedRowIndices)) {
                continue;
            }

            DB::transaction(function () use ($rowArray) {
                $val = function($index) use ($rowArray) {
                    return isset($rowArray[$index]) ? trim($rowArray[$index]) : null;
                };

                $rawNationalId = $val(2);
                if (empty($rawNationalId)) return;

                $nationalId = strlen((string)$rawNationalId) < 14 ? str_pad($rawNationalId, 14, '0', STR_PAD_LEFT) : trim((string)$rawNationalId);

                $districtName = $val(5);
                $district = !empty($districtName) ? District::firstOrCreate(['name' => $districtName]) : null;

                $facilityName = $val(6);
                $facility = !empty($facilityName) ? Facility::firstOrCreate(
                    ['name' => $facilityName],
                    ['district_id' => $district?->id, 'sector_id' => 1, 'type' => 'مستشفى عام']
                ) : null;

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

                $movementSpecialty = $val(12);
                $movementDate = $this->parseExcelDate($val(13));
                if (!empty($movementSpecialty) || !empty($movementDate)) {
                    MedicalMovement::updateOrCreate(
                        ['health_professional_id' => $healthProfessional->id],
                        [
                            'specialty' => $movementSpecialty,
                            'movement_date' => $movementDate,
                        ]
                    );
                }

                $requiredDegree = $val(9);
                $requiredSpecialty = $val(10);
                $registrationDate = $this->parseExcelDate($val(27), true);

                PostgraduateRegistration::create([
                    'health_professional_id'    => $healthProfessional->id,
                    'sponsorship_type'          => $val(8),
                    'required_degree'           => $requiredDegree,
                    'required_specialty'        => $requiredSpecialty,
                    'required_university'       => $val(11),
                    'prior_registration_status' => $val(20),
                    'prior_registration_study'  => $val(21),
                    'prior_registration_year'   => $val(22),
                    'cancellation_reason'       => $val(23),
                    'study_leave_type'          => $val(24),
                    'leaves_history'            => $val(25),
                    'application_date'          => $this->parseExcelDate($val(26)),
                    'registration_date'         => $registrationDate,
                    'nominated_degree_status'   => $val(28),
                    'degree_grade'              => $val(29),
                    'master_degree_date'        => $this->parseExcelDate($val(30)),
                    'nominated_degree_date'     => $this->parseExcelDate($val(31)),
                    'years_from_registration'   => $val(32),
                    'study_status'              => $val(33) ?: 'جاري فحص الطلب',
                    'apology_date'              => $this->parseExcelDate($val(34)),
                    'apology_reason'            => $val(34),
                    'rejection_date'            => $this->parseExcelDate($val(35)),
                    'rejection_reason'          => $val(36),
                    'execution_date'            => $this->parseExcelDate($val(37)),
                ]);
            });
        }
    }
}
