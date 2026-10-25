<?php

namespace App\Models\Trainees;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TrainingProgramSchedule;
use App\Models\User;

class Trainee extends Model
{
    use HasFactory;

    protected $table = 'trainees';

    protected $fillable = [
        'name_ar',
        'name_en',
        'national_id',
        'qualification',
        'job_title',
        'department',
        'workplace',
        'email',
        'phone_number',
        'user_id',
        'status',
        'created_by',
        'updated_by',
    ];

    // علاقة سجلات الالتحاق بالدورات (متعددة)
    public function enrollments()
    {
        return $this->hasMany(TraineeEnrollment::class, 'trainee_id');
    }

    // علاقة الدورات المجدولة التي التحق بها المتدرب عبر جدول الربط
    public function schedules()
    {
        return $this->belongsToMany(
            TrainingProgramSchedule::class,
            'trainee_enrollments',
            'trainee_id',
            'training_program_schedule_id'
        )->withPivot([
            'job_title',
            'department',
            'workplace',
            'enrolled_at',
            'attendance_status',
            'evaluation_status',
            'certificate_issued',
            'certificate_code',
            'certificate_issued_at'
        ])->withTimestamps();
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
