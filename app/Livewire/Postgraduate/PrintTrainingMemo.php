<?php

namespace App\Livewire\Postgraduate;

use App\Models\TrainingSecondment;
use Livewire\Component;
use App\Models\Setting;
use App\Models\OfficialHeader;

class PrintTrainingMemo extends Component
{
    public $search_national_id = ''; // تم توحيد الاسم هنا ليطابق الـ Blade
    public $selected_id = null;
    public $trainingRecord = null;
    public $printType = 'memo';

    // تحديث السجل عند اختيار ID من القائمة
    public function updatedSelectedId($value)
    {
        if ($value) {
            $this->trainingRecord = TrainingSecondment::with([
                'healthProfessional.facility',
                'healthProfessional.medicalMovements'
            ])->find($value);
        } else {
            $this->trainingRecord = null;
        }
    }

    // البحث المباشر بالرقم القومي أو الاسم
    public function updatedSearchNationalId($value)
    {
        $this->trainingRecord = null;

        if (!empty($value)) {
            $record = TrainingSecondment::with([
                'healthProfessional.facility',
                'healthProfessional.medicalMovements'
            ])
            ->whereHas('healthProfessional', function ($query) use ($value) {
                if (strlen($value) === 14) {
                    $query->where('national_id', $value); // مطابقة دقيقة للرقم القومي كاملاً
                } else {
                    $query->where('name', 'like', '%' . $value . '%')
                          ->orWhere('national_id', 'like', '%' . $value . '%');
                }
            })
            ->latest()
            ->first();

            if ($record) {
                $this->selected_id = $record->id;
                $this->trainingRecord = $record;
            }
        }
    }

    public function render()
    {
        $records = TrainingSecondment::with(['healthProfessional.facility'])
            ->when($this->search_national_id, function ($q) {
                $q->whereHas('healthProfessional', function ($query) {
                    $query->where('name', 'like', '%' . $this->search_national_id . '%')
                          ->orWhere('national_id', 'like', '%' . $this->search_national_id . '%');
                });
            })
            ->latest()
            ->take(20)
            ->get();

        return view('livewire.postgraduate.print-training-memo', [
            'records' => $records,
            'settings' => Setting::first(),
            'officialHeader' => OfficialHeader::first(),
        ])->layout('layouts.app');
    }
}
