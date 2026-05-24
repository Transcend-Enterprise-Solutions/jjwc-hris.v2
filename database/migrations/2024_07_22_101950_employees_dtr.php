<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('employees_dtr')) {
            Schema::create('employees_dtr', function (Blueprint $table) {
                $table->id('id');
                $table->unsignedBigInteger('user_id');
                $table->string('emp_code');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->date('date');
                $table->string('day_of_week')->nullable();
                $table->string('location')->nullable();
                $table->time('morning_in')->nullable();
                $table->time('morning_out')->nullable();
                $table->time('afternoon_in')->nullable();
                $table->time('afternoon_out')->nullable();
                $table->string('late')->nullable();
                $table->string('overtime')->nullable();
                $table->string('total_hours_rendered')->nullable();
                $table->string('remarks')->nullable();
                $table->timestamps();

                // Indexes for better performance
                $table->index(['user_id', 'date']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('employees_dtr')) {
            Schema::dropIfExists('employees_dtr');
        }
    }
};
