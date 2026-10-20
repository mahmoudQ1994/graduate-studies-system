<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_grades', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // كود الدرجة الوظيفية 
            $table->string('name'); // اسم الدرجة الوظيفية (مثل: زميل، أخصائي، استشاري)
            $table->text('description')->nullable(); // الوصف أو ملاحظات

            // تتبع المستخدمين
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_grades');
    }
};
