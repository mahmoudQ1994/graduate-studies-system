<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RejectionReason;
use Illuminate\Support\Facades\Auth;

class RejectionReasonsManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $reasonId;
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
            'name' => 'required|string|max:255|unique:rejection_reasons,name,' . $this->reasonId,
            'code' => 'required|string|max:50|unique:rejection_reasons,code,' . $this->reasonId,
            'notes' => 'nullable|string',
        ];
    }

    protected $messages = [
        'name.required' => 'سبب الرفض مطلوب.',
        'name.unique' => 'سبب الرفض موجود مسبقاً.',
        'code.required' => 'كود سبب الرفض مطلوب.',
        'code.unique' => 'كود سبب الرفض موجود مسبقاً.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function generateCode()
    {
        $latest = RejectionReason::latest()->first();
        if ($latest && $latest->code) {
            $number = (int)substr($latest->code, 4) + 1;
            $this->code = 'REJ-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        } else {
            $this->code = 'REJ-0001';
        }
    }

    public function resetFields()
    {
        $this->reset(['reasonId', 'name', 'notes', 'isEditMode']);
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
            RejectionReason::findOrFail($this->reasonId)->update($data);
            session()->flash('message', 'تم تحديث سبب الرفض بنجاح.');
        } else {
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();
            RejectionReason::create($data);
            session()->flash('message', 'تم إضافة سبب الرفض بنجاح.');
        }

        $this->resetFields();
    }

    public function edit($id)
    {
        $item = RejectionReason::findOrFail($id);
        $this->reasonId = $item->id;
        $this->code = $item->code;
        $this->name = $item->name;
        $this->notes = $item->notes;
        $this->isEditMode = true;
    }

    public function delete($id)
    {
        try {
            RejectionReason::findOrFail($id)->delete();
            session()->flash('message', 'تم حذف السجل بنجاح.');
            $this->generateCode();
        } catch (\Exception $e) {
            session()->flash('error', 'تعذر الحذف لوجود ارتباطات سابقة.');
        }
    }

    public function render()
    {
        $reasons = RejectionReason::query()
            ->with(['creator', 'editor'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('notes', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.training.rejection-reasons-manager', [
            'reasons' => $reasons,
        ])->layout('layouts.app', ['title' => 'قائمة سبب رفض البرامج التدريبية']);
    }
}
