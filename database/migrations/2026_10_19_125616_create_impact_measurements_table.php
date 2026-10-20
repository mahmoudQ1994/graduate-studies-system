<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impact_measurements', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // كود قياس الأثر (تلقائي مثل IMP-0001)
            $table->string('name'); // اسم أو عنوان قياس أثر التدريب
            $table->text('notes')->nullable(); // ملاحظات

            // تتبع المستخدمين
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impact_measurements');
    }
};
