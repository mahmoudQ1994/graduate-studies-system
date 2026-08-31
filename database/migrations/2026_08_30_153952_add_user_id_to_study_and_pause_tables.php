<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. جدول تسجيلات الدراسات العليا
        Schema::table('postgraduate_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('postgraduate_registrations', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            }
        });

        // 2. جدول أجازات التفرغ
        Schema::table('study_leaves', function (Blueprint $table) {
            if (!Schema::hasColumn('study_leaves', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            }
        });

        // 3. جدول إيقاف القيد
        Schema::table('registration_pauses', function (Blueprint $table) {
            if (!Schema::hasColumn('registration_pauses', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('postgraduate_registrations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('study_leaves', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('registration_pauses', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};

