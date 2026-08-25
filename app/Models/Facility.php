<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'district_id', 'sector_id', 'type', 'is_active'];

    // العلاقة: المستشفى ينتمي لمركز معين
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    // العلاقة: المستشفى ينتمي لقطاع معين (علاجي / وقائي...)
    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }
}
