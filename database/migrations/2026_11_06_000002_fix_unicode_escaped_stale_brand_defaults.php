<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 2026_10_12_000001_rebrand_default_site_settings.php tried to fix the same
 * stale "Dar Hadhramaut" defaults this migration fixes, but its footer_copyright
 * replacement used a `LIKE '%دار حضرموت%'` match against the raw payload
 * column. spatie/laravel-settings' migrator stores values via plain
 * json_encode() (no JSON_UNESCAPED_UNICODE), so every non-ASCII character is
 * stored as a \uXXXX escape sequence -- the literal UTF-8 bytes "دار حضرموت"
 * never appear in the column, so that LIKE clause silently never matched.
 * This went unnoticed because no view rendered footer_copyright until this
 * same round added it to footer.blade.php, which is what surfaced it as a
 * live SeoAndBrandingTest failure (the page unexpectedly containing "دار
 * حضرموت"). Also fixes general.site_email, which the 2026-10-12 migration
 * didn't touch at all and still defaults to the pre-rebrand
 * info@darhadhramaut.ye.
 *
 * Decodes each payload in PHP before comparing instead of pattern-matching
 * the raw column, so this works regardless of how the JSON happens to be
 * escaped. Data-only, one-way, and only touches rows that still hold one of
 * the known pre-rebrand defaults -- any value an operator has actually
 * customised through the Site Settings page is left untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $this->replaceIfStillDefault(
            'footer_copyright',
            fn (string $decoded) => str_contains($decoded, 'دار حضرموت') || str_contains($decoded, 'Dar Hadhramaut'),
            '© '.config('branding.name.en').'. All rights reserved. Developed by '.config('branding.company.name').'.'
        );

        $this->replaceIfStillDefault(
            'site_email',
            fn (string $decoded) => str_contains($decoded, 'darhadhramaut.ye'),
            config('branding.email')
        );

        $this->replaceIfStillDefault(
            'site_name',
            fn (string $decoded) => str_contains($decoded, 'دار حضرموت') || str_contains($decoded, 'Dar Hadhramaut'),
            config('branding.name.ar').' | '.config('branding.name.en')
        );
    }

    public function down(): void
    {
        // Data-only and one-way: the previous brand is not restored.
    }

    private function replaceIfStillDefault(string $name, callable $isStaleDefault, string $newValue): void
    {
        DB::table('settings')
            ->where('group', 'general')
            ->where('name', $name)
            ->get(['id', 'payload'])
            ->each(function ($row) use ($isStaleDefault, $newValue) {
                $decoded = json_decode($row->payload, true);

                if (! is_string($decoded) || ! $isStaleDefault($decoded)) {
                    return;
                }

                DB::table('settings')
                    ->where('id', $row->id)
                    ->update(['payload' => json_encode($newValue, JSON_UNESCAPED_UNICODE), 'updated_at' => now()]);
            });
    }
};
