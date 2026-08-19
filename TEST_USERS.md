# Test Users

Seeded by [UserSeeder.php](database/seeders/UserSeeder.php) — run `php artisan db:seed --class=UserSeeder` (or the full `DatabaseSeeder`) in a local/dev environment. This seeder refuses to run when `APP_ENV=production`.

Default password for **all** accounts: `password`

| Role | Email | Password | Notes |
|---|---|---|---|
| super_admin | admin@darhadhramaut.ye | password | Full access to all permissions |
| staff | staff@darhadhramaut.ye | password | view/create/update on most resources, no deletes |
| agent | agent@darhadhramaut.ye | password | Property, appointment, booking, lead management |
| buyer | buyer@darhadhramaut.ye | password | Browse properties, bookings, favorites, reviews |
| seller | seller@darhadhramaut.ye | password | Create/manage own property listings |
| landlord | landlord@darhadhramaut.ye | password | Property, lease, tenant, maintenance & utility management |
| tenant | tenant@darhadhramaut.ye | password | Lease view, maintenance requests, payments |
| contractor | contractor@darhadhramaut.ye | password | Maintenance requests, contractor profile |

## Notes

- Role → permission mapping is defined in [RolesSeeder.php](database/seeders/RolesSeeder.php).
- All seeded users are attached to the first `Team` record (`Team::first()`).
- This file is for local testing only — never commit real credentials here.
