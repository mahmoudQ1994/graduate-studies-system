<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ApprovedTraineesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $scheduleId;

    public function __construct($scheduleId)
    {
        $this->scheduleId = $scheduleId;
    }

    public function collection()
    {
        return DB::table('trainee_enrollments as te')
            ->join('trainees as t', 't.id', '=', 'te.trainee_id')
            ->join('training_program_schedules as tps', 'tps.id', '=', 'te.training_program_schedule_id')
            ->join('training_programs as tp', 'tp.id', '=', 'tps.training_program_id')
            ->where('te.training_program_schedule_id', $this->scheduleId)
            ->where('te.status', 'approved')
            ->select(
                'te.*',
                't.name_ar as trainee_name',
                't.national_id',
                't.qualification',
                't.phone_number',
                't.email',
                'tp.name_ar as program_name',
                'tps.start_date',
                'tps.end_date'
            )
            ->get();
    }

    // عناوين الأعمدة في شيت الإكسيل
    public function headings(): array
    {
        return [
            'م',
            'اسم البرنامج التدريبي',
            'تاريخ بداية البرنامج',
            'تاريخ نهاية البرنامج',
            'اسم المتدرب',
            'الرقم القومي',
            'المؤهل',
            'الوظيفة',
            'جهة العمل',
            'الإدارة',
            'رقم الهاتف',
            'الإيميل',
            'موقف الاعتماد',
            'حالة الحضور العامة'
        ];
    }

    // ربط البيانات بكل صف
    public function map($row): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $row->program_name,
            $row->start_date,
            $row->end_date,
            $row->trainee_name,
            $row->national_id,
            $row->qualification ?? '---',
            $row->job_title ?? '---',
            $row->workplace ?? '---',
            $row->department ?? '---',
            $row->phone_number ?? '---',
            $row->email ?? '---',
            'مقبول رسمياً',
            $row->attendance_status == 'attended' ? 'حاضر' : 'قيد الانتظار'
        ];
    }

    // تنسيق واستايل الإكسيل (ألوان، حدود، اتجاه RTL)
    public function styles(Worksheet $sheet)
    {
        // جعل اتجاه الشيت من اليمين لليسار (RTL) للغة العربية
        $sheet->getParent()->getActiveSheet()->setRightToLeft(true);

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $cellRange = 'A1:' . $highestColumn . $highestRow;

        return [
            // 1. تنسيق صف الهيدر الأول (خلفية أزرق داكن، خط أبيض عريض)
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '1F4E78'], // أزرق احترافي مريح للعين
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],

            // 2. تطبيق حدود (Borders) على كل خلايا الجدول وتوسيط المحاذاة العمودية
            $cellRange => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'D3D3D3'], // حدود رمادية خفيفة ونظيفة
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}
