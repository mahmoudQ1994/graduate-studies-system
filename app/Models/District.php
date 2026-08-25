<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    // العلاقة: المركز يحتوي على عدة مستشفيات وجبهات
    public function facilities()
    {
        return $this->hasMany(Facility::class);
    }
}
