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
        Schema::create('admin_role_accesses', function (Blueprint $table) {
            $table->id();
            $table->string('role_name');
            $table->string('role_code')->unique();
            $table->integer('hierarchy')->default(5)->nullable();
            $table->string('modules')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_role_accesses');
    }
};
