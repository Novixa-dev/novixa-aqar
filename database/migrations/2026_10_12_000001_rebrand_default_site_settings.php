<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The product now ships under the Novixa brand (config/branding.php). Settings
 * rows that still hold the old "Dar Hadhramaut" defaults are moved to the
 * configured brand; anything an operator has already customised is left as is.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $name = config('branding.name.ar').' | '.config('branding.name.en');
        $copyright = '© '.config('branding.name.en').'. All rights reserved. Developed by '.config('branding.company.name').'.';

        $this->replaceIfDefault('site_name', [
            'Dar Hadhramaut Real Estate | دار حضرموت العقارية',
        ], $name);

        // Every old copyright default mentioned the old name; match on that.
        DB::table('settings')
            ->where('group', 'general')
            ->where('name', 'footer_copyright')
            ->where(fn ($q) => $q->where('payload', 'like', '%دار حضرموت%')->orWhere('payload', 'like', '%Dar Hadhramaut%'))
            ->update(['payload' => json_encode($copyright, JSON_UNESCAPED_UNICODE), 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Data-only and one-way: the previous brand is not restored.
    }

    private function replaceIfDefault(string $setting, array $defaults, string $value): void
    {
        foreach ($defaults as $default) {
            foreach ([json_encode($default), json_encode($default, JSON_UNESCAPED_UNICODE)] as $payload) {
                DB::table('settings')
                    ->where('group', 'general')
                    ->where('name', $setting)
                    ->where('payload', $payload)
                    ->update(['payload' => json_encode($value, JSON_UNESCAPED_UNICODE), 'updated_at' => now()]);
            }
        }
    }
};
