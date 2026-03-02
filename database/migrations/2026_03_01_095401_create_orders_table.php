<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', static function(Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Requested resources
            $table->unsignedTinyInteger('cores');
            $table->unsignedSmallInteger('memory_mb');
            $table->unsignedSmallInteger('disk_gb');

            // Payment
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('IDR');
            $table->string('payment_status', 32)->default('pending');
            // pending | paid | failed | refunded

            $table->string('payment_reference')->nullable();

            // Order lifecycle
            $table->string('status', 32)->default('pending_payment');
            // pending_payment | provisioning | active | cancelled

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
