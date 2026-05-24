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
        Schema::create('dtrschedules', function (Blueprint $table) {
            $table->id();
            $table->string('emp_code'); // Employee ID (foreign key)
            $table->string('wfh_days')->nullable();; // Comma-separated days for WFH
            $table->time('default_start_time')->nullable(); // Default start time for office work
            $table->time('default_end_time')->nullable(); // Default end time for office work
            $table->date('start_date'); // Schedule start date
            $table->date('end_date'); // Schedule end date
            $table->timestamps();

            // Foreign key constraint
            // $table->foreign('emp_code')->references('emp_code')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dtrschedules');
    }
};
