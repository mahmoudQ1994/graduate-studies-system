<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\JobStatus;
use Illuminate\Support\Facades\Auth;

class JobStatusesManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $statusId;
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
            'code' => 'required|string|max:50|unique:job_statuses,code,' . $this->statusId,
            'name' => 'required|string|max:255|unique:job_statuses,name,' . $this->statusId,
            'description' => 'nullable|string',
        ];
    }

    protected $messages = [
        'code.required' => 'كود الحالة مطلوب.',
        'code.unique' => 'كود الحالة موجود مسبقاً.',
        'name.required' => 'اسم الحالة الوظيفية مطلوب.',
        'name.unique' => 'اسم الحالة الوظيفية موجود مسبقاً.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function generateCode()
    {
        $latest = JobStatus::latest()->first();
        if ($latest && $latest->code) {
            $number = (int)substr($latest->code, 4) + 1;
            $this->code = 'JOB-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        } else {
            $this->code = 'JOB-0001';
        }
    }

    public function resetFields()
    {
        $this->reset(['statusId', 'name', 'description', 'isEditMode']);
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
            JobStatus::findOrFail($this->statusId)->update($data);
            session()->flash('message', 'تم تحديث الحالة الوظيفية بنجاح.');
        } else {
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();
            JobStatus::create($data);
            session()->flash('message', 'تم إضافة الحالة الوظيفية بنجاح.');
        }

        $this->resetFields();
    }

    public function edit($id)
    {
        $item = JobStatus::findOrFail($id);
        $this->statusId = $item->id;
        $this->code = $item->code;
        $this->name = $item->name;
        $this->description = $item->description;
        $this->isEditMode = true;
    }

    public function delete($id)
    {
        try {
            JobStatus::findOrFail($id)->delete();
            session()->flash('message', 'تم حذف السجل بنجاح.');
            $this->generateCode();
        } catch (\Exception $e) {
            session()->flash('error', 'تعذر الحذف لوجود ارتباطات سابقة.');
        }
    }

    public function render()
    {
        $statuses = JobStatus::query()
            ->with(['creator', 'editor'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.training.job-statuses-manager', [
            'statuses' => $statuses,
        ])->layout('layouts.app', ['title' => 'قائمة الحالة الوظيفية']);
    }
}
