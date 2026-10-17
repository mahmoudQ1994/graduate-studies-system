<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class University extends Model
{
    protected $fillable = ['name', 'type', 'user_id', 'is_active'];

    public function colleges(): HasMany
    {
        return $this->hasMany(College::class);
    }

    // علاقة المستخدم الذي أضاف الجامعة
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
