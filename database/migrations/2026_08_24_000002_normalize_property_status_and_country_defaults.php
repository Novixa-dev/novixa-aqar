<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * `properties.status` was written with Title Case ('For Sale'/'For Rent'/
 * 'Sold'/'Rented') by the original seeder/factory, while the live Staff
 * edit form and most readers use snake_case. Property::setStatusAttribute()
 * now normalizes every future write of those exact four legacy values;
 * this migration is a one-time, non-destructive cleanup so any row written
 * before that fix (a prior seed run, or a restored backup) matches. Pure
 * value normalization — no schema/type change, no data loss, idempotent.
 *
 * Deliberately narrow: `status` also carries a second, legitimate
 * vocabulary (draft/available/under_offer/sstc/exchanged/to_let/let/
 * let_agreed/withdrawn/archived/pending/approved/rejected/active) used by
 * the V1 API/AutomationController — those are real, distinct states, not
 * synonyms of for_sale/for_rent/sold/rented, and must not be remapped.
 *
 * (properties.country's wrong 'UK' default is fixed at the model level —
 * Property::$attributes — rather than here, to avoid adding a new
 * doctrine/dbal dependency just for one column-default ALTER.)
 */
return new class extends Migration
{
    private const STATUS_MAP = [
        'For Sale' => 'for_sale',
        'For Rent' => 'for_rent',
        'Sold' => 'sold',
        'Rented' => 'rented',
    ];

    public function up(): void
    {
        foreach (self::STATUS_MAP as $legacy => $canonical) {
            DB::table('properties')->where('status', $legacy)->update(['status' => $canonical]);
        }
    }

    public function down(): void
    {
        // Intentionally one-directional — the prior multi-convention state
        // was the bug being fixed, not a design worth restoring.
    }
};
