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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message_ar');
            $table->text('message_en')->nullable();
            $table->string('type')->default('general'); // نوع الإشعار
            $table->enum('notification_status', ['new', 'read', 'archived', 'deleted', 'action_required'])->default('new');
            $table->unsignedBigInteger('notifiable_id');
            $table->string('notifiable_type');
            $table->timestamp('read_at')->nullable();
            $table->string('action_url')->nullable();
            $table->timestamps();
            $table->softDeletes(); // عمود deleted_at للحذف الناعم (soft delete)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
