<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('postgraduate_registrations', function (Blueprint $table) {
            $table->date('rejection_date')->nullable()->after('apology_reason');
            $table->text('rejection_reason')->nullable()->after('rejection_date');
        });
    }

    public function down(): void
    {
        Schema::table('postgraduate_registrations', function (Blueprint $table) {
            $table->dropColumn(['rejection_date', 'rejection_reason']);
        });
    }
};
