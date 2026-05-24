<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leave_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('duration');
            $table->enum('duration_type', ['days', 'months']);
            $table->boolean('requires_document')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_settings');
    }
};