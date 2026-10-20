<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('training_program_schedules')) {
            Schema::create('training_program_schedules', function (Blueprint $table) {
                $table->id();

                // ربط البرنامج التدريبي
                $table->unsignedBigInteger('training_program_id');
                $table->foreign('training_program_id')->references('id')->on('training_programs')->cascadeOnDelete();

                // ربط القاعة التدريبية بجدول training_halls الصحيح
                $table->unsignedBigInteger('hall_id')->nullable();
                $table->foreign('hall_id')->references('id')->on('training_halls')->nullOnDelete();

                // المواعيد والتوقيتات
                $table->date('start_date');
                $table->date('end_date');
                $table->string('time_slot')->nullable(); // صباحي / مسائي
                $table->integer('max_capacity')->default(25); // الحد الأقصى للمقاعد

                // حالة الجدولة والتنشيط
                $table->enum('schedule_status', ['draft', 'active', 'in_progress', 'completed', 'cancelled'])->default('draft');

                $table->text('notes')->nullable();

                // تتبع المستخدمين والتاريخ (Audit Trail)
                $table->unsignedBigInteger('created_by')->nullable();
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();

                $table->unsignedBigInteger('updated_by')->nullable();
                $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('training_program_schedules');
    }
};
