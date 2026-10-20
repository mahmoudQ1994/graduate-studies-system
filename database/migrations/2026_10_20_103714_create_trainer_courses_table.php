<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainer_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('trainers')->onDelete('cascade'); // ربط بالمدرب

            // إضافة $table-> قبل الـ enum
            $table->enum('course_type', ['obtained', 'offered', 'external_grant']);

            $table->string('course_name'); // اسم البرنامج / الدورة / المنحة
            $table->date('acquisition_date')->nullable(); // تاريخ الحصول عليها أو تقديمها
            $table->string('issuer_entity')->nullable(); // الجهة المانحة أو المنظمة

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_courses');
    }
};
