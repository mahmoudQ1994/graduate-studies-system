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
        Schema::table('professional_qualifications', function (Blueprint $table) {
            // إعادة تسمية العمود من faculty إلى qualification
            $table->renameColumn('faculty', 'qualification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professional_qualifications', function (Blueprint $table) {
            // للتراجع لو احتجت في المستقبل
            $table->renameColumn('qualification', 'faculty');
        });
    }
};
