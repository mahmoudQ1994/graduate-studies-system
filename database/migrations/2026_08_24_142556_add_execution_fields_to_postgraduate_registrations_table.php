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
        Schema::table('postgraduate_registrations', function (Blueprint $table) {
                $table->date('execution_date')->nullable()->after('study_status');
                $table->date('nominated_registration_date')
                ->nullable()->after('execution_date');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('postgraduate_registrations', function (Blueprint $table) {
                $table->dropColumn(['execution_date', 'nominated_registration_date']);
            });
    }
};
