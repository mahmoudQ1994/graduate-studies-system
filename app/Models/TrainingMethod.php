<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'notes',
        'created_by',
        'updated_by',
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
