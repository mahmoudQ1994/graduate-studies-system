<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('training_programs')) {
            Schema::create('training_programs', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique(); // كود البرنامج التلقائي (مثل PRG-0001)
                $table->string('name_ar'); // اسم البرنامج بالعربية
                $table->string('name_en')->nullable(); // اسم البرنامج بالإنجليزية

                // العلاقات مع الجداول الأساسية السابقة
                $table->foreignId('program_type_id')->constrained('program_types')->cascadeOnDelete(); // نوع البرنامج
                $table->foreignId('hall_id')->nullable()->constrained('training_halls')->nullOnDelete(); // مكان التدريب (القاعة)

                $table->string('level')->nullable(); // مستوى البرنامج (مبتدئ، متقدم، إلخ)
                $table->string('consultant_name')->nullable(); // استشاري البرنامج / المسؤول العلمي

                $table->integer('duration_days')->default(1); // عدد أيام البرنامج الواحد
                $table->integer('training_hours')->default(0); // عدد الساعات التدريبية
                $table->integer('courses_count_per_year')->default(1); // عدد الدورات خلال العام المالي

                // أهداف البرنامج مقسمة إلى ثلاثة محاور
                $table->text('objective_knowledge')->nullable(); // المعرفة (Knowledge)
                $table->text('objective_skills')->nullable(); // المهارات (Skills)
                $table->text('objective_attitude')->nullable(); // السلوك المهني (Attitude)

                $table->text('prerequisites')->nullable(); // متطلبات التدريب

                // التدقيق والمتابعة
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('training_programs');
    }
};
