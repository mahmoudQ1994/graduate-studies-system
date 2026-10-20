<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_program_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم نوع البرنامج التدريبي (مثل: ورشة عمل، مؤتمر، دورة تدريبية، دبلومة)
            $table->string('code')->nullable(); // كود اختياري للنوع
            $table->text('description')->nullable(); // وصف قصير
            $table->boolean('is_active')->default(true); // حالة التفعيل
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_program_types');
    }
};
