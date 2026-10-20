<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TrainingHourlyRate;
use Illuminate\Support\Facades\Auth;

class HourlyRatesManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $rateId;
    public $code = '';
    public $category_value = '';
    public $hourly_rate = '';
    public $regulation_name = '';
    public $description = '';

    public $search = '';
    public $isEditMode = false;

    public function mount()
    {
        $this->generateCode();
    }

    protected function rules()
    {
        return [
            'code' => 'required|string|max:50|unique:training_hourly_rates,code,' . $this->rateId,
            'category_value' => 'required|numeric|min:0',
            'hourly_rate' => 'required|numeric|min:0',
            'regulation_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    protected $messages = [
        'code.required' => 'كود المعدل مطلوب.',
        'code.unique' => 'كود المعدل موجود مسبقاً.',
        'category_value.required' => 'قيمة الفئة مطلوبة.',
        'hourly_rate.required' => 'معدل الساعة مطلوب.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function generateCode()
    {
        $latest = TrainingHourlyRate::latest()->first();
        if ($latest && $latest->code) {
            $number = (int)substr($latest->code, 4) + 1;
            $this->code = 'HRS-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        } else {
            $this->code = 'HRS-0001';
        }
    }

    public function resetFields()
    {
        $this->reset(['rateId', 'category_value', 'hourly_rate', 'regulation_name', 'description', 'isEditMode']);
        $this->resetValidation();
        $this->generateCode();
    }

    public function save()
    {
        if (empty($this->code)) {
            $this->generateCode();
        }

        $this->validate();

        $data = [
            'code' => $this->code,
            'category_value' => $this->category_value,
            'hourly_rate' => $this->hourly_rate,
            'regulation_name' => $this->regulation_name,
            'description' => $this->description,
        ];

        if ($this->isEditMode) {
            $data['updated_by'] = Auth::id();
            TrainingHourlyRate::findOrFail($this->rateId)->update($data);
            session()->flash('message', 'تم تحديث معدل الساعات بنجاح.');
        } else {
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();
            TrainingHourlyRate::create($data);
            session()->flash('message', 'تم إضافة معدل الساعات بنجاح.');
        }

        $this->resetFields();
    }

    public function edit($id)
    {
        $item = TrainingHourlyRate::findOrFail($id);
        $this->rateId = $item->id;
        $this->code = $item->code;
        $this->category_value = $item->category_value;
        $this->hourly_rate = $item->hourly_rate;
        $this->regulation_name = $item->regulation_name;
        $this->description = $item->description;
        $this->isEditMode = true;
    }

    public function delete($id)
    {
        try {
            TrainingHourlyRate::findOrFail($id)->delete();
            session()->flash('message', 'تم حذف السجل بنجاح.');
            $this->generateCode();
        } catch (\Exception $e) {
            session()->flash('error', 'تعذر الحذف لوجود ارتباطات سابقة.');
        }
    }

    public function render()
    {
        $rates = TrainingHourlyRate::query()
            ->with(['creator', 'editor'])
            ->when($this->search, function ($query) {
                $query->where('code', 'like', '%' . $this->search . '%')
                      ->orWhere('regulation_name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('category_value','desc')
            ->paginate(10);

        return view('livewire.training.hourly-rates-manager', [
            'rates' => $rates,
        ])->layout('layouts.app', ['title' => 'قائمة معدل ساعات التدريب']);
    }
}
