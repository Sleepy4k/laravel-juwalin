<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('settings')
            ->where('group', 'site')
            ->whereIn('name', ['payment_midtrans_server_key', 'payment_midtrans_client_key'])
            ->delete();

        $existing = DB::table('settings')
            ->where('group', 'site')
            ->pluck('name')
            ->toArray();

        $newSettings = [
            'payment_pakasir_project' => '""',
            'payment_pakasir_api_key' => '""',
        ];

        foreach ($newSettings as $name => $value) {
            if (!in_array($name, $existing)) {
                DB::table('settings')->insert([
                    'group'   => 'site',
                    'name'    => $name,
                    'payload' => $value,
                    'locked'  => false,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')
            ->where('group', 'site')
            ->whereIn('name', ['payment_pakasir_project', 'payment_pakasir_api_key'])
            ->delete();
    }
};
