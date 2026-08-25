<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('postgraduate_registration_id')->constrained('postgraduate_registrations')->onDelete('cascade');
            $table->string('leave_type'); // بمرتب، بدون مرتب
            $table->date('start_date'); // تاريخ بداية التفرغ
            $table->date('end_date')->nullable(); // تاريخ نهاية التفرغ
            $table->decimal('duration_years', 4, 2)->nullable(); // مدة التفرغ بالسنوات
            $table->text('decision_notes')->nullable(); // رقم القرار والملاحظات
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_leaves');
    }
};
