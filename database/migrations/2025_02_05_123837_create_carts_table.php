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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userauth_id')->constrained('userauths')->onDelete('cascade'); // المستخدم
            $table->foreignId('product_id')->constrained('addproducts')->onDelete('cascade'); // المنتج
            $table->integer('quantity')->default(1); // العدد
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
