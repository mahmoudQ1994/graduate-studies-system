<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class JobTitlesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return DB::table('job_titles')
            ->leftJoin('users', 'job_titles.created_by', '=', 'users.id')
            ->select('job_titles.*', 'users.name as creator_name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'م',
            'اسم الوظيفة',
            'الحالة',
            'تمت الإضافة بواسطة',
            'تاريخ الإضافة'
        ];
    }

    public function map($job): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $job->name,
            $job->is_active ? 'نشط' : 'غير نشط',
            $job->creator_name ?? '---',
            $job->created_at ? date('Y-m-d H:i', strtotime($job->created_at)) : '---'
        ];
    }

    // إضافة التنسيقات والألوان والخطوط لتطابق تصميم الموقع
    public function styles(Worksheet $sheet)
    {
        // جعل اتجاه الشيت من اليمين لليسار (RTL) ليتوافق مع اللغة العربية
        $sheet->setRightToLeft(true);

        // تنسيق صف العناوين (رأس الجدول)
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'], // نص أبيض
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0D6EFD'], // لون أزرق مشابه لزر الـ Primary في Bootstrap
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // تحديد ارتفاع صف العناوين
        $sheet->getRowDimension(1)->setRowHeight(25);

        // إضافة حدود (Borders) وتوسيط البيانات لكل الجدول
        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 1) {
            $sheet->getStyle('A1:E' . $highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // محاذاة "اسم الوظيفة" لليمين لتكون مريحة للعين
            $sheet->getStyle('B2:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
    }
}
