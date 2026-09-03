<?php

namespace App\Models;
use App\Models\User;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostgraduateRegistration extends Model
{
    protected $table = 'postgraduate_registrations';

    // تحديد الحقول المسموح بتعديلها وحفظها لتتوافق تماماً مع قاعدة البيانات
    protected $fillable = [
        'health_professional_id',
        'required_degree',              // نوع الدراسة (دبلوم - ماجستير - دكتوراة)
        'required_specialty',           // تخصص الدراسة المطلوبة
        'required_university',          // الجامعة المطلوبة للدراسة
        'sponsorship_type',             // نوع الترشيح / جهة التمويل (مثل: وزاري)
        'prior_registration_status',    // هل سبق القيد بالدراسات العليا (نعم / لا)
        'prior_registration_study',     // الدراسة السابقة
        'prior_registration_year',      // سنة القيد السابقة
        'cancellation_reason',          // سبب إلغاء الدراسة السابقة
        'study_leave_type',             // اجازة تفرغ دراسى
        'leaves_history',               // الاجازات المسجلة
        'application_date',             // تاريخ تسجيل الطلب
        'registration_date',            // تاريخ القيد بالدراسة
        'nominated_degree_status',      // موقف الحصول على الدرجة
        'nominated_degree_date',        // تاريخ الحصول على الدرجة
        'years_from_registration',      // عدد سنوات الدراسة
        'study_status',                 // موقف تنفيذ الدراسة (مستمر / اعتذر...)
        'apology_date',     // تاريخ الاعتذار عن الدراسة ان وجد
        'apology_reason',   // سبب الاعتذار عن الدراسة ان وجد
        'execution_date',   // تاريخ تنفيذ الدراسة ان وجد
        'nominated_registration_date',  // تاريخ الترشيح للقيد بالدراسة
        'user_id',    //
        'degree_grade',  // التقدير العام عند إنهاء الدراسة
        'master_degree_date',  // تاريخ الحصول على الماجستير (خاص بالتسجيل للدكتوراة)
        'rejection_date',  // تاريخ الرفض
        'rejection_reason',  // سبب الرفض
        
    ];

    /**
     * علاقة الإجازات والتفرغ الدراسي للمرشح
     */
    public function studyLeaves(): HasMany
    {
        return $this->hasMany(StudyLeave::class, 'postgraduate_registration_id');
    }

    /**
     * علاقة المرشح بالمعلومات الصحية (الكادر الطبي)
     */
    public function healthProfessional(): BelongsTo
    {
        return $this->belongsTo(HealthProfessional::class, 'health_professional_id');
    }

    public function pauses()
{
    return $this->hasMany(RegistrationPause::class, 'postgraduate_registration_id');
}

public function getTotalPausedDaysAttribute()
{
    return $this->pauses->sum(function ($pause) {
        if (!$pause->pause_start_date) return 0;

        $start = \Carbon\Carbon::parse($pause->pause_start_date);
        $end = $pause->resume_date ? \Carbon\Carbon::parse($pause->resume_date) :
               ($pause->pause_end_date ? \Carbon\Carbon::parse($pause->pause_end_date) : now());

        return max(0, $start->diffInDays($end));
    });
}

public function user()
{
    return $this->belongsTo(User::class); // أو User::class حسب اسم نموذج المستخدم لديك
}
}
