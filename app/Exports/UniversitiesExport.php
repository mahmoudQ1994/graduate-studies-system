<?php

namespace App\Exports;

use App\Models\University;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class UniversitiesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return University::with(['user', 'colleges'])->withCount('colleges')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'اسم الجامعة',
            'نوع الجامعة',
            'عدد الكليات',
            'أضيفت بواسطة',
            'الحالة'
        ];
    }

    public function map($university): array
    {
        return [
            $university->id,
            $university->name,
            $university->type,
            $university->colleges_count . ' كلية',
            $university->user->name ?? 'غير معروف',
            $university->is_active ? 'مفعل' : 'معطل'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // جعل اتجاه الشيت من اليمين لليسار يدعم العربية
        $sheet->setRightToLeft(true);

        // تنسيق الهيدر (الصف الأول): خلفية زرقاء، خط عريض أبيض
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '0D6EFD'], // لون أزرق مميز
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // إضافة حدود (Borders) لجميع خلايا الجدول
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:F{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'DDDDDD'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
    }
}
