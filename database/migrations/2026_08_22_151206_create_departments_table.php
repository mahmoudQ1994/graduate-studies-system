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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم القسم (مثل: قسم الدراسات العليا)
            $table->string('code')->nullable(); // كود القسم (مثل: PG-01)
            $table->text('description')->nullable(); // وصف مختصر
            $table->boolean('is_active')->default(true); // حالة القسم
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
