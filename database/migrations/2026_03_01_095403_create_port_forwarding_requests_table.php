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
        Schema::create('port_forwarding_requests', static function(Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('container_id')->constrained()->cascadeOnDelete();

            $table->string('protocol', 8)->default('tcp');  // tcp | udp
            $table->unsignedSmallInteger('source_port');    // public port on gateway
            $table->unsignedSmallInteger('destination_port'); // port inside the CT

            $table->string('status', 32)->default('pending');
            // pending | approved | active | rejected | removed

            $table->text('reason')->nullable(); // user's reason for forwarding
            $table->text('admin_note')->nullable();

            $table->timestamps();

            $table->unique(['source_port', 'protocol']); // no duplicate public port+protocol
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('port_forwarding_requests');
    }
};
