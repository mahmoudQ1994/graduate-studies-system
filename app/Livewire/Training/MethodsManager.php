<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TrainingMethod;
use Illuminate\Support\Facades\Auth;

class MethodsManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // حقول النموذج
    public $methodId;
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
            'name' => 'required|string|max:255|unique:training_methods,name,' . $this->methodId,
            'code' => 'required|string|max:50|unique:training_methods,code,' . $this->methodId,
            'notes' => 'nullable|string',
        ];
    }

    protected $messages = [
        'name.required' => 'اسم أسلوب التدريب مطلوب.',
        'name.unique' => 'اسم أسلوب التدريب موجود مسبقاً.',
        'code.required' => 'كود أسلوب التدريب مطلوب.',
        'code.unique' => 'كود أسلوب التدريب موجود مسبقاً.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function generateCode()
    {
        $latestMethod = TrainingMethod::latest()->first();
        if ($latestMethod && $latestMethod->code) {
            $number = (int)substr($latestMethod->code, 4) + 1;
            $this->code = 'MTH-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        } else {
            $this->code = 'MTH-0001';
        }
    }

    public function resetFields()
    {
        $this->reset(['methodId', 'name', 'notes', 'isEditMode']);
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
            TrainingMethod::findOrFail($this->methodId)->update($data);
            session()->flash('message', 'تم تحديث أسلوب التدريب بنجاح.');
        } else {
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();
            TrainingMethod::create($data);
            session()->flash('message', 'تم إضافة أسلوب التدريب بنجاح.');
        }

        $this->resetFields();
    }

    public function edit($id)
    {
        $method = TrainingMethod::findOrFail($id);
        $this->methodId = $method->id;
        $this->code = $method->code;
        $this->name = $method->name;
        $this->notes = $method->notes;
        $this->isEditMode = true;
    }

    public function delete($id)
    {
        try {
            TrainingMethod::findOrFail($id)->delete();
            session()->flash('message', 'تم حذف أسلوب التدريب بنجاح.');
            $this->generateCode();
        } catch (\Exception $e) {
            session()->flash('error', 'تعذر حذف أسلوب التدريب لوجود ارتباطات سابقة.');
        }
    }

    public function render()
    {
        $methods = TrainingMethod::query()
            ->with(['creator', 'editor'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('notes', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.training.methods-manager', [
            'methods' => $methods,
        ])->layout('layouts.app', ['title' => 'إدخال أسلوب التدريب']);
    }
}
