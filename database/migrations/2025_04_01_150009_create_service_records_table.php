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
        Schema::create('service_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->string('toPresent')->nullable();
            $table->string('designation')->nullable();
            $table->string('status')->nullable();
            $table->string('salary_annum')->nullable();
            $table->string('station_place_of_assignment')->nullable();
            $table->string('branch')->nullable();
            $table->string('lv_abs_wo_pay')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_records');
    }
};
