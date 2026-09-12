<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PostgraduateRegistration;

class SemiAnnualCandidatesReport extends Component
{
    use WithPagination;

    public $search = '';
    public $fromDate = '';
    public $toDate = '';
    public $sponsorshipType = '';
    public $requiredDegree = '';
    public $facilityId = '';

    public $isPrinting = false;

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFromDate() { $this->resetPage(); }
    public function updatingToDate() { $this->resetPage(); }
    public function updatingSponsorshipType() { $this->resetPage(); }
    public function updatingRequiredDegree() { $this->resetPage(); }
    public function updatingFacilityId() { $this->resetPage(); }

    public function printAll()
    {
        $this->isPrinting = true;
        $this->dispatch('trigger-print');
    }

    public function resetPrintState()
    {
        $this->isPrinting = false;
    }

    public function exportExcel()
    {
        $query = PostgraduateRegistration::with([
            'healthProfessional.facility',
            'healthProfessional.qualification',
            'healthProfessional.medicalMovements',
            'studyLeaves'
        ])->latest();

        if (!empty($this->fromDate)) {
            $query->whereDate('application_date', '>=', $this->fromDate);
        }
        if (!empty($this->toDate)) {
            $query->whereDate('application_date', '<=', $this->toDate);
        }
        if (!empty($this->sponsorshipType)) {
            $query->where('sponsorship_type', $this->sponsorshipType);
        }
        if (!empty($this->requiredDegree)) {
            $query->where('required_degree', $this->requiredDegree);
        }
        if (!empty($this->facilityId)) {
            $query->whereHas('healthProfessional', function($q) {
                $q->where('facility_id', $this->facilityId);
            });
        }
        if (!empty($this->search)) {
            $query->whereHas('healthProfessional', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('national_id', 'like', '%' . $this->search . '%');
            });
        }

        $candidates = $query->get();

        $filename = "semi-annual-candidates-" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($candidates) {
            $file = fopen('php://output', 'w');

            // إضافة BOM لدعم ظهور اللغة العربية في Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // استخدام الفاصلة المنقوطة (;) لتوزيع البيانات على الأعمدة في Excel
            fputcsv($file, [
                'م',
                'الاسم',
                'الرقم القومي',
                'المؤهل',
                'الوظيفة',
                'جهة العمل',
                'سنة الترشيح',
                'نوع الترشيح',
                'تخصص النيابة',
                'تاريخ النيابة',
                'جامعة الترشيح',
                'نوع الدراسة',
                'موقف التفرغ الدراسي'
            ], ';');

            foreach ($candidates as $index => $item) {
                $prof = $item->healthProfessional;
                $qual = $prof?->qualification;
                $mov = $prof?->medicalMovements?->first();

                $activeLeave = $item->studyLeaves()
                    ->where(function($q) {
                        $q->whereNull('actual_return_date')
                          ->orWhere('actual_return_date', '>', now());
                    })->first();

                $leaveStatus = $activeLeave ? "في إجازة: " . $activeLeave->leave_type : "لا يوجد";

                fputcsv($file, [
                    $index + 1,
                    $prof->name ?? '-',
                    $prof->national_id ?? '-',
                    $qual->qualification ?? '-',
                    $prof->profession ?? '-',
                    $prof->facility->name ?? '-',
                    $item->application_date ? date('Y', strtotime($item->application_date)) : '-',
                    $item->sponsorship_type ?? '-',
                    $mov->specialty ?? '-',
                    $mov->movement_date ?? '-',
                    $item->required_university ?? '-',
                    $item->required_degree ?? '-',
                    $leaveStatus
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        $query = PostgraduateRegistration::with([
            'healthProfessional.facility',
            'healthProfessional.qualification',
            'healthProfessional.medicalMovements',
            'studyLeaves'
        ])->latest();

        if (!empty($this->fromDate)) {
            $query->whereDate('application_date', '>=', $this->fromDate);
        }
        if (!empty($this->toDate)) {
            $query->whereDate('application_date', '<=', $this->toDate);
        }
        if (!empty($this->sponsorshipType)) {
            $query->where('sponsorship_type', $this->sponsorshipType);
        }
        if (!empty($this->requiredDegree)) {
            $query->where('required_degree', $this->requiredDegree);
        }
        if (!empty($this->facilityId)) {
            $query->whereHas('healthProfessional', function($q) {
                $q->where('facility_id', $this->facilityId);
            });
        }
        if (!empty($this->search)) {
            $query->whereHas('healthProfessional', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('national_id', 'like', '%' . $this->search . '%');
            });
        }

        $totalCount = (clone $query)->count();

        if ($this->isPrinting) {
            $candidates = $query->get();
        } else {
            $candidates = $query->paginate(10);
        }

        return view('livewire.reports.semi-annual-candidates-report', [
            'candidates' => $candidates,
            'totalCount' => $totalCount
        ])->layout('layouts.app');
    }
}
