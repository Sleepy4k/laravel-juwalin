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
        Schema::table('payments', static function(Blueprint $table): void {
            $table->string('payment_method')->nullable()->after('method');
            $table->string('gateway')->nullable()->after('payment_method');
            $table->string('proof_file')->nullable()->after('gateway_payload');
            $table->text('notes')->nullable()->after('proof_file');
        });
    }

    public function down(): void
    {
        Schema::table('payments', static function(Blueprint $table): void {
            $table->dropColumn(['payment_method', 'gateway', 'proof_file', 'notes']);
        });
    }
};
