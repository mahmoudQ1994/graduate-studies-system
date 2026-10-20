<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingHall extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'location',
        'chairs_count',
        'has_computer',
        'has_sound_system',
        'has_ac',
        'has_whiteboard',
        'has_projector',
        'is_lighting_sufficient',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'has_computer' => 'boolean',
        'has_sound_system' => 'boolean',
        'has_ac' => 'boolean',
        'has_whiteboard' => 'boolean',
        'has_projector' => 'boolean',
        'is_lighting_sufficient' => 'boolean',
    ];

    // علاقة المستخدم الذي قام بالإضافة
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // علاقة المستخدم الذي قام بآخر تعديل
    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
