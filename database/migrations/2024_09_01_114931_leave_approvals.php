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
        if (! Schema::hasTable('leave_approvals')) {
            Schema::create('leave_approvals', function (Blueprint $table) {
                $table->id('id');
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                $table->unsignedBigInteger('application_id');
                $table->foreign('application_id')->references('id')->on('leave_application')->onDelete('cascade');

                $table->unsignedBigInteger('first_approver')->nullable();
                $table->foreign('first_approver')->references('id')->on('users')->onDelete('cascade');

                $table->unsignedBigInteger('second_approver')->nullable();
                $table->foreign('second_approver')->references('id')->on('users')->onDelete('cascade');

                $table->unsignedBigInteger('third_approver')->nullable();
                $table->foreign('third_approver')->references('id')->on('users')->onDelete('cascade');

                $table->integer('stage')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('leave_approvals')) {
            Schema::dropIfExists('leave_approvals');
        }
    }
};
