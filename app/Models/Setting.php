<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'ministry_name',
        'governorate_name',
        'directorate_name',
        'administration_name',
        'department_name',
        'logo_path',
        'fiscal_year',
        'fiscal_year_start',
        'fiscal_year_end',
        'director_name',
        'phone'
    ];
}
