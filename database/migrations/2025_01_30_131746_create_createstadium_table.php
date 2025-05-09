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
        Schema::create('createstadium', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->string('image');
            $table->string('tax_record');
            $table->foreignId('sportsuser_id')->nullable()->constrained('sportsusers')->onDelete('cascade');
            $table->time('morning_start_time');
            $table->time('morning_end_time');
            $table->time('evening_start_time');
            $table->time('evening_end_time');
            $table->decimal('booking_price', 8, 2);
            //$table->boolean('evening_extra_enabled')->default(0);
            $table->decimal('evening_extra_price_per_hour', 8, 2)->nullable();
            $table->integer('team_members_count');
    $table->foreignId('providerauth_id')->nullable()->constrained('providerauths')->onDelete('cascade');



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('createstadium');
    }
};
