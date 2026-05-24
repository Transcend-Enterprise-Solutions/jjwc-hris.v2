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
        Schema::create('work_experience_sheet_tables', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('toPresent')->nullable();
            $table->string('position')->nullable();
            $table->string('office_unit')->nullable();
            $table->string('supervisor')->nullable();
            $table->string('agency_org')->nullable();
            $table->text('list_accomp_cont')->nullable();
            $table->text('sum_of_duties')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_experience_sheet_tables');
    }
};
