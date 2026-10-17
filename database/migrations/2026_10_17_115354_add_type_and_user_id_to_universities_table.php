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
        Schema::table('universities', function (Blueprint $table) {
            $table->string('type')->default('حكومية'); // نوع الجامعة
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // المستخدم الذي قام بالإضافة
        });
    }

    public function down(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->dropColumn(['type', 'user_id']);
        });
    }
};
