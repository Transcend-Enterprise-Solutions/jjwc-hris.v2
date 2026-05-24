<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_experience', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('toPresent')->nullable();
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->double('monthly_salary')->nullable();
            $table->string('sg_step')->nullable();
            $table->string('status_of_appointment')->nullable();
            $table->boolean('gov_service')->default(0);
            $table->double('pera')->nullable();
            $table->string('branch')->nullable();
            $table->integer('leave_absence_wo_pay')->nullable();
            $table->date('separation_date')->nullable();
            $table->string('separation_cause')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_experience');
    }
};
