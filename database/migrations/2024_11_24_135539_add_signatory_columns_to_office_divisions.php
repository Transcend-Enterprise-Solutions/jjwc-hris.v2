<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('office_divisions', function (Blueprint $table) {
            $table->string('sign_name')->nullable()->after('office_division');
            $table->string('sign_pos')->nullable()->after('sign_name');
        });
    }

    public function down()
    {
        Schema::table('office_divisions', function (Blueprint $table) {
            $table->dropColumn(['sign_name', 'sign_pos']);
        });
    }
};
