<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
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

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
