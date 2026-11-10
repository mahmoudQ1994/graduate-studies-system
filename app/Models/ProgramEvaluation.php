<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramEvaluation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function schedule()
    {
        return $this->belongsTo(TrainingProgramSchedule::class, 'training_program_schedule_id');
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class, 'trainee_id');
    }
}
