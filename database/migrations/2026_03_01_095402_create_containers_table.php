<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('containers', static function(Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();

            // Proxmox identifiers
            $table->unsignedInteger('vmid')->unique();
            $table->string('node', 64);          // e.g. "pve"
            $table->string('hostname', 253);

            // Allocated resources
            $table->unsignedTinyInteger('cores');
            $table->unsignedSmallInteger('memory_mb');
            $table->unsignedSmallInteger('disk_gb');
            $table->string('storage', 64)->default('local-lvm');

            // Lifecycle: provisioning | running | stopped | suspended | deleted
            $table->string('status', 32)->default('provisioning');
            $table->string('provision_task_upid')->nullable();
            $table->timestamp('provisioned_at')->nullable();

            // Networking (set post-provision)
            $table->string('ip_address', 45)->nullable();
            $table->string('gateway', 45)->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};
