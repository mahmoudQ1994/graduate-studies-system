<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficialHeader extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'manager_name',
        'undersecretary_name',
        'phone_fax',
        'official_email',
        'detailed_address',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
