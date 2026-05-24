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
        if (!Schema::hasTable('mandatory_form_request')) {
            Schema::create('mandatory_form_request', function (Blueprint $table) {
                $table->id('id');
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
                $table->date('date_requested')->useCurrent();
                $table->string('status')->default('pending');
                $table->date('date_completed')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('mandatory_form_request')) {
            Schema::dropIfExists('mandatory_form_request');
        }
    }
};
