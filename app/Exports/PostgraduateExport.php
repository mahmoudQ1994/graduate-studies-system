<?php

namespace App\Exports;

use App\Models\HealthProfessional;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class PostgraduateExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    public function collection()
    {
        $professionals = HealthProfessional::with([
            'qualification',
            'postgraduateRegistrations',
            'medicalMovements',
            'facility.district'
        ])->get();

        /** @var Collection<int, array{professional: HealthProfessional, registration: mixed, movement: mixed}> $rows */
        $rows = new Collection();

        foreach ($professionals as $professional) {
            if ($professional->postgraduateRegistrations->isNotEmpty()) {
                foreach ($professional->postgraduateRegistrations as $registration) {
                    $rows->push([
                        'professional' => $professional,
                        'registration' => $registration,
                        'movement' => $professional->medicalMovements->first()
                    ]);
                }
            } else {
                $rows->push([
                    'professional' => $professional,
                    'registration' => null,
                    'movement' => $professional->medicalMovements->first()
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'م',
            'الاسم',
            'الرقم القومي',
            'رقم التليفون',
            'الوظيفة',
            'المركز',
            'جهة العمل الأصلية',
            'الجهة المنتدب إليها',
            'نوع الترشيح للدراسة',
            'نوع الدراسة المطلوبة (دبلوم - ماجستير - دكتوراة)',
            'تخصص الدراسة المطلوبة',
            'الجامعة المطلوبة للدراسة',
            'حركة النيابة أو (إعارة)',
            'تاريخ حركة النيابة',
            'جامعة التخرج',
            ' المؤهل ',
            'دفعة التخرج',
            'التقدير العام',
            'تقدير المادة',
            'المجموع التراكمي',
            'هل سبق القيد بالدراسات العليا',
            'الدراسة السابقة',
            'سنة القيد السابقة',
            'سبب إلغاء الدراسة',
            'إجازة التفرغ الدراسي',
            'الأجازات المسجلة',
            'تاريخ تسجيل الطلب',
            'تاريخ القيد',
            'موقف الحصول على الدرجة المرشح لها',
            'تقدير الدرجة',
            'تاريخ الحصول على الماجستير',
            'تاريخ الحصول على الدرجة',
            'عدد سنوات الدراسة من تاريخ القيد',
            'موقف القيد بالدراسة',
            'تاريخ الاعتذار',
            'سبب الاعتذار',
            'تاريخ الرفض',
            'سبب الرفض',
            'تاريخ تنفيذ الدراسة'
        ];
    }

    public function map($row): array
    {
        $professional = $row['professional'];
        $reg = $row['registration'];
        $movement = $row['movement'];
        $qual = $professional->qualification;

        $formatDate = function($date) {
            return $date ? Carbon::parse($date)->format('Y-m-d') : null;
        };

        return [
            $professional->id,
            $professional->name,
            (string) $professional->national_id,
            (string) $professional->phone,
            $professional->profession,
            $professional->facility?->district?->name,
            $professional->facility?->name,
            $professional->secondment_facility,
            $reg?->sponsorship_type,
            $reg?->required_degree,
            $reg?->required_specialty,
            $reg?->required_university,
            $movement?->specialty,
            $formatDate($movement?->movement_date),
            $qual?->university,
            $qual?->qualification,
            $qual?->graduation_batch,
            $qual?->general_grade,
            $qual?->subject_grade,
            $qual?->total_marks,
            $reg?->prior_registration_status,
            $reg?->prior_registration_study,
            $reg?->prior_registration_year,
            $reg?->cancellation_reason,
            $reg?->study_leave_type,
            $reg?->leaves_history,
            $formatDate($reg?->application_date),
            $formatDate($reg?->registration_date),
            $reg?->nominated_degree_status,
            $reg?->degree_grade,
            $formatDate($reg?->master_degree_date),
            $formatDate($reg?->nominated_degree_date),
            $reg?->years_from_registration,
            $reg?->study_status,
            $formatDate($reg?->apology_date),
            $reg?->apology_reason,
            $formatDate($reg?->rejection_date),
            $reg?->rejection_reason,
            $formatDate($reg?->execution_date),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT, // الرقم القومي
            'D' => NumberFormat::FORMAT_TEXT, // رقم التليفون
        ];
    }
}
