<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professional_qualifications', function (Blueprint $table) {
            $table->id();

            // الربط بالطبيب
            $table->foreignId('health_professional_id')
                  ->constrained('health_professionals')
                  ->cascadeOnDelete()
                  ->comment('ربط بالطبيب / الموظف الصحي');

            // حقول التخرج من الإكسيل
            $table->string('university')->nullable()->comment('جامعة التخرج');
            $table->string('faculty')->nullable()->comment('كلية التخرج');
            $table->string('graduation_batch')->nullable()->comment('دفعة التخرج');
            $table->string('general_grade')->nullable()->comment('التقدير العام (ممتاز/جيد جدا...)');
            $table->string('subject_grade')->nullable()->comment('تقدير المادة');
            $table->string('total_marks')->nullable()->comment('المجموع التراكمي (درجات أو نسبة مئوية)');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professional_qualifications');
    }
};
