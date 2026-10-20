<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingProgramApproval extends Model
{
    use HasFactory;

    protected $table = 'training_program_approvals';

    protected $fillable = [
        'training_program_id',
        'user_id',
        'role_stage',
        'status',
        'rejection_reason_id',
        'notes',
        'action_at',
    ];

    protected $casts = [
        'action_at' => 'datetime',
    ];

    // علاقة البرنامج التدريبي المرتبط بهذا الاعتماد
    public function trainingProgram()
    {
        return $this->belongsTo(TrainingProgram::class, 'training_program_id');
    }

    // علاقة المستخدم الذي اتخذ القرار
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // علاقة سبب الرفض
    public function rejectionReason()
    {
        return $this->belongsTo(RejectionReason::class, 'rejection_reason_id');
    }
}
