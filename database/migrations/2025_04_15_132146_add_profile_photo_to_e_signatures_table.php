<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('e_signatures', function (Blueprint $table) {
            $table->string('profile_photo_path')->nullable()->after('file_path');
            $table->string('emergency_contact_name')->nullable()->after('profile_photo_path');
            $table->string('emergency_contact_number')->nullable()->after('emergency_contact_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('e_signatures', function (Blueprint $table) {
            $table->dropColumn(['profile_photo_path', 'emergency_contact_name', 'emergency_contact_number']);
        });
    }
};