<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TrainingSchedulesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $schedules;

    // تم استقبال مجموعة المواعيد التنفيذية مباشرة
    public function __construct($schedules)
    {
        $this->schedules = $schedules;
    }

    public function collection()
    {
        return $this->schedules;
    }

    public function map($schedule): array
    {
        // $schedule هنا يمثل موعد تنفيذ محدد (TrainingProgramSchedule)
        $item = $schedule->trainingProgram;

        $trainersNames = $schedule->trainers->count() > 0
            ? $schedule->trainers->pluck('trainer_name')->implode(', ')
            : ($item->consultant_name ?? '-');

        $trainersTopics = $schedule->trainers->count() > 0
            ? $schedule->trainers->pluck('session_topics')->implode(' | ')
            : '-';

        $statusMap = [
            'draft' => 'مسودة جدولة',
            'active' => 'نشط (جاهز للتسجيل)',
            'in_progress' => 'جاري التنفيذ',
            'completed' => 'تم التنفيذ',
            'cancelled' => 'ملغي'
        ];
        $statusText = $statusMap[$schedule->schedule_status] ?? $schedule->schedule_status;

        return [
            $item ? $item->code : '-',
            $item ? $item->name_ar : '-',
            $item && $item->name_en ? $item->name_en : '-',
            $item && $item->programType ? $item->programType->name : '-',
            $item && $item->level ? $item->level : '-',
            $item && $item->consultant_name ? $item->consultant_name : '-',
            $item ? ($item->duration_days ?? 0) : 0,
            $item ? ($item->training_hours ?? 0) : 0,
            $item ? ($item->courses_count_per_year ?? 0) : 0,
            $statusText,
            $schedule->start_date ?? '-',
            $schedule->end_date ?? '-',
            $schedule->time_slot ?? '-',
            $schedule->hall ? $schedule->hall->name : '-',
            $schedule->max_capacity ?? '-',
            $trainersNames,
            $trainersTopics,
            $item ? ($item->objective_knowledge ?? '-') : '-',
            $item ? ($item->objective_skills ?? '-') : '-',
            $item ? ($item->objective_attitude ?? '-') : '-',
            $item ? ($item->prerequisites ?? '-') : '-',
        ];
    }

    public function headings(): array
    {
        return [
            'كود البرنامج',
            'اسم البرنامج (عربي)',
            'اسم البرنامج (إنجليزي)',
            'نوع البرنامج',
            'المستوى',
            'الاستشاري / المحاضر الرئيسي',
            'المدة (أيام)',
            'الساعات التدريبية',
            'عدد الدورات السنوية',
            'حالة الجدولة',
            'تاريخ البداية',
            'تاريخ النهاية',
            'فترة التوقيت',
            'القاعة التدريبية',
            'الحد الأقصى للمقاعد',
            'المحاضرون المنفذون',
            'المحاور والجلسات التدريبية',
            'الأهداف المعرفية',
            'الأهداف المهارية',
            'الأهداف السلوكية',
            'متطلبات الالتحاق بالبرنامج',
        ];
    }

    // تطبيق الـ Styles والأنماط على الجدول
    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // 1. تنسيق الصف الأول (Header)
        $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E79'], // لون أزرق داكن احترافي
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // 2. تنسيق كامل خلايا الجدول (الحدود والمحاذاة)
        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D9D9D9'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // 3. محاذاة البيانات في المنتصف لكل الصفوف بدءاً من الصف الثاني
        if ($highestRow > 1) {
            $sheet->getStyle("A2:{$highestColumn}{$highestRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // تحديد ارتفاع صف العناوين
        $sheet->getRowDimension(1)->setRowHeight(30);

        return [];
    }

    // إضافة أحداث التحكم باتجاه ورقة العمل (Right-To-Left)
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // ضبط اتجاه الشيت من اليمين لليسار لتناسب اللغة العربية
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }
}
