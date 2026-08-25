<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionalQualification extends Model
{
    use HasFactory;

    protected $fillable = [
        'health_professional_id',
        'university',
        'faculty',
        'graduation_batch',
        'general_grade',
        'subject_grade',
        'total_marks',
    ];

    public function healthProfessional()
    {
        return $this->belongsTo(HealthProfessional::class);
    }
}
