<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_program_schedule_id')->constrained('training_program_schedules')->cascadeOnDelete();
            $table->foreignId('trainee_id')->constrained('trainees')->cascadeOnDelete();
            $table->date('attendance_date'); // تاريخ اليوم التدريبي
            $table->string('status', 50)->default('present'); // present: حاضر, absent: غائب, excused: متغيب بعذر
            $table->text('notes')->nullable(); // ملاحظات إن وجدت

            // إضافة مستخدم النظام الذي أجرى عملية الحضور ووقت التسجيل
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps(); // يحتوي على created_at الذي يمثل وقت وتسجيل الحضور بدقة

            // منع تكرار تسجيل حضور نفس المتدرب في نفس اليوم لنفس الجدول
            $table->unique(['training_program_schedule_id', 'trainee_id', 'attendance_date'], 'unique_daily_attendance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_attendances');
    }
};
