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
        Schema::table('user_data', function (Blueprint $table) {
            $table->boolean('ethnicity')->default(false)->after('pwd');
            $table->boolean('is_solo_parent')->default(false)->after('ethnicity');
            $table->string('solo_parent_id_no')->nullable()->after('is_solo_parent');
            $table->date('solo_parent_valid_until')->nullable()->after('solo_parent_id_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_data', function (Blueprint $table) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn(['ethnicity', 'is_solo_parent', 'solo_parent_id_no', 'solo_parent_valid_until']);
            });
        });
    }
};
