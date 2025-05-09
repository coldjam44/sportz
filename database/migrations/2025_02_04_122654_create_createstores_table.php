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
        Schema::create('createstores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
                      $table->string('store_type');

            $table->string('location');
            $table->string('image');
            $table->string('tax_record');
            $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('cascade');
                      $table->foreignId('store_type_id')->nullable()->constrained('storetypes')->onDelete('cascade');

                  $table->integer('orders_count')->nullable();
    $table->foreignId('providerauth_id')->nullable()->constrained('providerauths')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('createstores');
    }
};
