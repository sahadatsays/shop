<?php

use App\Enums\SupplierStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('country', 100)->nullable()->default('Bangladesh');
            $table->string('tax_id', 100)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default(SupplierStatus::Active->value)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('company_name');
            $table->index('phone');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
