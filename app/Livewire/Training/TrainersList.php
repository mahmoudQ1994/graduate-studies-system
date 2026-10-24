<?php

namespace App\Livewire\Training;

use App\Models\Training\Trainer;
use App\Models\TechnicalSpecialization;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TrainersExport;

class TrainersList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // حقول البحث والتصفية
    public $searchName = '';
    public $searchNationalId = '';
    public $filterSpecialization = '';
    public $filterStatus = '';
    public $dateFrom = '';
    public $dateTo = '';

    public function updatingSearchName() { $this->resetPage(); }
    public function updatingSearchNationalId() { $this->resetPage(); }
    public function updatingFilterSpecialization() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingDateFrom() { $this->resetPage(); }
    public function updatingDateTo() { $this->resetPage(); }


    public function exportExcel()
{
    return Excel::download(
        new TrainersExport(
            $this->searchName,
            $this->searchNationalId,
            $this->filterSpecialization,
            $this->filterStatus,
            $this->dateFrom,
            $this->dateTo
        ),
        'قائمة_المدربين_' . date('Y-m-d_H-i') . '.xlsx'
    );
}

    // دالة لحذف المدرب
    public function deleteTrainer($id)
    {
        $trainer = Trainer::find($id);
        if ($trainer) {
            if ($trainer->national_id_front_path) Storage::disk('public')->delete($trainer->national_id_front_path);
            if ($trainer->national_id_back_path) Storage::disk('public')->delete($trainer->national_id_back_path);
            if ($trainer->job_status_certificate_path) Storage::disk('public')->delete($trainer->job_status_certificate_path);
            if ($trainer->tot_certificate_path) Storage::disk('public')->delete($trainer->tot_certificate_path);

            $trainer->delete();
            session()->flash('success', 'تم حذف المحاضر بنجاح من النظام.');
        }
    }

    public function render()
    {
        $trainers = Trainer::query()
            ->with(['jobGrade', 'specialization', 'creator', 'editor', 'approver'])
            ->when($this->searchName, function ($query) {
                $query->where('name_ar', 'like', '%' . $this->searchName . '%')
                      ->orWhere('name_en', 'like', '%' . $this->searchName . '%');
            })
            ->when($this->searchNationalId, function ($query) {
                $query->where('national_id', 'like', '%' . $this->searchNationalId . '%');
            })
            ->when($this->filterSpecialization, function ($query) {
                $query->where('specialization_id', $this->filterSpecialization);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.training.trainers-list', [
            'trainers' => $trainers,
            'specializations' => TechnicalSpecialization::all()
        ])->layout('layouts.app');
    }
}
