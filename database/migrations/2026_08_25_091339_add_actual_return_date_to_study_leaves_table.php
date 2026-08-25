<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('study_leaves', function (Blueprint $table) {
            $table->date('actual_return_date')->nullable()->after('end_date'); // إضافة عمود لتاريخ العودة الفعلي بعد تاريخ الانتهاء
        });
    }

    public function down(): void
    {
        Schema::table('study_leaves', function (Blueprint $table) {
            $table->dropColumn('actual_return_date'); // إزالة العمود عند التراجع عن الترحيل
        });
    }
};
