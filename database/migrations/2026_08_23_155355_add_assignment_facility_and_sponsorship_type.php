<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. إضافة جهة الانتداب/النيابة/الإعارة بجدول الكوادر الطبية
        Schema::table('health_professionals', function (Blueprint $table) {
            $table->string('secondment_facility')->nullable()->after('facility_id')->comment('جهة الانتداب أو النيابة أو الإعارة');
        });

        // 2. إضافة نوع الترشيح بجدول تسجيل الدراسات العليا
        Schema::table('postgraduate_registrations', function (Blueprint $table) {
            $table->enum('sponsorship_type', ['وزاري', 'على النفقة الخاصة'])->default('وزاري')->after('required_university');
        });
    }

    public function down(): void
    {
        Schema::table('health_professionals', function (Blueprint $table) {
            $table->dropColumn('secondment_facility');
        });

        Schema::table('postgraduate_registrations', function (Blueprint $table) {
            $table->dropColumn('sponsorship_type');
        });
    }
};
