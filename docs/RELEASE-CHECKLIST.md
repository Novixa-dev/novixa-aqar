# Aqar Release Checklist

Two distinct gates. **LOCAL QA** proves the codebase itself is correct and complete — run it on every meaningful change, entirely on a dev machine, no production access needed. **PRODUCTION DEPLOYMENT QA** proves a specific deployment of that codebase is correctly configured — run it against the real target environment, after every deploy. Passing LOCAL QA is a prerequisite for attempting a deploy; it is not a substitute for PRODUCTION DEPLOYMENT QA, and vice versa.

See `docs/PRODUCTION-DEPLOYMENT.md` for what every environment variable in the second section actually means. See `docs/AQAR-FINAL-RELEASE-QA.md` for the most recent full run of this checklist and its results.

---

## LOCAL QA

Run entirely on a dev machine against a disposable local database. Nothing here touches or requires a production environment.

### 1. Fresh environment bootstrap
- [ ] `composer install`
- [ ] `npm install`
- [ ] `cp .env.example .env` (if `.env` doesn't already exist) and `php artisan key:generate`
- [ ] `php artisan migrate:fresh --seed` completes with **zero errors**, using the project's required PHP binary (`C:\Users\ak01redwan\.config\herd\bin\php84\php.exe` — the default `php` on PATH resolves to 8.4.0 and fatally errors before Laravel boots)
- [ ] Seed output's final tallies look sane (properties, users, leases, lease agreements, payments, maintenance requests, rental applications, news, roles, permissions, media — compare against the previous run's numbers if anything looks off)

### 2. Automated tests
- [ ] `php artisan test --no-ansi` (same required PHP binary) → **0 failures**. Record the exact pass/skip count.
- [ ] Any new failure is root-caused and fixed, never deleted or weakened, per `AQAR_RULES.md`.

### 3. Production build (still local)
- [ ] `npm run build` completes with no errors (a chunk-size warning on the main JS bundle is a known, tracked advisory — not a failure).

### 4. Live application smoke test
Start the app locally (`php artisan serve` or Octane) against the freshly-seeded database and actually click through it — a green test suite does not verify UI, translation, or theme correctness.

