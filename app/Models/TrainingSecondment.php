<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingSecondment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'training_days' => 'array', // لتحويل أيام الإفاد تلقائياً إلى مصفوفة والعكس
    ];

    public function healthProfessional()
    {
        return $this->belongsTo(HealthProfessional::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function asJson($value, $flags = 0)
    {
        return json_encode($value, $flags | JSON_UNESCAPED_UNICODE);
    }
}
