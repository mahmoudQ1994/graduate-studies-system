<?php

namespace App\Exports;

use App\Models\Trainees\Trainee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TraineesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $search;
    protected $workplace;
    protected $qualification;

    // استقبال فلاتر البحث الحالية لتصدير ما يراه المستخدم في الشاشة فقط لو أراد
    public function __construct($search = null, $workplace = null, $qualification = null)
    {
        $this->search = $search;
        $this->workplace = $workplace;
        $this->qualification = $qualification;
    }

    public function collection()
    {
        return Trainee::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name_ar', 'like', '%' . $this->search . '%')
                      ->orWhere('national_id', 'like', '%' . $this->search . '%')
                      ->orWhere('phone_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->workplace, function ($query) {
                $query->where('workplace', 'like', '%' . $this->workplace . '%');
            })
            ->when($this->qualification, function ($query) {
                $query->where('qualification', 'like', '%' . $this->qualification . '%');
            })
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'م',
            'اسم المتدرب (عربي)',
            'الاسم (إنجليزي)',
            'الرقم القومي',
            'الوظيفة',
            'المؤهل',
            'الإدارة',
            'جهة العمل',
            'رقم الموبايل',
            'البريد الإلكتروني',
            'تاريخ التسجيل'
        ];
    }

    public function map($trainee): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $trainee->name_ar,
            $trainee->name_en ?? '---',
            $trainee->national_id,
            $trainee->job_title ?? '---',
            $trainee->qualification ?? '---',
            $trainee->department ?? '---',
            $trainee->workplace ?? '---',
            $trainee->phone_number ?? '---',
            $trainee->email ?? '---',
            $trainee->created_at ? date('Y-m-d H:i', strtotime($trainee->created_at)) : '---'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // جعل اتجاه الشيت من اليمين لليسار (RTL) لدعم اللغة العربية
        $sheet->setRightToLeft(true);

        // تنسيق الهيدر (رأس الجدول) باللون الأزرق النص الأبيض
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0D6EFD'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(28);

        // تطبيق الحدود والمحاذاة لكافة صفوف الجدول
        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 1) {
            $sheet->getStyle('A1:K' . $highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E0E0E0'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // محاذاة الأسماء لليمين لسهولة القراءة
            $sheet->getStyle('B2:C' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
    }
}
