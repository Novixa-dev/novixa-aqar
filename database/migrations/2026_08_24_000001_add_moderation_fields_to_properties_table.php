<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PropertyModerationResource (Staff panel) previously bound moderated_at/
 * moderation_notes form fields that had no backing column, and reused the
 * listing `status` column (for_sale/for_rent/sold/rented) for a third,
 * incompatible meaning (pending/approved/rejected moderation state) —
 * saving a moderation decision silently corrupted the listing status.
 * This adds a dedicated moderation_status column (independent from the
 * listing status) plus the two missing fields, so moderation state and
 * listing state can no longer collide.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('moderation_status')->nullable()->after('status');
            $table->timestamp('moderated_at')->nullable()->after('moderation_status');
            $table->text('moderation_notes')->nullable()->after('moderated_at');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['moderation_status', 'moderated_at', 'moderation_notes']);
        });
    }
};
