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
        Schema::create('id_signatories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('position_id');
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('cascade');
            $table->unsignedBigInteger('office_division_id');
            $table->foreign('office_division_id')->references('id')->on('office_divisions')->onDelete('cascade');
            $table->string('signature_path')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('id_signatories');
    }
};
