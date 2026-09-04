<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('orders', 'billing_address')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('billing_address');
            });
        }

        if (Schema::hasTable('customer_addresses')) {
            DB::table('customer_addresses')
                ->whereIn('type', ['billing', 'both'])
                ->update(['type' => 'shipping']);
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('orders', 'billing_address')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->json('billing_address')->nullable()->after('shipping_address');
            });
        }
    }
};
