<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PostgraduateRegistration;
use App\Models\Setting;
use App\Models\OfficialHeader;

class MedicalDegreesArchiveReport extends Component
{
    use WithPagination;

    public $degreeType = '';     // تصفية حسب نوع الدرجة (دبلوم - ماجستير - دكتوراه)
    public $searchWorkPlace = ''; // البحث بجهة العمل
    public $searchProfession = ''; // البحث بالوظيفة
    public $fromDate = '';
    public $toDate = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingQuery()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['degreeType', 'searchWorkPlace', 'searchProfession', 'fromDate', 'toDate']);
    }

    public function render()
    {
        // استعلام لجلب الحاصلين على الدرجات العلمية بناءً على الحقول الفعلية للمودل
        $query = PostgraduateRegistration::query()
            ->with(['healthProfessional.facility'])
            ->whereNotNull('nominated_degree_date'); // من حصلوا على الدرجة ولهم تاريخ حصول مسجل

        // تصفية حسب نوع الدرجة المطلوبة/الحاصل عليها
        if (!empty($this->degreeType)) {
            $query->where('required_degree', $this->degreeType);
        }

        // فلترة بالفترة الزمنية لتاريخ الحصول على الدرجة
        if (!empty($this->fromDate)) {
            $query->whereDate('nominated_degree_date', '>=', $this->fromDate);
        }
        if (!empty($this->toDate)) {
            $query->whereDate('nominated_degree_date', '<=', $this->toDate);
        }

        // فلترة بجهة العمل عبر الكادر الطبي
        if (!empty($this->searchWorkPlace)) {
            $query->whereHas('healthProfessional.facility', function($q) {
                $q->where('name', 'like', '%' . $this->searchWorkPlace . '%');
            });
        }

        // فلترة بالوظيفة عبر الكادر الطبي
        if (!empty($this->searchProfession)) {
            $query->whereHas('healthProfessional', function($q) {
                $q->where('profession', 'like', '%' . $this->searchProfession . '%');
            });
        }

        $archivesData = $query->latest('nominated_degree_date')->get();

        $settings = Setting::first();
        $officialHeader = OfficialHeader::first();

        return view('livewire.reports.medical-degrees-archive-report', [
            'archivesData' => $archivesData,
            'settings' => $settings,
            'officialHeader' => $officialHeader,
        ])->layout('layouts.app');
    }
}
