<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `buyers.email` was NOT NULL (and unique) since the original migration,
 * but PropertyRequestForm's own validation rule has always treated email
 * as optional (`'email' => 'nullable|email|max:255'`) — submitting the
 * public Property Request form (one of the two required public MVP
 * forms) with a blank email, exactly as the form's own validation
 * allows, throws a live SQLSTATE[23000] NOT NULL constraint violation.
 * Reproduced live via a real form submission, then fixed here.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buyers', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Not reversed: a blanket "make NOT NULL again" would fail on any
        // row a null-email submission already created since this fix.
    }
};
