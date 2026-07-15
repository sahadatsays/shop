<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->string('status');
            $table->unsignedInteger('subtotal_cents');
            $table->unsignedInteger('total_cents');
            $table->timestamp('placed_at');
            $table->timestamps();

            $table->index(['status', 'placed_at']);
            $table->index('placed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
