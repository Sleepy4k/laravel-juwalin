<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('orders', static function(Blueprint $table): void {
            $table->foreignId('package_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });

        Schema::table('containers', static function(Blueprint $table): void {
            $table->foreignId('package_id')->nullable()->after('order_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', static function(Blueprint $table): void {
            $table->dropConstrainedForeignId('package_id');
        });

        Schema::table('containers', static function(Blueprint $table): void {
            $table->dropConstrainedForeignId('package_id');
        });
    }
};
