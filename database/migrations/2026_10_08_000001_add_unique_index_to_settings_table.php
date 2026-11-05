<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // spatie/laravel-settings upserts each property by (group, name);
        // without this index at the DB level, a concurrent save can create
        // duplicate rows for the same setting instead of updating the one
        // that already exists. The original 2023_05_15 migration that
        // created this table never had it.
        //
        // Any duplicates that already exist would make the index creation
        // fail (and with it every deploy), so collapse them first, keeping
        // the most recently written row per (group, name).
        $keep = DB::table('settings')
            ->selectRaw('MAX(id) as id')
            ->groupBy('group', 'name');

        DB::table('settings')
            ->whereNotIn('id', DB::query()->fromSub($keep, 'keep')->select('id'))
            ->delete();

        // On some deployments the table-creation migration already added this
        // same unique index inline (pre-dates the two histories diverging),
        // so a fresh migrate-from-scratch would otherwise fail here with
        // "index already exists" the moment both migrations run together.
        Schema::whenTableDoesntHaveIndex('settings', ['group', 'name'], function (Blueprint $table) {
            $table->unique(['group', 'name']);
        }, 'unique');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::whenTableHasIndex('settings', ['group', 'name'], function (Blueprint $table) {
            $table->dropUnique(['group', 'name']);
        }, 'unique');
    }
};
