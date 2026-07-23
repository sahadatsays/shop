<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('store_name');
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('support_email')->nullable();
            $table->json('social_links')->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('timezone')->default('America/New_York');
            $table->string('mail_from_name')->nullable();
            $table->string('mail_from_address')->nullable();
            $table->boolean('maintenance_enabled')->default(false);
            $table->text('maintenance_message')->nullable();
            $table->string('maintenance_secret')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_image_path')->nullable();
            $table->string('utility_bar_message')->nullable();
            $table->unsignedInteger('free_shipping_threshold_cents')->nullable();
            $table->string('google_analytics_id')->nullable();
            $table->json('theme_colors')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
