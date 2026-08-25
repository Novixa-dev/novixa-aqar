<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * `buyers.email` was NOT NULL (and unique) since the original migration,
 * but PropertyRequestForm's own validation rule has always treated email
 * as optional (`'email' => 'nullable|email|max:255'`) — submitting the
 * public Property Request form (one of the two required public MVP
 * forms) with a blank email, exactly as the form's own validation
 * allows, throws a live SQLSTATE[23000] NOT NULL constraint violation.
 * Reproduced live via a real form submission, then fixed here.
 *
 * Uses a raw ALTER rather than Schema::table()->change() to avoid adding
 * a new doctrine/dbal dependency just for one column-nullability change
 * (same reasoning as the properties.country fix earlier this session).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE buyers MODIFY email VARCHAR(255) NULL');
    }

    public function down(): void
    {
        // Not reversed: a blanket "make NOT NULL again" would fail on any
        // row a null-email submission already created since this fix.
    }
};
