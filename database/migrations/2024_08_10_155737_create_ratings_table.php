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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('doc_request_id')->constrained('doc_requests')->onDelete('cascade');
            $table->integer('responsiveness')->nullable();
            $table->integer('reliability')->nullable();
            $table->integer('access_facilities')->nullable();
            $table->integer('communication')->nullable();
            $table->integer('cost')->nullable();
            $table->integer('integrity')->nullable();
            $table->integer('assurance')->nullable();
            $table->integer('outcome')->nullable();
            $table->float('overall')->nullable();
            $table->timestamps();

            // Optional: Add indexes
            $table->index('user_id');
            $table->index('doc_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
