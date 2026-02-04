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
    Schema::create('ratings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
    $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
    $table->tinyInteger('rating');
    $table->text('comment')->nullable();
    $table->timestamps();

    $table->unique(['order_id', 'customer_id']);
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
