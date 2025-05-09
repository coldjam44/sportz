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
        Schema::create('user_sports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('userauths')->onDelete('cascade');
            $table->foreignId('sportsuser_id')->constrained('sportsusers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sports');
    }
};
