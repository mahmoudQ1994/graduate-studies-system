<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('postgraduate_registrations', function (Blueprint $table) {
            $table->string('study_status')->default('جاري فحص الطلب')->change();
        });
    }

    public function down(): void
    {
        Schema::table('postgraduate_registrations', function (Blueprint $table) {
            $table->string('study_status')->default(null)->change();
        });
    }
};
