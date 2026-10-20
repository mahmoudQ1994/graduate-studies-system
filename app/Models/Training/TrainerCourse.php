<?php

namespace App\Models\Training;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainerCourse extends Model
{
    use HasFactory;

    protected $table = 'trainer_courses';

    protected $fillable = [
        'trainer_id',
        'course_type', // 'obtained', 'offered', 'external_grant'
        'course_name',
        'acquisition_date',
        'issuer_entity',
    ];

    // العلاقة العكسية مع المدرب
    public function trainer()
    {
        return $this->belongsTo(Trainer::class, 'trainer_id');
    }
}
