# Test Users

Demo/test accounts created by `database/seeders/UserSeeder.php`. These are seeded
only in non-production environments (the seeder throws if `app()->environment('production')`).

Seed them with:

```bash
php artisan db:seed --class=RolesSeeder
php artisan db:seed --class=UserSeeder
```

Default password for **every** account below: `password`

| Role | Email | Name | Notes |
|---|---|---|---|
| super_admin | admin@darhadhramaut.ye | Admin User | Full access, assigned all permissions |
| staff | staff@darhadhramaut.ye | Staff User | view/create/update on most resources, no deletes |
| agent | agent@darhadhramaut.ye | Agent User | Properties, appointments, bookings, leads |
| buyer | buyer@darhadhramaut.ye | Buyer User | Browse properties, bookings, favorites, reviews |
| seller | seller@darhadhramaut.ye | Seller User | Manage own property listings and documents |
| landlord | landlord@darhadhramaut.ye | Landlord User | Properties, leases, tenants, rental applications |
| tenant | tenant@darhadhramaut.ye | Tenant User | Lease, maintenance requests, payments |
| contractor | contractor@darhadhramaut.ye | Contractor User | Maintenance requests, documents |

## Notes

- All users are attached to the first `Team` record (`Team::first()`), if one exists.
- Roles/permissions come from `database/seeders/RolesSeeder.php` — run it before
  `UserSeeder` so roles exist to assign.
- `UserSeeder` uses `firstOrCreate`, so re-running it is safe and won't duplicate users.
- Do not run this seeder in production; it will throw a `RuntimeException`.
