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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userauth_id')->constrained('userauths')->onDelete('cascade'); // ربط الطلب بالمستخدم
        $table->decimal('total_price', 10, 2);
        $table->enum('status', ['pending', 'completed', 'canceled','current'])->default('pending');
          $table->string('current_status')->nullable();
        $table->foreign('userstore_id')->references('id')->on('userstores')->onDelete('cascade');  // تعريف العلاقة مع جدول userstores
$table->foreign('createstore_id')->references('id')->on('createstores')->onDelete('cascade');
        $table->foreignId('cart_id')->nullable()->constrained('carts')->onDelete('cascade');



        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
