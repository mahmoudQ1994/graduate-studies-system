<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_program_schedule_id')->constrained('training_program_schedules')->onDelete('cascade');
            $table->foreignId('trainee_id')->constrained('trainees')->onDelete('cascade');

            // البيانات الرسمية والتواصل
            $table->string('official_email'); // البريد الإلكتروني لإرسال الشهادة
            $table->string('phone_number')->nullable();

            // 1. التقييم العلمي والمحتوى (Program & Content)
            $table->tinyInteger('content_clarity')->default(5);        // وضوح الأهداف والمحتوى
            $table->tinyInteger('content_relevance')->default(5);      // ملاءمة المحتوى لمتطلبات العمل
            $table->tinyInteger('content_depth')->default(5);          // عمق الحصيلة والمادة العلمية
            $table->tinyInteger('content_time_balance')->default(5);   // التوافق بين زمن الدورة وحجم المحتوى

            // 2. أداء المدرب / المحاضر (Trainer Evaluation)
            $table->tinyInteger('trainer_knowledge')->default(5);      // تمكن وإلمام المدرب بالمادة
            $table->tinyInteger('trainer_delivery')->default(5);       // مهارات العرض وأسلوب الشرح
            $table->tinyInteger('trainer_interaction')->default(5);    // تشجيع المشاركة والإجابة عن الاستفسارات
            $table->tinyInteger('trainer_time_management')->default(5);// الالتزام بالوقت وإدارة الجلسات

            // 3. البيئة والتجهيزات (Environment & Facilities)
            $table->tinyInteger('hall_comfort')->default(5);           // ملاءمة القاعة (المقاعد، الإضاءة، التكييف)
            $table->tinyInteger('equipment_quality')->default(5);     // جودة أجهزة العرض والشاشات والصوتيات
            $table->tinyInteger('catering_services')->default(5);      // خدمات الضيافة والاستراحة (إن وجدت)

            // 4. التنظيم والدعم الإداري (Administrative Support)
            $table->tinyInteger('admin_response')->default(5);         // تعامل واستجابة الفريق الإداري
            $table->tinyInteger('registration_process')->default(5);   // سهولة إجراءات التسجيل والدخول

            // 5. الأثر والفائدة التطبيقية (Impact & Application)
            $table->tinyInteger('practical_application')->default(5);  // مدى القدرة على تطبيق ما تم تعلمه في الميدان
            $table->tinyInteger('overall_satisfaction')->default(5);   // الرضا العام عن البرنامج التدريبي
            $table->boolean('recommend_to_colleague')->default(true);  // هل توصي زملائك بحضور هذه الدورة؟

            // الملاحظات والآراء الجودة
            $table->text('strengths_notes')->nullable();               // أهم نقاط القوة في البرنامج
            $table->text('improvement_notes')->nullable();             // المقترحات والتوصيات للتطوير

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_evaluations');
    }
};