- [ ] **All 9 seeded personas** (`docs/TEST_USERS.md`, password `password` for all) log in successfully and land on a working dashboard with **zero console/page/5xx errors**: `admin`, `staff`, `agent`, `buyer`, `seller`, `landlord`, `tenant`, `contractor`.
- [ ] Log out works for at least one persona.
- [ ] Each persona's primary workflow loads without error (e.g. Staff → Properties list; Landlord → own Properties/Leases; Tenant → Maintenance Requests; Contractor → assigned Work Orders).
- [ ] **Public pages**, both locales (`?lang=ar` / `?lang=en` or the navbar switcher): home, properties listing, a property detail page, about, contact, services, calculators, news, submit-property, property-request, terms, privacy. Confirm correct `dir="rtl"`/`dir="ltr"`, correct localized title, no untranslated raw keys visible.
- [ ] **Language switching**: use the actual UI control (public navbar pill; the new Filament user-menu language item in each of the 9 panels) — not just the `?lang=` query string — and confirm the page reloads in the other language.
- [ ] **RTL/LTR**: spot-check the properties listing and a property detail page in Arabic — icons/arrows mirror correctly, no obviously LTR-only spacing.
- [ ] **Light/dark mode**: toggle dark mode on **login, register, forgot-password** specifically (previously broken, fixed this pass) plus the homepage and a property detail page. No solid-white cards on a dark background.
- [ ] **Property search/filter**: filter by purpose (for sale/for rent) on the public properties page and confirm it actually narrows the results (this exercises the `properties.status` canonicalization — a stale cached view or an un-migrated dev DB would show 0 results here).
- [ ] **Property detail page**: confirm the photo gallery/thumbnail strip appears for a property with multiple photos, and clicking a thumbnail swaps the hero image.
- [ ] **Forms & validation**: submit the contact form and the property-submission/property-request forms with both valid and deliberately invalid input; confirm validation messages appear and are localized.
- [ ] **Booking/request flows**: schedule a property viewing/live tour; confirm a real appointment record is created (spot-check via `tinker` or the Staff panel) and the confirmation UI is correct.
- [ ] **Calculators** (`/calculators`): run the mortgage and tax estimator calculators; confirm output is in YER (not `$`), and "Total Tax"/"Effective Tax Rate" render in the active locale (previously hardcoded English, fixed this pass).
- [ ] **Chatbot**: open it, send a message, confirm a response and no console spam.
- [ ] **Virtual tours / 3D**: on a property seeded with a virtual tour or 3D model, confirm the tour/model actually renders.
- [ ] **Menus, dropdowns, modals**: open the user-account dropdown, a booking modal, and the mobile hamburger menu; confirm each opens/closes correctly and traps no focus permanently.
- [ ] **Image loading**: open DevTools Network tab while browsing the public site — zero CSP `img-src` violations, zero broken/404 image requests (this directly verifies the `ASSET_URL`/relative-storage-URL fix).
- [ ] **Console/page errors**: zero JS console errors and zero PHP page errors across the above walkthrough.
- [ ] **404/500 handling**: visit a nonexistent property ID and a nonexistent route; confirm a proper localized 404, not a raw stack trace (with `APP_DEBUG=true` locally you *will* see a trace — that's expected locally, just confirm the route itself 404s cleanly rather than 500ing).
- [ ] **Authorization boundaries**: confirm a non-privileged persona is denied (403, not a crash) when directly navigating to another role's panel URL (e.g. buyer hitting `/admin`).
- [ ] **Key API endpoints**: spot-check `GET /api/v1/maintenance` as a tenant persona's token — confirm it returns only that tenant's own records, not the whole team's (this exercises the IDOR fix from this pass).
- [ ] **Responsive/mobile**: resize to ~375px width on the homepage, properties listing, and a property detail page — no horizontal overflow, nav collapses correctly, touch targets aren't cramped.

### 5. Manual code-level sanity (already covered by tooling, listed for completeness)
- [ ] `php -l` clean on every changed PHP file.
- [ ] `lang/en.json` and `lang/ar.json` both parse as valid JSON.
- [ ] No new secrets committed (`git diff` reviewed before commit).

---

## PRODUCTION DEPLOYMENT QA

Run against the **real target environment**, after every deploy — LOCAL QA passing does not imply these are true, since they depend entirely on environment configuration, not code.

### 1. Environment configuration
- [ ] Every item in `docs/PRODUCTION-DEPLOYMENT.md` is set correctly for this environment (`APP_ENV=production`, `APP_DEBUG=false`, real `APP_URL`, real `APP_KEY`, real DB credentials, mail provider if needed, Stripe keys if needed).
- [ ] `.env` is not committed to version control and not web-accessible.

### 2. Deploy steps executed
- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] `npm ci && npm run build`
- [ ] `php artisan migrate --force` (never `migrate:fresh` against real data)
- [ ] `php artisan storage:link`
- [ ] `php artisan optimize` (config/route/view cache)
- [ ] Demo/dev-only seeders (`UserSeeder`, `DemoWorkflowSeeder`, `DatabaseSeeder`) were **not** run against this environment.

### 3. Post-deploy smoke test (see `docs/PRODUCTION-DEPLOYMENT.md` §10 for the full list)
- [ ] Homepage loads over HTTPS with a valid certificate, correct brand name, property photos actually render.
- [ ] `View Source`: canonical/OG tags show the real production domain, not `localhost`.
- [ ] `/sitemap.xml` and `/robots.txt` serve correctly with real URLs.
- [ ] A real admin-equivalent login works; the panel loads with zero console errors.
- [ ] Contact form submission actually delivers an email (if mail is configured for this environment).
- [ ] Triggering an error confirms `APP_DEBUG` is truly off (generic error page, no stack trace, no file paths).
- [ ] If queue/Horizon is enabled: `php artisan horizon:status` shows an active supervisor.
- [ ] If Reverb/broadcasting is enabled: a real-time feature actually delivers a live update.
- [ ] Scheduler is running (cron or the Docker scheduler container mode) — confirm the daily sitemap regeneration fires (check `sitemap.xml`'s last-modified time after 24h, or `php artisan schedule:list` on the server).

### 4. Rollback readiness
- [ ] A database backup exists from immediately before this deploy's migrations ran.
- [ ] The previous release's build artifacts/container image are still available to roll back to if the smoke test fails.
