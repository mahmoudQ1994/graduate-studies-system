<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProgramScheduleTrainer extends Model
{
    use HasFactory;

    protected $table = 'program_schedule_trainers';

    protected $fillable = [
        'training_program_schedule_id',
        'trainer_name',
        'assigned_days',
        'session_topics',
    ];

    // علاقة جدول الجدولة
    public function schedule()
    {
        return $this->belongsTo(TrainingProgramSchedule::class, 'training_program_schedule_id');
    }
}
