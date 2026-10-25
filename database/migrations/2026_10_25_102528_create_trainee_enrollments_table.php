<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainee_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')->constrained('trainees')->cascadeOnDelete();
            $table->foreignId('training_program_schedule_id')->constrained('training_program_schedules')->cascadeOnDelete();

            // بيانات جهة العمل ووقت التدريب (لحفظ الأرشيف حتى لو تغيرت جهة عمله لاحقاً)
            $table->string('job_title')->nullable(); // الوظيفة
            $table->string('department')->nullable(); // الإدارة
            $table->string('workplace')->nullable(); // جهة العمل (مثل مستشفى سوهاج العام أو جرجا العام)

            $table->timestamp('enrolled_at')->nullable();
            $table->enum('attendance_status', ['enrolled', 'attended', 'absent', 'excused'])->default('enrolled');
            $table->enum('evaluation_status', ['pending', 'passed', 'failed'])->default('pending');

            $table->boolean('certificate_issued')->default(false);
            $table->string('certificate_code')->nullable()->unique();
            $table->timestamp('certificate_issued_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainee_enrollments');
    }
};
