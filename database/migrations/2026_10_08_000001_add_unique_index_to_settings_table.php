<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('settings', function (Blueprint $table) {
            $table->unique(['group', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['group', 'name']);
        });
    }
};
