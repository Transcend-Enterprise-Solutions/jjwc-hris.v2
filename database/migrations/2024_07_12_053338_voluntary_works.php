<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voluntary_works', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('org_name');
            $table->string('org_address');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('toPresent')->nullable();
            $table->string('no_of_hours');
            $table->string('position_nature');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void{
        Schema::dropIfExists('voluntary_works');
    }
};
