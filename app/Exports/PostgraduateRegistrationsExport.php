<?php

namespace App\Exports;

use App\Models\HealthProfessional;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class PostgraduateExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithStyles, WithEvents
{
    public function collection()
    {
        $professionals = HealthProfessional::with([
            'qualification',
            'postgraduateRegistrations',
            'medicalMovements',
            'facility.district'
        ])->get();

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

    // تنسيق الهيدر (الصف الأول)
    public function styles(Worksheet $sheet)
    {
        // جعل شيت الإكسيل يبدأ من اليمين لليسار (RTL) لدعم اللغة العربية
        $sheet->getParent()->getActiveSheet()->setRightToLeft(true);

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFF'],
                    'size' => 11,
                    'name' => 'Cairo'
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '2C3E50'], // لون خلفية احترافي داكن للهيدر
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    // إضافة الحدود (Borders) لكل خلايا الجدول وضبط الارتفاع
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $cellRange = "A1:{$highestColumn}{$highestRow}";

                // تطبيق حدود رفيعة (Thin Borders) على كل الجدول
                $sheet->getStyle($cellRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'D3D3D3'], // لون رمادي خفيف للحدود
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // ضبط ارتفاع صف العناوين (Header) ليكون واضحاً وجميلاً
                $sheet->getRowDimension(1)->setRowHeight(30);
            },
        ];
    }
}
