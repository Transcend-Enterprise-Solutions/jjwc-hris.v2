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
        Schema::create('wfh_location_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('message')->nullable();
            $table->string('attachment')->nullable();
            $table->string('address')->nullable();
            $table->string('curr_lat')->nullable();
            $table->string('curr_lng')->nullable();
            $table->boolean('status')->nullable();
            $table->string('approver')->nullable();
            $table->date('date_approved')->nullable();
            $table->string('disapprover')->nullable();
            $table->date('date_disapproved')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wfh_location_requests');
    }
};
