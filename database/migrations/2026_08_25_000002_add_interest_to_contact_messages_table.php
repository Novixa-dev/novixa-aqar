<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ContactMessage::$fillable and ContactController's validation rules both
 * already reference `interest` (buying/selling/renting/other), but no
 * migration ever added the column — every public contact form submission
 * fatally errors (SQLSTATE 42S22, unknown column) before this fix.
 * Reproduced live via a real form submission on the running dev server,
 * then fixed. The contact form is the site's primary lead-generation
 * channel, so this was a full outage of that flow, not a cosmetic gap.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('interest')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn('interest');
        });
    }
};
