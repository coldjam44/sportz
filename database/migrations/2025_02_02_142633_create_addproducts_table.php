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
        Schema::create('addproducts', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('description_ar');
            $table->text('description_en');
            $table->string('image');
            $table->decimal('price', 8, 2);
            $table->string('discount')->nullable();
            $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('cascade');
                      $table->foreignId('store_id')->nullable()->constrained('createstores')->onDelete('cascade');

            $table->date('start_time')->nullable();
            $table->date('end_time')->nullable();
            $table->timestamps();
              $table->foreignId('providerauth_id')->nullable()->constrained('providerauths')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addproducts');
    }
};
