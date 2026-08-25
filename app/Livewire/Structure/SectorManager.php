<?php

namespace App\Livewire\Structure;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Sector;
use App\Models\District;
use App\Models\facility;

#[Layout('layouts.app')]
class SectorManager extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $name = '';
    public string $description = '';
    public bool $is_active = true;
    public ?int $sector_id = null;
    public string $search = '';
    public bool $isEdit = false;
    public bool $showForm = false;

    protected array $rules = [
        'name' => 'required|min:3|max:100',
        'description' => 'nullable|max:255',
        'is_active' => 'boolean',
    ];

    protected array $messages = [
        'name.required' => 'اسم القطاع مطلوب',
        'name.min' => 'يجب أن لا يقل اسم القطاع عن 3 حروف',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetFields();
        }
    }

    public function resetFields(): void
    {
        $this->name = '';
        $this->description = '';
        $this->is_active = true;
        $this->sector_id = null;
        $this->isEdit = false;
        $this->showForm = false;
        $this->resetValidation();
    }

    public function store(): void
    {
        $this->validate();

        Sector::create([
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'تم إضافة القطاع بنجاح');
        $this->resetFields();
    }

    public function edit(int $id): void
    {
        $sector = Sector::findOrFail($id);
        $this->sector_id = $sector->id;
        $this->name = $sector->name;
        $this->description = $sector->description ?? '';
        $this->is_active = (bool) $sector->is_active;
        $this->isEdit = true;
        $this->showForm = true;
    }

    public function update(): void
    {
        $this->validate();

        $sector = Sector::findOrFail($this->sector_id);
        $sector->update([
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'تم تحديث بيانات القطاع بنجاح');
        $this->resetFields();
    }

    public function delete(int $id): void
    {
        Sector::findOrFail($id)->delete();
        session()->flash('message', 'تم حذف القطاع بنجاح');
    }

    public function toggleStatus(int $id): void
    {
        $sector = Sector::findOrFail($id);
        $sector->update(['is_active' => !$sector->is_active]);
    }

    public function render()
    {
        $sectors = Sector::where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(6);

        return view('livewire.structure.sector-manager', [
            'sectors' => $sectors
        ]);
    }
}
