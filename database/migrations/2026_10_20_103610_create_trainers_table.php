<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();

            // البيانات الشخصية الأساسية
            $table->enum('trainer_type', ['trainer', 'consultant'])->default('trainer'); // صفته (مدرب أو استشاري)
            $table->string('name_ar'); // الاسم رباعي بالعربي
            $table->string('name_en')->nullable(); // الاسم رباعي بالانجليزي
            $table->string('national_id', 14)->unique(); // الرقم القومي (فريد غير قابل للتكرار)
            $table->date('birth_date')->nullable(); // تاريخ الميلاد (يتم تعبئته تلقائياً)
            $table->enum('gender', ['male', 'female'])->nullable(); // النوع (يُستنتج من الرقم القومي)
            $table->string('phone')->nullable(); // رقم المحمول
            $table->string('email')->nullable(); // البريد الإلكتروني

            // بيانات المؤهل والخبرات والوظيفة (يمكن ربطها بجداول الإعدادات الموجودة لديك)
            $table->string('academic_qualification')->nullable(); // المؤهل العلمي الأساسي
            $table->year('qualification_year')->nullable(); // سنة الحصول عليه
            $table->string('university_or_institute')->nullable(); // الجامعة أو المعهد
            $table->string('current_job')->nullable(); // الوظيفة الحالية
            $table->string('workplace')->nullable(); // جهة العمل
            $table->unsignedBigInteger('job_grade_id')->nullable(); // الدرجة الوظيفية (مرتبطة بجدول الدرجات الوظيفية)
            $table->string('workplace_governorate')->nullable(); // محافظة جهة العمل
            $table->unsignedBigInteger('job_status_id')->nullable(); // الحالة الوظيفية (مرتبطة بجدول الحالة الوظيفية)

            // بيانات التخصص الفني والإداري
            $table->unsignedBigInteger('specialization_id')->nullable(); // التخصص الفني (مرتبط بجدول التخصصات)
            $table->string('postgraduate_specialization')->nullable(); // الدراسات العليا في التخصص
            $table->year('postgraduate_specialization_year')->nullable(); // سنة الحصول على الدراسات العليا
            $table->integer('specialization_experience_years')->default(0); // عدد سنوات الخبرة بالتخصص
            $table->string('administrative_postgraduates')->nullable(); // الدراسات العليا الإدارية
            $table->string('english_proficiency_level')->nullable(); // مستوى إجادة اللغة الإنجليزية

            // المالية والمكافآت
            $table->decimal('capacity_reward_amount', 10, 2)->default(0); // مكافأة السعة التدريبية (تظهر تلقائياً بناءً على الدرجة الوظيفية)

            // برنامج إعداد المدربين (TOT)
            $table->boolean('has_tot_certificate')->default(false); // موقف الحصول على برنامج إعداد مدربين
            $table->string('tot_provider')->nullable(); // جهة الحصول على برنامج إعداد مدربين
            $table->date('tot_date')->nullable(); // تاريخ الحصول على برنامج إعداد مدربين

            // مسارات المرفقات الرسمية (تخزين مسارات الملفات)
            $table->string('national_id_front_path')->nullable(); // صورة الرقم القومي الوجه الأمامي
            $table->string('national_id_back_path')->nullable(); // صورة الرقم القومي الوجه الخلفي
            $table->string('job_status_certificate_path')->nullable(); // صورة بيان الحالة الوظيفية
            $table->string('tot_certificate_path')->nullable(); // صورة شهادة إعداد مدربين

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
};
