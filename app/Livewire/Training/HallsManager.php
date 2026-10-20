<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TrainingHall;
use Illuminate\Support\Facades\Auth;

class HallsManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // حقول النموذج
    public $hallId;
    public $code = '';
    public $name = '';
    public $location = '';
    public $chairs_count = 0;

    // التجهيزات
    public $has_computer = false;
    public $has_sound_system = false;
    public $has_ac = false;
    public $has_whiteboard = false;
    public $has_projector = false;
    public $is_lighting_sufficient = true;

    public $search = '';
    public $isEditMode = false;

    public function mount()
    {
        $this->generateCode();
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:training_halls,code,' . $this->hallId,
            'location' => 'nullable|string|max:255',
            'chairs_count' => 'required|integer|min:0',
            'has_computer' => 'boolean',
            'has_sound_system' => 'boolean',
            'has_ac' => 'boolean',
            'has_whiteboard' => 'boolean',
            'has_projector' => 'boolean',
            'is_lighting_sufficient' => 'boolean',
        ];
    }

    protected $messages = [
        'name.required' => 'اسم القاعة مطلوب.',
        'code.required' => 'كود القاعة مطلوب.',
        'code.unique' => 'كود القاعة موجود مسبقاً.',
        'chairs_count.required' => 'عدد الكراسي مطلوب.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function generateCode()
    {
        $latestHall = TrainingHall::latest()->first();
        if ($latestHall && $latestHall->code) {
            $number = (int)substr($latestHall->code, 3) + 1;
            $this->code = 'HL-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        } else {
            $this->code = 'HL-0001';
        }
    }

    public function resetFields()
    {
        $this->reset([
            'hallId', 'name', 'location', 'chairs_count',
            'has_computer', 'has_sound_system', 'has_ac',
            'has_whiteboard', 'has_projector', 'is_lighting_sufficient', 'isEditMode'
        ]);
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
            'location' => $this->location,
            'chairs_count' => $this->chairs_count,
            'has_computer' => $this->has_computer,
            'has_sound_system' => $this->has_sound_system,
            'has_ac' => $this->has_ac,
            'has_whiteboard' => $this->has_whiteboard,
            'has_projector' => $this->has_projector,
            'is_lighting_sufficient' => $this->is_lighting_sufficient,
        ];

        if ($this->isEditMode) {
            $data['updated_by'] = Auth::id();
            TrainingHall::findOrFail($this->hallId)->update($data);
            session()->flash('message', 'تم تحديث بيانات القاعة بنجاح.');
        } else {
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();
            TrainingHall::create($data);
            session()->flash('message', 'تم إضافة القاعة بنجاح.');
        }

        $this->resetFields();
    }

    public function edit($id)
    {
        $hall = TrainingHall::findOrFail($id);
        $this->hallId = $hall->id;
        $this->code = $hall->code;
        $this->name = $hall->name;
        $this->location = $hall->location;
        $this->chairs_count = $hall->chairs_count;
        $this->has_computer = $hall->has_computer;
        $this->has_sound_system = $hall->has_sound_system;
        $this->has_ac = $hall->has_ac;
        $this->has_whiteboard = $hall->has_whiteboard;
        $this->has_projector = $hall->has_projector;
        $this->is_lighting_sufficient = $hall->is_lighting_sufficient;
        $this->isEditMode = true;
    }

    public function delete($id)
    {
        try {
            TrainingHall::findOrFail($id)->delete();
            session()->flash('message', 'تم حذف القاعة بنجاح.');
            $this->generateCode();
        } catch (\Exception $e) {
            session()->flash('error', 'تعذر حذف القاعة لوجود ارتباطات سابقة.');
        }
    }

    public function render()
    {
        $halls = TrainingHall::query()
            ->with(['creator', 'editor'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('location', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.training.halls-manager', [
            'halls' => $halls,
        ])->layout('layouts.app', ['title' => 'تكويد قاعات التدريب']);
    }
}
