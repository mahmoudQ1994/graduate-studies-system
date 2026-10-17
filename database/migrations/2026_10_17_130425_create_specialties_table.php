<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specialties', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم التخصص الوظيفي
            $table->boolean('is_active')->default(true); // الحالة (مفعل/معطل)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // المستخدم الذي قام بالإضافة
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specialties');
    }
};
