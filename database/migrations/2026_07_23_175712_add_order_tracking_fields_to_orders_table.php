<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('payment_status')->default('paid')->after('status');
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->unsignedInteger('discount_cents')->default(0)->after('subtotal_cents');
            $table->unsignedInteger('shipping_cents')->default(0)->after('discount_cents');
            $table->unsignedInteger('tax_cents')->default(0)->after('shipping_cents');
            $table->json('shipping_address')->nullable()->after('tax_cents');
            $table->json('billing_address')->nullable()->after('shipping_address');
            $table->string('courier_name')->nullable()->after('billing_address');
            $table->string('tracking_number')->nullable()->after('courier_name');
            $table->timestamp('estimated_delivery_at')->nullable()->after('tracking_number');
            $table->text('delivery_instructions')->nullable()->after('estimated_delivery_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn([
                'payment_status',
                'payment_method',
                'discount_cents',
                'shipping_cents',
                'tax_cents',
                'shipping_address',
                'billing_address',
                'courier_name',
                'tracking_number',
                'estimated_delivery_at',
                'delivery_instructions',
            ]);
        });
    }
};
