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
        Schema::create('providerrates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('rate');
            $table->text('description');
            $table->foreignId('stadium_id')->nullable()->constrained('createstadium')->onDelete('cascade');
                      $table->foreignId('store_id')->nullable()->constrained('createstores')->onDelete('cascade');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('providerrates');
    }
};
