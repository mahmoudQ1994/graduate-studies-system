<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('trainer_type');
            $table->unsignedBigInteger('created_by')->nullable()->after('status');
            $table->unsignedBigInteger('approved_by')->nullable()->after('created_by');
            $table->timestamp('approval_date')->nullable()->after('approved_by');
            $table->unsignedBigInteger('updated_by')->nullable()->after('approval_date');
            $table->text('rejection_reason')->nullable()->after('updated_by');

            // ربط العلاقات بجدول المستخدمين
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn([
                'status', 'created_by', 'approved_by',
                'approval_date', 'updated_by', 'rejection_reason'
            ]);
        });
    }
};
