<?php

namespace App\Livewire\Postgraduate;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TrainingSecondment;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TrainingSecondmentIndex extends Component
{
    use WithPagination;

    // حقول البحث المتقدمة
    public $search_name = '';
    public $search_national_id = '';
    public $search_facility = '';
    public $search_training_entity = '';
    public $search_from_date = '';
    public $search_to_date = '';

    // متغيرات التعديل عبر Modal
    public $editingId;
    public $action_type, $training_entity, $training_department, $duration_months, $start_date, $end_date, $training_days = [];

    protected $paginationTheme = 'bootstrap';

    // إعادة تعيين الصفحات عند أي تعديل في البحث
    public function updatedSearchName() { $this->resetPage(); }
    public function updatedSearchNationalId() { $this->resetPage(); }
    public function updatedSearchFacility() { $this->resetPage(); }
    public function updatedSearchTrainingEntity() { $this->resetPage(); }
    public function updatedSearchFromDate() { $this->resetPage(); }
    public function updatedSearchToDate() { $this->resetPage(); }

    // زر إعادة تعيين البحث
    public function resetSearch()
    {
        $this->reset([
            'search_name', 'search_national_id', 'search_facility',
            'search_training_entity', 'search_from_date', 'search_to_date'
        ]);
        $this->resetPage();
    }

    public function export(): StreamedResponse
    {
        $trainings = TrainingSecondment::with(['healthProfessional.facility'])
            ->when($this->search_name, function($query) {
                $query->whereHas('healthProfessional', fn($q) => $q->where('name', 'like', '%' . $this->search_name . '%'));
            })
            ->when($this->search_national_id, function($query) {
                $query->whereHas('healthProfessional', fn($q) => $q->where('national_id', 'like', '%' . $this->search_national_id . '%'));
            })
            ->when($this->search_facility, function($query) {
                $query->whereHas('healthProfessional.facility', fn($q) => $q->where('name', 'like', '%' . $this->search_facility . '%'));
            })
            ->when($this->search_training_entity, function($query) {
                $query->where('training_entity', 'like', '%' . $this->search_training_entity . '%');
            })
            ->when($this->search_from_date, function($query) {
                $query->where('start_date', '>=', $this->search_from_date);
            })
            ->when($this->search_to_date, function($query) {
                $query->where('end_date', '<=', $this->search_to_date);
            })
            ->latest()
            ->get();

        $filename = 'training-secondment-' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($trainings) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            $delimiter = ';';
            fputcsv($file, ['الاسم', 'الرقم القومي', 'الوظيفة', 'جهة العمل الأصلية', 'نوع الإجراء', 'جهة الإفاد للتدريب', 'تاريخ بداية الإفاد', 'تاريخ نهاية الإفاد', 'مدة الإفاد (شهور)', 'أيام الإفاد'], $delimiter);

                foreach ($trainings as $item) {
                    fputcsv($file, [
                        $item->healthProfessional->name ?? '-',
                        $item->healthProfessional->national_id ?? '-',
                        $item->healthProfessional->profession ?? '-',
                        $item->healthProfessional->facility->name ?? '-',
                        $item->action_type,
                        $item->training_entity,
                        $item->start_date,
                        $item->end_date,
                        $item->duration_months,
                        is_array($item->training_days) ? implode('، ', $item->training_days) : '-',
                    ], $delimiter);
                }
            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }

    /**
     * دالة لتحديد أو إلغاء تحديد جميع أيام الأسبوع (انتداب كلي)
     */
    public function toggleAllDays()
    {
        $days = ['السبت', 'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'];

        if (is_array($this->training_days) && count($this->training_days) == count($days)) {
            $this->training_days = [];
        } else {
            $this->training_days = $days;
        }
    }

    public function edit($id)
    {
        $training = TrainingSecondment::findOrFail($id);

        $this->editingId = $training->id;
        $this->action_type = $training->action_type;
        $this->training_entity = $training->training_entity;
        $this->training_department = $training->training_department;
        $this->duration_months = $training->duration_months;
        $this->start_date = $training->start_date;
        $this->end_date = $training->end_date;
        $this->training_days = is_array($training->training_days) ? $training->training_days : [];

        $this->dispatch('show-edit-modal');
    }

    public function updatedStartDate() { $this->calculateEndDate(); }
    public function updatedDurationMonths() { $this->calculateEndDate(); }

    public function calculateEndDate()
    {
        if (!empty($this->start_date) && is_numeric($this->duration_months)) {
            $this->end_date = Carbon::parse($this->start_date)
                ->addMonths((int)$this->duration_months)
                ->format('Y-m-d');
        } else {
            $this->end_date = null;
        }
    }

    public function update()
    {
        $this->validate([
            'action_type' => 'required|string',
            'training_entity' => 'required|string',
            'duration_months' => 'required|integer|min:1',
            'start_date' => 'required|date',
        ]);

        $training = TrainingSecondment::findOrFail($this->editingId);
        $training->update([
            'action_type' => $this->action_type,
            'training_entity' => $this->training_entity,
            'training_department' => $this->training_department,
            'duration_months' => $this->duration_months,
            'training_days' => $this->training_days,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);

        $this->dispatch('hide-edit-modal');
        session()->flash('success', 'تم تعديل السجل بنجاح.');
    }

    public function delete($id)
    {
        $training = TrainingSecondment::find($id);
        if ($training) {
            $training->delete();
            session()->flash('success', 'تم حذف سجل الإفاد بنجاح.');
        }
    }

    public function render()
    {
        $trainings = TrainingSecondment::with(['healthProfessional.facility', 'user'])
            ->when($this->search_name, function($query) {
                $query->whereHas('healthProfessional', function($q) {
                    $q->where('name', 'like', '%' . $this->search_name . '%');
                });
            })
            ->when($this->search_national_id, function($query) {
                $query->whereHas('healthProfessional', function($q) {
                    $q->where('national_id', 'like', '%' . $this->search_national_id . '%');
                });
            })
            ->when($this->search_facility, function($query) {
                $query->whereHas('healthProfessional.facility', function($q) {
                    $q->where('name', 'like', '%' . $this->search_facility . '%');
                });
            })
            ->when($this->search_training_entity, function($query) {
                $query->where('training_entity', 'like', '%' . $this->search_training_entity . '%');
            })
            ->when($this->search_from_date, function($query) {
                $query->where('start_date', '>=', $this->search_from_date);
            })
            ->when($this->search_to_date, function($query) {
                $query->where('end_date', '<=', $this->search_to_date);
            })
            ->latest()
            ->paginate(5);

        return view('livewire.postgraduate.training-secondment-index', compact('trainings'))->layout('layouts.app');
    }
}
