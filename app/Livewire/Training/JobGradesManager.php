<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\JobGrade;
use Illuminate\Support\Facades\Auth;

class JobGradesManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $gradeId;
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
            'code' => 'required|string|max:50|unique:job_grades,code,' . $this->gradeId,
            'name' => 'required|string|max:255|unique:job_grades,name,' . $this->gradeId,
            'description' => 'nullable|string',
        ];
    }

    protected $messages = [
        'code.required' => 'كود الدرجة مطلوب.',
        'code.unique' => 'كود الدرجة موجود مسبقاً.',
        'name.required' => 'اسم الدرجة الوظيفية مطلوب.',
        'name.unique' => 'اسم الدرجة الوظيفية موجود مسبقاً.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function generateCode()
    {
        $latest = JobGrade::latest()->first();
        if ($latest && $latest->code) {
            $number = (int)substr($latest->code, 4) + 1;
            $this->code = 'GRD-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        } else {
            $this->code = 'GRD-0001';
        }
    }

    public function resetFields()
    {
        $this->reset(['gradeId', 'name', 'description', 'isEditMode']);
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
            JobGrade::findOrFail($this->gradeId)->update($data);
            session()->flash('message', 'تم تحديث الدرجة الوظيفية بنجاح.');
        } else {
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();
            JobGrade::create($data);
            session()->flash('message', 'تم إضافة الدرجة الوظيفية بنجاح.');
        }

        $this->resetFields();
    }

    public function edit($id)
    {
        $item = JobGrade::findOrFail($id);
        $this->gradeId = $item->id;
        $this->code = $item->code;
        $this->name = $item->name;
        $this->description = $item->description;
        $this->isEditMode = true;
    }

    public function delete($id)
    {
        try {
            JobGrade::findOrFail($id)->delete();
            session()->flash('message', 'تم حذف السجل بنجاح.');
            $this->generateCode();
        } catch (\Exception $e) {
            session()->flash('error', 'تعذر الحذف لوجود ارتباطات سابقة.');
        }
    }

    public function render()
    {
        $grades = JobGrade::query()
            ->with(['creator', 'editor'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.training.job-grades-manager', [
            'grades' => $grades,
        ])->layout('layouts.app', ['title' => 'قائمة الدرجات الوظيفية']);
    }
}
