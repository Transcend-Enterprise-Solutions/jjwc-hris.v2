<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(){
        Schema::create('office_division_units', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('office_division_id');
            $table->foreign('office_division_id')->references('id')->on('office_divisions')->onDelete('cascade');
            $table->string('unit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('office_division_units');
    }
};
