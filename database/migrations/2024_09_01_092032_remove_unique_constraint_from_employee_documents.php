<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'employee_documents'
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");

        Schema::table('employee_documents', function (Blueprint $table) use ($foreignKeys) {
            foreach ($foreignKeys as $foreignKey) {
                $table->dropForeign($foreignKey->CONSTRAINT_NAME);
            }
        });

        Schema::table('employee_documents', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'document_type']);
        });

        // Optionally, recreate the foreign key without the unique constraint
        // Uncomment and adjust the following lines if needed:
        /*
        Schema::table('employee_documents', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Add any other necessary foreign key constraints here
        });
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the unique constraint
        Schema::table('employee_documents', function (Blueprint $table) {
            $table->unique(['user_id', 'document_type']);
        });

        // Recreate the foreign key constraints

        Schema::table('employee_documents', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }
};
