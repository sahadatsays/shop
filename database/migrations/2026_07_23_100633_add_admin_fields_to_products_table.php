<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable()->after('slug');
            $table->string('barcode')->nullable()->after('sku');
            $table->text('short_description')->nullable()->after('barcode');
            $table->text('description')->nullable()->after('short_description');
            $table->string('status')->default('draft')->after('description');
            $table->boolean('is_featured')->default(false)->after('status');
            $table->boolean('is_new_arrival')->default(false)->after('is_featured');
            $table->string('meta_title')->nullable()->after('is_new_arrival');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->unsignedSmallInteger('sort_order')->default(0)->after('meta_keywords');
            $table->softDeletes();
        });

        DB::table('products')->update([
            'status' => DB::raw("CASE WHEN is_active = 1 THEN 'published' ELSE 'draft' END"),
        ]);

        DB::table('products')->orderBy('id')->get(['id'])->each(function ($product): void {
            DB::table('products')->where('id', $product->id)->update([
                'sku' => 'PRD-'.str_pad((string) $product->id, 5, '0', STR_PAD_LEFT),
            ]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'stock_quantity']);
            $table->dropColumn('is_active');
            $table->string('sku')->nullable(false)->change();
            $table->unique('sku');
            $table->unique('barcode');
            $table->index(['status', 'stock_quantity']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('low_stock_threshold');
        });

        DB::table('products')->update([
            'is_active' => DB::raw("CASE WHEN status = 'published' THEN 1 ELSE 0 END"),
        ]);

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['status', 'stock_quantity']);
            $table->dropUnique(['sku']);
            $table->dropUnique(['barcode']);
            $table->dropSoftDeletes();
            $table->dropColumn([
                'sku',
                'barcode',
                'short_description',
                'description',
                'status',
                'is_featured',
                'is_new_arrival',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'sort_order',
            ]);
            $table->index(['is_active', 'stock_quantity']);
        });
    }
};
