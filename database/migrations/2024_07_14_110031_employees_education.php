<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees_education', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('level_code')->nullable();
            $table->string('level')->nullable();
            $table->string('name_of_school')->nullable();
            $table->string('basic_educ_degree_course')->nullable();
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->string('toPresent')->nullable();
            $table->string('highest_level_unit_earned')->nullable();
            $table->string('year_graduated')->nullable();
            $table->string('award')->nullable();
            $table->boolean('is_bachelor')->nullable();
            $table->boolean('is_master')->nullable();
            $table->boolean('is_doctor')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees_education');
    }
};
