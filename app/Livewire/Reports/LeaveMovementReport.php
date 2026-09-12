<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\StudyLeave;
use App\Models\Setting;

class LeaveMovementReport extends Component
{
    public $fromDate;
    public $toDate;
    public $searchWorkPlace = '';
    public $searchProfession = '';

    public function mount()
    {
        $this->fromDate = date('Y-01-01');
        $this->toDate = date('Y-12-31');
    }

    public function resetFilters()
    {
        $this->reset(['fromDate', 'toDate', 'searchWorkPlace', 'searchProfession']);
        $this->mount();
    }

    public function render()
    {
        $query = StudyLeave::with(['registration.healthProfessional.facility', 'registration.user']);

        // فلترة بالتواريخ
        if ($this->fromDate) {
            $query->whereDate('start_date', '>=', $this->fromDate);
        }

        if ($this->toDate) {
            $query->whereDate('end_date', '<=', $this->toDate);
        }

        // فلترة بجهة العمل من جدول facilities المرتبط عبر facility_id
        if (!empty($this->searchWorkPlace)) {
            $query->whereHas('registration.healthProfessional.facility', function($q) {
                $q->where('name', 'like', '%' . $this->searchWorkPlace . '%');
            });
        }

        // فلترة بالوظيفة (profession)
        if (!empty($this->searchProfession)) {
            $query->whereHas('registration.healthProfessional', function($q) {
                $q->where('profession', 'like', '%' . $this->searchProfession . '%');
            });
        }

        $leavesData = $query->latest()->get();
        $settings = Setting::first();

        return view('livewire.reports.leave-movement-report', [
            'leavesData' => $leavesData,
            'settings' => $settings,
            'officialHeader' => $settings
        ])->layout('layouts.app');
    }
}
