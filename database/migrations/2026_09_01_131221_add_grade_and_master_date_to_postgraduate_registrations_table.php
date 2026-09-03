<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('postgraduate_registrations', function (Blueprint $table) {
            $table->string('degree_grade')->nullable()->after('nominated_degree_status'); // التقدير العام عند إنهاء الدراسة
            $table->date('master_degree_date')->nullable()->after('degree_grade'); // تاريخ الحصول على الماجستير (خاص بالتسجيل للدكتوراة)
        });
    }

    public function down(): void
    {
        Schema::table('postgraduate_registrations', function (Blueprint $table) {
            $table->dropColumn(['degree_grade', 'master_degree_date']);
        });
    }
};
