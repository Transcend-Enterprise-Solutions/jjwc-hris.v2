<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('leave_credits')) {
            Schema::create('leave_credits', function (Blueprint $table) {
                $table->id('id');
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->decimal('total_credits', 10, 3)->nullable();
                $table->decimal('vl_claimable_credits', 10, 3)->nullable();
                $table->decimal('sl_claimable_credits', 10, 3)->nullable();
                $table->decimal('spl_claimable_credits', 10, 3)->nullable();
                $table->decimal('vl_claimed_credits', 10, 3)->nullable();
                $table->decimal('sl_claimed_credits', 10, 3)->nullable();
                $table->decimal('spl_claimed_credits', 10, 3)->nullable();
                $table->boolean('credits_transferred')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('leave_credits')) {
            Schema::dropIfExists('leave_credits');
        }
    }
};
