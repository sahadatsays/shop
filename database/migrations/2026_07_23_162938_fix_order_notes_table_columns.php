<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('order_notes')) {
            return;
        }

        Schema::table('order_notes', function (Blueprint $table): void {
            if (! Schema::hasColumn('order_notes', 'order_id')) {
                $table->foreignId('order_id')->after('id')->constrained()->cascadeOnDelete();
            }

            if (! Schema::hasColumn('order_notes', 'body')) {
                $table->text('body')->after('order_id');
            }

            if (! Schema::hasColumn('order_notes', 'author_name')) {
                $table->string('author_name')->nullable()->after('body');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('order_notes')) {
            return;
        }

        Schema::table('order_notes', function (Blueprint $table): void {
            if (Schema::hasColumn('order_notes', 'order_id')) {
                $table->dropConstrainedForeignId('order_id');
            }

            if (Schema::hasColumn('order_notes', 'body')) {
                $table->dropColumn('body');
            }

            if (Schema::hasColumn('order_notes', 'author_name')) {
                $table->dropColumn('author_name');
            }
        });
    }
};
