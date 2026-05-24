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
        Schema::create('system_modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_module_id')->nullable();
            $table->foreign('parent_module_id')->references('id')->on('parent_modules')->onDelete('set null');
            $table->string('module_name')->nullable();
            $table->string('module_key')->nullable();
            $table->string('component_class')->nullable();
            $table->string('route')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_modules');
    }
};
