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
        Schema::create('official_businesses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('reference_number')->nullable();
            $table->string('company')->nullable();
            $table->string('address')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->date('date')->nullable();
            $table->time('time_start')->nullable();
            $table->time('time_end')->nullable();
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->string('purpose')->nullable();
            $table->boolean('status')->default(0)->nullable();
            $table->unsignedBigInteger('sup_approver')->nullable();
            $table->date('date_sup_approved')->nullable();
            $table->unsignedBigInteger('sup_disapprover')->nullable();
            $table->date('date_sup_disapproved')->nullable();
            $table->unsignedBigInteger('approver')->nullable();
            $table->date('date_approved')->nullable();
            $table->unsignedBigInteger('disapprover')->nullable();
            $table->date('date_disapproved')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_businesses');
    }
};
