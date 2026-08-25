<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_changes_history', function (Blueprint $table) {
            $table->id();

            // تحديد اسم المفتاح الأجنبي يدوياً لمنع تجاوَز حد 64 حرفاً
            $table->foreignId('postgraduate_registration_id')
                  ->constrained('postgraduate_registrations', 'id', 'reg_changes_postgrad_id_fk')
                  ->onDelete('cascade');

            $table->string('change_type'); // university, specialty, degree
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->date('change_date');
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_changes_history');
    }
};
