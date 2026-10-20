<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TrainingProgram;
use App\Models\TrainingProgramType;
use App\Models\TrainingHall;
use App\Models\TrainingMethod;
use App\Models\KnowledgeMeasurement;
use App\Models\ImpactMeasurement;
use App\Models\JobStatus;
use Illuminate\Support\Facades\Auth;

class TrainingProgramManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // حقول النموذج الأساسية
    public $programId;
    public $code = '';
    public $name_ar = '';
    public $name_en = '';
    public $program_type_id = '';
    public $hall_id = '';
    public $level = '';
    public $consultant_name = '';
    public $duration_days = 1;
    public $training_hours = 0;
    public $courses_count_per_year = 1;

    // أهداف البرنامج ومططلباته
    public $objective_knowledge = '';
    public $objective_skills = '';
    public $objective_attitude = '';
    public $prerequisites = '';

    // حقول الاختيارات المتعددة (Arrays)
    public $selectedMethods = [];
    public $selectedKnowledge = [];
    public $selectedImpact = [];
    public $selectedAudiences = [];

    // التحكم بالعرض والبحث
    public $search = '';
    public $isEditMode = false;

    public function mount()
    {
        $this->generateCode();
    }

    protected function rules()
    {
        return [
            'code' => 'required|string|max:50|unique:training_programs,code,' . $this->programId,
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'program_type_id' => 'required|exists:training_program_types,id',
            'hall_id' => 'nullable|exists:training_halls,id',
            'level' => 'nullable|string|max:100',
            'consultant_name' => 'nullable|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'training_hours' => 'required|integer|min:0',
            'courses_count_per_year' => 'required|integer|min:1',
            'objective_knowledge' => 'nullable|string',
            'objective_skills' => 'nullable|string',
            'objective_attitude' => 'nullable|string',
            'prerequisites' => 'nullable|string',
            'selectedMethods' => 'array',
            'selectedKnowledge' => 'array',
            'selectedImpact' => 'array',
            'selectedAudiences' => 'array',
        ];
    }

    protected $messages = [
        'code.required' => 'كود البرنامج مطلوب.',
        'code.unique' => 'كود البرنامج موجود مسبقاً.',
        'name_ar.required' => 'اسم البرنامج بالعربية مطلوب.',
        'program_type_id.required' => 'نوع البرنامج مطلوب.',
        'duration_days.required' => 'عدد الأيام مطلوب.',
        'training_hours.required' => 'عدد الساعات التدريبية مطلوب.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function generateCode()
    {
        $latest = TrainingProgram::latest()->first();
        if ($latest && $latest->code) {
            $number = (int)substr($latest->code, 4) + 1;
            $this->code = 'PRG-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        } else {
            $this->code = 'PRG-0001';
        }
    }

    public function resetFields()
    {
        $this->reset([
            'programId', 'name_ar', 'name_en', 'program_type_id', 'hall_id',
            'level', 'consultant_name', 'duration_days', 'training_hours',
            'courses_count_per_year', 'objective_knowledge', 'objective_skills',
            'objective_attitude', 'prerequisites', 'selectedMethods',
            'selectedKnowledge', 'selectedImpact', 'selectedAudiences', 'isEditMode'
        ]);
        $this->duration_days = 1;
        $this->training_hours = 0;
        $this->courses_count_per_year = 1;
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
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'program_type_id' => $this->program_type_id,
            'hall_id' => $this->hall_id ?: null,
            'level' => $this->level,
            'consultant_name' => $this->consultant_name,
            'duration_days' => $this->duration_days,
            'training_hours' => $this->training_hours,
            'courses_count_per_year' => $this->courses_count_per_year,
            'objective_knowledge' => $this->objective_knowledge,
            'objective_skills' => $this->objective_skills,
            'objective_attitude' => $this->objective_attitude,
            'prerequisites' => $this->prerequisites,
        ];

        if ($this->isEditMode) {
            $data['updated_by'] = Auth::id();
            $program = TrainingProgram::findOrFail($this->programId);
            $program->update($data);
            session()->flash('message', 'تم تحديث البرنامج التدريبي بنجاح.');
        } else {
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();
            $program = TrainingProgram::create($data);
            session()->flash('message', 'تم إدخال البرنامج التدريبي بنجاح.');
        }

        // مزامنة الجداول الوسيطة (Pivot Relations)
        $program->methods()->sync($this->selectedMethods);
        $program->knowledgeMeasurements()->sync($this->selectedKnowledge);
        $program->impactMeasurements()->sync($this->selectedImpact);
        $program->targetAudiences()->sync($this->selectedAudiences);

        $this->resetFields();
    }

    public function edit($id)
    {
        $program = TrainingProgram::with(['methods', 'knowledgeMeasurements', 'impactMeasurements', 'targetAudiences'])->findOrFail($id);

        $this->programId = $program->id;
        $this->code = $program->code;
        $this->name_ar = $program->name_ar;
        $this->name_en = $program->name_en;
        $this->program_type_id = $program->program_type_id;
        $this->hall_id = $program->hall_id;
        $this->level = $program->level;
        $this->consultant_name = $program->consultant_name;
        $this->duration_days = $program->duration_days;
        $this->training_hours = $program->training_hours;
        $this->courses_count_per_year = $program->courses_count_per_year;
        $this->objective_knowledge = $program->objective_knowledge;
        $this->objective_skills = $program->objective_skills;
        $this->objective_attitude = $program->objective_attitude;
        $this->prerequisites = $program->prerequisites;

        // تعبئة الاختيارات المتعددة للعرض والتعديل
        $this->selectedMethods = $program->methods->pluck('id')->toArray();
        $this->selectedKnowledge = $program->knowledgeMeasurements->pluck('id')->toArray();
        $this->selectedImpact = $program->impactMeasurements->pluck('id')->toArray();
        $this->selectedAudiences = $program->targetAudiences->pluck('id')->toArray();

        $this->isEditMode = true;
    }

    public function delete($id)
    {
        try {
            $program = TrainingProgram::findOrFail($id);
            // حذف الارتباطات أولاً لضمان عدم وجود قيود
            $program->methods()->detach();
            $program->knowledgeMeasurements()->detach();
            $program->impactMeasurements()->detach();
            $program->targetAudiences()->detach();

            $program->delete();
            session()->flash('message', 'تم حذف البرنامج التدريبي بنجاح.');
            $this->generateCode();
        } catch (\Exception $e) {
            session()->flash('error', 'تعذر الحذف لوجود ارتباطات سابقة.');
        }
    }

    public function render()
    {
        $programs = TrainingProgram::query()
            ->with(['programType', 'hall', 'creator', 'editor'])
            ->when($this->search, function ($query) {
                $query->where('name_ar', 'like', '%' . $this->search . '%')
                      ->orWhere('name_en', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%')
                      ->orWhere('consultant_name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.training.training-program-manager', [
            'programs' => $programs,
            'programTypes' => TrainingProgramType::all(),
            'halls' => TrainingHall::all(),
            'allMethods' => TrainingMethod::all(),
            'allKnowledge' => KnowledgeMeasurement::all(),
            'allImpact' => ImpactMeasurement::all(),
            'allAudiences' => JobStatus::all(), // استخدام الحالات الوظيفية كفئات مستهدفة
        ])->layout('layouts.app', ['title' => 'إدخال برنامج تدريبي']);
    }
}
