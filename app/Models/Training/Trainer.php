<?php

namespace App\Models\Training;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\JobGrade;
use App\Models\JobStatus;
use App\Models\TechnicalSpecialization;
use App\Models\User;

class Trainer extends Model
{
    use HasFactory;

    protected $table = 'trainers';

    protected $fillable = [
        'trainer_type',
        'status',
        'created_by',
        'approved_by',
        'approval_date',
        'updated_by',
        'rejection_reason',
        'name_ar',
        'name_en',
        'national_id',
        'birth_date',
        'gender',
        'phone',
        'email',
        'academic_qualification',
        'qualification_year',
        'university_or_institute',
        'current_job',
        'workplace',
        'job_grade_id',
        'workplace_governorate',
        'job_status_id',
        'specialization_id',
        'postgraduate_specialization',
        'postgraduate_specialization_year',
        'specialization_experience_years',
        'administrative_postgraduates',
        'english_proficiency_level',
        'capacity_reward_amount',
        'has_tot_certificate',
        'tot_provider',
        'tot_date',
        'national_id_front_path',
        'national_id_back_path',
        'job_status_certificate_path',
        'tot_certificate_path',
    ];

    // علاقة المدرب بالدورات والدورات التي يقدمها والمنح
    public function courses()
    {
        return $this->hasMany(TrainerCourse::class, 'trainer_id');
    }

    // علاقة الدرجة الوظيفية
    public function jobGrade()
    {
        return $this->belongsTo(JobGrade::class, 'job_grade_id');
    }

    // علاقة الحالة الوظيفية
    public function jobStatus()
    {
        return $this->belongsTo(JobStatus::class, 'job_status_id');
    }

    // علاقة التخصص الفني
    public function specialization()
    {
        return $this->belongsTo(TechnicalSpecialization::class, 'specialization_id');
    }

    // علاقات المستخدمين للتدقيق (Audit Trail)

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
