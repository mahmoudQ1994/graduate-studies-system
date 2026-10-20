<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_schedule_trainers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_program_schedule_id')->constrained('training_program_schedules')->cascadeOnDelete();

            $table->string('trainer_name'); // اسم المحاضر أو الاستشاري
            $table->string('assigned_days')->nullable(); // الأيام المخصصة (مثال: اليوم الأول والثاني، أو 2026-11-01 إلى 2026-11-02)
            $table->text('session_topics')->nullable(); // المحاور أو الجلسات التي يقدمها

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_schedule_trainers');
    }
};
