<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('idempotency_key')->unique();
            $table->text('notes')->nullable();
            $table->timestamp('received_at');
            $table->timestamps();

            $table->index(['purchase_id', 'received_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_receipts');
    }
};
