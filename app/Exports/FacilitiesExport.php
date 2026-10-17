<?php

namespace App\Exports;

use App\Models\Facility;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class FacilitiesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return Facility::with(['district', 'sector'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'اسم المنشأة',
            'النوع',
            'المركز / المدينة',
            'القطاع',
            'الحالة'
        ];
    }

    public function map($facility): array
    {
        return [
            $facility->id,
            $facility->name,
            $facility->type,
            $facility->district->name ?? '—',
            $facility->sector->name ?? 'غير محدد',
            $facility->is_active ? 'مفعل' : 'معطل'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // اتجاه الصفحة من اليمين لليسار
        $sheet->setRightToLeft(true);

        // تنسيق الهيدر (الصف الأول): خلفية ملونة، خط عريض، أبيض
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '0D6EFD'], // لون أزرق مميز يناسب التصميم
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // إضافة حدود (Borders) لجميع الخلايا الموجودة في الجدول
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
