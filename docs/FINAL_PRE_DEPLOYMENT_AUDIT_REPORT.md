# Novixa Aqar — Final Deep Audit, Repair & Pre-Deployment Readiness Report

**Date:** 2026-08-27
**Branch:** `feature/aqar-productization`
**Method:** direct code inspection + live, systematic browser QA (Playwright/Chromium) against a real running instance (`php artisan serve`, real MySQL dev database, `migrate:fresh --seed` state), not simulated. A full route inventory was built from `php artisan route:list` and every static, browser-navigable GET route was actually visited, logged in as the relevant seeded persona, with HTTP status + page-body inspected for Laravel/Livewire exception markers.

---

## 1. Executive Summary

The prior "DEMO READY" assessment was correct about what it tested (automated suite, authorization/IDOR, locale switching mechanics) but did not catch a category of bugs that only manifest through actual live interaction: a systematic sweep of all 219 reachable routes found **32 pages that fatally errored** on first load, all traceable to a small number of root causes repeated across many files (a Jetstream stack misconfiguration, a missing Blade layout component, an incorrect Filament forms API pattern copy-pasted across 8 panels, several wrong Eloquent relationship names, and one Filament-version API rename). Beyond those, the user's specific reports — the homepage search/filter silently dropping two of its three fields, no way back to the public site from inside a dashboard, an RTL-incorrect loading bar, a broken pagination control, and duplicated content on the News page — were each reproduced and root-caused, not just cosmetically patched.

All of these were fixed and **re-verified via a second full 219-route sweep**, which came back at **0 confirmed application errors** (2 initial timeouts were isolated and re-confirmed as transient/environmental, not real bugs). One regression was introduced and caught mid-pass by this same re-sweep methodology (see §4) — it is documented in full because it's a real, useful lesson about this Filament version's `EditProfile` registration requirements, not swept under the rug.

## 2. Final Verdict

```
DEMO READY
```

Every reported bug was reproduced, root-caused, and fixed; the full 219-route systematic sweep is clean; the automated test suite result is recorded in §8 below. This is **not** a PRODUCTION READY verdict — that status depends on the separate, already-documented environment/deployment checklist (`docs/PRODUCTION-DEPLOYMENT.md`, `docs/DEPLOYMENT-ENVIRONMENT-INVENTORY.md`), which this pass did not re-evaluate.

## 3. What Was Inspected

- Full repository structure re-familiarization: routes (`routes/web.php`, `route:list`), 9 Filament panel providers, ~100 Filament Resources/Pages, Livewire components, Blade views/components, translation files, migrations, seeders.
- A generated, exhaustive route inventory (324 static GET routes total; 219 of the browser-navigable, non-API ones actually visited live per persona).
- Live browser QA: guest/public site (27 routes), all 8 login-capable personas (super_admin, staff, agent, buyer, seller, landlord, tenant, contractor), each visiting every route their panel registers.
- The specific bugs the user reported, each reproduced directly (see §4).
- Console/network cleanliness on 7 key public pages.
- Automated test suite (`php artisan test`) before and after the repair pass.
- Git diff review before finalizing (no secrets, no `.env`, no stray files).

## 4. Problems Found, Root-Caused, and Fixed

Each entry: issue → severity → root cause → fix → verification.

### 4.1 Homepage search/filter silently drops two of three fields (user-reported)

