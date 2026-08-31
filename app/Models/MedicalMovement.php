<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'health_professional_id',  // معرف الموظف الصحي
        'specialty',  // التخصص
        'movement_date',  // تاريخ الحركة
    ];

    // العلاقة: سجل الحركة ينتمي لموظف صحي معين
    public function healthProfessional()
    {
        return $this->belongsTo(HealthProfessional::class);
    }


    public function medicalMovement()
    {
        return $this->hasOne(MedicalMovement::class, 'health_professional_id');
    }
}
