<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->unsignedInteger('flat_shipping_cents')->nullable()->after('free_shipping_threshold_cents');
            $table->unsignedInteger('inside_dhaka_shipping_cents')->nullable()->after('flat_shipping_cents');
            $table->unsignedInteger('outside_dhaka_shipping_cents')->nullable()->after('inside_dhaka_shipping_cents');
        });

        DB::table('store_settings')->update([
            'flat_shipping_cents' => 8000,
            'inside_dhaka_shipping_cents' => 6000,
            'outside_dhaka_shipping_cents' => 12000,
        ]);
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'flat_shipping_cents',
                'inside_dhaka_shipping_cents',
                'outside_dhaka_shipping_cents',
            ]);
        });
    }
};
