<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_timeline_events', function (Blueprint $table): void {
            $table->foreignId('changed_by')
                ->nullable()
                ->after('author_name')
                ->constrained('users')
                ->nullOnDelete();

            $table->index(['order_id', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('order_timeline_events', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('changed_by');
        });
    }
};
