<?php

namespace App\Models\Trainees;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TrainingProgramSchedule;

class TraineeEnrollment extends Model
{
    use HasFactory;

    protected $table = 'trainee_enrollments';

    protected $fillable = [
        'trainee_id',
        'training_program_schedule_id',
        'job_title',
        'department',
        'workplace',
        'enrolled_at',
        'attendance_status',
        'evaluation_status',
        'certificate_issued',
        'certificate_code',
        'certificate_issued_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'certificate_issued_at' => 'datetime',
        'certificate_issued' => 'boolean',
    ];

    // العلاقة العكسية مع المتدرب
    public function trainee()
    {
        return $this->belongsTo(Trainee::class, 'trainee_id');
    }

    // العلاقة مع جدول الموعد / الدورة المجدولة
    public function schedule()
    {
        return $this->belongsTo(TrainingProgramSchedule::class, 'training_program_schedule_id');
    }
}
