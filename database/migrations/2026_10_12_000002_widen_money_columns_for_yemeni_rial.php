<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Money columns were decimal(10,2): at most 99,999,999.99. This product's
 * primary currency is the Yemeni rial, where villas, buildings and land -
 * and so sale transactions, commissions, insurance values and price
 * alerts - routinely exceed 100 million; inserting a 165,000,000 YER villa
 * failed with "Out of range value for column 'price'". Widened to
 * decimal(15,2) (up to ~10 trillion), each column keeping its nullability
 * and default. SQLite never enforced the precision, which is why the test
 * suite did not catch it; MySQL does.
 */
return new class extends Migration
{
    /** [table, column, nullable, default] - every decimal(10,2) money column. */
    private const COLUMNS = [
        ['alerts', 'max_price', true, null],
        ['alerts', 'min_price', true, null],
        ['compliance_items', 'actual_cost', true, null],
        ['compliance_items', 'cost_estimate', true, null],
        ['invoices', 'amount', false, 0.00],
        ['lease_agreements', 'monthly_rent', true, null],
        ['lease_agreements', 'rent_amount', true, null],
        ['lease_agreements', 'security_deposit', true, null],
        ['leases', 'rent_amount', false, null],
        ['leases', 'security_deposit', true, null],
        ['market_appraisals', 'rental_value_monthly', true, null],
        ['market_appraisals', 'rental_value_weekly', true, null],
        ['neighborhoods', 'median_income', true, null],
        ['payments', 'amount', false, null],
        ['properties', 'insurance_value', true, null],
        ['properties', 'price', false, null],
        ['property_valuations', 'rental_value', true, null],
        ['rental_applications', 'annual_income', true, null],
        ['rental_applications', 'monthly_income', true, null],
        ['rooms', 'rent', false, null],
        ['smart_contract_transactions', 'amount', true, null],
        ['smart_contracts', 'rent_amount', false, null],
        ['smart_contracts', 'security_deposit', false, 0.00],
        ['smart_contracts', 'total_rent_paid', false, 0.00],
        ['transactions', 'amount', false, null],
        ['transactions', 'commission_amount', true, null],
        ['utility_payments', 'amount', false, null],
        ['vendor_quotes', 'additional_costs', true, null],
        ['vendor_quotes', 'labor_cost', true, null],
        ['vendor_quotes', 'materials_cost', true, null],
        ['vendor_quotes', 'quote_amount', false, null],
        ['work_orders', 'actual_cost', true, null],
        ['work_orders', 'estimated_cost', true, null],
        ['work_orders', 'labor_cost', true, null],
        ['work_orders', 'materials_cost', true, null],
    ];

    public function up(): void
    {
        $this->resize(15);
    }

    public function down(): void
    {
        // Narrowing back fails if any value no longer fits - by design.
        $this->resize(10);
    }

    private function resize(int $precision): void
    {
        foreach (self::COLUMNS as [$table, $column, $nullable, $default]) {
            if (! Schema::hasColumn($table, $column)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($column, $nullable, $default, $precision) {
                $definition = $blueprint->decimal($column, $precision, 2)->nullable($nullable);
                if ($default !== null) {
                    $definition->default($default);
                }
                $definition->change();
            });
        }
    }
};
