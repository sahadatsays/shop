<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('categories')->nullOnDelete();
            $table->string('image_path')->nullable()->after('slug');
            $table->string('banner_path')->nullable()->after('image_path');
            $table->string('status')->default('active')->after('banner_path');
            $table->string('meta_title')->nullable()->after('status');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->unsignedInteger('sort_order')->default(0)->after('meta_keywords');
            $table->softDeletes();

            $table->index(['status', 'sort_order']);
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn([
                'parent_id',
                'image_path',
                'banner_path',
                'status',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'sort_order',
                'deleted_at',
            ]);
        });
    }
};
