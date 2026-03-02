<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('packages', static function(Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('cores');
            $table->unsignedSmallInteger('memory_mb');
            $table->unsignedSmallInteger('disk_gb');
            $table->string('storage_pool', 64)->default('local-lvm');
            $table->string('network_bridge', 32)->default('vmbr0');
            $table->decimal('price_monthly', 12, 2);
            $table->decimal('price_setup', 12, 2)->default(0);
            $table->string('currency', 3)->default('IDR');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->json('features')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
