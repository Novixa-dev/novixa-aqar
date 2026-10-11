<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * The seeded demo personas — one per role, one panel each.
 *
 * Single source of truth for the demo logins: UserSeeder creates them, the
 * other seeders attach demo data to them, `php artisan demo:audit` logs in
 * as them, and tests look them up through here. Change the domain or an
 * address here and every one of those follows; nothing else should
 * hard-code a demo email. The human-facing list is docs/TEST_USERS.md.
 *
 * These accounts share a public default password and exist for demos only:
 * UserSeeder refuses to run in production.
 */
final class DemoAccounts
{
    public const DOMAIN = 'novixa.dev';

    public const PASSWORD = 'password';

    /**
     * Keyed by Spatie role name, in the order the demo guide lists them.
     *
     * @var array<string, array{local: string, name: string, panel: string, label_ar: string, label_en: string}>
     */
    private const ACCOUNTS = [
        'super_admin' => ['local' => 'admin', 'name' => 'عبدالرحمن باعباد', 'panel' => '/admin', 'label_ar' => 'مدير النظام', 'label_en' => 'System administrator'],
        'staff' => ['local' => 'staff', 'name' => 'سارة الحضرمي', 'panel' => '/staff', 'label_ar' => 'موظف الإدارة', 'label_en' => 'Office staff'],
        'agent' => ['local' => 'agent', 'name' => 'خالد بن طالب', 'panel' => '/agent', 'label_ar' => 'وكيل عقاري', 'label_en' => 'Real-estate agent'],
        'landlord' => ['local' => 'landlord', 'name' => 'سالم باوزير', 'panel' => '/landlord', 'label_ar' => 'مالك عقار', 'label_en' => 'Landlord'],
        'tenant' => ['local' => 'tenant', 'name' => 'محمد العمودي', 'panel' => '/tenant', 'label_ar' => 'مستأجر', 'label_en' => 'Tenant'],
        'buyer' => ['local' => 'buyer', 'name' => 'فاطمة الكثيري', 'panel' => '/buyer', 'label_ar' => 'مشتري', 'label_en' => 'Buyer'],
        'seller' => ['local' => 'seller', 'name' => 'عمر بامطرف', 'panel' => '/seller', 'label_ar' => 'بائع', 'label_en' => 'Seller'],
        'contractor' => ['local' => 'contractor', 'name' => 'ياسر بن بريك', 'panel' => '/contractor', 'label_ar' => 'مقاول صيانة', 'label_en' => 'Maintenance contractor'],
    ];

    /** The demo login email for a role, e.g. email('landlord') → landlord@novixa.dev. */
    public static function email(string $role): string
    {
        return self::account($role)['email'];
    }

    /**
     * Every persona, keyed by role, with its email resolved.
     *
     * @return array<string, array{email: string, name: string, panel: string, label_ar: string, label_en: string}>
     */
    public static function all(): array
    {
        $accounts = [];
        foreach (array_keys(self::ACCOUNTS) as $role) {
            $accounts[$role] = self::account($role);
        }

        return $accounts;
    }

    /** @return array{email: string, name: string, panel: string, label_ar: string, label_en: string} */
    public static function account(string $role): array
    {
        $account = self::ACCOUNTS[$role] ?? throw new InvalidArgumentException("No demo account for role [{$role}].");
        $account['email'] = $account['local'].'@'.self::DOMAIN;
        unset($account['local']);

        return $account;
    }
}
