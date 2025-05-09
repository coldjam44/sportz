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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->onDelete('cascade'); // ربط الفاتورة بالسلة
            $table->text('address'); // العنوان
            $table->text('notes')->nullable(); // الملاحظات
            $table->decimal('total_amount', 10, 2); // المبلغ الإجمالي
            $table->timestamps();
                            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
