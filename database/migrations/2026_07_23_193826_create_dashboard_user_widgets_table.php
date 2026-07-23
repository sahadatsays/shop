<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dashboard_user_widgets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dashboard_widget_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_collapsed')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->unsignedInteger('position')->nullable();
            $table->unsignedTinyInteger('width')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'dashboard_widget_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_user_widgets');
    }
};
