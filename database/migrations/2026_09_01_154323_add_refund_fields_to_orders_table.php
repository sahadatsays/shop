<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('refunded_cents')->default(0)->after('total_cents');
            $table->timestamp('return_requested_at')->nullable()->after('placed_at');
            $table->text('return_reason')->nullable()->after('return_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['refunded_cents', 'return_requested_at', 'return_reason']);
        });
    }
};
