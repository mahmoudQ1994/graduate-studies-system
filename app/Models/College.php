<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class College extends Model
{
    protected $fillable = ['university_id', 'name','user_id', 'is_active'];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
