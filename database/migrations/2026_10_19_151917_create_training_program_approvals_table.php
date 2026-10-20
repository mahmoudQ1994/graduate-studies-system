<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_program_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_program_id')->constrained('training_programs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // المستخدم المسؤول عن الاعتماد في هذه المرحلة
            $table->string('role_stage')->default('reviewer'); // مرحلة الاعتماد (مثلاً: reviewer, approver, admin)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // حالة الاعتماد
            $table->text('notes')->nullable(); // ملاحظات أو أسباب الرفض
            $table->timestamp('action_at')->nullable(); // وقت اتخاذ القرار
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_program_approvals');
    }
};
