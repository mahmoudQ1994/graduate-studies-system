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
        Schema::create('registration_pauses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('postgraduate_registration_id')
                    ->constrained('postgraduate_registrations')
                    ->onDelete('cascade');
                $table->date('pause_start_date');
                $table->date('pause_end_date')->nullable();
                $table->string('pause_reason');
                $table->date('resume_date')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_pauses');
    }
};
