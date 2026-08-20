<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ZooplaSettings was the one settings table with no team_id, unlike
        // RightMoveSettings/OnTheMarketSettings - a single global settings row
        // meant a single global API key for all teams' properties. See
        // AQAR_PRE_RELEASE_AUDIT_REPORT.md P0-5 / docs/AQAR_DECISIONS.md.
        Schema::table('zoopla_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('zoopla_settings', 'team_id')) {
                $table->foreignId('team_id')->nullable()->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('zoopla_settings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('team_id');
        });
    }
};
