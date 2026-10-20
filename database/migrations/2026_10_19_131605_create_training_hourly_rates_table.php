<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_hourly_rates', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // كود المعدل (تلقائي مثل HRS-0001)
            $table->decimal('category_value', 10, 2)->default(0); // الفئة (القيمة بالجنيه)
            $table->decimal('hourly_rate', 10, 2)->default(0); // المعدل للساعة بالجنيه
            $table->string('regulation_name')->nullable(); // اللائحة المعتمد عليها
            $table->text('description')->nullable(); // الوصف

            // تتبع المستخدمين
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_hourly_rates');
    }
};
