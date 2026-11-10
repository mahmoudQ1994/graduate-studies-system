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
            $table->string('status')->default('approved'); // approved, rejected, pending
            $table->string('rejection_reason')->nullable();
        });
    }

    public function down()
    {
        Schema::table('trainee_enrollments', function (Blueprint $table) {
            $table->dropColumn(['status', 'rejection_reason']);
        });
    }
};
