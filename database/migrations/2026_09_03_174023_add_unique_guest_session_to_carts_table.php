<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('carts')
            ->select('session_id')
            ->whereNull('customer_id')
            ->whereNotNull('session_id')
            ->groupBy('session_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('session_id');

        foreach ($duplicates as $sessionId) {
            $ids = DB::table('carts')
                ->where('session_id', $sessionId)
                ->whereNull('customer_id')
                ->orderBy('id')
                ->pluck('id');

            $keep = $ids->shift();

            if ($keep !== null && $ids->isNotEmpty()) {
                DB::table('cart_items')->whereIn('cart_id', $ids)->update(['cart_id' => $keep]);
                DB::table('carts')->whereIn('id', $ids)->delete();
            }
        }

        Schema::table('carts', function (Blueprint $table): void {
            $table->unique('session_id');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table): void {
            $table->dropUnique(['session_id']);
        });
    }
};
