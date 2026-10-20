<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingProgram extends Model
{
    use HasFactory;

    protected $table = 'training_programs';

    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'program_type_id',
        'hall_id',
        'level',
        'consultant_name',
        'duration_days',
        'training_hours',
        'courses_count_per_year',
        'objective_knowledge',
        'objective_skills',
        'objective_attitude',
        'prerequisites',
        'approval_status',
        'created_by',
        'updated_by',
    ];

    // علاقة نوع البرنامج
    public function programType()
    {
        return $this->belongsTo(TrainingProgramType::class, 'program_type_id');
    }

    // علاقة قاعة التدريب (مكان التدريب)
    public function hall()
    {
        return $this->belongsTo(TrainingHall::class, 'hall_id');
    }

    // المستخدم المنشئ
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // آخر مستخدم عدل السجل
    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // العلاقات المتعددة (Many-to-Many) عبر الجداول الوسيطة
    public function methods()
    {
        return $this->belongsToMany(TrainingMethod::class, 'program_training_method', 'training_program_id', 'training_method_id');
    }

    public function knowledgeMeasurements()
    {
        return $this->belongsToMany(KnowledgeMeasurement::class, 'program_knowledge_measurement', 'training_program_id', 'knowledge_measurement_id');
    }

    public function impactMeasurements()
    {
        return $this->belongsToMany(ImpactMeasurement::class, 'program_impact_measurement', 'training_program_id', 'impact_measurement_id');
    }

    public function targetAudiences()
    {
        return $this->belongsToMany(JobStatus::class, 'program_target_audience', 'training_program_id', 'job_status_id');
    }

    // علاقة اعتمادات وموافقات البرنامج
    public function approvals()
    {
        return $this->hasMany(TrainingProgramApproval::class, 'training_program_id');
    }

    
    public function schedule()
    {
        return $this->hasOne(TrainingProgramSchedule::class);
    }
}
