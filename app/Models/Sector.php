<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    // العلاقة: القطاع يضم عدة مستشفيات
    public function facilities()
    {
        return $this->hasMany(Facility::class);
    }
}
