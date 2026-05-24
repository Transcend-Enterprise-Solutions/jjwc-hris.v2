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
        if (!Schema::hasTable('monthly_credits')) {
            Schema::create('monthly_credits', function (Blueprint $table) {
                $table->id('id');
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->string('month')->nullable();
                $table->string('year')->nullable();
                $table->decimal('vl_latest_credits', 10, 3)->nullable();
                $table->decimal('vl_latest_claimed', 10, 3)->nullable();
                $table->decimal('sl_latest_credits', 10, 3)->nullable();
                $table->decimal('sl_latest_claimed', 10, 3)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('monthly_credits')) {
            Schema::dropIfExists('monthly_credits');
        }
    }
};
