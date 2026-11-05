<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Property::scopePubliclyVisible() filters on moderation_status on every
 * public page (homepage, /properties, property detail) -- the only index
 * touching this table is a composite (team_id, branch_id, status), which
 * is useless here (leftmost-prefix rule, and these queries aren't
 * team-scoped at all). is_featured (homepage) and latitude/longitude (the
 * map query) are filtered on every homepage hit and are likewise
 * unindexed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->index('moderation_status');
            $table->index('is_featured');
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['moderation_status']);
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['latitude', 'longitude']);
        });
    }
};
