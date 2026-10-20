<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_program_approvals', function (Blueprint $table) {
            $table->foreignId('rejection_reason_id')->nullable()->after('status')->constrained('rejection_reasons')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('training_program_approvals', function (Blueprint $table) {
            $table->dropForeign(['rejection_reason_id']);
            $table->dropColumn('rejection_reason_id');
        });
    }
};
