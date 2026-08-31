<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('postgraduate_registrations', function (Blueprint $table) {
            $table->date('apology_date')->nullable()->after('study_status');
            $table->text('apology_reason')->nullable()->after('apology_date');
        });
    }

    public function down(): void
    {
        Schema::table('postgraduate_registrations', function (Blueprint $table) {
            $table->dropColumn(['apology_date', 'apology_reason']);
        });
    }
};
