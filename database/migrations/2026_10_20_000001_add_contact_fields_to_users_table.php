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
        // Staff's Landlord/Contractor management forms (and the Contractor
        // panel's own self-service profile form) have always collected
        // phone/address/company_name/tax_id/preferred_contact_method on the
        // User model, but none of these columns ever existed -- every save
        // silently discarded them (none are in User::$fillable either).
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('company_name')->nullable()->after('address');
            $table->string('tax_id', 50)->nullable()->after('company_name');
            $table->string('preferred_contact_method')->nullable()->after('tax_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'address', 'company_name', 'tax_id', 'preferred_contact_method']);
        });
    }
};
