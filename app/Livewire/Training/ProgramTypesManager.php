<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TrainingProgramType;

class ProgramTypesManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // حقول النموذج
    public $typeId;
    public $name = '';
    public $code = '';
    public $description = '';
    public $is_active = true;

    // حقول البحث والفلترة
    public $search = '';
    public $isEditMode = false;

    // تشغيل التوليد التلقائي عند فتح الصفحة
    public function mount()
    {
        $this->generateCode();
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:training_program_types,name,' . $this->typeId,
            'code' => 'nullable|string|max:50|unique:training_program_types,code,' . $this->typeId,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    protected $messages = [
        'name.required' => 'اسم نوع البرنامج المطلوب مدخل أساسي.',
        'name.unique' => 'اسم نوع البرنامج موجود بالفعل.',
        'code.unique' => 'كود البرنامج موجود بالفعل.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // دالة لتوليد كود تلقائي جديد
    public function generateCode()
    {
        $latestType = TrainingProgramType::latest()->first();
        if ($latestType && $latestType->code) {
            $lastCode = $latestType->code;
            // استخراج الأرقام بعد الحرفين وتوليد الرقم التالي
            $number = (int)substr($lastCode, 2) + 1;
            $newCode = 'TP' . str_pad($number, 4, '0', STR_PAD_LEFT);
        } else {
            $newCode = 'TP0001';
        }
        $this->code = $newCode;
    }

    public function resetFields()
    {
        $this->reset(['typeId', 'name', 'code', 'description', 'is_active', 'isEditMode']);
        $this->resetValidation();
        $this->generateCode(); // توليد كود جديد للإضافة القادمة
    }

    public function save()
    {
        // إذا كان الكود فارغاً لأي سبب، نقوم بتوليده قبل التحفظ
        if (empty($this->code)) {
            $this->generateCode();
        }

        $this->validate();

        TrainingProgramType::updateOrCreate(
            ['id' => $this->typeId],
            [
                'name' => $this->name,
                'code' => $this->code,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ]
        );

        session()->flash('message', $this->isEditMode ? 'تم تحديث نوع البرنامج بنجاح.' : 'تم إضافة نوع البرنامج بنجاح.');

        $this->resetFields();
    }

    public function edit($id)
    {
        $type = TrainingProgramType::findOrFail($id);
        $this->typeId = $type->id;
        $this->name = $type->name;
        $this->code = $type->code;
        $this->description = $type->description;
        $this->is_active = $type->is_active;
        $this->isEditMode = true;
    }

    public function delete($id)
    {
        try {
            TrainingProgramType::findOrFail($id)->delete();
            session()->flash('message', 'تم حذف نوع البرنامج بنجاح.');
            $this->generateCode();
        } catch (\Exception $e) {
            session()->flash('error', 'تعذر الحذف لوجود بيانات مرتبطة بهذا النوع.');
        }
    }

    public function toggleStatus($id)
    {
        $type = TrainingProgramType::findOrFail($id);
        $type->update(['is_active' => !$type->is_active]);
    }

    public function render()
    {
        $types = TrainingProgramType::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.training.program-types-manager', [
            'types' => $types,
        ])->layout('layouts.app', ['title' => 'إدخال نوع البرامج التدريبية']);
    }
}
