<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // كود أسلوب التدريب (تلقائي)
            $table->string('name'); // اسم أسلوب التدريب
            $table->text('notes')->nullable(); // ملاحظات

            // تتبع المستخدمين
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_methods');
    }
};
