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
        Schema::create('bookstadia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userauth_id')->constrained('userauths')->onDelete('cascade');
            $table->foreignId('createstadium_id')->constrained('createstadium')->onDelete('cascade');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('booking_type', ['individual', 'team', 'field']);
            $table->integer('players_count')->nullable();
            $table->integer('teams_count')->nullable();
            $table->integer('min_players_per_team')->nullable();
            $table->decimal('total_price', 8, 2);
                      $table->decimal('player_price');

            $table->integer('remaining_teams')->nullable();
            $table->timestamps();
          $table->date('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookstadia');
    }
};
