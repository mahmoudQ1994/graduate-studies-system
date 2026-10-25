<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainees', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar'); // الاسم بالعربي
            $table->string('name_en')->nullable(); // الاسم بالإنجليزي
            $table->string('national_id', 14)->unique()->index(); // الرقم القومي (فريد)
            $table->string('qualification')->nullable(); // المؤهل
            $table->string('email')->nullable(); // الإيميل
            $table->string('phone_number', 20)->nullable(); // رقم الموبايل
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainees');
    }
};
