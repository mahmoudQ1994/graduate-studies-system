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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('ministry_name')->default('وزارة الصحة والسكان');
            $table->string('governorate_name')->default('محافظة سوهاج');
            $table->string('directorate_name')->default('مديرية الشؤون الصحية');
            $table->string('administration_name')->default('إدارة التدريب والدراسات العليا');
            $table->string('department_name')->nullable()->default('قسم التعليم الطبي');
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
