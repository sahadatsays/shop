<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table): void {
            $table->foreignId('discount_id')
                ->nullable()
                ->after('expires_at')
                ->constrained()
                ->nullOnDelete();
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->foreignId('discount_id')
                ->nullable()
                ->after('discount_cents')
                ->constrained()
                ->nullOnDelete();

            $table->string('coupon_code', 50)
                ->nullable()
                ->after('discount_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('discount_id');
            $table->dropColumn('coupon_code');
        });

        Schema::table('carts', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('discount_id');
        });
    }
};
