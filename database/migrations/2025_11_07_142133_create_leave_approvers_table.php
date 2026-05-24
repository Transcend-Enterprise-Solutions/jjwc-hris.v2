<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leave_approvers', function (Blueprint $table) {
            $table->id();
            $table->enum('approver_level', ['first', 'second', 'third']);
            $table->string('required_role');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_approvers');
    }
};
