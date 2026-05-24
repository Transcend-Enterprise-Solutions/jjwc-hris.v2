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
        Schema::create('cos_salary_grades', function (Blueprint $table) {
            $table->id();
            $table->integer('salary_grade')->nullable();
            $table->decimal('step1', 10, 2)->nullable();
            $table->decimal('step2', 10, 2)->nullable();
            $table->decimal('step3', 10, 2)->nullable();
            $table->decimal('step4', 10, 2)->nullable();
            $table->decimal('step5', 10, 2)->nullable();
            $table->decimal('step6', 10, 2)->nullable();
            $table->decimal('step7', 10, 2)->nullable();
            $table->decimal('step8', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cos_salary_grades');
    }
};
