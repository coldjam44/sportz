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
        Schema::create('book_stadium_canceled_reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userauth_id');
            $table->unsignedBigInteger('createstadium_id');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('booking_type', ['individual', 'team', 'field']);
            $table->integer('players_count')->nullable();
            $table->integer('remaining_players')->nullable();
            $table->integer('teams_count')->nullable();
            $table->integer('min_players_per_team')->nullable();
            $table->decimal('total_price', 8, 2);
            $table->timestamps();
            $table->integer('remaining_teams')->nullable();
            $table->string('status')->default('pending');
            $table->text('cancellation_reason')->nullable();
            $table->date('date');
            $table->decimal('player_price', 10, 0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_stadium_canceled_reservations');
    }
};
