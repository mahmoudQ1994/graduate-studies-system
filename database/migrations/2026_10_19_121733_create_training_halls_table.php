<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_halls', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // كود القاعة (تلقائي)
            $table->string('name'); // اسم القاعة
            $table->string('location')->nullable(); // مكان القاعة
            $table->integer('chairs_count')->default(0); // عدد الكراسي

            // تجهيزات القاعة (Boolean)
            $table->boolean('has_computer')->default(false);
            $table->boolean('has_sound_system')->default(false);
            $table->boolean('has_ac')->default(false);
            $table->boolean('has_whiteboard')->default(false);
            $table->boolean('has_projector')->default(false); // شاشة عرض
            $table->boolean('is_lighting_sufficient')->default(true); // جودة الإضاءة كافية أم لا

            // تتبع المستخدمين
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_halls');
    }
};
