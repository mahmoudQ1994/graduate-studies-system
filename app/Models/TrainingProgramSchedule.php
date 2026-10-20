<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrainingProgramSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_program_id',
        'hall_id',
        'start_date',
        'end_date',
        'time_slot',
        'max_capacity',
        'schedule_status',
        'notes',
        'created_by',
        'updated_by',
    ];

    // علاقة البرنامج التدريبي
    public function trainingProgram()
    {
        return $this->belongsTo(TrainingProgram::class);
    }

    // علاقة القاعة التدريبية (مرتبطة بـ training_halls)
    public function hall()
    {
        return $this->belongsTo(TrainingHall::class, 'hall_id');
    }

    // علاقة المحاضرين المتعددين
    public function trainers()
    {
        return $this->hasMany(ProgramScheduleTrainer::class, 'training_program_schedule_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
