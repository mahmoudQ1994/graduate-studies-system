<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
        $table->string('fiscal_year')->nullable(); // العام المالي مثل 2026 / 2027
        $table->date('fiscal_year_start')->nullable(); // بداية العام المالي
        $table->date('fiscal_year_end')->nullable(); // نهاية العام المالي
        $table->string('director_name')->nullable(); // اسم مدير الإدارة للتوقيعات
        $table->string('phone')->nullable();
        $table->string('email')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });
    }
};
