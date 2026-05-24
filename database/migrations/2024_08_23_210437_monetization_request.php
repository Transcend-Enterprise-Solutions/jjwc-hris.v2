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
        if (!Schema::hasTable('monetization_request')) {
            Schema::create('monetization_request', function (Blueprint $table) {
                $table->id('id');
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->decimal('vl_credits_requested', 10, 3)->nullable();
                $table->decimal('sl_credits_requested', 10, 3)->nullable();
                $table->decimal('vl_monetize_credits', 10, 3)->nullable();
                $table->decimal('sl_monetize_credits', 10, 3)->nullable();
                $table->string('status');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('monetization_request')) {
            Schema::dropIfExists('monetization_request');
        }
    }
};