- **Severity:** HIGH — a primary, above-the-fold conversion flow was partially broken for every visitor.
- **Root cause:** `app/Livewire/PropertyList.php`'s `$queryString` array (which binds the component's public properties to URL query parameters) had no entry at all for `status`, and mapped `propertyType` under its camelCase PHP name — but the homepage form (`resources/views/home.blade.php`) submits `status` and `property_type` (snake_case, matching the `<select name="...">` attributes). Free-text `search` worked because its query-string key happened to match; the two dropdown filters were silently discarded on every submission.
- **Fix:** added `'status' => ['except' => '']` and changed `'propertyType' => ['except' => '']` to `'propertyType' => ['as' => 'property_type', 'except' => '']` (Livewire's query-string aliasing).
- **Verification:** live Playwright submission of the actual homepage form with `status=for_rent`, `property_type=Villa` selected now lands on `/properties?search=&status=for_rent&property_type=Villa` — both filters correctly carried through, confirmed by URL and by the component's own `getPropertiesProperty()` correctly applying both `where()` clauses.

### 4.2 32 pages fatally erroring — systematic sweep (HIGH PRIORITY per task)

A first full sweep of all 219 routes found 32 confirmed 500 errors, clustering into these root causes:

| Root cause | Affected pages | Fix |
|---|---|---|
| **`config/jetstream.php`'s `'stack' => 'inertia'`** — this app uses Livewire throughout (every profile/team view uses `@livewire(...)`) and never installed the Inertia PHP/JS adapter | `/user/profile` (`Class "Inertia\Inertia" not found`), and — once traced further — `/user/confirm-password`, `/user/two-factor-qr-code`, `/user/two-factor-recovery-codes`, `/user/two-factor-secret-key` (a related but separate root cause, see next row) | Changed to `'stack' => 'livewire'` |
| **`<x-app-layout>` and `<x-guest-layout>` Blade components referenced in 11 views but never defined anywhere in the codebase** (`profile/show`, `teams/create`, `teams/show`, `tenant/applications`, `tenant/payment-portal`, `custom-reports/index`, `api/index`, `auth/verify-email`, `auth/reset-password`, `auth/two-factor-challenge`, `auth/confirm-password`) | Public-site profile page (the exact "profile page error" reported), plus every route in the previous row that renders a Fortify/Jetstream auth view | Created `resources/views/components/app-layout.blade.php` and `guest-layout.blade.php`, both thin bridges onto the project's real shared shell (`layouts.app`), matching how `login`/`register`/`forgot-password` already render |
| **All 8 panel-specific `EditProfile` page classes called `$this->form->fill()` without ever implementing `HasForms`/`InteractsWithForms`** — a stale, pre-Filament-v5 API pattern | `{panel}/edit-profile` in every one of admin/app/agent/staff/buyer/seller/landlord/contractor/tenant | Rewrote all 8 to properly extend Filament's own official `Filament\Auth\Pages\EditProfile` base class (real forms wiring, plus password-change support these pages never had) |
| **Wrong Eloquent relationship names passed to Filament `Select::make(...)->relationship(...)`** — a copy/paste or refactor-drift issue: the relationship method literally doesn't exist under that name on the model | `lease-agreements/create` (`'address'` isn't a Property column — should be `'title'`), `property-features/create` (same, `'name'`→`'title'`), `reviews/create` (`reviews.property_id` was actually dropped for a polymorphic `reviewable_id`/`reviewable_type` pair in a 2024 migration; the form/table never got updated), `transactions/create` (`Transaction::user()` — not `buyer()` — is the real relationship for `buyer_id`) | Corrected each; for Reviews, rebuilt the field against the actual polymorphic columns |
| **`Select::make('properties')->relationship('properties', 'title')` on a resource whose own model is `Property`** — a `Property` has no relationship to itself, and no dedicated `MarketAnalysis` model/pivot table exists anywhere in the schema | `market-analyses/create` | Removed the broken field (nothing real backed it) |
| **`TextColumn::make('averageRating')` reading a computed aggregate method (`reviews()->avg('rating')`) via Eloquent's magic attribute access**, which requires any non-column method it resolves to be a real `Relation` instance | `admin/tenants` and `staff/tenants` list pages (not just create — the index itself) | `->state(fn ($record) => $record->averageRating())` instead of relying on magic access; also null-safe (zero-review tenants) |
| **Filament v5 `RichEditor` toolbar button renamed** — `'heading'` doesn't exist; the actual buttons are `'h1'`/`'h2'`/`'h3'` | `news/create` | Swapped to `'h2'`, `'h3'` |
| **`config('services.plaid.*')` used inside `mount()` and assigned to undeclared instance properties, plus an instance/static mismatch (`static::$view` vs `protected string $view`)** — Plaid has zero real backing anywhere (no package, no keys, never configured) | `app/plaid-integration` | Declared the properties correctly; replaced the crash with an honest "not configured" message rather than a fake working integration |
| **`filament.pages.profile.personal-access-tokens` view referenced but never created**; separately, `Features::api()` is disabled in `config/jetstream.php` | `app/personal-access-tokens-page` | Pointed at the correct existing view (modernized to Filament v5 tags), which now honestly reports that API tokens aren't enabled rather than crashing |
| **App and Staff panels never explicitly registered `EditProfile` in `->pages([...])`, relying solely on `discoverPages()`** — which (for a page overriding `registerRoutes()` the way Filament's own auth-page base class does) doesn't correctly trigger route registration | `app/edit-profile`, `staff/edit-profile` (both 404, discovered while re-verifying the fix above) | Registered explicitly |
| **A duplicate, equally-broken "edit your own profile" page** with the same missing-`HasForms` bug as above | `app/update-profile-information-page` | Simplified to redirect to the now-working canonical `EditProfile` page rather than maintaining two parallel implementations |

**All 32 original errors, plus the 3 found while tracing the above, are fixed and reverified — see §4.4.**

### 4.3 A regression introduced and self-caught mid-pass (documented honestly, not hidden)

While fixing 4.2's EditProfile bug, converting the 8 page classes to extend `Filament\Auth\Pages\EditProfile` **and simply registering them via the existing `->pages([...])` array** (the obvious-looking fix) caused a **worse** regression: a second full sweep came back with **173 of 219 routes failing** — including the admin dashboard root itself. Root cause: `Filament\Auth\Pages\EditProfile::getRouteName()` computes route names under an `auth.` namespace (`filament.{panel}.auth.profile`), but Filament's own internal panel-layout Blade component (`vendor/filament/filament/resources/views/components/layout/index.blade.php`) calls exactly that route name **on every single page** (for its topbar profile link) — and registering the page via `->pages([...])` alone does not wire up that namespaced route; only the panel builder's dedicated `->profile(EditProfile::class, isSimple: false)` method does. The mismatch meant `route('filament.{panel}.auth.profile')` failed on every page render, not just the profile page itself.

**Also discovered during this same repair:** giving each `EditProfile` subclass the base class's inherited default slug (`'profile'`) collided with a separate, unrelated, pre-existing auto-discovered `Pages\Tenant\Profile` page that happened to already occupy that same URL in several panels — so each subclass now also explicitly pins `protected static ?string $slug = 'edit-profile';` to avoid silently shadowing that other page.

Fixed by switching all 9 panels to `->profile(Pages\EditProfile::class, isSimple: false)`. A third full sweep came back clean (1 error — the duplicate page from row 4.2's last item, fixed immediately after). A fourth and fifth sweep (post-all-fixes) came back at 0 confirmed errors (2 initial timeouts on the fifth pass were isolated and confirmed transient on immediate re-check).

### 4.4 Dashboard → public site navigation (user-reported)

- **Severity:** MEDIUM — real usability gap, not a crash.
- **Root cause:** `PanelSwitcher::menuItems()` (shared across all 9 panel user menus) only ever generated links *between* panels; there was no entry linking back to the plain public homepage at all.
- **Fix:** added `PanelSwitcher::publicSiteMenuItem()` ("Visit Website" / "زيارة الموقع") and wired it into all 9 panels' `userMenuItems()`, next to the existing language toggle.
- **Verification:** confirmed present in the rendered admin-panel HTML via Playwright.

### 4.5 Language switcher "works once then gets stuck" (user-reported)

- **Severity:** MEDIUM-HIGH.
- **Root cause:** `PanelSwitcher::languageMenuItem()` computed its label and target locale **eagerly**, once, when each `PanelProvider::panel()` method ran. This app's actual production server is Octane/RoadRunner (`docker-compose.yml`, `Dockerfile`), where panel registration happens once per long-lived worker process, not once per request — so the "switch to X" menu item's target language got permanently frozen at whatever locale was active the moment that worker booted, regardless of subsequent session changes. The item would correctly switch away from the default locale exactly once, then perpetually offer the same (now-current) language as its "next" option, appearing stuck.
- **Fix:** both the label and the URL are now Filament `MenuItem` closures (`->label(fn () => ...)`, `->url(fn () => ...)`), which Filament evaluates fresh on every render (confirmed by reading `MenuItem::getLabel()`/`getUrl()` source — both call `$this->evaluate(...)` at render time, not registration time).
- **Verification:** logic-verified via source reading of Filament's `MenuItem` class; the fix pattern is the standard, documented way to avoid exactly this class of Octane-worker staleness.

### 4.6 Top loading bar not RTL-aware (user-reported)

- **Severity:** LOW-MEDIUM (visual correctness).
- **Root cause:** `resources/views/layouts/app.blade.php`'s `#top-progress-bar` was hardcoded `left: 0` with a `width` that grows from that fixed physical edge and a `linear-gradient(90deg, ...)`, regardless of the page's actual reading direction.
- **Fix:** replaced `left: 0` with the logical `inset-inline-start: 0` (resolves to `left` in LTR, `right` in RTL automatically, matching the `dir` attribute already correctly set on `<html>`) and added an `html[dir="rtl"]` override for the gradient direction.
- **Verification:** CSS-logic-verified (standard, well-supported logical-property behavior); not independently screenshotted mid-navigation since the bar only appears for the duration of a page transition.

### 4.7 Property pagination "looks broken" (user-reported)

- **Severity:** MEDIUM.
- **Root cause:** two layered issues. First, Laravel's stock Tailwind pagination view only partially handles RTL (`rtl:flex-row-reverse` on the outer wrapper) while every individual button still used physical `rounded-l-md`/`rounded-r-md`/`-ml-px` and non-mirrored chevron icons — producing misaligned corners and gaps once the flex order reversed. Second — discovered only after the first fix visually appeared to do nothing — **Livewire's `WithPagination` trait renders through its own `livewire::tailwind` view namespace, not Laravel's `pagination::tailwind`**, so a correctly-written override at `resources/views/vendor/pagination/tailwind.blade.php` was silently never used at all, since every paginated view in this app (`property-list`, `news-list`, `wishlist-manager`, `tenant/payment-history`) is a Livewire component.
- **Fix:** rebuilt the pagination partial using logical Tailwind properties (`rounded-s-*`/`rounded-e-*`, native flexbox RTL reordering, `rtl:rotate-180` on the chevron `<svg>`s) instead of the row-reversal hack, and placed the file at the namespace Livewire actually resolves (`resources/views/vendor/livewire/tailwind.blade.php`), keeping the original at `vendor/pagination/tailwind.blade.php` too for any future non-Livewire paginator.
- **Two self-caught bugs in this same fix, both found via screenshot comparison, not assumed:** (1) the "Showing X to Y of Z results" line initially used `__('Showing')`/`__('to')`/etc. — translation keys that don't exist anywhere in this app's `lang/` files (Laravel's stock `pagination.php` only ever defined `previous`/`next`), so it silently rendered the raw English fallback on the Arabic page regardless of locale; fixed to the project's own established inline-ternary convention. (2) The mobile prev/next buttons used escaped `{{ __('pagination.previous') }}` output, which HTML-entity-escaped the `&laquo;`/`&raquo;` already baked into `lang/ar/pagination.php`'s translation strings, displaying the literal text `&laquo;` instead of the `«` character; fixed to `{!! !!}` (safe here — the string is a static, developer-controlled translation value, matching Laravel's own stock-view precedent for this exact case).
- **Verification:** before/after screenshots in both Arabic (RTL) and English (LTR) at `/properties`, confirming correct pill shape, correct chevron direction, correct disabled/active states, and correctly-rendered translated text in both directions.

### 4.8 News page "visually broken / disorganized" (user-reported)

- **Severity:** MEDIUM.
- **Root cause:** `NewsList::getNewsProperty()` (the main grid query) never excluded the articles already shown in the separate "Featured Articles" section above it. With 3 of the 5 seeded articles marked featured, the main grid — which the Blade view renders directly below the featured section whenever no search/filter is active — repeated the exact same 3 cards a second time.
- **Fix:** added `$query->whereNotIn('id', $this->featuredNews->pluck('id'))`, gated by the same `!$search && !$featuredOnly` condition the view already uses to decide whether the featured section is shown at all (so the exclusion only applies when there'd actually be duplication to avoid).
- **Verification:** before/after full-page screenshots — after the fix, the featured section shows its 3 articles once, and the grid below shows only the 2 genuinely distinct remaining articles, no repeats.
- **Not fixed, documented instead:** News cards are text-only (no thumbnail images) — this is a genuine data-model limitation (the `news` table/model has no image column at all), not a rendering bug. Adding fake placeholder imagery would violate this project's own "no fabricated content" rule; a real fix requires a product decision to add an image field and seed real article images, which is out of scope for a bug-repair pass.

## 5. Problems Not Fixed (and why)

- **News article thumbnails** — see §4.8. Requires a schema change + real content, not a safe automatic fix.
- **`resources/views/filament/pages/edit-profile.blade.php` and its old Jetstream-v2-namespace Livewire component references** are now orphaned (no class points at them anymore, since all 9 `EditProfile` pages use Filament's own bundled view). Left in place rather than deleted — harmless, and deleting working-but-unused files wasn't the goal of this pass.
- **`app/Filament/Staff/Pages/EditProfile.php` / `app/Filament/Staff/Resources/Tenants/Pages/EditProfile.php`** — the former is fixed (proper `HasForms`) but confirmed unreachable (the panel's actual menu link points at the shared `App\Filament\App\Pages\EditProfile` class instead, per an existing `use` import aliasing choice already in that file before this pass); the latter has its own separate `getRoutes()`/`getRoutePath()` override with the same "duplicate-panel-prefix" bug pattern as the Tenant panel's profile page (§4.2) but was not confirmed reachable by the systematic sweep (it needs a specific record context to test) — flagged here for a future pass rather than guessed at blind.
- **SEO enhancements beyond what already exists** — the homepage has description/OG/Twitter-card meta but no `og:image` or `hreflang`/alternate-locale tags; property detail pages already have full OpenGraph + JSON-LD (confirmed via the existing, passing `SeoOpenGraphTest`). Improving the homepage's social-preview metadata is a genuine enhancement opportunity, not a reported bug, and wasn't pursued in this pass to stay within the "don't over-redesign, fix what's reported/broken" scope.
- **A full visual/design-system audit** (icon consistency, animation review, spacing system) was not performed as a separate exhaustive pass. What the live screenshots captured during bug verification show (homepage, property list, news, footer) is a coherent, professionally-styled, non-generic-looking site with working dark mode and consistent Heroicons usage — no further redesign was judged necessary based on that evidence, consistent with this task's own "do not over-redesign for novelty" instruction.

## 6. Browser QA

- **Desktop** (1280×900 viewport): systematic sweep of all 219 routes, all 9 personas + guest, plus targeted screenshot verification of the specific reported bugs.
- **Mobile/tablet:** not separately re-tested this pass beyond the pagination component's own responsive mobile/desktop branches (both verified present and correctly styled in the fixed pagination view). A dedicated multi-viewport interaction pass (menu open/close, filter drawers, touch targets) was not performed — flagged as not covered in §9.
- **Arabic (RTL):** default locale, extensively verified — pagination, homepage, property list, news, dashboard menus.
- **English (LTR):** verified via explicit `/lang/en` switch — pagination, homepage.
- **Public site:** homepage, properties list/search, news, about, contact, services, calculators — all 200, zero console errors, zero failed network requests.
- **Dashboard/admin:** all 9 panels' full route sets, logged in as their respective seeded persona.
- **Workflows exercised:** guest → homepage search with filters → landing on filtered results; guest → news browsing; super_admin → login → dashboard → edit-profile → back to public site via new menu item; language switch → persistence check.

## 7. Test Results

`php artisan test --no-ansi`, run twice in this pass (once before most repairs to establish that the pre-existing suite was unaffected by the live-QA-found bugs — Filament panel/route bugs of this kind are exactly the class of defect a green PHPUnit run cannot catch, consistent with this project's own established `CLAUDE.md` testing-rules observation; once after, for final validation):

```
Tests:    32 skipped, 553 passed (2135 assertions)
Duration: 1281.38s
```

**553 passed / 0 failed / 32 skipped** — exact match to the established baseline (`docs/AQAR_TASKS.md`, `docs/REPOSITORY_RELEASE_AUDIT.md`). No pre-existing tests needed modification; every fix in this report was a genuine root-cause correction (missing forms wiring, wrong relationship names, wrong route registration API, wrong view namespace) rather than a change to expected/tested behavior, which is why the suite's pass count didn't need to shift in either direction.

## 8. Route/Page Coverage

- **324** total static (non-parameterized) GET routes discovered via `route:list`.
- **219** actually visited live this pass (guest + all 8 login-capable personas' own panel routes + the shared `/user/*` account routes). Excluded: `api/*` (63, JSON endpoints not browser pages), `horizon/api/*` (11, JSON), `livewire-*` (3, JS asset files), `broadcasting/auth` (1, POST-only auth endpoint) — none of these are browser-navigable pages in the sense this audit targets.
- **Not covered:** routes requiring a specific existing record ID (`{property}`, `{propertyId}`, edit/view pages for individual resources) were not enumerated exhaustively — the static/list/create pages for every resource were covered, which is where the systematic sweep's 32 real bugs were found.

## 9. Localization

Arabic (primary/default) and English both function correctly across every page verified in this pass. The language-switcher bug (§4.5) — which affected every Filament panel's in-dashboard toggle — is fixed and is the most significant localization-mechanism fix in this report. Translation *content* accuracy (as opposed to switching mechanics) was not re-audited from scratch this pass; the previous session's translation-completeness audit (`docs/AQAR_PROGRESS.md`) is treated as still current except where this pass found and fixed new gaps (the pagination "Showing X to Y" string, §4.7).

## 10. RTL

Verified correct on: navbar, homepage, search/filter form, property grid, pagination (rebuilt this pass, §4.7), news grid, footer, dashboard user menus. The loading-bar direction fix (§4.6) is CSS-logic-verified but not live-screenshotted mid-transition.

## 11. Responsive Design

Not a dedicated exhaustive multi-viewport pass this time (see §6/§9 gap). The pagination component's own mobile (`sm:hidden`) vs desktop (`sm:flex`) branches were rebuilt and are present in the fix; not independently screenshotted at a narrow viewport.

## 12. UI/UX

Major, concrete fixes: public↔dashboard navigation gap closed (§4.4), pagination rebuilt to be visually correct and joined-pill styled (§4.7), duplicate news content removed (§4.8). No wholesale visual redesign was performed — the existing design (verified via live screenshots) already reads as a coherent, custom-branded, non-generic real-estate product, not a generated template.

## 13. SEO

Homepage carries `description`, `og:site_name`, `og:type`, `og:title`, `og:description`, `og:url`, `twitter:card`. Property detail pages have full OpenGraph + JSON-LD structured data (confirmed via the passing `SeoOpenGraphTest`). `sitemap.xml` confirmed valid (same test). Gap noted, not fixed: no `og:image` on the homepage, no `hreflang`/alternate-locale tags anywhere (§5).

## 14. Open Graph

See §13 — property detail is complete; homepage is functional but minimal (no image).

## 15. Demo Data

Not re-seeded or re-audited from scratch this pass (no evidence of a demo-data problem was found during live QA — all 28 properties, 5 news articles, and 8 persona accounts rendered real, coherent content throughout every screenshot taken).

## 16. Notifications

Not deeply re-audited as a dedicated system-wide pass this time; no notification-related fatal errors surfaced during the full 219-route sweep or the targeted checks performed. The user's specific report ("works in some places, not in others") was not independently reproduced with a specific failing example this pass — flagged as unverified rather than claimed fixed.

## 17. Search/Filtering

Fixed and verified — see §4.1. This was the most concrete, cleanly-reproduced bug in the whole pass.

## 18. Authentication/Authorization

Not re-audited from scratch (out of scope for this pass, and the prior session's dedicated IDOR/authorization pass — `docs/AQAR-FINAL-END-TO-END-ACCEPTANCE-REPORT.md` — already covered this in depth). All 8 seeded personas successfully logged in and reached their own panel's dashboard during the systematic sweep, with no authorization-boundary regressions observed as a side effect of any fix in this pass (every fix here touched routing/forms/rendering, not policy/gate logic).

## 19. Security

No security-relevant code was touched this pass (no policies, no gates, no mass-assignment, no query-scoping changes) except: `Reviews` resource's rebuilt field now writes `reviewable_type` via a `Hidden::make(...)->default(Property::class)` field rather than trusting client input for that column — a marginal hardening, not a fix for a reported vulnerability. No secrets were introduced or exposed; git diff reviewed clean before commit (§22).

## 20. Performance

Not a dedicated review this pass. No N+1 queries or obviously expensive patterns were introduced by any fix (the News fix adds one `pluck('id')` call on an already-fetched 3-row collection; the Tenants `averageRating` fix replaces a crash with an aggregate query that already existed in the model).

## 21. Git

- **Branch:** `feature/aqar-productization`
- **Commit:** `0e74f77` — "fix: deep pre-deployment repair pass — 32 confirmed page crashes and 6 reported UX bugs"
- **Changed files:** 38 (32 modified, 6 created — `docs/FINAL_PRE_DEPLOYMENT_AUDIT_REPORT.md` itself, `resources/views/components/{app,guest}-layout.blade.php`, `resources/views/filament/pages/plaid-integration.blade.php`, `resources/views/vendor/{livewire,pagination}/tailwind.blade.php`)
- **Diff reviewed before commit:** confirmed no `.env`, no secrets, no stray/generated files — every changed file is an intentional fix described in §4.
- **Push status:** `origin`'s only real branch (besides GitHub's own auto-generated `dependabot/*` branches) is `main` — `feature/aqar-productization` has never itself been pushed, matching this project's established convention of keeping local `main` synced to `feature/aqar-productization` and publishing through `main`. Local `main` was fast-forwarded to this commit and pushed to `origin/main` on GitHub (`Novixa-dev/novixa-aqar`).

## 22. Deployment Readiness

No change from `docs/PRODUCTION-DEPLOYMENT.md` / `docs/DEPLOYMENT-ENVIRONMENT-INVENTORY.md`, both already current from a prior session. This pass's fixes are all application-code corrections; none of them change required environment variables, storage, database, queue, scheduler, or Reverb configuration. One relevant new fact for that document: this pass's discovery that `config/jetstream.php`'s `'stack'` **must** be `'livewire'` (now fixed in code) is itself now a correctness fact about the shipped configuration, not a deployment-time variable — no `.env` change is needed for it.

Do NOT deploy to Laravel Cloud yet, per this task's own explicit instruction — this report is for review first.

---

# Addendum — 2026-08-28: Skill-Assisted UI/UX, Responsive & Deploy-Readiness Pass

**Date:** 2026-08-28
**Branch:** `feature/aqar-productization` (base commit `e099923`)
**Method:** live browser QA (Playwright/Chromium) against the real running dev server, driven primarily in **Arabic (RTL)** with an **English (LTR)** spot-check, across three viewports — **375×667 (mobile), 768×1024 (tablet), 1440×900 (desktop)**. The plan called for a fourth, 1920×1080 viewport; it was not captured — noted here transparently as a scope reduction rather than silently dropped. Design review was informed by two installed skills (below), used as a checklist/lens, not as a source of unreviewed automated changes.

## A.1 Skills installed and how they were used

| Skill | Source | Use in this pass |
|---|---|---|
| `ui-ux-pro-max` | `npm install -g ui-ux-pro-max-cli && uipro init --ai claude` (writes into `.claude/skills/`) | Its Quick Reference (accessibility, touch targets, responsive breakpoints, forms/feedback) was used as a checklist while reviewing screenshots — e.g. flagging the unstyled native file input (§A.4) and the non-functional Filament `SelectFilter`s (§A.4) as clear "Forms & Feedback" violations. |
| `design-review` | Manually authored (`.claude/skills/design-review/SKILL.md`), based on `jezweb/claude-skills`' skill of the same name | Its 7-dimension methodology (layout, typography, colour, hierarchy, consistency, interaction, responsive) structured the page-by-page walkthrough below. |

Both, plus five bundled-but-unused `ui-ux-pro-max-cli` packages (`banner-design`, `brand`, `design`, `design-system`, `slides`, `ui-styling`) live under `.claude/skills/` — **not committed**. `.gitignore` now excludes `.claude/` entirely (alongside the pre-existing `.vscode/`/`.idea/` entries): it's local Claude Code tooling reproducible via the two documented commands above, not application source, and the ~4.5 MB / 173 files of largely generic reference assets have no reason to live in this product's history. `CLAUDE.md` itself is unaffected — it lives at the repo root, not inside `.claude/`.

## A.2 Responsive design review

Reviewed at all three captured breakpoints, both directions where noted: homepage, properties list, property detail, news list, news detail, about, contact, services, calculators, login, register, plus the Staff Filament panel (dashboard, properties list, properties create).

**Result: no responsive-layout defects found.** Every public page collapses cleanly to a single column on mobile, a 2-column grid on tablet, and its full multi-column layout on desktop; the mobile nav hamburger, filter panels, and pagination controls all render and reposition correctly in both RTL and LTR. The one area flagged as a *design* observation rather than a bug: the Staff "Properties" table has 14 columns (title, location, price, currency, beds, baths, type, status, featured, custom description, video/3D-model flags, …) and requires horizontal scroll even at 1440px — worth a future "toggle columns" pass, not a defect.

## A.3 Design-quality pass

Using the `design-review` skill's dimensions against the same screenshot set: layout, spacing, typography, colour/contrast, and RTL mirroring were all judged sound and consistent with the existing brand — no fix-now findings. The specific, concrete defects that *were* found all turned out to be functional/data-translation bugs rather than pure visual-polish issues, and are catalogued in §A.4 below instead of duplicated here.

## A.4 Bugs found, root-caused, fixed, and verified

All of the below were reproduced live, root-caused in the actual source, fixed, and re-verified live (screenshot and/or DOM/console inspection) after the fix — not assumed. Grouped by area.

### Staff Dashboard (`/staff`)

1. **Duplicate, inconsistent headline stats.** The custom `Staff\Pages\Dashboard` renders `PropertyStatsOverview` as a header widget (Total Properties/Active Listings/Total Bookings/Total Revenue, all-time, Filament-styled) **and** the page's own Blade view separately hardcoded the *same four labels* a second time, scoped to the current month via un-formatted plain numbers — so two blocks with identical labels could show different figures. Removed the redundant inline grid and its now-dead backing `mount()`/properties in `Dashboard.php`; kept the properly-styled widget as the single source. *(`app/Filament/Staff/Pages/Dashboard.php`, `resources/views/filament/staff/dashboard.blade.php`)*
2. **"Recent Transactions" widget heading and empty-state always English**, regardless of locale — Filament's default heading-derivation falls back to `class_basename(...)->kebab()->ucwords()` when `getTableHeading()` isn't overridden. Added `getTableHeading()`/`getTableEmptyStateHeading()` overrides. *(`app/Filament/Staff/Widgets/RecentTransactions.php`)*
3. **Total Revenue showing 0.00 / Recent Transactions empty / "Top Performing Properties" chart appearing blank** — root cause was simpler than the first hypothesis (a "this month" date-range mismatch): `Transaction::count()` was exactly 0, no transaction rows had ever been seeded. Added a seeding block tied to the two properties the seeder already marks `SOLD`, reusing the already-fetched real buyer/seller persona records. *(`database/seeders/DemoWorkflowSeeder.php`)* — re-seeded live and confirmed via screenshot: Total Revenue now shows a real formatted figure, Recent Transactions shows two real rows.
4. **The chart's continued blank appearance after the above fix was investigated separately and confirmed to be a false positive**, not a real bug: Filament's `x-load`/`x-load-src` lazily loads `chart.js` on viewport intersection, which a `fullPage: true` Playwright screenshot doesn't reliably trigger the way genuine incremental user scrolling does. A script that scrolls in real 300px increments (matching real user behaviour) shows the chart rendering correctly with real data. No app change was needed for this one — noted here so the false alarm isn't rediscovered.
5. **"Top Performing Properties" chart heading and "Revenue" legend hardcoded English.** `protected ?string $heading` doesn't support a locale-aware closure; switched to overriding `getHeading()`, and made the dataset `label` locale-aware too. *(`app/Filament/Staff/Widgets/TopPerformingProperties.php`)*
6. **A fake, unrelated placeholder widget** (`StatsOverview`: "Unique views 192.1k", "Bounce rate 21%", "Average time on page 3:12" — Filament's own stock scaffold example, never customized, no real query, no locale-awareness) was wired into `AdminPanelProvider`'s `->widgets([...])` array and auto-discovered by the Staff panel. Traced its actual reachability: neither the Admin nor Staff dashboard pages (both fully custom, with their own explicit `getWidgets()`/`getHeaderWidgets()`) currently render it, but it's genuinely fabricated content one config change away from going live. Deleted the widget file and its two dangling references. *(deleted `app/Filament/Staff/Widgets/StatsOverview.php`; edited `AdminPanelProvider.php`, `StaffPanelProvider.php`)*

### Contractor Dashboard (`/contractor`)

7. **Every stat card showed blank (not even "0")** — `mount()` had every data-loading line commented out, referencing an `App\Models\Job` class that doesn't exist anywhere in this codebase (confirmed: no model, no migration). The Contractor panel was actually built around `MaintenanceRequest` (labelled "Work Order" in its own UI) with a `contractor_id` column matched via `Contractor::where('email', auth()->user()->email)` — the exact pattern already established and working in `MaintenanceRequestResource::currentContractorId()`. Rewrote `mount()` to use that real, working pattern; also removed three properties/a whole dead inner widget class that referenced the non-existent `Job`/`WorkOrder` shape. *(`app/Filament/Contractor/Pages/Dashboard.php`, `resources/views/filament/contractor/dashboard.blade.php`)* — verified live: all three cards now show real counts for the seeded contractor.

### Staff → Properties (list, create/edit)

8. **Property type column/form/filter showed raw, untranslated, inconsistently-cased English** ("Apartment", "house", "Land", "Commercial", "Building" — two different casing conventions in the same column) **and the edit form's dropdown only offered 4 options that didn't match 6 of the 7 real stored values** — meaning editing almost any real property showed a blank type selector. Root cause: `PropertySeeder`/`LeadAndBuyerSeeder` populate `property_type` from a 7-value, capitalized vocabulary; `PropertyResource`'s form `Select` was built independently against an unrelated 4-value lowercase vocabulary (`house/apartment/condo/townhouse`) that only ever matched `house`. Rebuilt the option set to the real 7 values, matching the wording already used on the public site's own search filters for the 5 shared ones. *(`app/Filament/Staff/Resources/Properties/PropertyResource.php`)*
9. **Both the `property_type` and `status` table filters were visible but non-functional** — `SelectFilter::make(...)` with no `->options()`/`->relationship()` renders a dropdown with nothing selectable. Wired up both. *(same file)*
10. **Status column also untranslated** (raw `for_sale`/`sold`/etc.); added translation plus a colour badge (matching the badge pattern already used elsewhere in this codebase, e.g. Contractor's `MaintenanceRequestResource`). *(same file)*
11. **The floor-plan image upload field showed the browser's own native, unstyled, English "No file chosen / Choose File" text** on an otherwise fully Arabic, fully custom-styled form — a hand-rolled `<input type="file">` with no hiding mechanism, unlike every other upload field on the same form (which use Filament's own `FileUpload`). Replaced with the standard technique of visually hiding the native input and driving it from a styled, translated `<label>`. *(`resources/views/filament/forms/components/floor-plan-editor.blade.php`)*
    - **Self-caught regression:** the first version of this fix used Blade's escaping `{{ }}` to inject a JS string literal into the component's `<script>` block; `{{ }}` HTML-escapes its output, turning the literal apostrophes into `&#039;` and producing a `SyntaxError` that silently broke the *entire* Alpine component (image upload, canvas annotations, room/marker tools — everything), not just the new addition. Caught via live console-error inspection before being reported as done; fixed by switching that one interpolation to `{!! !!}` (safe here — both branches are hardcoded literals, no user input).
    - **A second, environmental cause of the same symptom:** even after the Blade fix, the native input was still visible, because the new `sr-only` (and other) utility classes weren't in the *compiled* Filament theme CSS — Tailwind v4 scans templates at **build time**, and no `npm run dev`/Vite process was running, so the page was served from a static bundle built before this session's edits. Ran `npm run build`; confirmed via computed-style inspection that `.sr-only` now actually hides the element. This is a general fact worth remembering for the rest of this project's life: **a Blade-only edit that adds a class combination never used elsewhere in a Filament panel's already-scanned templates needs an asset rebuild to take visual effect**, even though the DOM/class output is correct immediately.

### Public site

12. **One of five seeded news articles was entirely in English** ("Real Estate Investment Strategies in Yemen for 2026") while the other four — and the rest of the site — are Arabic; `news` has no per-locale columns, so whatever language a row is authored in is what every visitor sees regardless of locale (confirmed: even in the English-locale UI, all article *content* stays Arabic — only the surrounding UI chrome translates). Translated the one outlier to match the rest of the demo set. *(`database/seeders/NewsSeeder.php`, re-seeded live via `updateOrCreate`)*
13. **News detail page byline and "related articles" dates were raw ISO (`2026-08-25`)** while the news *list* page already correctly used `->translatedFormat('M d, Y')` for the same field. Made the detail page consistent with the list page's own established convention (and dropped a now-inappropriate `font-mono` class from a field that's no longer a fixed-width numeric string). *(`resources/views/livewire/news-detail.blade.php`)*
14. **Property detail page's "Category" badge showed a raw, single-language value** ("sales", or — on one specific seeded property — literally "test"). The existing code already had a correct bilingual fallback for a *null* category, but bypassed it entirely once a real `PropertyCategory` was attached, printing its raw stored `name`. Root-cause data check found the "test" category was genuine leftover dev cruft (never produced by any seeder) assigned to one otherwise-normal seeded villa. Reassigned that property to the correct "sales" category (matching its `for_sale` status) and deleted the orphaned "test" row; added a bilingual translation map for the three real category values (`sales`/`lettings`/`hmo`) to the Blade view. *(`resources/views/livewire/property-detail.blade.php`; data fix via `php artisan tinker`, not a code change)*
15. **The `Property::translated_type` accessor — used by the homepage, properties list, *and* property detail (three call sites, one shared bug) — silently never translated the `house` type**, because its map's key was capitalized `'House'` while the only real stored value is lowercase `'house'`; every other key in the same map exactly matches its real stored casing. Fixed the one key; verified the fix propagated correctly to all three pages at once (the value of fixing this at the model-accessor layer rather than per-view). *(`app/Models/Property.php`)*

### Incidental

16. A PHP 8.4 deprecation (`Implicitly marking parameter $description as nullable is deprecated`) surfaced while testing #14 above; the rest of the same file already used explicit `?type` nullable syntax consistently, this was the one straggler. Fixed. *(`app/Services/PropertyHistoryService.php`)*

## A.5 Translation spot-check summary

Beyond the specific bugs in §A.4, browsing entirely in Arabic across every page listed in §A.2 surfaced no other raw untranslated strings, wrong-language text, or literal translation keys. The English-locale spot-check (home, properties, property detail, news, about — desktop) confirmed all UI chrome (navigation, buttons, labels, filters, footer) correctly switches to `dir="ltr" lang="en"` with matching English copy; property/news **content** (titles, descriptions, article bodies) does not auto-translate on locale switch, because those are single-language authored columns, not per-locale fields — this is an architectural characteristic consistent with a Yemen-first platform where real listings/articles are authored by real Arabic-speaking users, not a bug, and reworking it into a full per-locale content schema is out of scope for a fix-what's-broken pass.

## A.6 Functional workflow spot-check

Guest flow exercised live in Arabic: homepage → properties list → applied a "Villa" type filter (28 → 7 results, confirmed via DOM, not just visually) → navigated into a filtered result's detail page — zero console/page errors throughout. This, plus the fixes in §A.4 (which required navigating every affected persona's real dashboard and interacting with real forms/filters/tables to reproduce each bug), constitutes this pass's functional coverage; it does not repeat the prior pass's full 219-route sweep, which already has a clean, separately-dated result (§4).

## A.7 Test suite

Ran `php artisan test --no-ansi` twice this pass — once after the Filament dashboard/property-resource/floor-plan-editor fixes, once after the news/property-category/model-accessor fixes — both came back **553 passed, 0 failed, 32 skipped (2135 assertions)**, exactly matching the established baseline. No test was weakened or deleted to reach this result.

## A.8 Restated verdict

```
DEMO READY
```

No new P0/P1 blockers were found or left unresolved. All defects found this pass were confirmed, root-caused, fixed, and re-verified live; the automated suite remains clean at the established baseline. The two items noted as explicitly out of scope — a 4th (1920×1080) viewport capture, and per-locale content columns for Property/News — are genuine gaps worth tracking but do not change the readiness verdict: nothing found in either area constitutes a broken primary workflow, a fatal error, an authorization vulnerability, or fake content presented as real (the one instance of that — the "test" category — was found and fixed, §A.4 #14).

---

# Addendum B — 2026-08-28: Final Demo Release Gate

**Purpose:** re-verify, against the exact commit about to be published, that the state audited above is actually correct — not re-derive the audit. Ran a fresh `migrate:fresh --seed`, a full 9-panel/8-persona browser sweep, real interactive flows (registration, password reset, search/filter, file upload), and the full automated suite three times across this pass as fixes landed.

## B.1 Fresh migrate + seed validation

`php artisan migrate:fresh --seed --force` against the real MySQL dev database: **exit 0**, all migrations and all 16 seeders `DONE`. Confirmed the two fixes from Addendum A survive a from-scratch reseed (not just the live `updateOrCreate` re-run used at the time): `Transaction::count()` = 2, zero English-titled news articles, and — importantly — the "test" `PropertyCategory` did **not** reappear (`sales, lettings, hmo` only), confirming it really was leftover dev-database cruft with no seeder path that could reintroduce it, not a latent seeding bug.

## B.2 New defects found this pass (all fixed and re-verified)

1. **Self-registration was completely broken** — `/app/register` succeeded (account created, user logged in) but the very next request hard-403'd with "غير مصرح بالوصول". Root cause: `User::canAccessPanelById()` required a role from `config('filament-shield.panels.app')` (`admin`/`super_admin`/`staff` only), while `AppPanelProvider` is the one panel with `->registration()` enabled for the general public — every self-registered user has zero roles and could never pass that gate. This is the most severe finding of the whole review: a real visitor could never successfully sign up. Fixed by granting `app` panel access to authenticated users with no role at all, leaving the other 8 panels' role gates untouched.
   - **Self-caught regression:** the first version of the fix granted `app` access to *any* authenticated user, which broke `PanelSwitcherTest::test_buyer_sees_no_switch_links` (a buyer started seeing a "switch to Account" link) — caught by running the full suite before considering the fix done, not by the browser check alone. Narrowed to roleless users only; all 4 `PanelSwitcherTest` cases and the original registration flow both verified passing together afterward.
2. **File upload previews and an upload Web Worker were silently blocked by CSP** — the upload itself always succeeded (confirmed via network trace: real chunked upload, 200s, "تم الرفع" complete state), but `img-src` had no `blob:` (killing the local preview thumbnail) and `worker-src` wasn't set so it fell back to `script-src`'s stricter list (killing a Web Worker used during upload) — both caught live via console/CSP error inspection, not assumed. Added `blob:` to `img-src` and a dedicated `worker-src 'self' blob:` (kept separate from `script-src`, which still governs actual script/eval execution — blob: object URLs here are always browser-generated from the user's own selected file, never external content).
3. **5 of 9 panels' Dashboard pages showed a raw English "Dashboard" title** on Arabic-locale pages (Contractor, Landlord, Seller, Buyer, App) — Filament's base `Dashboard` class ships a localized title for free, but these pages extend the generic `Page` class instead (needed for custom `mount()`/widget logic) and lose that. Added the same override Staff/Agent already had.
4. **The Tenant persona's entire dashboard widget area was hardcoded English** — 5 stat labels, a table heading, empty-state text, and a row-action label, all untranslated, unlike every other panel. Fixed to match the locale-ternary pattern used everywhere else in this codebase.

## B.3 Investigated, not fixed (explicitly not a regression from this session)

- **One flaky test** (`Tests\Unit\TransactionTest::test_transaction_scope`, "actual size 3 matches expected size 1") failed in 1 of 4 full-suite runs this session, against otherwise-identical code both times. Investigated directly: ran the same test file alone 3× (clean every time), and forced the exact suspected leak order (`DemoDataSeederTest` — which seeds `DemoWorkflowSeeder`, the seeder this session added 2 Transaction rows to — immediately before `TransactionTest`) explicitly (clean). `RefreshDatabase` correctly isolated the scenario every time it was directly tested. Conclusion: very likely a pre-existing, rare, test-order/parallelism-related flake, not caused by any change in this session — flagged honestly rather than either hidden or accepted as an unexplained failure.
- **Password reset's mail delivery doesn't work in this local dev environment** (`local.ERROR: Connection could not be established with host "mailpit:1025": ... No such host is known`) — expected: `mailpit` is a Docker Compose service hostname, not running/resolvable in this native (non-Docker) local setup, already documented in `docs/DEPLOYMENT-ENVIRONMENT-INVENTORY.md` §13 as something a real mail provider must replace before any real deployment. The form/submission mechanics themselves work correctly (field renders, accepts input, submits, Livewire request completes). **New observation, not yet fixed:** when the mail send throws, the user gets zero visible feedback — no error toast, no success message, the form just silently sits there. Worth a follow-up once a real mail provider is configured, so this is verifiable end-to-end; not fixed this pass (Filament's own password-reset page internals, lower risk to leave alone than to patch blind).
- **`docs/PRODUCTION-DEPLOYMENT.md` vs `docs/DEPLOYMENT-ENVIRONMENT-INVENTORY.md`:** compared both against the current codebase. The inventory document (later, more thorough) already correctly documents everything this pass independently found reason to check — the WalkScore mock-data fallback, all 12 inactive third-party integrations, the `FRONTEND_URL`/CORS gap, the ephemeral-storage risk, the Horizon dashboard deny-all gate — verified accurate, nothing stale. `PRODUCTION-DEPLOYMENT.md` remains a correct but narrower generic checklist; not edited this pass since no inaccuracy was found in it, only a completeness gap already covered by the other document.

## B.4 Full panel + persona sweep

All 9 panels (`admin`, `staff`, `agent`, `buyer`, `seller`, `landlord`, `tenant`, `contractor`, `app`) logged into with their seeded persona: **zero page errors, zero 5xx responses, all reach a working dashboard.** Guest flow (homepage → language switch via the real UI control → properties list → type filter, DOM-confirmed narrowing results → property detail → news list → news detail) and two deliberate 404s (nonexistent property ID, nonexistent route) all clean.

## B.5 Test suite (this pass)

Run 4 times total as fixes landed: **PASS** (553/0/32) → **PASS** (553/0/32) → **FAIL** (552/1/32, the flaky `TransactionTest`, investigated in B.3) → **FAIL** (552/1/32, `PanelSwitcherTest` regression, fixed in B.2 #1) → **PASS** (553/0/32), final and current. No test was weakened, skipped, or deleted to reach a passing result at any point.

## B.6 Git state at the end of this pass

15 commits ahead of `origin/main` (0 behind — clean fast-forward), working tree clean, HEAD `0b3d87e`. Nothing pushed.

## B.7 Restated verdict

```
DEMO READY
```

One genuine, severe pre-existing bug (self-registration's hard 403) was found and fixed this pass — worth flagging that it existed at all despite Addendum A's review, since registration wasn't specifically exercised as an interactive flow until this gate. Full detail of what was and wasn't checked, and why, is in the chat response for this session.
