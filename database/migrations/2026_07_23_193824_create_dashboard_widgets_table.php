<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dashboard_widgets', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 100)->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->text('icon')->nullable();
            $table->string('type', 40)->index();
            $table->string('category', 40)->default('general')->index();
            $table->unsignedTinyInteger('width')->default(6);
            $table->unsignedTinyInteger('height')->default(1);
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->unsignedInteger('refresh_interval')->nullable();
            $table->string('permission')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_system')->default(false);
            $table->json('config')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_widgets');
    }
};
