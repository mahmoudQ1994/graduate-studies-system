<?php
namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Department;

class DepartmentManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $department_id;
    public $name;
    public $code;
    public $description;
    public $is_active = true;

    public $search = '';
    public $isEditMode = false;

    // دالة لتوليد كود تلقائي برقم مسلسل
    public function generateDepartmentCode()
    {
        $lastId = Department::max('id') ?? 0;
        $nextNumber = str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
        return 'DEP-' . $nextNumber; // ينتج كود مثل: DEP-001
    }

    // إعادة ضبط الحقول وتوليد كود جديد تلقائياً
    public function resetFields()
    {
        $this->department_id = null;
        $this->name = '';
        $this->code = $this->generateDepartmentCode(); // توليد الكود آلياً عند إضافة جديد
        $this->description = '';
        $this->is_active = true;
        $this->isEditMode = false;
        $this->resetValidation();
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code,' . $this->department_id,
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ];
    }

    public function save()
    {
        // إذا كان الحقل فارغاً لأي سبب، يتم توليد الكود تلقائياً
        if (empty($this->code) && !$this->isEditMode) {
            $this->code = $this->generateDepartmentCode();
        }

        $this->validate();

        Department::updateOrCreate(
            ['id' => $this->department_id],
            [
                'name' => $this->name,
                'code' => $this->code,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ]
        );

        session()->flash('success', $this->isEditMode ? 'تم تعديل بيانات القسم بنجاح' : 'تم إضافة القسم بنجاح');

        $this->resetFields();
        $this->dispatch('close-modal');
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);
        $this->department_id = $department->id;
        $this->name = $department->name;
        $this->code = $department->code;
        $this->description = $department->description;
        $this->is_active = $department->is_active;
        $this->isEditMode = true;
    }

    public function toggleStatus($id)
    {
        $department = Department::findOrFail($id);
        $department->is_active = !$department->is_active;
        $department->save();

        session()->flash('success', 'تم تغيير حالة القسم بنجاح');
    }

    public function delete($id)
    {
        Department::findOrFail($id)->delete();
        session()->flash('success', 'تم حذف القسم بنجاح');
    }

    public function render()
    {
        $departments = Department::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('code', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.settings.department-manager', [
            'departments' => $departments
        ])->layout('layouts.app');
    }
}
