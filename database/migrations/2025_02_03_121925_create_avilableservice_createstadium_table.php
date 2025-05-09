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
        Schema::create('avilableservice_createstadium', function (Blueprint $table) {
            $table->id();
            $table->foreignId('avilableservice_id')->constrained('avilableservices')->onDelete('cascade');
            $table->foreignId('createstadium_id')->constrained('createstadium')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avilableservice_createstadium');
    }
};
