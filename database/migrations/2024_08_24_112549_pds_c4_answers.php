<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pds_c4_answers', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('question_number')->nullable();
            $table->string('question_letter')->nullable();
            $table->boolean('answer')->nullable();
            $table->string('details')->nullable();        
            $table->date('date_filed')->format('F d Y')->nullable();        
            $table->string('status')->nullable();        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pds_c4_answers');
    }
};
