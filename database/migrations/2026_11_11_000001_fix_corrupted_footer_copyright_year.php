<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Live aqar-demo.novixa.dev footer rendered "© 20266 Novixa Aqar." — the
 * stored `general.footer_copyright` value has a stray extra digit in its
 * year ("20266" instead of "2026"). Confirmed as corrupted data rather
 * than a seeder/migration bug: aqar-demo2 (same seeder code, same app
 * version) renders its own footer_copyright with the correct "2026".
 *
 * Narrow, one-way text correction. Only touches a row whose decoded value
 * contains the exact "20266" artifact; any other footer_copyright value
 * (including the correct default) is left untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        DB::table('settings')
            ->where('group', 'general')
            ->where('name', 'footer_copyright')
            ->get(['id', 'payload'])
            ->each(function ($row) {
                $decoded = json_decode($row->payload, true);

                if (! is_string($decoded) || ! str_contains($decoded, '20266')) {
                    return;
                }

                $fixed = str_replace('20266', '2026', $decoded);

                DB::table('settings')
                    ->where('id', $row->id)
                    ->update(['payload' => json_encode($fixed, JSON_UNESCAPED_UNICODE), 'updated_at' => now()]);
            });
    }

    public function down(): void
    {
        // Data-only and one-way: the corrupted value is not worth restoring.
    }
};
