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
        Schema::create('transactions_wfh', function (Blueprint $table) {
            $table->id();
            $table->string('emp_code');
            $table->timestamp('punch_time')->nullable();
            $table->string('punch_state')->nullable();
            $table->string('punch_state_display')->nullable();
            $table->integer('verify_type')->nullable();
            $table->string('verify_type_display')->nullable();
            $table->string('area_alias')->nullable();
            $table->timestamp('upload_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions_wfh');
    }
};
