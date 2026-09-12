<?php

namespace App\Models;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class StudyLeave extends Model
{
    protected $fillable = [
        'postgraduate_registration_id',
        'leave_type',
        'start_date',   // تاريخ بداية التفرغ
        'end_date',     // تاريخ نهاية التفرغ
        'actual_return_date',
        'duration_years',  //
        'decision_notes',
        'user_id',
    ];

    public function registration()
    {
        return $this->belongsTo(PostgraduateRegistration::class, 'postgraduate_registration_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
