<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyLeave extends Model
{
    protected $fillable = [
        'postgraduate_registration_id',
        'leave_type',
        'start_date',
        'end_date',
        'actual_return_date',
        'duration_years',
        'decision_notes',
    ];

    public function registration()
    {
        return $table->belongsTo(PostgraduateRegistration::class, 'postgraduate_registration_id');
    }
}
