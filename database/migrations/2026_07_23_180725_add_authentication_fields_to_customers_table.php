<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            $table->string('password')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->string('provider')->nullable()->after('avatar');
            $table->string('provider_id')->nullable()->after('provider');
            $table->timestamp('email_verified_at')->nullable()->after('provider_id');
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->boolean('newsletter_subscribed')->default(false)->after('last_login_at');
            $table->rememberToken()->after('newsletter_subscribed');

            $table->unique('phone');
            $table->index(['provider', 'provider_id']);
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            $table->dropUnique(['phone']);
            $table->dropIndex(['provider', 'provider_id']);
            $table->dropColumn([
                'password',
                'avatar',
                'provider',
                'provider_id',
                'email_verified_at',
                'last_login_at',
                'newsletter_subscribed',
                'remember_token',
            ]);
        });
    }
};
