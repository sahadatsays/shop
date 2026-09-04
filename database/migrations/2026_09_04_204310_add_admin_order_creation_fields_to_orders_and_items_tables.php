<?php

use App\Enums\PaymentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('source')->default('website')->after('order_number');
            $table->foreignId('created_by')->nullable()->after('customer_id')->constrained('users')->nullOnDelete();
            $table->unsignedInteger('paid_cents')->default(0)->after('total_cents');
            $table->string('shipping_method')->nullable()->after('shipping_cents');
            $table->string('idempotency_key')->nullable()->unique()->after('payment_reference');
            $table->text('admin_notes')->nullable()->after('delivery_instructions');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('product_id');
            $table->string('sku')->nullable()->after('product_name');
            $table->unsignedInteger('discount_cents')->default(0)->after('unit_price_cents');
        });

        DB::table('orders')
            ->whereIn('payment_status', [
                PaymentStatus::Paid->value,
                PaymentStatus::PartiallyRefunded->value,
                PaymentStatus::Refunded->value,
            ])
            ->update([
                'paid_cents' => DB::raw('total_cents'),
            ]);

        DB::table('order_items')
            ->orderBy('id')
            ->chunkById(100, function ($items): void {
                foreach ($items as $item) {
                    $product = DB::table('products')->where('id', $item->product_id)->first();

                    if ($product === null) {
                        continue;
                    }

                    DB::table('order_items')->where('id', $item->id)->update([
                        'product_name' => $product->name,
                        'sku' => $product->sku,
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'sku', 'discount_cents']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn([
                'source',
                'paid_cents',
                'shipping_method',
                'idempotency_key',
                'admin_notes',
            ]);
        });
    }
};
