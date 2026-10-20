<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingHourlyRate extends Model
{
    use HasFactory;

    protected $table = 'training_hourly_rates';

    protected $fillable = [
        'code',
        'category_value',
        'hourly_rate',
        'regulation_name',
        'description',
        'created_by',
        'updated_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
