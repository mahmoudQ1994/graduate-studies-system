<?php

namespace App\Exports;

use App\Models\Specialty;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SpecialtiesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return Specialty::with('user')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'اسم المؤهل الوظيفي',
            'أضيف بواسطة',
            'الحالة'
        ];
    }

    public function map($specialty): array
    {
        return [
            $specialty->id,
            $specialty->name,
            $specialty->user->name ?? 'غير معروف',
            $specialty->is_active ? 'مفعل' : 'معطل'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // جعل اتجاه الشيت من اليمين لليسار لدعم العربية
        $sheet->setRightToLeft(true);

        // تنسيق الهيدر (الصف الأول): خلفية زرقاء، خط عريض أبيض
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '0D6EFD'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // إضافة حدود (Borders) لجميع خلايا الجدول
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:D{$lastRow}")->applyFromArray([
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
