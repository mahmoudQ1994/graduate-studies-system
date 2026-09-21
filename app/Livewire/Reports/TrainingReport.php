<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TrainingSecondment;
use App\Models\Facility;
use App\Models\Setting;
use App\Models\OfficialHeader;

use function Livewire\Volt\layout;

class TrainingReport extends Component
{
    use WithPagination;

    public $search = '';
    public $facility_id = '';
    public $action_type = '';
    public $training_entity = ''; // جهة الإفاد
    public $date_from = '';      // من تاريخ
    public $date_to = '';        // إلى تاريخ

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'facility_id', 'action_type', 'training_entity', 'date_from', 'date_to']);
    }

    public function exportExcel()
    {
        $fileName = 'training-report-' . date('Y-m-d') . '.xls';

        $query = TrainingSecondment::with(['healthProfessional.facility']);

        // تطبيق شروط البحث والفلترة الحالية
        if ($this->search) {
            $query->whereHas('healthProfessional', function($emp) {
                $emp->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('national_id', 'like', '%' . $this->search . '%');
            });
        }
        if ($this->facility_id) {
            $query->whereHas('healthProfessional', function($emp) {
                $emp->where('facility_id', $this->facility_id);
            });
        }
        if ($this->action_type) {
            $query->where('action_type', $this->action_type);
        }
        if ($this->training_entity) {
            $query->where('training_entity', 'like', '%' . $this->training_entity . '%');
        }
        if ($this->date_from) {
            $query->where('start_date', '>=', $this->date_from);
        }
        if ($this->date_to) {
            $query->where('start_date', '<=', $this->date_to);
        }

        $trainings = $query->latest()->get();

        $headers = [
            "Content-Type" => "application/vnd.ms-excel; charset=utf-8",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        return response()->stream(function() use ($trainings) {
            echo chr(0xEF).chr(0xBB).chr(0xBF); // دعم اللغة العربية

            echo "<table border='1'>";
            echo "<tr style='background-color: #f2f2f2; font-weight: bold;'>
                <th>#</th>
                <th>الاسم</th>
                <th>الرقم القومي</th>
                <th>الوظيفة</th>
                <th>جهة العمل</th>
                <th>جهة الإفاد</th>
                <th>نوع الإجراء</th>
                <th>تاريخ البداية</th>
                <th>تاريخ النهاية</th>
                <th>مدة الإفاد (شهور)</th>
            </tr>";

            foreach ($trainings as $index => $row) {
                echo "<tr>";
                echo "<td>" . ($index + 1) . "</td>";
                echo "<td>" . ($row->healthProfessional->name ?? '-') . "</td>";
                echo "<td style='mso-number-format:\"\@\";'>" . ($row->healthProfessional->national_id ?? '-') . "</td>";
                echo "<td>" . ($row->healthProfessional->profession ?? '-') . "</td>";
                echo "<td>" . ($row->healthProfessional->facility->name ?? '-') . "</td>";
                echo "<td>" . $row->training_entity . "</td>";
                echo "<td>" . $row->action_type . "</td>";
                echo "<td>" . $row->start_date . "</td>";
                echo "<td>" . $row->end_date . "</td>";
                echo "<td>" . $row->duration_months . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }, 200, $headers);
    }

    public function render()
    {
        $query = TrainingSecondment::with(['healthProfessional.facility', 'user']);

        // البحث العام (بالاسم أو الرقم القومي)
        if ($this->search) {
            $query->whereHas('healthProfessional', function($emp) {
                $emp->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('national_id', 'like', '%' . $this->search . '%');
            });
        }

        // الفلترة بجهة العمل الأصلية
        if ($this->facility_id) {
            $query->whereHas('healthProfessional', function($emp) {
                $emp->where('facility_id', $this->facility_id);
            });
        }

        // الفلترة بنوع الإجراء (إيفاد / مد إيفاد)
        if ($this->action_type) {
            $query->where('action_type', $this->action_type);
        }

        // الفلترة بجهة الإفاد
        if ($this->training_entity) {
            $query->where('training_entity', 'like', '%' . $this->training_entity . '%');
        }

        // الفلترة بفترة التاريخ (من تاريخ إلى تاريخ بناءً على تاريخ البداية)
        if ($this->date_from) {
            $query->where('start_date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->where('start_date', '<=', $this->date_to);
        }

        $trainings = $query->latest()->paginate(5);
        $facilities = Facility::all();
        $settings = Setting::first();
        $headerInfo = OfficialHeader::first();

        return view('livewire.reports.training-report', [
            'trainings' => $trainings,
            'facilities' => $facilities,
            'settings' => $settings,
            'headerInfo' => $headerInfo
        ])->layout('layouts.app');
    }
}
