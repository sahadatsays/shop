<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_settings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedTinyInteger('featured_products_limit')->default(8);
            $table->unsignedTinyInteger('new_arrivals_limit')->default(8);
            $table->unsignedTinyInteger('best_sellers_limit')->default(4);
            $table->unsignedTinyInteger('brands_limit')->default(8);
            $table->unsignedTinyInteger('categories_limit')->default(8);
            $table->unsignedTinyInteger('reviews_limit')->default(6);
            $table->unsignedSmallInteger('new_badge_days')->default(30);
            $table->boolean('hide_out_of_stock')->default(true);
            $table->json('sections')->nullable();
            $table->json('popular_searches')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->string('meta_keywords')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_settings');
    }
};
