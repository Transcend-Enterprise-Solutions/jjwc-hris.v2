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
        Schema::create('branding_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('primary_color_light')->default('#ffffff');
            $table->string('primary_color_dark')->default('#1e293b');
            $table->string('secondary_color_light')->default('#f1f5f9');
            $table->string('secondary_color_dark')->default('#0f172a');
            $table->string('primary_font_color_light')->default('#252a3b');
            $table->string('primary_font_color_dark')->default('#ffffff');
            $table->string('secondary_font_color_light')->default('#4e5a61');
            $table->string('secondary_font_color_dark')->default('#808080');
            $table->string('logo_light_path')->nullable();
            $table->string('logo_dark_path')->nullable();
            $table->string('site_icon_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branding_configurations');
    }
};
