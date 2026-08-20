# 16 — Manual Actions Required Checklist Report

**Product**: Aqar (عقاري) — Novixa Private Edition  

---

## 1. Action Items Requiring Human / Administrative Execution

This report outlines all non-code administrative and deployment tasks required prior to launch.

| Action Item | Target File / Location | Priority | Reason & Instructions |
|---|---|---|---|
| **1. Disconnect Upstream Git Remote** | Terminal / Git | **HIGH** | Run `git remote remove origin` to prevent accidental pushes to original repo. |
| **2. Create Private Novixa GitHub Repo** | GitHub Web Console | **HIGH** | Create private repository `https://github.com/novixa/aqar-real-estate.git`. |
| **3. Update Composer Package Name** | [`composer.json#L2`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/composer.json#L2) | **MEDIUM** | Change `"liberu-real-estate/real-estate-laravel"` to `"novixa/aqar"`. |
| **4. Provision Production `.env` File** | Server `/var/www/aqar/.env` | **HIGH** | Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://aqar.novixa.com`, DB credentials, and SMTP details. |
| **5. Configure SSL & Production Domain** | Nginx / Cloudflare | **HIGH** | Provision SSL certificate (Let's Encrypt / Cloudflare) and point DNS A record. |
| **6. Run Production Migrations & Storage Link** | Production Terminal | **HIGH** | Run `php artisan migrate --force` and `php artisan storage:link`. |
| **7. Execute Caching Commands** | Production Terminal | **HIGH** | Run `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`. |
| **8. Create Super Admin User** | Production Terminal | **MEDIUM** | Run `php artisan make:filament-user` or `php artisan db:seed --class=UserSeeder`. |
