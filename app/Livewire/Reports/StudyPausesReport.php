<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PostgraduateRegistration;
use App\Models\Setting;
use App\Models\OfficialHeader;

class StudyPausesReport extends Component
{
    use WithPagination;

    public $searchWorkPlace = '';
    public $pauseReason = '';
    public $fromDate = '';
    public $toDate = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingQuery()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['searchWorkPlace', 'pauseReason', 'fromDate', 'toDate']);
    }

    public function render()
    {
        // الاستعلام يعتمد على جدول إيقاف القيد المرتبط
        $query = PostgraduateRegistration::query()
            ->with(['healthProfessional.facility', 'studyPauses'])
            ->whereHas('studyPauses');

        // فلترة بسبب الإيقاف المطابق لحقل pause_reason الفعلي
        if (!empty($this->pauseReason)) {
            $query->whereHas('studyPauses', function($q) {
                $q->where('pause_reason', 'like', '%' . $this->pauseReason . '%');
            });
        }

        // فلترة بالفترة الزمنية بناءً على تاريخ بدء الإيقاف pause_start_date
        if (!empty($this->fromDate)) {
            $query->whereHas('studyPauses', function($q) {
                $q->whereDate('pause_start_date', '>=', $this->fromDate);
            });
        }
        if (!empty($this->toDate)) {
            $query->whereHas('studyPauses', function($q) {
                $q->whereDate('pause_start_date', '<=', $this->toDate);
            });
        }

        // فلترة بجهة العمل عبر الكادر الطبي
        if (!empty($this->searchWorkPlace)) {
            $query->whereHas('healthProfessional.facility', function($q) {
                $q->where('name', 'like', '%' . $this->searchWorkPlace . '%');
            });
        }

        $pausesData = $query->latest()->get();

        $settings = Setting::first();
        $officialHeader = OfficialHeader::first();

        return view('livewire.reports.study-pauses-report', [
            'pausesData' => $pausesData,
            'settings' => $settings,
            'officialHeader' => $officialHeader,
        ])->layout('layouts.app');
    }
}
