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
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المستشفى أو الإدارة (مثال: مستشفى أخميم المركزي)
            $table->foreignId('district_id')->constrained('districts')->cascadeOnDelete(); // ربطه بالمركز
            $table->foreignId('sector_id')->nullable()->constrained('sectors')->nullOnDelete(); // ربطه بقطاع التبعية
            $table->string('type'); // نوع الجهة
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
