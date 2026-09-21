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
    Schema::create('training_secondments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('health_professional_id')->constrained('health_professionals')->onDelete('cascade');
        $table->string('action_type')->default('إفاد');
        $table->string('training_duration_text')->nullable();
        $table->string('training_entity'); // جهة الإفاد
        $table->string('training_department')->nullable(); // القسم بجهة الإفاد
        $table->integer('duration_months'); // مدة الإفاد بالشهور
        $table->json('training_days')->nullable(); // أيام الإفاد (مثل السبت، الاثنين...)
        $table->date('start_date'); // تاريخ بداية الإفاد
        $table->date('end_date'); // تاريخ نهاية الإفاد (يحسب تلقائياً)
        $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // المستخدم الذي سجل البيانات
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_secondments');
    }
};
