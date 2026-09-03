<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\HealthProfessional;
use App\Models\PostgraduateRegistration;
use App\Models\StudyLeave;
use App\Models\RegistrationPause;
use Illuminate\Support\Facades\DB;

class GeneralDashboard extends Component
{
    // =========================================================================
    // [تعريف المتغيرات العامة Component Properties]
    // تخزين متغيرات الفلاتر الإحصائية، بيانات البحث، ونتائج الاستعلام الفردي.
    // =========================================================================
    public $fromDate;
    public $toDate;

    public $search_national_id = '';
    public $search_name = '';
    public $searched = false;
    public $candidate = null;

    public $settings = null;
    public $officialHeader = null;

    // =========================================================================
    // [مراقب الأحداث Livewire Updated Hooks]
    // يتم استدعاؤها تلقائياً بمجرد قيام المستخدم بالكتابة في حقول البحث أو تغيير التواريخ.
    // =========================================================================
    public function updatedSearchNationalId($value)
    {
        $this->performSearch();
    }

    public function updatedSearchName($value)
    {
        $this->performSearch();
    }

    public function updatedFromDate($value)
    {
        $this->dispatchChartDataUpdate();
    }

    public function updatedToDate($value)
    {
        $this->dispatchChartDataUpdate();
    }

    // =========================================================================
    // [دالة تحديث بيانات الرسوم البيانية وإرسالها للواجهة]
    // =========================================================================
    private function dispatchChartDataUpdate()
    {
        $registrationsQuery = PostgraduateRegistration::query();
        $professionalsQuery = HealthProfessional::query();

        if (!empty($this->fromDate)) {
            $registrationsQuery->whereDate('application_date', '>=', $this->fromDate);
            $professionalsQuery->whereDate('created_at', '>=', $this->fromDate);
        }

        if (!empty($this->toDate)) {
            $registrationsQuery->whereDate('application_date', '<=', $this->toDate);
            $professionalsQuery->whereDate('created_at', '<=', $this->toDate);
        }

        $degrees = (clone $registrationsQuery)->select('required_degree', DB::raw('count(*) as total'))
            ->groupBy('required_degree')
            ->pluck('total', 'required_degree');

        $professions = (clone $professionalsQuery)->select('profession', DB::raw('count(*) as total'))
            ->groupBy('profession')
            ->pluck('total', 'profession');

        $this->dispatch('chartDataUpdated', [
            'degreesLabels' => $degrees->keys(),
            'degreesData'   => $degrees->values(),
            'profLabels'    => $professions->keys(),
            'profData'      => $professions->values(),
        ]);
    }

    // =========================================================================
    // [دالة تنفيذ البحث الفردي عن المرشح]
    // تبحث في جدول الكادر الطبي بناءً على الرقم القومي أو الاسم مع جلب العلاقات.
    // =========================================================================
    public function performSearch()
    {
        if (!empty($this->search_national_id) || !empty($this->search_name)) {
            $this->searched = true;

            $query = HealthProfessional::with([
                'facility.district',
                'qualification',
                'postgraduateRegistrations.studyLeaves.user',
                'postgraduateRegistrations.pauses.user'
            ]);

            if (!empty($this->search_national_id)) {
                $query->where('national_id', 'like', '%' . $this->search_national_id . '%');
            }

            if (!empty($this->search_name)) {
                $query->where('name', 'like', '%' . $this->search_name . '%');
            }

            $this->candidate = $query->first();
        } else {
            $this->searched = false;
            $this->candidate = null;
        }
    }

    // =========================================================================
    // [دالة تصفير وتفريغ نتائج البحث الفردي]
    // =========================================================================
    public function resetResults()
    {
        $this->search_national_id = '';
    }

    // =========================================================================
    // [دالة تفريغ فلاتر التواريخ الإحصائية]
    // =========================================================================
    public function resetFilters()
    {
        $this->reset(['fromDate', 'toDate']);
    }

    // =========================================================================
    // [دالة العرض الرئيسية Render Function]
    // تقوم بتجهيز وإرجاع البيانات والإحصائيات وتمريرها إلى صفحة الـ Blade.
    // =========================================================================
    public function render()
    {
        $registrationsQuery = PostgraduateRegistration::query();
        $leavesQuery = StudyLeave::query();
        $pausesQuery = RegistrationPause::whereNull('resume_date');

        if (!empty($this->fromDate)) {
            $registrationsQuery->whereDate('application_date', '>=', $this->fromDate);
            $leavesQuery->whereDate('start_date', '>=', $this->fromDate);
            $pausesQuery->whereDate('pause_start_date', '>=', $this->fromDate);
        }

        if (!empty($this->toDate)) {
            $registrationsQuery->whereDate('application_date', '<=', $this->toDate);
            $leavesQuery->whereDate('start_date', '<=', $this->toDate);
            $pausesQuery->whereDate('pause_start_date', '<=', $this->toDate);
        }

        $totalRegistrations = $registrationsQuery->count();
        $totalLeaves = $leavesQuery->count();
        $totalPauses = $pausesQuery->count();

        $degrees = (clone $registrationsQuery)->select('required_degree', DB::raw('count(*) as total'))
            ->groupBy('required_degree')
            ->pluck('total', 'required_degree');

        $statuses = (clone $registrationsQuery)->select('study_status', DB::raw('count(*) as total'))
            ->groupBy('study_status')
            ->pluck('total', 'study_status');

        $professionalsQuery = HealthProfessional::query();
        if (!empty($this->fromDate)) {
            $professionalsQuery->whereDate('created_at', '>=', $this->fromDate);
        }
        if (!empty($this->toDate)) {
            $professionalsQuery->whereDate('created_at', '<=', $this->toDate);
        }
        $totalProfessionals = $professionalsQuery->count();

        $professions = (clone $professionalsQuery)->select('profession', DB::raw('count(*) as total'))
            ->groupBy('profession')
            ->pluck('total', 'profession');

        $facilities = (clone $professionalsQuery)->select('secondment_facility', DB::raw('count(*) as total'))
            ->groupBy('secondment_facility')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'secondment_facility');

        return view('livewire.reports.general-dashboard', [
            'totalProfessionals' => $totalProfessionals,
            'totalRegistrations' => $totalRegistrations,
            'totalLeaves' => $totalLeaves,
            'totalPauses' => $totalPauses,

            'degreesBreakdown' => $degrees,
            'statusBreakdown' => $statuses,
            'professionBreakdown' => $professions,
            'facilityBreakdown' => $facilities,

            'chartDegreesLabels' => $degrees->keys(),
            'chartDegreesData' => $degrees->values(),

            'chartProfLabels' => $professions->keys(),
            'chartProfData' => $professions->values(),
        ])->layout('layouts.app');
    }
}
