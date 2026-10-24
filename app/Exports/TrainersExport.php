<?php

namespace App\Exports;

use App\Models\Training\Trainer;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TrainersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $searchName;
    protected $searchNationalId;
    protected $filterSpecialization;
    protected $filterStatus;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($searchName = null, $searchNationalId = null, $filterSpecialization = null, $filterStatus = null, $dateFrom = null, $dateTo = null)
    {
        $this->searchName = $searchName;
        $this->searchNationalId = $searchNationalId;
        $this->filterSpecialization = $filterSpecialization;
        $this->filterStatus = $filterStatus;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function query()
    {
        return Trainer::query()
            ->with(['specialization', 'jobGrade', 'jobStatus', 'creator', 'editor', 'approver'])
            ->when($this->searchName, function ($q) {
                $q->where('name_ar', 'like', '%' . $this->searchName . '%')
                  ->orWhere('name_en', 'like', '%' . $this->searchName . '%');
            })
            ->when($this->searchNationalId, function ($q) {
                $q->where('national_id', 'like', '%' . $this->searchNationalId . '%');
            })
            ->when($this->filterSpecialization, function ($q) {
                $q->where('specialization_id', $this->filterSpecialization);
            })
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->when($this->dateFrom, function ($q) {
                $q->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($q) {
                $q->whereDate('created_at', '<=', $this->dateTo);
            })
            ->latest();
    }

    public function headings(): array
    {
        return [
            'م',
            'الصفة التدريبية',
            'حالة الاعتماد',
            'سبب الرفض (إن وجد)',
            'الاسم بالعربي',
            'الاسم بالإنجليزي',
            'الرقم القومي',
            'تاريخ الميلاد',
            'النوع',
            'رقم المحمول',
            'البريد الإلكتروني',
            'المؤهل العلمي',
            'سنة الحصول عليه',
            'الجامعة / المعهد',
            'الوظيفة الحالية',
            'جهة العمل',
            'محافظة جهة العمل',
            'الدرجة الوظيفية',
            'الحالة الوظيفية',
            'التخصص الفني',
            'تخصص الدراسات العليا',
            'سنة الدراسات العليا',
            'سنوات الخبرة بالتخصص',
            'الدراسات العليا الإدارية',
            'مستوى اللغة الإنجليزية',
            'مكافأة السعة التدريبية',
            'موقف شهادة TOT',
            'جهة إصدار TOT',
            'تاريخ TOT',
            'أُضيف بواسطة',
            'تاريخ الإضافة',
            'آخر تعديل بواسطة',
            'تاريخ آخر تعديل',
            'اعتمد بواسطة',
            'تاريخ الاعتماد',
        ];
    }

    public function map($trainer): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $statusText = match($trainer->status) {
            'approved' => 'معتمد',
            'rejected' => 'مرفوض',
            default => 'قيد الانتظار',
        };

        return [
            $rowNumber,
            $trainer->trainer_type == 'trainer' ? 'مدرب' : 'استشاري',
            $statusText,
            $trainer->rejection_reason ?? '---',
            $trainer->name_ar,
            $trainer->name_en ?? '---',
            $trainer->national_id,
            $trainer->birth_date ?? '---',
            $trainer->gender == 'male' ? 'ذكر' : 'أنثى',
            $trainer->phone ?? '---',
            $trainer->email ?? '---',
            $trainer->academic_qualification ?? '---',
            $trainer->qualification_year ?? '---',
            $trainer->university_or_institute ?? '---',
            $trainer->current_job ?? '---',
            $trainer->workplace ?? '---',
            $trainer->workplace_governorate ?? '---',
            $trainer->jobGrade->name ?? $trainer->job_grade_id ?? '---',
            $trainer->jobStatus->name ?? $trainer->job_status_id ?? '---',
            $trainer->specialization->name ?? '---',
            $trainer->postgraduate_specialization ?? '---',
            $trainer->postgraduate_specialization_year ?? '---',
            $trainer->specialization_experience_years ?? 0,
            $trainer->administrative_postgraduates ?? '---',
            $trainer->english_proficiency_level ?? '---',
            number_format($trainer->capacity_reward_amount, 2),
            $trainer->has_tot_certificate ? 'حاصل عليها' : 'غير حاصل عليها',
            $trainer->tot_provider ?? '---',
            $trainer->tot_date ?? '---',
            $trainer->creator->name ?? 'النظام',
            $trainer->created_at ? $trainer->created_at->format('Y-m-d H:i') : '---',
            $trainer->editor->name ?? 'لا يوجد',
            $trainer->updated_at ? $trainer->updated_at->format('Y-m-d H:i') : '---',
            $trainer->approver->name ?? '---',
            $trainer->approval_date ?? '---',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // ضبط اتجاه الشيت من اليمين لليسار RTL
        $sheet->setRightToLeft(true);

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // 1. تنسيق رأس الجدول (Header Styling)
        $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
                'name' => 'Segoe UI',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'], // كحلي إداري أنيق
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(32);

        // 2. تنسيق خلايا البيانات (Data Rows Styling)
        if ($highestRow > 1) {
            $sheet->getStyle("A2:{$highestColumn}{$highestRow}")->applyFromArray([
                'font' => [
                    'size' => 10,
                    'name' => 'Segoe UI',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D9D9D9'],
                    ],
                ],
            ]);

            // ضبط ارتفاع صفوف البيانات بالتساوي
            for ($row = 2; $row <= $highestRow; $row++) {
                $sheet->getRowDimension($row)->setRowHeight(24);
            }
        }

        return [];
    }
}
