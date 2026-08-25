<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasRoles; // تفعيل ميزة الأدوار والصلاحيات

    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
        'is_active',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
