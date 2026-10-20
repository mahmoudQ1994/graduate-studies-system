<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TechnicalSpecialization;
use Illuminate\Support\Facades\Auth;

class SpecializationsManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $specializationId;
    public $code = '';
    public $name = '';
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
            'code' => 'required|string|max:50|unique:technical_specializations,code,' . $this->specializationId,
            'name' => 'required|string|max:255|unique:technical_specializations,name,' . $this->specializationId,
            'description' => 'nullable|string',
        ];
    }

    protected $messages = [
        'code.required' => 'كود التخصص مطلوب.',
        'code.unique' => 'كود التخصص موجود مسبقاً.',
        'name.required' => 'اسم التخصص الفني مطلوب.',
        'name.unique' => 'اسم التخصص الفني موجود مسبقاً.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function generateCode()
    {
        $latest = TechnicalSpecialization::latest()->first();
        if ($latest && $latest->code) {
            $number = (int)substr($latest->code, 5) + 1;
            $this->code = 'SPEC-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        } else {
            $this->code = 'SPEC-0001';
        }
    }

    public function resetFields()
    {
        $this->reset(['specializationId', 'name', 'description', 'isEditMode']);
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
            'description' => $this->description,
        ];

        if ($this->isEditMode) {
            $data['updated_by'] = Auth::id();
            TechnicalSpecialization::findOrFail($this->specializationId)->update($data);
            session()->flash('message', 'تم تحديث التخصص الفني بنجاح.');
        } else {
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();
            TechnicalSpecialization::create($data);
            session()->flash('message', 'تم إضافة التخصص الفني بنجاح.');
        }

        $this->resetFields();
    }

    public function edit($id)
    {
        $item = TechnicalSpecialization::findOrFail($id);
        $this->specializationId = $item->id;
        $this->code = $item->code;
        $this->name = $item->name;
        $this->description = $item->description;
        $this->isEditMode = true;
    }

    public function delete($id)
    {
        try {
            TechnicalSpecialization::findOrFail($id)->delete();
            session()->flash('message', 'تم حذف السجل بنجاح.');
            $this->generateCode();
        } catch (\Exception $e) {
            session()->flash('error', 'تعذر الحذف لوجود ارتباطات سابقة.');
        }
    }

    public function render()
    {
        $specializations = TechnicalSpecialization::query()
            ->with(['creator', 'editor'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.training.specializations-manager', [
            'specializations' => $specializations,
        ])->layout('layouts.app', ['title' => 'قائمة التخصص الفني']);
    }
}
