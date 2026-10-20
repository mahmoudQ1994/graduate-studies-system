<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ImpactMeasurement;
use Illuminate\Support\Facades\Auth;

class ImpactManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $impactId;
    public $code = '';
    public $name = '';
    public $notes = '';

    public $search = '';
    public $isEditMode = false;

    public function mount()
    {
        $this->generateCode();
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:impact_measurements,name,' . $this->impactId,
            'code' => 'required|string|max:50|unique:impact_measurements,code,' . $this->impactId,
            'notes' => 'nullable|string',
        ];
    }

    protected $messages = [
        'name.required' => 'اسم قياس أثر التدريب مطلوب.',
        'name.unique' => 'اسم قياس أثر التدريب موجود مسبقاً.',
        'code.required' => 'كود القياس مطلوب.',
        'code.unique' => 'كود القياس موجود مسبقاً.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function generateCode()
    {
        $latest = ImpactMeasurement::latest()->first();
        if ($latest && $latest->code) {
            $number = (int)substr($latest->code, 4) + 1;
            $this->code = 'IMP-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        } else {
            $this->code = 'IMP-0001';
        }
    }

    public function resetFields()
    {
        $this->reset(['impactId', 'name', 'notes', 'isEditMode']);
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
            'name' => $this->name,
            'notes' => $this->notes,
        ];

        if ($this->isEditMode) {
            $data['updated_by'] = Auth::id();
            ImpactMeasurement::findOrFail($this->impactId)->update($data);
            session()->flash('message', 'تم تحديث بيانات قياس أثر التدريب بنجاح.');
        } else {
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();
            ImpactMeasurement::create($data);
            session()->flash('message', 'تم إضافة قياس أثر التدريب بنجاح.');
        }

        $this->resetFields();
    }

    public function edit($id)
    {
        $item = ImpactMeasurement::findOrFail($id);
        $this->impactId = $item->id;
        $this->code = $item->code;
        $this->name = $item->name;
        $this->notes = $item->notes;
        $this->isEditMode = true;
    }

    public function delete($id)
    {
        try {
            ImpactMeasurement::findOrFail($id)->delete();
            session()->flash('message', 'تم حذف السجل بنجاح.');
            $this->generateCode();
        } catch (\Exception $e) {
            session()->flash('error', 'تعذر الحذف لوجود ارتباطات سابقة.');
        }
    }

    public function render()
    {
        $measurements = ImpactMeasurement::query()
            ->with(['creator', 'editor'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('notes', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.training.impact-manager', [
            'measurements' => $measurements,
        ])->layout('layouts.app', ['title' => 'قائمة قياس أثر التدريب']);
    }
}
