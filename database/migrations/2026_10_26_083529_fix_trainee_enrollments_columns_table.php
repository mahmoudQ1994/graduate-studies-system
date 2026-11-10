<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('trainee_enrollments', function (Blueprint $table) {
            // تحويل حقول الـ ENUM إلى VARCHAR لتجنب قيود الإدخال وحل مشكلة الـ pending
            $table->string('attendance_status', 50)->default('pending')->change();
            $table->string('evaluation_status', 50)->default('pending')->change();
            $table->string('status', 50)->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainee_enrollments', function (Blueprint $table) {
            //
        });
    }
};
