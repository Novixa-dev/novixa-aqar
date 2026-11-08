<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * News had no image field at all. The public /news pages compensated with
 * a hardcoded Unsplash-cycling fallback in one Blade view only, which
 * never matched between the two deployed branches (one showed stock
 * photos, the other showed nothing) -- this is the real fix: a real,
 * admin-uploadable image per article.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('excerpt');
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });
    }
};
