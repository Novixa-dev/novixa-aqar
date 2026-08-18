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
        Schema::table('lease_agreements', function (Blueprint $table) {
            $table->boolean('landlord_signed')->default(false)->after('is_signed');
            $table->boolean('tenant_signed')->default(false)->after('landlord_signed');
        });

        Schema::table('rental_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('rental_applications', 'rental_history_status')) {
                $table->string('rental_history_status')->nullable()->after('credit_report_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lease_agreements', function (Blueprint $table) {
            $table->dropColumn([
                'landlord_signed',
                'tenant_signed',
            ]);
        });

        Schema::table('rental_applications', function (Blueprint $table) {
            if (Schema::hasColumn('rental_applications', 'rental_history_status')) {
                $table->dropColumn('rental_history_status');
            }
        });
    }
};
