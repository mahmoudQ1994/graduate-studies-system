<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_professionals', function (Blueprint $table) {
            $table->id(); // الرقم التعريفي للجدول
            $table->string('national_id', 14)->unique()->comment('الرقم القومي (14 رقم فريد)');
            $table->string('name')->comment('الاسم بالكامل من شيت الإكسيل');
            $table->string('phone')->nullable()->comment('رقم التليفون');
            $table->string('profession')->default('طبيب')->comment('الوظيفة (طبيب / صيدلي / تمريض / أخرى)');

            // الربط بجدول المنشآت المكودة (من خلالها نعرف الإدارة/المستشفى والمركز والقطاع)
            $table->foreignId('facility_id')
                  ->nullable()
                  ->constrained('facilities')
                  ->nullOnDelete()
                  ->comment('جهة العمل: ربط بجدول المنشآت (المستشفى/الإدارة الصحة)');

            $table->timestamps(); // تاريخ الإنشاء والتحديث
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('health_professionals');
    }


};
