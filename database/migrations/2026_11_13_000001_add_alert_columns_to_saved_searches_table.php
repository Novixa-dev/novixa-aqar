<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Saved searches had a table and model but no UI or alerting (P33-2).
    // These columns back the "save this search" + new-listing alert flow.
    public function up(): void
    {
        Schema::table('saved_searches', function (Blueprint $table) {
            $table->string('name', 160)->nullable()->after('user_id');
            $table->boolean('alerts_enabled')->default(true)->after('criteria');
            $table->timestamp('last_notified_at')->nullable()->after('alerts_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('saved_searches', function (Blueprint $table) {
            $table->dropColumn(['name', 'alerts_enabled', 'last_notified_at']);
        });
    }
};
