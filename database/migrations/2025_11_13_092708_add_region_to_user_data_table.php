<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_data', function (Blueprint $table) {
            $table->string('permanent_selectedRegion')->nullable()->after('user_id');
            $table->string('residential_selectedRegion')->nullable()->after('permanent_selectedZipcode');
        });
    }

    public function down(): void
    {
        Schema::table('user_data', function (Blueprint $table) {
            $table->dropColumn(['permanent_selectedRegion', 'residential_selectedRegion']);
        });
    }
};