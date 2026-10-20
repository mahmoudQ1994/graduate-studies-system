<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technical_specializations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // كود التخصص الفني (تلقائي مثل SPEC-0001)
            $table->string('name'); // اسم التخصص الفني
            $table->text('description')->nullable(); // الوصف أو ملاحظات

            // تتبع المستخدمين
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technical_specializations');
    }
};
