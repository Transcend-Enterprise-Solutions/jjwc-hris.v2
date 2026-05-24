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
        if (!Schema::hasTable('leave_credits_calculation')) {
            Schema::create('leave_credits_calculation', function (Blueprint $table) {
                $table->id('id');
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->string('month')->nullable();
                $table->string('year')->nullable();
                $table->string('late_time')->nullable();
                $table->decimal('late_in_credits', 10, 3)->nullable();
                $table->decimal('leave_credits_earned', 10, 3)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('leave_credits_calculation')) {
            Schema::dropIfExists('leave_credits_calculation');
        }
    }
};
