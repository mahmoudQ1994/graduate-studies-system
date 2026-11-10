<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Exports\ApprovedTraineesExport;
use Maatwebsite\Excel\Facades\Excel;

class AttendedTraineesReport extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $programSearch = '';      // متغير حقل البحث عن البرنامج
    public $selected_program_id = ''; // معرف البرنامج المختار
    public $selected_schedule_id = ''; // معرف موعد التنفيذ المختار
    public $search = '';              // بحث داخل جدول المتدربين

    // تصفير الموعد عند تغيير بحث البرنامج
    public function updatedProgramSearch($value)
    {
        if (empty($value)) {
            $this->selected_program_id = '';
            $this->selected_schedule_id = '';
        }
    }

    public function updatedSelectedProgramId()
    {
        $this->selected_schedule_id = '';
        $this->resetPage();
    }

    public function updatingSelectedScheduleId() { $this->resetPage(); }
    public function updatingSearch() { $this->resetPage(); }

    // دالة جلب البرامج المطابقة للبحث التفاعلي
    public function getMatchingProgramsProperty()
    {
        if (strlen(trim($this->programSearch)) < 1) {
            return collect();
        }
        return DB::table('training_programs')
            ->where('name_ar', 'like', '%' . $this->programSearch . '%')
            ->limit(6)
            ->get();
    }

    // دالة اختيار البرنامج من القائمة التفاعلية
    public function selectProgram($id, $name)
    {
        $this->selected_program_id = $id;
        $this->programSearch = $name;
        $this->selected_schedule_id = '';
        $this->resetPage();
    }

    // دالة تصدير البيانات إلى Excel باستخدام الكلاس المنفصل
    public function exportExcel()
    {
        if (!$this->selected_schedule_id) {
            session()->flash('error', 'يرجى اختيار تاريخ التنفيذ للبرنامج أولاً.');
            return;
        }

        $schedule = DB::table('training_program_schedules as tps')
            ->join('training_programs as tp', 'tp.id', '=', 'tps.training_program_id')
            ->where('tps.id', $this->selected_schedule_id)
            ->select('tp.name_ar as program_name', 'tps.start_date')
            ->first();

        $fileName = 'كشف_المقبولين_' . str_replace(' ', '_', $schedule->program_name ?? 'البرنامج') . '_' . ($schedule->start_date ?? '') . '.xlsx';

        return Excel::download(new ApprovedTraineesExport($this->selected_schedule_id), $fileName);
    }

    public function render()
    {
        // جلب تواريخ التنفيذ (Schedules) التابعة للبرنامج المختار فقط
        $availableSchedules = collect();
        if ($this->selected_program_id) {
            $availableSchedules = DB::table('training_program_schedules')
                ->where('training_program_id', $this->selected_program_id)
                ->select('id', 'start_date', 'end_date', 'time_slot', 'max_capacity')
                ->orderBy('start_date', 'desc')
                ->get();
        }

        // جلب بيانات المتدربين المقبولين بناءً على الموعد المحدد
        $approvedTrainees = collect();
        $selectedScheduleInfo = null;

        if ($this->selected_schedule_id) {
            $selectedScheduleInfo = DB::table('training_program_schedules as tps')
                ->join('training_programs as tp', 'tp.id', '=', 'tps.training_program_id')
                ->where('tps.id', $this->selected_schedule_id)
                ->select('tp.name_ar as program_name', 'tps.start_date', 'tps.end_date', 'tps.max_capacity')
                ->first();

            $approvedTrainees = DB::table('trainee_enrollments as te')
                ->join('trainees as t', 't.id', '=', 'te.trainee_id')
                ->where('te.training_program_schedule_id', $this->selected_schedule_id)
                ->where('te.status', 'approved')
                ->when($this->search, function ($q) {
                    $q->where(function ($query) {
                        $query->where('t.name_ar', 'like', '%' . $this->search . '%')
                              ->orWhere('t.national_id', 'like', '%' . $this->search . '%');
                    });
                })
                ->select('te.*', 't.name_ar', 't.national_id', 't.qualification', 't.phone_number', 't.email')
                ->paginate(15);
        }

        return view('livewire.training.attended-trainees-report', [
            'availableSchedules' => $availableSchedules,
            'approvedTrainees' => $approvedTrainees,
            'selectedScheduleInfo' => $selectedScheduleInfo,
        ])->layout('layouts.app');
    }
}
