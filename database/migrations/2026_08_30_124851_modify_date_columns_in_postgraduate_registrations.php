<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('postgraduate_registrations', function (Blueprint $table) {
            $table->date('application_date')->nullable()->change();
            $table->date('registration_date')->nullable()->change();
            $table->date('nominated_degree_date')->nullable()->change();
            $table->date('apology_date')->nullable()->change();
            $table->date('execution_date')->nullable()->change();
            $table->date('nominated_registration_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('postgraduate_registrations', function (Blueprint $table) {
            $table->string('application_date')->nullable()->change();
            $table->string('registration_date')->nullable()->change();
            $table->string('nominated_degree_date')->nullable()->change();
            $table->string('apology_date')->nullable()->change();
            $table->string('execution_date')->nullable()->change();
            $table->string('nominated_registration_date')->nullable()->change();
        });
    }
};
