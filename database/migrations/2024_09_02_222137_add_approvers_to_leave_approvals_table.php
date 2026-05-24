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
        Schema::table('leave_approvals', function (Blueprint $table) {
            $table->unsignedBigInteger('first_approver')->nullable()->after('application_id');
            $table->foreign('first_approver')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('second_approver')->nullable()->after('first_approver');
            $table->foreign('second_approver')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('third_approver')->nullable()->after('second_approver');
            $table->foreign('third_approver')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_approvals', function (Blueprint $table) {
            $table->dropForeign(['first_approver']);
            $table->dropColumn('first_approver');

            $table->dropForeign(['second_approver']);
            $table->dropColumn('second_approver');

            $table->dropForeign(['third_approver']);
            $table->dropColumn('third_approver');
        });
    }
};
