<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postgraduate_registrations', function (Blueprint $table) {
            $table->id();

            // الربط بالطبيب
            $table->foreignId('health_professional_id')
                  ->constrained('health_professionals')
                  ->cascadeOnDelete()
                  ->comment('ربط بالطبيب / الموظف الصحي');

            // 1. الدراسة المطلوبة
            $table->string('required_degree')->comment('نوع الدراسة (دبلوم - ماجستير - دكتوراة)');
            $table->string('required_specialty')->comment('تخصص الدراسة المطلوبة');
            $table->string('required_university')->comment('الجامعة المطلوبة للدراسة');

            // 2. موقف القيد السابق بالدراسات العليا
            $table->string('prior_registration_status')->nullable()->comment('هل سبق القيد بالدراسات العليا (نعم / لا)');
            $table->string('prior_registration_study')->nullable()->comment('الدراسة السابقة في حالة سبوق القيد');
            $table->string('prior_registration_year')->nullable()->comment('سنة القيد السابقة');
            $table->text('cancellation_reason')->nullable()->comment('سبب إلغاء الدراسة السابقة إن وجد');

            // 3. الأجازات والتفرغ
            $table->string('study_leave_type')->nullable()->comment('اجازة تفرغ دراسى (بمرتب / بدون مرتب)');
            $table->text('leaves_history')->nullable()->comment('الأجازات المسجلة للطبيب');

            // 4. التواريخ والموقف التنفيذي
            $table->string('application_date')->nullable()->comment('تاريخ تسجيل الطلب');
            $table->string('registration_date')->nullable()->comment('تاريخ القيد بالدراسة');
            $table->string('nominated_degree_status')->nullable()->comment('موقف الحصول على الدرجة المرشح لها');
            $table->string('nominated_degree_date')->nullable()->comment('تاريخ الحصول على الدرجة العلمية المرشح لها');
            $table->string('years_from_registration')->nullable()->comment('عدد سنوات الدراسة من تاريخ القيد');
            $table->string('study_status')->default('مستمر')->comment('موقف تنفيذ الدراسة (مستمر / اعتذر / نفذ / تم إلغاء القيد)');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postgraduate_registrations');
    }
};
