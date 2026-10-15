<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The original settings migration seeded info@darhadhramaut.ye, a domain this
 * product does not control, so contact mail could go to a stranger. Move it
 * to the Novixa domain — only while it is still that untouched default, so an
 * address an operator has already set is never overwritten.
 *
 * A plain, guarded UPDATE: it touches exactly one existing row and is safe
 * whether or not the (group, name) unique index from 2026_10_08 is present.
 */
return new class extends Migration
{
    private const OLD = 'info@darhadhramaut.ye';

    private const NEW = 'info@novixa.dev';

    public function up(): void
    {
        $this->move(self::OLD, self::NEW);
    }

    public function down(): void
    {
        $this->move(self::NEW, self::OLD);
    }

    private function move(string $from, string $to): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        DB::table('settings')
            ->where('group', 'general')
            ->where('name', 'site_email')
            ->where('payload', json_encode($from))
            ->update(['payload' => json_encode($to), 'updated_at' => now()]);
    }
};
