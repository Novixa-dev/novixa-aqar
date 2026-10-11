# Test Users

Demo accounts created by `database/seeders/UserSeeder.php` from the single
source of truth `app/Support/DemoAccounts.php`. They are seeded only in
non-production environments (the seeder throws in `production`).

**Live demos:** <https://aqar-demo.novixa.dev> (`main`) ·
<https://aqar-demo2.novixa.dev> (`ui-improves`)
**Password for every account:** `password`

| Role (Spatie) | Arabic label | Email | Name | Panel |
|---|---|---|---|---|
| `super_admin` | مدير النظام | `admin@novixa.dev` | عبدالرحمن باعباد | `/admin` |
| `staff` | موظف الإدارة | `staff@novixa.dev` | سارة الحضرمي | `/staff` |
| `agent` | وكيل عقاري | `agent@novixa.dev` | خالد بن طالب | `/agent` |
| `landlord` | مالك عقار | `landlord@novixa.dev` | سالم باوزير | `/landlord` |
| `tenant` | مستأجر | `tenant@novixa.dev` | محمد العمودي | `/tenant` |
| `buyer` | مشتري | `buyer@novixa.dev` | فاطمة الكثيري | `/buyer` |
| `seller` | بائع | `seller@novixa.dev` | عمر بامطرف | `/seller` |
| `contractor` | مقاول صيانة | `contractor@novixa.dev` | ياسر بن بريك | `/contractor` |

All accounts end in **`@novixa.dev`**. The earlier `@darhadhramaut.ye` and
`@aqar-demo.dexal.net` addresses no longer exist in newly seeded databases.

What each role can do: [`docs/product/02-USERS-AND-ROLES.md`](product/02-USERS-AND-ROLES.md).
Guided demo script (Arabic): [`docs/product/05-DEMO-GUIDE-AR.md`](product/05-DEMO-GUIDE-AR.md).

## Seeding

```bash
php artisan db:seed                       # everything, including these users
php artisan db:seed --class=RolesSeeder   # roles must exist first …
php artisan db:seed --class=UserSeeder    # … then the users
```

- Every account joins the first `Team` and has it set as its current team.
- `UserSeeder` uses `firstOrCreate`, so re-running it never duplicates users.
- Changing an address or the domain: edit `app/Support/DemoAccounts.php` only.
  Seeders, `php artisan demo:audit` and the tests all read from it.
- On the live demos, bump `DEMO_SEED_VERSION` in `deploy/<site>/app.env` to
  rebuild the demo database from the seeders on the next deploy (zero
  downtime; see `deploy/aqar-demo/README.md`).
- `php artisan demo:audit` logs in as all eight accounts over HTTP and checks
  that each reaches its panel.
