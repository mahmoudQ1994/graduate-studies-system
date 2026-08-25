<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_movements', function (Blueprint $table) {
            $table->id();

            // الربط بالطبيب
            $table->foreignId('health_professional_id')
                  ->constrained('health_professionals')
                  ->cascadeOnDelete()
                  ->comment('ربط بالطبيب / الموظف الصحي');

            // حقول حركة النيابة والإعارة من الإكسيل
            $table->string('specialty')->nullable()->comment('تخصص الحركة/النيابة');
            $table->string('movement_date')->nullable()->comment('تاريخ الحركة (تاريخ حركة النيابة أو الإعارة)');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_movements');
    }
};
