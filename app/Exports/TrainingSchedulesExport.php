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
    protected $programs;

    public function __construct($programs)
    {
        $this->programs = $programs;
    }

    public function collection()
    {
        return $this->programs;
    }

    public function map($item): array
    {
        $schedule = $item->schedule;

        $trainersNames = $schedule && $schedule->trainers->count() > 0
            ? $schedule->trainers->pluck('trainer_name')->implode(', ')
            : ($item->consultant_name ?? '-');

        $trainersTopics = $schedule && $schedule->trainers->count() > 0
            ? $schedule->trainers->pluck('session_topics')->implode(' | ')
            : '-';

        $statusMap = [
            'draft' => 'مسودة جدولة',
            'active' => 'نشط (جاهز للتسجيل)',
            'in_progress' => 'جاري التنفيذ',
            'completed' => 'تم التنفيذ',
            'cancelled' => 'ملغي'
        ];
        $statusText = $schedule ? ($statusMap[$schedule->schedule_status] ?? $schedule->schedule_status) : 'غير مجدول';

        return [
            $item->code,
            $item->name_ar,
            $item->name_en ?? '-',
            $item->programType->name ?? '-',
            $item->level ?? '-',
            $item->consultant_name ?? '-',
            $item->duration_days ?? 0,
            $item->training_hours ?? 0,
            $item->courses_count_per_year ?? 0,
            $statusText,
            $schedule ? $schedule->start_date : '-',
            $schedule ? $schedule->end_date : '-',
            $schedule ? $schedule->time_slot : '-',
            $schedule && $schedule->hall ? $schedule->hall->name : ($item->hall->name ?? '-'),
            $schedule ? $schedule->max_capacity : '-',
            $trainersNames,
            $trainersTopics,
            $item->objective_knowledge ?? '-',
            $item->objective_skills ?? '-',
            $item->objective_attitude ?? '-',
            $item->prerequisites ?? '-',
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
                // ضبط اتجاه الشيت من اليمين للسيار لتناسب اللغة العربية
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }
}
