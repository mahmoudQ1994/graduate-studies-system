<?php

namespace App\Livewire\Training;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ProgramEvaluationReport extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $selected_program_id = '';
    public $selected_schedule_id = '';
    public $search = '';
    public $evaluationLink = '';

    public $selectedEvaluation = null;
    public $selectedTraineeName = '';

    public function updatedSelectedProgramId($value)
    {
        $this->selected_schedule_id = '';
        $this->evaluationLink = '';
        $this->resetPage();
    }

    public function updatedSelectedScheduleId($value)
    {
        if ($value) {
            $this->evaluationLink = route('training.evaluation', ['schedule_id' => $value]);
        } else {
            $this->evaluationLink = '';
        }
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewTraineeEvaluation($traineeId)
    {
        $enrollment = DB::table('trainee_enrollments as te')
            ->join('trainees as t', 't.id', '=', 'te.trainee_id')
            ->leftJoin('program_evaluations as pe', function($join) {
                $join->on('pe.training_program_schedule_id', '=', 'te.training_program_schedule_id')
                     ->on('pe.trainee_id', '=', 'te.trainee_id');
            })
            ->where('te.training_program_schedule_id', $this->selected_schedule_id)
            ->where('te.trainee_id', $traineeId)
            ->select('pe.*', 't.name_ar as trainee_name')
            ->first();

        if ($enrollment) {
            $this->selectedTraineeName = $enrollment->trainee_name;
            $this->selectedEvaluation = $enrollment;
            $this->dispatch('show-evaluation-modal');
        }
    }

    public function render()
    {
        $programs = DB::table('training_programs as tp')
            ->join('training_program_schedules as tps', 'tp.id', '=', 'tps.training_program_id')
            ->select('tp.id', 'tp.name_ar')
            ->distinct()
            ->orderBy('tp.name_ar', 'asc')
            ->get();

        $availableSchedules = collect();
        if ($this->selected_program_id) {
            $availableSchedules = DB::table('training_program_schedules')
                ->where('training_program_id', $this->selected_program_id)
                ->select('id', 'start_date', 'end_date')
                ->orderBy('start_date', 'desc')
                ->get();
        }

        $enrollments = collect();
        $scheduleDetails = null;

        // تعريف مصفوفة الإحصائيات مع تضمين نسبة الرضا والتقييم ككل
        $stats = [
            'total_enrolled' => 0,
            'submitted_evaluations' => 0,
            'pending_evaluations' => 0,
            'satisfaction_rate' => 0,
        ];

        if ($this->selected_schedule_id) {
            $scheduleDetails = DB::table('training_program_schedules as tps')
                ->join('training_programs as tp', 'tp.id', '=', 'tps.training_program_id')
                ->where('tps.id', $this->selected_schedule_id)
                ->select('tps.*', 'tp.name_ar as program_name')
                ->first();

            $stats['total_enrolled'] = DB::table('trainee_enrollments')
                ->where('training_program_schedule_id', $this->selected_schedule_id)
                ->count();

            $stats['submitted_evaluations'] = DB::table('program_evaluations')
                ->where('training_program_schedule_id', $this->selected_schedule_id)
                ->count();

            $stats['pending_evaluations'] = max(0, $stats['total_enrolled'] - $stats['submitted_evaluations']);

            // حساب نسبة التقييم والرضا كنسبة مؤوية للتسليم أو التقييم الفعلي
            $stats['satisfaction_rate'] = $stats['total_enrolled'] > 0
                ? round(($stats['submitted_evaluations'] / $stats['total_enrolled']) * 100)
                : 0;

            $query = DB::table('trainee_enrollments as te')
                ->join('trainees as t', 't.id', '=', 'te.trainee_id')
                ->leftJoin('program_evaluations as pe', function($join) {
                    $join->on('pe.training_program_schedule_id', '=', 'te.training_program_schedule_id')
                         ->on('pe.trainee_id', '=', 'te.trainee_id');
                })
                ->where('te.training_program_schedule_id', $this->selected_schedule_id)
                ->select(
                    'te.*',
                    'pe.official_email',
                    'pe.id as evaluation_id',
                    't.id as trainee_id',
                    't.name_ar as trainee_name',
                    't.national_id',
                    't.phone_number',
                    't.job_title',
                    't.workplace'
                )
                ->when($this->search, function ($q) {
                    $q->where(function ($query) {
                        $query->where('t.name_ar', 'like', '%' . $this->search . '%')
                              ->orWhere('t.national_id', 'like', '%' . $this->search . '%')
                              ->orWhere('t.phone_number', 'like', '%' . $this->search . '%');
                    });
                });

            $enrollments = $query->paginate(15);
        }

        return view('livewire.training.program-evaluation-report', [
            'programs' => $programs,
            'availableSchedules' => $availableSchedules,
            'enrollments' => $enrollments,
            'scheduleDetails' => $scheduleDetails,
            'stats' => $stats,
        ])->layout('layouts.app');
    }
}
