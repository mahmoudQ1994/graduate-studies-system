<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Facility;
use App\Models\ProfessionalQualification;
use App\Models\MedicalMovement;
use App\Models\PostgraduateRegistration;
use App\Models\Department;


class HealthProfessional extends Model
{
    use HasFactory;

    protected $fillable = [
        'national_id',  // الرقم القومي
        'name',     // الاسم
        'phone',// رقم الهاتف
        'profession',  //التخصصص
        'facility_id', // معرف المنشأة/المستشفى
        'secondment_facility'
    ];

    // العلاقة: الموظف ينتمي لمنشأة/مستشفى معينة
    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    // العلاقة: الموظف له مؤهل سابق/بيانات تخرج
    public function qualification()
    {
        return $this->hasOne(ProfessionalQualification::class);
    }

    // العلاقة: الموظف له سجل حركة نيابة أو تكليف
    public function movement()
    {
        return $this->hasOne(MedicalMovement::class);
    }

    // العلاقة: الموظف له عدة قيود أو تسجيلات بالدراسات العليا
    public function postgraduateRegistrations()
    {
        return $this->hasMany(PostgraduateRegistration::class);
    }
}
