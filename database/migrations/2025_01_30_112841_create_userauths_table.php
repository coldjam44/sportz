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
        Schema::create('userauths', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone')->unique();
            $table->string('email')->nullable()->unique();
            $table->enum('gender', ['male', 'female']);
            $table->string('province');  // المحافظة
            $table->string('city');      // المدينة
            $table->string('area');      // المنطقة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('userauths');
    }
};
