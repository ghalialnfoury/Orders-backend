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
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
    $table->foreignId('driver_id')->nullable()->constrained('users');
    $table->decimal('total_price', 8, 2)->default(0);
    $table->enum('status', [
        'pending',
        'accepted',
        'preparing',
        'on_the_way',
        'delivered',
        'canceled'
    ])->default('pending');
    $table->string('payment_method')->default('cash');
    $table->string('payment_status')->default('unpaid');
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
