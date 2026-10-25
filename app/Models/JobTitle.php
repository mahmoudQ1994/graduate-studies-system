<?php

namespace App\Models\Trainees;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class JobTitle extends Model
{
    use HasFactory;

    protected $table = 'job_titles';

    protected $fillable = [
        'name',
        'is_active',
        'created_by',
    ];

      protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


}
