<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')->where('status', 'refund_ready')->update(['status' => 'refunded']);
        DB::table('order_timeline_events')->where('status', 'refund_ready')->update(['status' => 'refunded']);
    }

    public function down(): void
    {
        DB::table('orders')->where('status', 'refunded')->update(['status' => 'refund_ready']);
        DB::table('order_timeline_events')->where('status', 'refunded')->update(['status' => 'refund_ready']);
    }
};
