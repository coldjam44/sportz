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
        Schema::create('bookstadium_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bookstadium_id')->constrained('bookstadia')->onDelete('cascade');
            $table->foreignId('userauth_id')->constrained('userauths')->onDelete('cascade'); // اللاعب المشارك
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookstadium_players');
    }
};
