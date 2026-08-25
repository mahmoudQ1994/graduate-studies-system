<?php

namespace App\Livewire\Postgraduate;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PostgraduateRegistration;
use App\Models\StudyLeave;
use Carbon\Carbon;

class ManageStudyLeaves extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // البحث والتصفية
    public $search = '';
    public $filter_leave_type = '';
    public $filter_expiring_soon = false;

    // بيانات إدارة التفرغ داخل Modal
    public $selectedRegId = null;
    public $selectedReg = null;

    // عناصر النموذج
    public $editing_leave_id = null;
    public $leave_type = 'تفرغ بمرتب';
    public $start_date;
    public $end_date;
    public $notes;
    public $return_date;

    public $calculated_duration_text = '-';

    // مصفوفة لتخزين تواريخ العودة لكل إجازة لتجنب التداخل
    public $return_dates = [];

    // مراقبة التغيير في البحث لتصفير الصفحة
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterLeaveType()
    {
        $this->resetPage();
    }

    // إحصائيات التفرغ الكلية (Computed Property)
    public function getStatsProperty()
    {
        return [
            'total_registered' => PostgraduateRegistration::count(),
            'total_with_leave' => PostgraduateRegistration::whereIn('study_leave_type', ['تفرغ بمرتب', 'تفرغ بدون مرتب'])->count(),
            'paid_leave'       => PostgraduateRegistration::where('study_leave_type', 'تفرغ بمرتب')->count(),
            'unpaid_leave'     => PostgraduateRegistration::where('study_leave_type', 'تفرغ بدون مرتب')->count(),
            'no_leave'         => PostgraduateRegistration::where('study_leave_type', 'بدون تفرغ')->orWhereNull('study_leave_type')->count(),
        ];
    }

    // الأطباء الذين أوشكت إجازاتهم على الانتهاء (Computed Property)
    public function getExpiringLeavesProperty()
    {
        return PostgraduateRegistration::whereHas('studyLeaves', function ($query) {
            $query->whereNull('actual_return_date')
                  ->whereNotNull('end_date')
                  ->where('end_date', '<=', Carbon::now()->addDays(30));
        })
        ->with(['healthProfessional', 'studyLeaves' => function($q) {
            $q->whereNull('actual_return_date')->latest();
        }])
        ->get();
    }

    public function updatedStartDate()
    {
        $this->calculateDuration();
    }

    public function updatedEndDate()
    {
        $this->calculateDuration();
    }

    private function calculateDuration()
    {
        if ($this->start_date && $this->end_date) {
            $this->calculated_duration_text = static::getFormattedDuration($this->start_date, $this->end_date);
        } else {
            $this->calculated_duration_text = '-';
        }
    }

    public static function getFormattedDuration($start, $end)
    {
        if (!$start) return '-';
        $startDate = Carbon::parse($start);
        $endDate = $end ? Carbon::parse($end) : Carbon::now();

        $diff = $startDate->diff($endDate);

        $parts = [];
        if ($diff->y > 0) $parts[] = $diff->y . ' سنة';
        if ($diff->m > 0) $parts[] = $diff->m . ' شهر';
        if ($diff->d > 0) $parts[] = $diff->d . ' يوم';

        return count($parts) > 0 ? implode(' و ', $parts) : 'أقل من يوم';
    }

    public function toggleExpiringFilter()
    {
        $this->filter_expiring_soon = !$this->filter_expiring_soon;
        $this->resetPage();
    }

    public function openLeavesModal($regId)
    {
        $this->selectedRegId = $regId;
        $this->selectedReg = PostgraduateRegistration::with('studyLeaves', 'healthProfessional')->find($regId);
        $this->resetLeaveForm();
        $this->dispatch('open-modal', name: 'leavesHistoryModal');
    }

    public function resetLeaveForm()
    {
        $this->editing_leave_id = null;
        $this->leave_type = $this->selectedReg->study_leave_type ?? 'تفرغ بمرتب';
        $this->start_date = null;
        $this->end_date = null;
        $this->notes = null;
        $this->return_date = null;
        $this->calculated_duration_text = '-';
    }

    public function editLeave($leaveId)
    {
        $leave = StudyLeave::findOrFail($leaveId);
        $this->editing_leave_id = $leave->id;
        $this->leave_type = $leave->leave_type;
        $this->start_date = $leave->start_date;
        $this->end_date = $leave->end_date;
        $this->notes = $leave->notes;
        $this->calculateDuration();
    }

    public function saveLeave()
    {
        $this->validate([
            'leave_type' => 'required',
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($this->editing_leave_id) {
            $leave = StudyLeave::findOrFail($this->editing_leave_id);
            $leave->update([
                'leave_type' => $this->leave_type,
                'start_date' => $this->start_date,
                'end_date'   => $this->end_date,
                'notes'      => $this->notes,
            ]);
        } else {
            StudyLeave::create([
                'postgraduate_registration_id' => $this->selectedRegId,
                'leave_type'                   => $this->leave_type,
                'start_date'                   => $this->start_date,
                'end_date'                     => $this->end_date,
                'notes'                        => $this->notes,
            ]);
        }

        // تحديث نوع التفرغ في السجل الرئيسي
        $this->selectedReg->update([
            'study_leave_type' => $this->leave_type,
        ]);

        $this->selectedReg->refresh();
        $this->resetLeaveForm();
        session()->flash('modal_success', 'تم حفظ بيانات التفرغ بنجاح.');
    }

    public function returnToWork($leaveId)
    {
        $this->validate([
            "return_dates.{$leaveId}" => 'required|date',
        ], [
            "return_dates.{$leaveId}.required" => 'يرجى إدخال تاريخ استلام العمل أولاً.',
        ]);

        $leave = StudyLeave::findOrFail($leaveId);

        // 1. تسجيل تاريخ العودة الفعلي للإجازة في العمود الصحيح
        $leave->update([
            'actual_return_date' => $this->return_dates[$leaveId],
        ]);

        //تحديث تاريخ نهاية التفرغ بناءا على تاريخ استلام العمل إذا كان موجود
        if ($this->return_dates[$leaveId]) {
            $leave->update([
                'end_date' => $this->return_dates[$leaveId],
            ]);
        }

        // 2. تحديث موقف المرشح الرئيسي إلى "بدون تفرغ" نظراً لإنهاء التفرغ واستلام العمل
        $this->selectedReg->update([
            'study_leave_type' => 'بدون تفرغ',
        ]);

        // تحديث البيانات المرتبطة في المكون
        $this->selectedReg->refresh();

        // مسح الحقل الخاص بهذه الإجازة
        unset($this->return_dates[$leaveId]);

        session()->flash('modal_success', 'تم تسجيل استلام العمل وإنهاء التفرغ بنجاح، وتحويل الموقف إلى بدون تفرغ.');
    }

    public function deleteLeave($leaveId)
    {
        StudyLeave::destroy($leaveId);

        // التحقق من آخر إجازة متبقية للمرشح لتحديث حالته الرئيسية تلقائياً
        $latestLeave = $this->selectedReg->studyLeaves()->latest()->first();

        $this->selectedReg->update([
            'study_leave_type' => $latestLeave ? $latestLeave->leave_type : 'بدون تفرغ'
        ]);

        $this->selectedReg->refresh();
        session()->flash('modal_success', 'تم حذف الإجازة بنجاح.');
    }

    public function render()
    {
        $query = PostgraduateRegistration::with(['healthProfessional', 'studyLeaves']);

        if ($this->search) {
            $query->whereHas('healthProfessional', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('national_id', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filter_leave_type) {
            $query->where('study_leave_type', $this->filter_leave_type);
        }

        if ($this->filter_expiring_soon) {
            $query->whereHas('studyLeaves', function ($q) {
                $q->whereNull('actual_return_date')
                  ->whereNotNull('end_date')
                  ->where('end_date', '<=', Carbon::now()->addDays(30));
            });
        }

        return view('livewire.postgraduate.manage-study-leaves', [
            'registrations'  => $query->latest()->paginate(10),
            'expiringLeaves' => $this->expiringLeaves,
            'stats'          => $this->stats,
        ])->layout('layouts.app');
    }
}
