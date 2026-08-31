<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\User;

class RegistrationPause extends Model
{
    use HasFactory;

    protected $fillable = [
        'postgraduate_registration_id',
        'pause_start_date',
        'pause_end_date',
        'resume_date',
        'pause_reason',
        'notes',
        'user_id',
    ];

    /**
     * العلاقة مع جدول تسجيلات الدراسات العليا الرئيسي
     */
    public function registration()
    {
        return $this->belongsTo(PostgraduateRegistration::class, 'postgraduate_registration_id');
    }

    /**
     * حساب مدة الإيقاف
     */
    public function getPauseDurationAttribute()
    {
        if (!$this->pause_start_date) return '-';

        $start = Carbon::parse($this->pause_start_date);
        $end = $this->resume_date ? Carbon::parse($this->resume_date) :
               ($this->pause_end_date ? Carbon::parse($this->pause_end_date) : now());

        $diff = $start->diff($end);
        $parts = [];
        if ($diff->y > 0) $parts[] = $diff->y . ' سنة';
        if ($diff->m > 0) $parts[] = $diff->m . ' شهر';
        if ($diff->d > 0 || empty($parts)) $parts[] = $diff->d . ' يوم';

        return implode(' و ', $parts);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
