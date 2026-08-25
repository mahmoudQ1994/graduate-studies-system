<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('official_headers', function (Blueprint $table) {
            $table->id();

            // ربط القسم المسجل (اختياري)
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();

            // بيانات المسئولين والتواصل المخصصة للطباعة والتنفيذ
            $table->string('manager_name')->nullable();           // اسم المدير
            $table->string('undersecretary_name')->nullable();    // اسم وكيل وزارة الصحة
            $table->string('phone_fax')->nullable();               // رقم الهاتف / الفاكس
            $table->string('official_email')->nullable();          // البريد الإلكتروني الرسمي
            $table->text('detailed_address')->nullable();          // العنوان التفصيلي (ليظهر في ذيل الصفحات Footer)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_headers');
    }
};
