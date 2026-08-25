<?php

namespace App\Livewire\Structure;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\District;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class DistrictManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // التأكد من وجود الخصائص العامة التالية
    public string $name;
    public bool $is_active = true;
    public ?int $district_id = null;
    public string $search = '';
    public bool $isEdit = false;
    public bool $showForm = false; // <-- هذا المتغير الهام للتحكم بالإظهار والإخفاء

    protected $rules = [
        'name' => 'required|min:3|max:100',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'اسم المركز أو المدينة مطلوب',
        'name.min' => 'يجب أن لا يقل اسم المركز عن 3 حروف',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetFields();
        }
    }

    public function resetFields()
    {
        $this->name = '';
        $this->is_active = true;
        $this->district_id = null;
        $this->isEdit = false;
        $this->showForm = false;
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();

        District::create([
            'name' => $this->name,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'تم إضافة المركز بنجاح');
        $this->resetFields();
    }

    public function edit($id)
    {
        $district = District::findOrFail($id);
        $this->district_id = $district->id;
        $this->name = $district->name;
        $this->is_active = (bool) $district->is_active;
        $this->isEdit = true;
        $this->showForm = true;
    }

    public function update()
    {
        $this->validate();

        $district = District::findOrFail($this->district_id);
        $district->update([
            'name' => $this->name,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'تم تحديث بيانات المركز بنجاح');
        $this->resetFields();
    }

    public function delete($id)
    {
        District::findOrFail($id)->delete();
        session()->flash('message', 'تم حذف المركز بنجاح');
    }

    public function toggleStatus($id)
    {
        $district = District::findOrFail($id);
        $district->update(['is_active' => !$district->is_active]);
    }

    public function render()
    {
        $districts = District::where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(6);

        return view('livewire.structure.district-manager', [
            'districts' => $districts
        ])->layout('layouts.app');
    }
}
