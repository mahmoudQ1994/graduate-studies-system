<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingAttendance extends Model
{
    use HasFactory;

    protected $table = 'training_attendances';

    protected $fillable = [
        'training_program_schedule_id',
        'trainee_id',
        'attendance_date',
        'status',
        'notes',
        'created_by',
    ];

    // علاقة الحضور بالمتدرب
    public function trainee()
    {
        return $this->belongsTo(Trainee::class, 'trainee_id');
    }

    // علاقة الحضور بجدول البرنامج التدريبي
    public function schedule()
    {
        return $this->belongsTo(TrainingProgramSchedule::class, 'training_program_schedule_id');
    }

    // علاقة الحضور بالمستخدم (المسؤول) الذي سجل الحضور
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
