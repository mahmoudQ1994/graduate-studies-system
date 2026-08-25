<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostgraduateRegistration extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'national_id',
        'university',
        'faculty',
        'department',
        'study_program',
        'admission_date',
        'status',
        'study_leave_type',
        'health_professional_id',
    ];

    /**
     * علاقة الإجازات والتفرغ الدراسي للمرشح
     */
    public function studyLeaves(): HasMany
    {
        return $this->hasMany(StudyLeave::class, 'postgraduate_registration_id');
    }

    /**
     * علاقة المرشح بالمعلومات الصحية
     */
    public function healthProfessional(): BelongsTo
    {
        return $this->belongsTo(HealthProfessional::class, 'health_professional_id');
    }
}
