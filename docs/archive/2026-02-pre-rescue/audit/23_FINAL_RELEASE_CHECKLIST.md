# 23 — Final Release & Repository Creation Checklist Report

**Product**: Aqar (عقاري) — Novixa Private Edition  

---

## 1. Master Release Execution Checklist

Use this step-by-step checklist to finalize repository creation and launch the Novixa edition.

### Phase A: Repository Separation & Branding
- [ ] 1. Run `git remote remove origin` in local terminal.
- [ ] 2. Create new private repository `https://github.com/novixa/aqar-real-estate.git` on GitHub.
- [ ] 3. Run `git remote add origin https://github.com/novixa/aqar-real-estate.git`.
- [ ] 4. Update `"name": "novixa/aqar"` in `composer.json`.
- [ ] 5. Run `git push -u origin main`.

### Phase B: Production Server Configuration
- [ ] 6. Provision production server running PHP 8.4 and Nginx.
- [ ] 7. Clone Novixa private repository to `/var/www/aqar`.
- [ ] 8. Create production `.env` file (`APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://aqar.novixa.com`).
- [ ] 9. Run `composer install --no-dev --optimize-autoloader`.
- [ ] 10. Run `npm ci && npm run build`.
- [ ] 11. Run `php artisan key:generate` and `php artisan storage:link`.
- [ ] 12. Run `php artisan migrate --force`.
- [ ] 13. Run `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`.

### Phase C: Domain, SSL & Launch Sign-off
- [ ] 14. Configure SSL certificate (Let's Encrypt / Cloudflare).
- [ ] 15. Point DNS A record to production server IP.
- [ ] 16. Perform smoke test on live homepage, property listings, and owner forms.
- [ ] 17. Confirm 0 runtime errors in `storage/logs/laravel.log`.
