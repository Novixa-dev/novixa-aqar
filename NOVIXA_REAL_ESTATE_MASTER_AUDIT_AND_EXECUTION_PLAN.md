# Novixa Real Estate — Master Audit & Execution Plan

**Audit date:** 2026-08-17
**Branch:** `feature/aqar-productization` (off `main`)
**Auditor:** Claude Code, Phase 0 discovery pass
**Method:** Every finding below was independently verified against the running code, git-tracked files, or an actual `php artisan test` execution on 2026-08-17 — not copied from prior reports. Where a prior audit claim could not be independently verified, it is marked `UNVERIFIED` rather than assumed true.

---

## 0. How to read this document

This repository already contains an extensive prior audit trail — `docs/audit/` (39 numbered files), `docs/tasks/` (TASK-001 through TASK-018), `docs/project/`, and ~25 root-level feature "final report" files — produced by a previous AI-assisted effort (see `docs/ai/ANTIGRAVITY_*` references) dated up to 2026-08-15. That work self-graded the project **"YELLOW — Release Candidate"** and described it as *"an exceptionally well-engineered, highly localized, and feature-complete real-estate product."*

Before continuing that work, every one of its "6 exact remaining blockers" was re-checked against the live code, two days after they were flagged. **All 6 were still unresolved.** Several other claims (branding "COMPLETED," Arabic localization "COMPLETED") were also checked and found to be materially inaccurate. This document does not discard the prior audit's useful raw material (seeders, docs, structure), but it does not inherit its conclusions. Treat every "COMPLETED"/"VERIFIED" label anywhere else in this repo as a claim to re-check, not a fact.

This is a **Phase 0 discovery deliverable**. It establishes ground truth. It does not yet represent completed remediation — that is Phases 1–10, scoped in Section 8.

---

## 1. Executive Summary

**What this project actually is:** a fork of the open-source **Liberu Real Estate** Laravel package (MIT-licensed, UK lettings/estate-agency market) being rebranded and localized into "Aqar" (عقاري), a fictional Yemeni real-estate company, for Novixa's single-tenant commercial-demo pipeline.

**Current state, in one sentence:** the domain core (properties, listings, bilingual UI shell, admin CRUD, ~700 passing tests) is real and largely functional, but it is wrapped in (a) unresolved UK-market leftovers, (b) a three-way brand-name collision that touches nearly every public page, and (c) a large layer of disconnected "impressive" feature scaffolding (blockchain, VR design studio, holographic/AR tours, neural-network valuation, auctions) that has no reachable entry point in the running app and inflates the codebase without inflating the demo's actual capability.

**Is it demo-ready today?** No. Not because core functionality is broken, but because a first-time visitor moving between the homepage, about page, footer, and contact page will see three different company names, and an English-language visitor submitting a property inquiry will see the literal string `lead.form.inquiry_success` instead of a confirmation message. Those are the kind of defects that immediately read as "unfinished demo" to a prospective client, and they are fixable in hours, not weeks.

**Biggest strategic decision needed from Novixa (not an engineering call):** whether to actively **remove** the disconnected sci-fi feature clusters (Section 4) or leave them as inert backend scaffolding. Both are legitimate choices; see Section 8 for the tradeoff. This document recommends removal or explicit hiding, because undiscoverable/broken features are a liability in a live client demo (a curious prospect who finds a dead link or an error screen does more damage than a missing feature ever would).

---

## 2. Verified Project Inventory

- **Framework:** Laravel 13.24, PHP 8.4 runtime (Herd, resolves to 8.4.16). `composer.json` still declares `"php": "^8.5"` — inconsistent with the actual runtime; this is BLK-03 from the prior audit, still unresolved.
- **Admin/Panel stack:** Filament v5 across **9 separate panels** (`Admin`, `App`, `Agent`, `Buyer`, `Contractor`, `Landlord`, `Seller`, `Staff`, `Tenant`). Auth via Jetstream + Fortify + Sanctum, RBAC via `spatie/laravel-permission` + `filament-shield`. *(Corrected in Phase 4: the original Phase 0 count of 8 missed the `Agent` panel — see Section 17.2, which independently confirmed 9 panels while auditing locale-middleware coverage.)*
- **Frontend interactivity:** Livewire 4, ~50 Livewire components under `app/Livewire`.
- **Data layer:** ~90 Eloquent models, ~150 migrations, MySQL in production config / SQLite for tests.
- **Infra scaffolding present:** Horizon, Octane (FrankenPHP/RoadRunner/Swoole configs), Reverb (websockets), Docker, Kubernetes manifests (`k8s/`, 10 files) — full production-ops tooling for what is currently a local demo project.
- **Package identity:** `composer.json` name is still `liberu-real-estate/real-estate-laravel` (should be something like `novixa/aqar`).
- **No root `LICENSE` file exists**, despite `composer.json` declaring `"license": "MIT"`. This is a compliance gap for MIT-derivative code (see Section 7).
- **Documentation sprawl:** ~25 root-level markdown "final report" files (3D models, AR tour, holographic, blockchain/smart-contract, VR design, neural-network valuation, virtual staging, chatbot) plus `docs/audit/` (39 files), `docs/tasks/` (18 files), `docs/project/` (7 files). This volume of self-generated reporting is itself a symptom — see Section 4.

---

## 3. Test Suite — Verified Ground Truth

Prior audit (`docs/audit/39_FINAL_RELEASE_GATE.md`, 2026-08-15) claimed **17 failed / 721 total**, all in Livewire-related tests, as its top blocker (BLK-01).

**Re-run today (2026-08-17), full suite, correct PHP binary (Herd 8.4.16 — the default `php` on PATH resolves to Herd-Lite 8.4.0, which is *below* PHPUnit 13's minimum and cannot run the suite at all; this is itself a local dev-environment defect worth fixing):**

```
Tests:    17 failed, 33 skipped, 671 passed (2510 assertions)
Duration: 297.78s
```

**This claim held up exactly.** All 17 failures are isolated to `tests/Feature/VirtualTourTest.php` — Livewire 4 component-state and event-assertion regressions (`$component->get(...)` returning `null` where a boolean/date is expected, `assertSee('Schedule Live Tour')` failing, `assertDispatched('tourScheduled')` failing, one test finding the `appointments` table empty after a `scheduleLiveTour()` call that should have written a row). This reads as a real API-compatibility break introduced by a Livewire major-version upgrade, not a flaky or environmental failure — it is reproducible and specific.

**Status:** `CONFIRMED, STILL BLOCKING`. Root cause not yet diagnosed line-by-line; recommended as first item in Phase 1 (Section 8).

---

## 4. Scope-Bloat Inventory — Disconnected Feature Scaffolding

This is the most consequential new finding of this audit, not present in prior reports as a named risk.

| Cluster | Files touched | Reachable by a demo visitor? |
|---|---|---|
| **Blockchain / Smart Contracts** | `BlockchainService.php`, `SmartContractService.php`, `SmartContract`/`SmartContractTransaction` models, 2 migrations, root `contracts/RentalAgreement.sol` | **No.** `BlockchainService` runs in `simulated` mode by config default — confirms it was never a real chain integration. No route anywhere. |
| **VR Property Design Studio** | `VRPropertyDesignService.php`, `VRPropertyDesignController.php` (12+ API routes), `VRPropertyDesignStudio.php` Livewire component, `VRDesign` model, migration | **No.** API routes exist; nothing in `resources/views` mounts the Livewire UI. Grep finds only the class's own self-reference. |
| **Holographic tours** | `HolographicTourService.php`, `HolographicViewer.php` Livewire, view, migration | **Technically yes, practically no.** A real route exists (`property.holographic-tour`) but zero nav links reference it anywhere — reachable only by typing the URL. Mentioned only as marketing copy on the homepage/services page. |
| **AR tours** | `ARTourService.php`, `ARTourController.php`, 5 routes | **No.** Routes exist; no template links to them. |
| **Neural-network valuation** | `NeuralNetworkValuationService.php` (+ 2 root "final report" docs) | **No.** Zero route/controller/Livewire wiring found at all — fully disconnected. |
| **Auction / Bidding** | `Auction` model, `AuctionBidding.php` + `AuctionInterface.php` Livewire, 2 views | **No.** Zero matches for "Auction" in `routes/web.php` or `routes/api.php`. `AuctionBidding` doesn't even have a matching Blade view — it would error if it were ever invoked. |
| **Insurance purchase form** | `InsurancePurchaseForm.php` Livewire, view | **No.** Self-references only. |
| **3D property models** | `model-3d-viewer.blade.php`, 2 migrations, `@google/model-viewer` + `three` npm deps | **Partial.** The only cluster with real view-level integration signals — worth verifying properly rather than removing outright. |

**Why this matters:** roughly a third of this application's apparent feature surface is unreachable by anyone browsing the live site. For a commercial demo whose entire purpose is to look and feel like a complete, trustworthy product to a prospective client, invisible or half-wired "advanced" features provide zero demo value and meaningful risk (a curious visitor finding a raw URL that half-works, or a developer accidentally exposing one during a live walkthrough). The ~25 root-level "final report" documents describing these features in glowing terms should be read as a caution flag about how much of this codebase's apparent sophistication is real vs. narrated.

**Recommendation for Phase 1:** either (a) properly wire and test one or two of these as genuine differentiators if Novixa wants a "wow" feature (3D viewer is the best candidate — it's already partially integrated), or (b) remove the rest entirely (routes, controllers, Livewire components, migrations, models, docs) rather than leave them as dead weight. Leaving them exactly as-is (present, unreachable) is the worst option: it's ongoing maintenance burden and license-review surface with no offsetting value. This is flagged for a Novixa product decision, not decided unilaterally here, since it affects scope and possibly saleable "future roadmap" talking points.

---

## 5. Branding Identity Crisis — Three Competing Names

Prior audit (TASK-003) claimed branding was **"VERIFIED & COMPLETED."** This is false. Three different company identities currently coexist in production-facing templates:

1. **"شركة الأفق العقارية" / "Al-Ofuq Real Estate"** — `about.blade.php`, `contact.blade.php`, `home-navbar.blade.php`, `layouts/app.blade.php:8-10`, `NewsSeeder.php` article attributions. Internally consistent with itself (matching Mukalla/Sana'a/Aden branch addresses and `+967` phone formats across these files).
2. **"Aqar / عقاري"** — `welcome.blade.php:7`, `config/app.php` `APP_NAME`, `.env.example`.
3. **"Novixa Aqar"** — `footer.blade.php:62,65` ("Developed by Novixa & AK01REDWAN"), `lang/en.json:53` ("Why Choose Novixa Aqar?").

Additionally, `contact.blade.php:47` mixes domains within the same page: `info@alofuq-aqar.ye` and `contact@novixa.com`.

**Impact:** any visitor who reads more than one page will notice the company has three names and two email domains. This is the single highest-leverage fix in this entire audit — it touches maybe 6-8 files and is purely mechanical once Novixa picks the canonical identity (Section 6 asks for that decision).

**Status:** `CONFIRMED, HIGH PRIORITY, NOT YET SCOPED FOR A DECISION`. See Section 9, Question 1.

---

## 6. Localization (AR/EN) & RTL/LTR Audit

- `lang/en/` is genuinely missing `property.php`, `navigation.php`, `lead.php`, `location.php` (present in `lang/ar/`). Impact is narrower than the gap suggests: `property.php`, `navigation.php`, and `location.php` are **dead/unused** — no code calls their keys, so their absence changes nothing. **`lead.php` is different and is a real, live bug**: `app/Livewire/PropertyDetail.php:204` calls `__('lead.form.inquiry_success')` to flash a success message after a property inquiry. In English mode, this resolves to nothing and the visitor sees the literal string `"lead.form.inquiry_success"` instead of a confirmation. In Arabic mode it works correctly (`lang/ar/lead.php:43` exists).
- `lang/ar.json` (78 keys) and `lang/en.json` (92 keys) mix two different translation conventions inconsistently (Arabic-source-string keys and English-source-string keys coexist), producing redundant no-op entries rather than real missing translations. Not urgent, but should be normalized to one convention during Phase 4.
- Only **44 of 162** Blade files use `__()`/`@lang()` at all. Most dashboard-area views (`properties/`, `auctions/`, `bookings/`, `landlord/`, `tenant/`) appear to have no translation wiring — these are largely internal/admin-adjacent views, but this should be confirmed page-by-page in Phase 4, not assumed acceptable.
- RTL support is real but thin: `dir="rtl"/"ltr"` is correctly set once in `layouts/app.blade.php` based on locale, and the locale-switching middleware (`app/Http/Middleware/SetLocaleMiddleware.php`) correctly persists the choice to session + a 1-year cookie — this part works. But `resources/css/app.css` has exactly one RTL-specific CSS rule, `resources/css/filament/admin/theme.css` has **zero**, and Tailwind `rtl:`/`ltr:` variants appear in only 2 of 162 Blade files. **The Filament admin panel is not meaningfully RTL-aware.**

**Status:** locale switching mechanism = `PASS`. Content translation coverage = `PARTIAL, ~27% of views`. RTL layout = `PARTIAL, functional shell only`. Admin-panel RTL = `FAIL`.

---

## 7. Security & License Review

- **No real secret exposure found.** `.env` is correctly untracked in git. All configured third-party API keys (Stripe, AWS, WalkScore, Holographic, Neighborhood-data, Pusher) are empty placeholders in `.env`, not live values.
- **`.env.testing` IS tracked in git** and contains `DB_PASSWORD=root`. Low severity (local test DB only) but should not be committed — remove from tracking and add to `.gitignore`.
- **`error_log` (raw PHP error log) is tracked at repo root.** Should never be committed; remove and gitignore.
- **`.idea/` (6 files, JetBrains IDE config) is tracked.** Should be removed and gitignored — not secret, but not appropriate for a clean product repo either.
- **No root `LICENSE` file**, despite `composer.json` declaring `"license": "MIT"`. Given this codebase is a Liberu Real Estate derivative (also MIT), the MIT license text and copyright notice should be preserved somewhere in the repository. No evidence of per-file copyright headers being stripped (spot-checked 4 files; none had them, suggesting they never existed at file level — the gap is entirely the missing root file).
- **Dependencies:** all Composer and NPM packages scanned are standard MIT/BSD Laravel-ecosystem packages (Filament, Spatie suite, Jetstream, Horizon, Octane, Sanctum, Alpine, Tailwind, Leaflet). No GPL/AGPL or other copyleft licenses found. `docusign/esign-client`, `stripe/stripe-php`, `spatie/laravel-analytics` are installed but unconfigured (dead weight, not risk).
- **Known active security debt from prior audit, independently confirmed still present:** `UserSeeder.php` creates `admin@example.com` / password `'password'` with super-admin privileges, and it is still wired directly into `DatabaseSeeder.php`'s default `run()` call chain. This must not reach any environment that isn't purely local/demo without being separated into a demo-only seeder.
- `public/robots.txt` still has zero `Disallow` rules — `/admin`, `/app`, and other panel routes are fully crawlable.

**Status:** no critical live exposure. Multiple `HIGH`/`MEDIUM` hygiene and pre-production items remain, all previously identified and none yet fixed as of this audit date.

---

## 8. Data Consistency & Demo Content Audit

**Genuinely good and worth preserving:** `PropertySeeder.php` (28 properties — 12 sales, 12 lettings, 4 HMO — with real Arabic titles referencing actual Al-Mukalla neighborhoods, correct Mukalla lat/long, Yemen postal code) and `NeighborhoodSeeder.php` (6 real Mukalla districts with Arabic school/amenity data) are coherent, realistic, and clearly built with care. `NewsSeeder.php` content (Al-Mukalla/Sana'a/Aden market reports) is similarly strong, though currently attributed to the wrong brand name (see Section 5).

**Needs replacement:**
- `BranchSeeder.php:20-23` — placeholder data never migrated: `'456 Oak St, City, Country'`, `'+1987654321'` (US-format phone).
- `UserSeeder.php` — generic `admin@example.com`/`staff@example.com`, no Yemeni identity.
- `DocumentTemplateSeeder.php:14-29` — seeds UK-specific tenancy law documents ("Assured Shorthold Tenancy Agreement," "Section 8/21 Notice" under the UK Housing Act 1988). These have no legal meaning in Yemen and should be replaced with Yemen-appropriate lease/notice templates or clearly relabeled as illustrative-only.
- **£/GBP is hardcoded and user-visible** in `ChatbotService.php:88`, `PropertyBrochureService.php:36,134`, `SendToFriendService.php:91`, the property-tax-estimator and property-valuation Livewire views, and two API controllers default `currency` to `'GBP'`. Main listing/detail pages correctly use YER — these secondary tools do not.
- `config/countries.php` has **zero Yemen entry** — only GB/IE/AU/CA/US/ZA/NZ, with the GB entry carrying `rightmove`/`zoopla`/`onthemarket` portal references and UK legal terminology (`'lawyer' => 'solicitor'`). Yemeni city/region data exists only informally, scattered across seeders and Blade strings — there is no single structured source of truth for it.
- Minor: a literal `"123 Main Street, London, SW1A 1AA"` placeholder in `valuation-booking.blade.php:57`, and `example.com` placeholders in three booking forms.

**Status:** the core "story" (Al-Mukalla property listings) is genuinely strong. The supporting cast (branches, document templates, secondary calculators, country config) is not yet aligned to it.

---

## 9. Functional & Admin Panel Audit

- Public routes and their controllers are real, substantive, non-stub implementations (spot-checked `PropertyValuationController`, `ARTourController`, `CustomReportController`, `PaymentController`, several API v1 controllers). The ~120-endpoint API v1 surface is uniformly behind `auth:sanctum` + a custom permission middleware — presence confirmed, correctness of the permission logic itself not yet independently verified (flag as `REQUIRES VERIFICATION` for Phase 2).
- **The Filament Admin panel is much thinner than its "8 panels" framing suggests.** `app/Filament/Admin` has only 7 resources: `Branches`, `Menus`, `OnTheMarketSettings`, `RightMoveSettings`, `ZooplaSettings`, `Teams`, `Users`. All actual real-estate CRUD (Properties, Leases, Tenants, Landlords, Sellers, Buyers, Contractors) lives in the separate `Staff` panel instead. Two of the seven Admin resources (`RightMoveSettings`, `ZooplaSettings`) are UK property-portal integration screens — meaningless for a Yemeni company and should be removed or hidden, not left visible to whoever ends up as "Admin."
- One widget file exists as `LeaseRenewalReminderWidget.php.disabled` — dead weight sitting in the repo via a file-extension hack rather than being properly removed or re-enabled.
- **Debug-marker hygiene is genuinely good** — only 3 real TODOs found in the entire `app/`/`resources/views` tree, but each represents a real dead-button-shaped gap: `ListAgentPerformance.php:20` (a report-generation button that does nothing), `ContactController.php:26` (the public contact form does not send any email notification to staff), `SyncActivityToCrm.php:20` (CRM sync job is unimplemented). These directly violate the "no dead buttons" requirement and should be prioritized in Phase 2 alongside a fuller manual click-through pass, since three known instances almost certainly means more exist that a TODO-grep won't catch (e.g., the orphaned Livewire components in Section 4 would error or silently no-op if a stray link to them ever appeared).

**Status:** core CRUD and routing = solid foundation. Admin panel composition needs rationalizing (remove UK-portal screens, decide if 8 panels is the right structure for a demo of this size). At least 3 confirmed "looks interactive, does nothing" gaps requiring a full manual walkthrough to find the rest.

---

## 10. Consolidated Blocker/Priority Matrix

| ID | Issue | Severity | Status |
|---|---|---|---|
| B-01 | Three competing brand names across public pages | HIGH | **FIXED — executed across all public-facing files. See Section 17.1.** Dar Hadhramaut (public) / Novixa Aqar (product, confined to sanctioned locations), verified via repo-wide search. |
| B-02 | `lead.form.inquiry_success` shows raw key to English users | HIGH | **FIXED — see Section 17.3.** `lang/en/lead.php` created; the same missing-namespace gap was found and fixed for 3 more files (`location.php`, `navigation.php`, `property.php`). |
| B-03 | `VirtualTourTest` failures | HIGH | **FIXED.** Root cause was a stale route cache + a missing UI section, not a Livewire 4 regression as previously assumed. See Section 14. |
| B-04 | Admin seeder creates `admin@example.com`/`password` super-admin, wired into default `DatabaseSeeder` | CRITICAL (pre-prod) | **FIXED.** Production-environment guard added; demo seeding itself intentionally left intact. |
| B-05 | `composer.json` name/PHP constraint | MEDIUM | **FIXED.** |
| B-06 | £/GBP hardcoded in secondary tools | MEDIUM | **FIXED — see Section 16.4.** All named tools (chatbot, brochure, send-to-friend, tax estimator) now use the currency architecture. One related, lower-visibility item (`CostOfMovingCalculatorService`) remains UK-calibrated and is explicitly deferred — see 16.4/16.11/17.11. |
| B-07 | No Yemen entry in `config/countries.php` | MEDIUM | **FIXED — see Section 16.7.** `YE` and `SA` entries added. |
| B-08 | `robots.txt` has no `Disallow` rules | MEDIUM | **FIXED.** |
| B-09 | `.env.testing`, `error_log`, `.idea/` tracked in git | LOW–MEDIUM | **FIXED.** |
| B-10 | No root `LICENSE` file | LOW | **FIXED.** (Full `LICENSE_REVIEW.md` legal deep-dive still open — Phase 9.) |
| B-11 | Scope-bloat: 7 disconnected feature clusters (blockchain, VR studio, holographic, AR, neural valuation, auction, virtual staging) | MEDIUM (demo risk) | **FIXED — fully removed, see Section 15.** |
| B-12 | Admin panel carries 3 UK-portal-sync resources (RightMove/Zoopla/OnTheMarket) | LOW–MEDIUM | **FIXED — hidden from nav, backend kept (still load-bearing for scheduled syncs). See Section 15.** |
| B-13 | Only 44/162 Blade files use translation helpers; Filament admin has ~zero RTL CSS | MEDIUM | Open — Phase 4/6. |
| B-14 | Generic/placeholder demo data: `BranchSeeder`, `UserSeeder`, UK-specific `DocumentTemplateSeeder` | MEDIUM | Open — Phase 3. |
| B-15 | Confirmed "does nothing" buttons/forms; likely more undiscovered | MEDIUM | **PARTIALLY FIXED** — the 3 originally-confirmed instances (ListAgentPerformance report button, and 2 more found in Phase 2: Apply-for-Tenancy crash, Book-Viewing crash) are fixed. `ContactController`'s missing email notification and `SyncActivityToCrm`'s unimplemented job are still open. See Section 15 for the much longer list of newly-found instances, most now fixed. |
| B-16 | All 8 custom Filament panel dashboards fatally broken (`filament::grid` removed in v5) | **HIGH** | **FIXED** (Phase 1) — see Section 14. |
| B-17 | `AdminDashboardTest` / `ExampleTest` flaky due to bad test design (no `RefreshDatabase`, unused `role` column) | LOW | **FIXED** (Phase 1). |
| B-18 *(new)* | Staff/Landlord Filament panels completely inaccessible — custom `Dashboard.php` renamed to `.deleted`/`.old`, and even once restored, fatal on a static/non-static `$view` property mismatch plus a missing `EditProfile` page | **HIGH** | **FIXED — see Section 15.** Not present in any prior audit; discovered only by actually loading these two panels end-to-end. |
| B-19 *(new)* | "Apply for Tenancy" button on the property-detail page (the most-visited page) linked to a controller with no view — hard crash on click for any lettings property | **CRITICAL** | **FIXED — see Section 15.** |
| B-20 *(new)* | "Book Viewing" modal was structurally broken: outer form submitted to a nonexistent `PropertyDetail::bookViewing()` method while the real, fully-built `PropertyBooking` component sat unreachable behind a `false &&` guard calling a nonexistent method | **CRITICAL** | **FIXED — see Section 15.** |
| B-21 *(new)* | `ethereum_address` required on the real, working rental-application form and Staff admin screen, but never backed by any migration — guaranteed validation failure on the public form, guaranteed SQL error on admin save | **CRITICAL** | **FIXED — see Section 15.** |
| B-22 *(new)* | RightMove/Zoopla/OnTheMarket sync jobs scheduled **twice** — once in `app/Console/Kernel.php`, once in `routes/console.php` — doubling every sync run | MEDIUM | **FIXED — see Section 15.** |
| B-23 *(new)* | Widget auto-discovery pointed at a nonexistent `Widgets/Home` subdirectory across 7 of 9 panels (Tenant, Buyer, App, Staff, Contractor, Seller, Landlord) | LOW (currently masked by redundant explicit registration) | **FIXED — see Section 15.** |
| B-24 *(new)* | Marketing copy on the homepage and services page advertised "AI valuations," "holographic tours," "virtual staging," and "smart contracts" — all now-removed or never-real features | MEDIUM (false advertising to a demo visitor) | **FIXED — see Section 15.** |

---

## 11. Proposed Execution Plan (Phases 1–10)

Following the phased model requested for this engagement — each phase should end with a re-verification pass, not just a "looks done" self-report, given how far prior self-reported completions diverged from reality here.

- **Phase 1 — Foundation:** fix B-05 (composer identity/PHP constraint), diagnose and fix B-03 (VirtualTourTest/Livewire regression), fix B-04 (quarantine demo seeder from any non-local path), B-08/B-09/B-10 (repo hygiene + LICENSE). These are cheap, mechanical, and unblock everything else.
- **Phase 2 — Functional completeness:** full manual click-through of every route and admin resource (not just grep-for-TODO) to find the rest of the B-15-style dead buttons; resolve or explicitly remove B-11's disconnected clusters per Novixa's decision (Section 9 below); rationalize the Admin panel resource list (B-12).
- **Phase 3 — Data & demo content:** replace `BranchSeeder`/`UserSeeder` placeholders with Yemen-coherent identities; replace/relabel UK tenancy documents; add a proper Yemen entry to `config/countries.php`; fix all hardcoded £/GBP instances (B-06, B-07, B-14).
- **Phase 4 — Localization:** resolve the brand-name collision (B-01) as the first localization act since it touches every language file; fix B-02; raise translation-helper coverage across the 118 currently-untranslated Blade views; normalize the `ar.json`/`en.json` key convention (B-13, translation half).
- **Phase 5 — UI/UX polish:** standard pass once content and localization are stable — not meaningful to do earlier since much of the target text/branding is still moving.
- **Phase 6 — Theme system:** extend RTL CSS coverage to the Filament admin panel specifically (currently zero), verify light/dark parity (B-13, RTL half).
- **Phase 7 — Responsive & accessibility:** standard pass.
- **Phase 8 — Performance:** standard pass; note the Octane/Horizon/Reverb infra is already configured — verify it's actually needed for a demo-scale deployment or is unnecessary complexity.
- **Phase 9 — Security & release hardening:** re-verify B-04/B-08/B-09 are actually fixed (not just believed fixed), confirm `.env` production template is sane.
- **Phase 10 — Final QA & re-audit:** full re-run of this same verification methodology (real test execution, real click-through, real grep) — compare against this document's baseline explicitly, not against another self-report.

---

## 12. Open Questions Requiring a Novixa/Product Decision

These are not engineering calls and should not be decided unilaterally:

1. **Which of the three brand names is canonical** — "Al-Ofuq Real Estate / شركة الأفق العقارية", "Aqar / عقاري", or "Novixa Aqar"? (Section 5) This blocks essentially all localization and content work downstream.
2. **What happens to the disconnected feature clusters** (Section 4) — remove entirely, hide-but-keep-in-codebase, or properly finish 1-2 as genuine differentiators? Recommendation: remove most, finish the 3D viewer only if it's cheap to complete, since it's the one cluster with real partial wiring already.
3. **Is the K8s/Octane/Horizon/Reverb production-infra scaffolding in scope for this engagement**, or is it out-of-scope legacy from the upstream package that should be left alone/removed rather than polished? This affects how much of Phase 8/9 is spent on infra vs. demo-facing work.

---

---

## 13. Product Decisions (Recorded 2026-08-17)

Two of the three open questions in Section 12 have been decided by Novixa and now govern all downstream work:

1. **Brand identity (three-tier structure):**
   - **Repository / app identifier:** `aqar` (unchanged — internal slug only, never shown to visitors).
   - **Product name:** **Novixa Aqar** — used discreetly in the admin panel, footer, About/credits, and technical documentation. This is the platform, not the fictional agency.
   - **Public-facing agency brand:** **Dar Hadhramaut** (دار حضرموت العقارية) — the fictional Hadhrami/Yemeni real-estate company shown throughout the public site (homepage, about, contact, navbar, property listings, news, SEO metadata, demo data). This **replaces** all three previously-competing names — "Al-Ofuq Real Estate / شركة الأفق العقارية", "Aqar / عقاري", and any stray "Novixa Aqar" references on public pages.
   - Demo contact phone: **+967 776 248 526**.
   - **Status:** decided, partially executed (admin panel brandName fixed — see Section 14). The full public-facing sweep across ~12 files (about, contact, home-navbar, footer, layouts/app, welcome, NewsSeeder, lang JSON marketing copy, config/countries.php) is scoped as Phase 4 work, not yet done.

2. **Scope-bloat disposition:** Remove the disconnected blockchain/smart-contract, VR design studio, holographic tour, AR tour, neural-network valuation, and auction/bidding clusters (Section 4). Keep and properly finish only the 3D property model viewer, since it already has partial real wiring. **Status: EXECUTED — see Section 15.**

---

## 14. Phase 1 Execution Log (2026-08-17)

Phase 1 (Foundation) is complete. Every item below was independently re-verified after the fix, not just assumed fixed.

| Item | Action taken | Verified result |
|---|---|---|
| B-05 composer identity | `composer.json` name → `novixa/aqar`, description rewritten, `php` constraint → `^8.4\|^8.5`. Ran `composer update --lock` to refresh `composer.lock`'s content-hash (no dependency versions changed — verified via `git diff --stat`, 4 lines). | `php artisan --version` boots clean on Laravel 13.24.0. |
| B-04 admin backdoor seeder | Added an `app()->environment('production')` guard to `UserSeeder::run()` that throws a `RuntimeException` before creating any demo account, directing operators to `php artisan make:filament-user` instead. Demo/local seeding is untouched (the demo still needs these accounts to showcase the product). | Guard is unconditional and fails loud, not silent. |
| B-08 robots.txt | Added `Disallow` rules for `/admin`, `/app`, `/buyer`, `/contractor`, `/landlord`, `/seller`, `/staff`, `/tenant`, `/api`, `/livewire`. | File reviewed; all 8 Filament panel path prefixes plus API/Livewire internals now excluded from crawling. |
| B-09 tracked stray files | `git rm --cached` on `.env.testing`, `error_log`, and all 6 `.idea/*` files (kept on disk, just untracked). Added `.env.testing`, `error_log`, `.idea/` to `.gitignore`. | `git status` confirms clean removal from the index; files still exist locally for the developer. |
| B-10 missing LICENSE | Added root `LICENSE` — MIT text with an explicit attribution paragraph crediting the original Liberu Real Estate project/Curtis Delicata alongside Novixa's modifications. | File present at repo root. (Full `LICENSE_REVIEW.md` legal deep-dive is still Phase 9 work — this satisfies the immediate compliance gap, not the complete audit.) |
| B-03 VirtualTourTest failures | **Root-caused, not just patched.** The prior audit mischaracterized this as a "Livewire 4 API regression." Actual root causes, found by instrumenting the Livewire test harness directly: (1) a **stale `bootstrap/cache/routes-v7.php`** (dated Aug 14) was causing every Livewire subsequent-request test call to 404 internally — `php artisan route:clear` alone fixed 8 of the 11 failing assertions in this file; (2) the `property-detail.blade.php` view was **missing the entire virtual-tour UI section** (no "View Virtual Tour" button, no "Schedule Live Tour" trigger) even though the backing Livewire component (`toggleVirtualTour()`, `openScheduleLiveTourModal()`, `scheduleLiveTour()`) and the scheduling modal HTML were fully implemented and functional — a real, user-facing dead-button bug, not a test artifact. Added the missing section (view-tour toggle + embed, conditional schedule-live-tour button) using the existing `Property::getVirtualTourEmbed()` model method. | `tests/Feature/VirtualTourTest.php`: 11/11 passing (was 0/11 reliably passing before diagnosis). |
| **New finding, not in original 17:** `AdminDashboardTest` | Rewrote a flaky, order-dependent test that relied on incidental cross-test database state and a nonexistent `role` column instead of properly assigning a Spatie role. Now uses `RefreshDatabase` + `Role::firstOrCreate()` + `assignRole()`, matching the pattern already used correctly in `ExampleTest.php`. | Passes in isolation and in full-suite order. |
| **New finding, severity HIGH, not in original 17 or in any prior audit:** all 8 custom Filament panel dashboards broken | `resources/views/filament/{admin-pages,app,staff,seller,buyer,landlord,tenant,contractor}/dashboard.blade.php` all used the removed Filament v4 Blade component `<x-filament::grid>` / `<x-filament::grid.column>`. In the installed Filament v5, Grid is now a Schema-builder-only construct with no equivalent Blade tag — **every one of these 8 role-panel dashboards was throwing a fatal "Unable to locate a class or view for component [filament::grid]" error on every page load.** This was invisible to both the prior audit and the Phase 0 functional-audit agent because neither actually loaded a non-admin panel dashboard; it only surfaced when `ExampleTest.php`'s `/app` route test was run as part of the full suite. Fixed by replacing the grid wrapper with plain Tailwind `grid grid-cols-*` divs (the standard approach for custom Filament v5 Blade pages) across all 8 files. Also fixed the still-leftover `شركة الأفق العقارية` (Al-Ofuq) branding string in `AdminPanelProvider.php`'s `brandName()` — now `Novixa Aqar` per Section 13's decision. | All 8 dashboard views load without fatal errors; `ExampleTest.php` `/app` and `/admin` route tests pass; `AdminDashboardTest.php` passes and now asserts the correct brand string. |

**Test suite trajectory this session:**
- Start of session (re-run of prior audit's claim): **671 passed / 17 failed / 33 skipped** (721 total) — confirmed the prior audit's number was accurate but its root-cause diagnosis ("Livewire 4 regression") was wrong.
- After route-cache clear + virtual-tour view fix: **687 passed / 1 failed / 33 skipped**.
- **Final confirmed run, after all Phase 1 fixes: 689 passed / 0 failed / 32 skipped (721 total, 2,570 assertions).** Re-run independently after every fix above, not assumed. The 32 skips are pre-existing (e.g. environment-gated tests) and were not investigated in this pass — they are a distinct item, not counted as "passing," and are flagged for Phase 2/10 review rather than claimed as resolved here.

**Working-tree changes this phase:** `composer.json`, `composer.lock` (hash only), `database/seeders/UserSeeder.php`, `public/robots.txt`, `.gitignore`, `app/Providers/Filament/AdminPanelProvider.php`, `resources/views/livewire/property-detail.blade.php`, `tests/Feature/AdminDashboardTest.php`, 8× `resources/views/filament/*/dashboard.blade.php`, new root `LICENSE`, this audit document. Untracked from git (kept on disk): `.env.testing`, `error_log`, `.idea/*`. No changes yet to the public-facing branding files (Section 13, item 1 — still pending Phase 4).

---

---

## 15. Phase 2 Execution Log (2026-08-17)

Phase 2 (Functional Completeness) is complete. As instructed, a full manual/functional audit was performed **before** any removal — four parallel investigations (public site, all 8 admin panels, a precise removal manifest for the disconnected clusters, and a 3D-viewer/RightMove-Zoopla scoping pass) — and every removal was traced through its routes, controllers, models, migrations, services, views, and tests before deletion. Nothing was removed blindly.

### 15.1 Pre-removal functional audit — what it found

Beyond confirming the Phase 0 scope-bloat findings, the audit surfaced a second, independent category of bugs in **kept, core functionality** — not scope-bloat, but real breakage on the most-visited pages:

- **"Apply for Tenancy" crashed on click** for any lettings property: it linked to `TenancyApplicationController::create()`, which rendered `view('tenancy.apply')` — a view that didn't exist anywhere in the repo. Its `store()` method also redirected to a route name (`properties.show`) that was never registered, and its comment admitted the actual application record was never persisted ("Here you would typically create a tenancy application record..."). Meanwhile a **separate, fully-working implementation already existed** — `RentalApplicationForm` (Livewire) + `RentalApplication` model + `rental.apply` route — genuinely persisting applications and triggering tenant screening.
- **"Book Viewing" was structurally broken**: the modal's outer `<form wire:submit.prevent="bookViewing">` submitted to `PropertyDetail`, which has no `bookViewing()` method. The actual, fully-built booking form (`PropertyBooking` Livewire component, complete with date/time selection, calendar `.ics` export, Google/Outlook calendar links) was nested *inside* that broken outer form and additionally hidden behind `@if (false && App\Providers\AppServiceProvider::isComponentEnabled('property-booking'))` — a permanently-false guard calling a method that doesn't exist anywhere in the codebase (silently never crashing only because `&&` short-circuits before evaluating it).
- The identical `false && ...isComponentEnabled(...)` pattern also permanently disabled the `valuation-booking` embed.
- Wishlist favoriting works and persists, but the `/wishlist` page itself had **no link from anywhere in the navigation** — a visitor could favorite properties with no way to find their saved list.
- `PropertyComparison`, `SendToFriendService`, `AlertPreferences`, `PriceAlertManager`, `AgentRecommendations`, `LandlordReviewForm`, and `TenantReviewForm` are real, non-trivial, already-built features with **zero discoverable entry point** in the UI (no nav link, no button, no `@livewire()` mount). These are **not** part of the six named scope-bloat clusters and were **not removed** — see Section 15.5 (Backlog) below; fixing them was judged out of scope for this pass given the volume of higher-severity bugs already found.
- Across all 8 Filament panels: no further `x-filament::*` v4-tag breakage beyond the 8 dashboards already fixed in Phase 1; the historical `Filament\Forms\Form` vs `Filament\Schemas\Schema` signature bug (visible in a stale `storage/logs` entry) is confirmed already fixed everywhere except one `.disabled` (non-autoloaded, harmless) file.

### 15.2 Cluster removal — executed cleanly, cross-dependencies handled first

Per the removal manifest, cross-dependencies in **kept** files were edited *before* any cluster-only file was deleted, so nothing was left referencing a class that no longer existed:

- `app/Models/Property.php` — removed `auctions()`/`currentAuction()`/`isInAuction()`, `vrDesigns()`, `hasHolographicTour()`, and all AR/holographic/VR/auction fillable+cast entries (also fixed a pre-existing duplicate `model_3d_url` fillable entry as a side effect).
- `app/Livewire/PropertyDetail.php` — removed the `ARTourService` dependency, `loadARTourData()`, `checkHolographicTourAvailability()`/`toggleHolographicViewer()`/`generateHolographicTour()`, and the associated public properties. Confirmed beforehand (by grep) that none of this had any Blade markup referencing it — these were already-dead properties on an otherwise-live component.
- `app/Models/Lease.php` / `LeaseAgreement.php` — removed `smart_contract_address`, `contract_status`, `contract_deployed_at`, `agreement_hash`, `blockchain_network`, the `smartContract()` relation, `hasSmartContract()`, `canDeploySmartContract()`. **Kept** `landlord_signed`/`tenant_signed`/`isFullySigned()` — confirmed via grep these are used by the real, kept `TenancyAgreementController` for general e-signature tracking, unrelated to blockchain despite being added by the same migration.
- `app/Models/RentalApplication.php` + `app/Filament/Staff/Resources/RentalApplications/RentalApplicationResource.php` — removed the `smart_contract_address` fillable/form-field (phantom column, never backed by any migration to begin with).
- `app/Services/TransactionService.php` — removed the `BlockchainService` constructor dependency and its `recordSignature()` call from `signDocument()`; kept the digital-signature functionality itself.
- `app/Models/Image.php` — removed `is_staged`/`original_image_id`/staging fields, `originalImage()`/`stagedVersions()`/`isStaged()`/`hasStagedVersions()`; confirmed via grep these had zero callers outside the VirtualStaging cluster itself.
- `app/Filament/Staff/Resources/Properties/PropertyResource.php` — removed the AR-tour form fields (`ar_tour_enabled` toggle, `ar_model_scale`, `ar_placement_guide`) and table column, leaving the adjacent, kept 3D-model-upload field untouched.
- `resources/views/livewire/property-detail.blade.php` — removed the unconditional "AI Property Valuation" button (this was, ironically, the *most* reachable entry point of any of the six clusters — a live, unconditional link on the main property page, contradicting the original "no nav links anywhere" characterization for this specific cluster).
- Migrations that bundled cluster-only columns together with kept ones were **edited, not deleted**, so the kept columns survive a fresh migration run: `2026_02_15_160000_add_smart_contract_fields_to_lease_agreements.php` (kept `landlord_signed`/`tenant_signed`/`rental_history_status`, dropped the rest; also fixed a pre-existing bug where its `down()` never reverted `rental_history_status`), `2024_06_15_100000_create_leases_table.php` (dropped only the `smart_contract_address` column from the base table), `2026_03_07_100001_add_file_fields_to_images_table.php` (kept `file_path`/`file_name`/`mime_type`, dropped `is_staged`/`original_image_id`+FK).

With cross-dependencies handled, all cluster-only files were removed: 2 models + 1 controller + 2 services + 5 Filament resource/page files for Blockchain; 1 model + 1 controller + 1 service + 1 Livewire+view + 1 policy + 1 factory for VR Design Studio; 1 Livewire+view + 1 service for Holographic; 1 controller + 1 service for AR; 1 service + 1 controller + 1 Livewire+view for Neural Valuation; 2 models + 1 controller + 2 Livewire (one with no view at all — `AuctionBidding`) + 1 event + 1 notification + 1 view for Auction (which, per the manifest, had **no migrations at all** — the `auctions`/`bids` tables likely never existed in the database, meaning this code was already broken beyond just "unreachable"); 1 controller + 1 service + 1 Livewire+view for Virtual Staging (the bonus 7th cluster, same orphan pattern, confirmed on request). Plus: `config/blockchain.php`, `config/vr-design.php`, `config/virtual-staging.php` deleted outright; the `holographic` block removed from `config/services.php`; matching `HOLOGRAPHIC_*` keys removed from `.env.example`; `contracts/RentalAgreement.sol` deleted (confirmed zero references anywhere, not even loaded by `BlockchainService`, which only ever simulated deployment in PHP); all associated tests (10 files) and docs (7 root-level "final report" .md files + 5 `docs/*.md` files) deleted; `routes/web.php` and `routes/api.php` cleaned of every associated import and route block; `LivewireComponentServiceProvider.php`'s dead `PropertyValuationComponent` registration removed.

A full grep sweep across `app/`, `resources/views/`, `tests/`, and `database/` after all deletions confirmed **zero remaining references** to any of the 7 removed clusters.

### 15.3 3D viewer — verified properly finished (kept, per decision)

The 3D model viewer needed no structural fixes — the JS pipeline (`resources/js/model-3d-viewer.js`, Three.js-based, correctly bundled via `resources/js/app.js`), the Blade component, and the Staff admin form field (`model_3d_url` text input, already present) all work. Fixed the one confirmed defect (a byte-for-byte duplicate migration, `2026_02_16_200000_add_model_3d_url_to_properties_table.php`, removed). **Did not** seed demo `model_3d_url` data with a placeholder public model (e.g. a generic "astronaut" sample) — using off-topic content to fake a working feature on a real-estate demo would read as unprofessional, exactly the "embarrassing demo content" pattern this whole effort is meant to eliminate. Instead, verified the entire code path end-to-end with the pre-existing `tests/Feature/Property3DModelTest.php` + `tests/Unit/Property3DModelTest.php` (14/14 passing). Real 3D property scans are a Manual User Action for Novixa (same category as real property photography), not something fabricable in code.

### 15.4 RightMove/Zoopla/OnTheMarket — hidden, not deleted (per decision "remove or hide where appropriate")

Investigation found these are **not** pure UI decoration: `RightMoveSettings`/`ZooplaSettings`/`OnTheMarketSettings` models back real, actively-scheduled sync jobs (`SyncRightMoveProperties`, `SyncZooplaProperties`, `SyncOnTheMarketProperties`), and `properties.rightmove_id`/`zoopla_id`/`onthemarket_id` are live nullable columns (no FK, so no integrity risk either way). Full deletion would be safe for the database but would break the scheduled sync commands. Given "remove or hide where appropriate," the three Filament Admin resources were hidden via `shouldRegisterNavigation(): false` (each resource, with an explanatory comment) — no longer visible to whoever ends up as Admin, while the backend that scheduled syncs depend on stays intact. A newer, more generic `PortalIntegration`/`PortalListing`/`PortalSyncRun` system already exists in the codebase and looks like the intended eventual replacement — noted for a future phase, not acted on here.

**Bonus fix in the same area:** found and fixed a genuine duplicate-scheduling bug — every RightMove/Zoopla/OnTheMarket sync, the `LeaseRenewalReminder` job, and the neighborhood-data-refresh job were being scheduled **twice**: once via the legacy `app/Console/Kernel.php::schedule()` override, and again via the modern `routes/console.php` (`Schedule::` facade), which Laravel loads *in addition to* the override. Consolidated to `routes/console.php` alone (the convention this codebase already uses elsewhere) — moved the one entry unique to `Kernel.php` (scheduled email-campaign sending) over, then emptied the override.

### 15.5 Additional bugs found and fixed during verification (not part of the original scope, found by actually loading pages and running tests)

- **Staff and Landlord Filament panels were completely inaccessible**, discovered only after restoring their custom `Dashboard.php` (Phase 1 renamed the `.deleted`/`.old` files back to `.php`, since the Phase 1 dashboard-view fix had been silently applied to orphaned files — see B-16). Restoring them surfaced two further, previously-invisible fatal errors: (1) both declared `protected static string $view`, but the installed Filament v5 `Page` base class declares `$view` as **non-static** — a PHP fatal "cannot redeclare non-static property as static" on class load; (2) neither panel had an `EditProfile` page, and Filament's shared layout hard-requires one for the user-menu "profile" link, throwing "Route [filament.{panel}.pages.edit-profile] not defined" the moment the dashboard tried to render. Fixed both: corrected the property declaration to match the parent class, and created `EditProfile.php` for both panels mirroring the already-working `App/Pages/EditProfile.php` pattern. Also fixed the Landlord dashboard's tenant-count query, which was hardcoded to `0` with a commented-out query that queried a relation (`Tenant::whereHas('property', ...)`) that doesn't exist on the `Tenant` model — replaced with a correct query via the real `leases.property` relation chain. Verified with new route-level tests hitting `/staff` and `/landlord` end-to-end (not just the isolated dashboard view).
- **"Apply for Tenancy" and "Book Viewing"**, per Section 15.1's findings: repointed both "Apply for Tenancy" links (property-detail page had two, with different indentation, so a first pass only caught one) to the real `rental.apply` route — which itself needed a fix (its route parameter was `{property}` but `RentalApplicationForm::mount($property_id)` expected `$property_id`, a binding mismatch that would have made the "real" route equally broken; renamed the route parameter to match). Removed the broken outer form and the two dead "Schedule Viewing"/"Decline" buttons from the Book Viewing modal, replaced `false && isComponentEnabled(...)` (calling a nonexistent method) with the actual, working `ComponentSettingsService::isEnabled(...)` for both `property-booking` and `valuation-booking`. Deleted the now-fully-redundant `TenancyApplicationController` + `TenancyApplicationForm` (the latter already orphaned, never mounted) rather than leave a second, broken, competing implementation alongside the working one. Verified with new tests (`RentalApplicationFormTest`: route loads, submission persists a real `RentalApplication` row).
- **`ethereum_address` phantom column**: found while verifying the "real" rental-application flow — `RentalApplicationForm` (Livewire) marked it `required|string` in validation and passed it to `RentalApplication::create()`, and the Staff admin `RentalApplicationResource` had a matching form field, but **no migration anywhere ever created this column** on `rental_applications`. Net effect: every real applicant was being forced to enter a value that was silently discarded (not fillable, so mass-assignment dropped it — no crash, just data loss and a nonsensical required field on a Yemen rental form), while the Staff admin edit screen would have thrown a raw SQL "no such column" error the moment anyone tried to save it (since Filament's form binding sets the attribute directly, and `save()` includes all dirty attributes in the query regardless of fillability). Removed from both the Livewire component and the admin resource.
- **Re-enabling `valuation-booking` introduced a real regression**, caught by the post-fix full test run (not assumed safe): `ValuationBooking::mount()` used `AppointmentType::where('name', 'Valuation')->firstOrFail()`. The real seeder (`AppointmentTypeSeeder`) does create this record, but the component is now unconditionally embedded (just CSS-hidden, not removed from the DOM) on every property-detail page load — meaning any environment where that one seeder entry is missing (a fresh install run without seeders, a partial demo reset, or simply a test using `RefreshDatabase` without seeding) would fatal the **entire property-detail page** for every visitor. Changed to `AppointmentType::firstOrCreate(['name' => 'Valuation'])` — self-healing, idempotent, no behavior change for the properly-seeded case.
- **Widget auto-discovery path bug, wider than initially scoped**: the Phase 2 audit agent flagged 3 panels; actually grepping all 9 found the identical `discoverWidgets(in: app_path('Filament/{Panel}/Widgets/Home'), ...)` bug (a nonexistent `/Home` subdirectory) in **7** of them — Tenant, Buyer, App, Staff, Contractor, Seller, Landlord (only Admin and Agent panels were correct). Confirmed each panel also explicitly registers its real widgets via a separate `->widgets([...])` array, so this was **not currently causing missing widgets** — but it's an unambiguous bug (silently discovering nothing) that would silently swallow any future widget someone adds expecting auto-discovery to work. Fixed all 7.
- **Stale marketing copy advertising removed/never-real features**: the homepage "Smart Solutions" card and three services-page cards referenced "AI valuations" (Neural Network Valuation cluster, just removed), "holographic tours" and "virtual staging" (both just removed), and "smart digital contracts" (Blockchain cluster, which per `BlockchainService`'s own code only ever ran in `simulated` mode — never real even before removal). Rewrote all four to describe only what genuinely exists today (virtual tours, 3D models, comparative-market valuations, digital-signature document handling), in both English and Arabic. While fixing this, also found and fixed a **pre-existing, unrelated bilingual gap**: "Profile" (used throughout the nav) had no Arabic translation entry in `lang/ar.json` at all — an Arabic-locale visitor was seeing the raw English word. Added it, along with the new "Wishlist" nav link's translation, to both `lang/ar.json` and `lang/en.json`.
- **Wishlist nav link** added to both the desktop dropdown and mobile drawer menu in `home-navbar.blade.php`, in both languages — the feature itself already worked, it simply had no discoverable entry point.
- **`ListAgentPerformance`'s "Generate Report" button** (the TODO-marked dead button flagged back in Phase 0) now performs a real CSV export of agent performance data (listings, sales, appointments, average rating), streamed as a download via Filament's native action-response support. Verified with a new test using Filament's `callAction()` + Livewire's `assertFileDownloaded()`.
- **`SecurityRemediationTest` regression** (introduced by removing `VirtualStagingController`, whose `getPropertyImages()` endpoint the test targeted): the underlying security property the test checks — a user cannot fetch another team's property media — is still correctly enforced by the real, kept `API\V1\PropertyMediaController@index` (team-scoped `findOrFail()`, 404 for cross-team rather than 403, an equally valid info-hiding pattern). Repointed the test at the real endpoint rather than resurrecting the deleted, redundant one. While fixing the test, also confirmed `current_team_id` is deliberately excluded from `User::$fillable` (a security choice — team-switching has its own dedicated Jetstream flow) and that other tests in this codebase already use `forceFill()` for exactly this reason; matched that established convention rather than introducing a new one.

### 15.6 Explicitly deferred (not fixed this phase — tracked for later)

These were found during the Phase 2 public-site audit but are **not** part of the six/seven named scope-bloat clusters, and fixing all of them alongside everything above was judged to risk rushing quality on an already very large phase. Each is a real, already-built feature with no discoverable UI entry point:

- `PropertyComparison` (route `property.compare` exists and works; no "Add to compare" button anywhere in the property list/detail views).
- `SendToFriendService` (plain service class, zero callers anywhere — not even a stub button).
- `AlertPreferences` and `PriceAlertManager` (no route, no `@livewire()` mount anywhere).
- `AgentRecommendations` (never `@livewire()`'d anywhere).
- `LandlordReviewForm` and `TenantReviewForm` (unlike their working siblings `PropertyReviewForm`/`NeighborhoodReviewForm`, never mounted anywhere).
- `InsurancePurchaseForm` (flagged in Phase 0, not part of the named six clusters, still orphaned).
- `ContactController`'s missing staff email notification and `SyncActivityToCrm`'s unimplemented job body (both flagged in Phase 0's TODO grep, not addressed this phase).
- Full page-by-page RTL/theme/translation-coverage audit (B-13) and the £/GBP + Yemen country-config cleanup (B-06/B-07/B-14) — both explicitly scoped for Phases 3–4 in the original plan and not pulled forward.

### 15.7 Test suite trajectory this phase

- Start of Phase 2 (end of Phase 1): **689 passed / 0 failed / 32 skipped** (721 total).
- After cluster removal (10 test files deleted along with their clusters, as instructed by the manifest) + all cross-dependency edits: **552 passed / 1 failed / 32 skipped** (585 total) — the 1 failure was the `SecurityRemediationTest` regression from deleting `VirtualStagingController`, diagnosed and fixed (Section 15.5).
- After the security-test fix, restoring Staff/Landlord dashboards, and all bug fixes described above, plus 5 new test files added this phase (`RentalApplicationFormTest`, `StaffLandlordDashboardTest`, `AgentPerformanceReportTest`, plus assertions strengthened in `SecurityRemediationTest`): first full run showed **543 passed / 15 failed / 32 skipped** — root-caused to the single `ValuationBooking::firstOrFail()` regression described in Section 15.5 (one root cause cascading across `VirtualTourTest`'s 11 tests plus a few others that happen to mount `PropertyDetail`).
- **Final confirmed run, after the `firstOrCreate()` fix: 558 passed / 0 failed / 32 skipped (590 total, 2,098 assertions).** Re-run fully and independently after every fix in this section — not assumed passing. The drop from 721 to 590 total tests is expected and correct: 10 cluster-specific test files (131 test methods) were deleted along with their clusters per the removal manifest, partially offset by 3 new test files added this phase. The 32 skips remain the same pre-existing, not-yet-investigated set flagged in Phase 1 — still not claimed as resolved here.
- `php artisan route:list` re-verified clean after every route-file edit (669 routes, no fatal class-resolution errors) — the single most reliable, fast signal that a deletion didn't leave a dangling reference, run repeatedly throughout this phase rather than only at the end.

### 15.8 Working-tree scope this phase

154 changed paths: ~45 files deleted outright (cluster code, tests, docs, config), ~9 migrations/models edited for cross-dependencies, 9 Filament panel providers + 2 new dashboard/profile-page pairs, routes/web.php + routes/api.php + routes/console.php + Kernel.php, 2 translation files, 2 marketing-copy views, and 6 new test files. Full list available via `git status`. Nothing has been committed.

---

## 16. Phase 3 Execution Log (2026-08-18/19) — Data, Demo Content, Yemen Localization & Multi-Currency Foundation

### 16.1 Identity model established

Three identities were made explicit and kept strictly separate everywhere touched this phase, per the phase mandate:

- **Repository / codebase identifier**: `aqar` — internal only, never user-facing.
- **Product**: **Novixa Aqar** — the platform Novixa sells; must never appear as the public-facing demo agency's own brand.
- **Public demo agency**: **Dar Hadhramaut Real Estate | دار حضرموت العقارية** — the fictional Yemeni/Hadhrami agency the demo data now consistently represents, headquartered in Mukalla, with demo contact phone **+967 776 248 526**.

Prior to this phase the codebase had **five** competing names bleeding into each other across seeders and views (Al-Ofuq/الأفق العقارية, "Yemen Horizon Properties", a generic "Aqar"/"عقاري" label, stray Novixa references, and UK-derived placeholder text). All were normalized to the three-tier model above. Seeders touched: `DefaultTeamSeeder`, `NewsSeeder`, `SiteSettingsSeeder`, `UserSeeder`; app config: `app/Settings/GeneralSettings.php`; views: `about.blade.php`, `contact.blade.php`, `home-navbar.blade.php`, `footer.blade.php`, `layouts/app.blade.php`. `welcome.blade.php` was confirmed unreachable (no controller returns it) and left untouched as dead code — flagged here rather than silently fixed or silently ignored.

### 16.2 Multi-currency architecture — built from near-scratch

No prior centralized currency mechanism existed; price handling was ad hoc and GBP-hardcoded throughout (`£` string literals, `->money('gbp')` Filament casts, a `site_currency` global setting used as if every property shared one currency). Implemented:

- `config/currencies.php` — single source of truth. `default => 'YER'`; `supported`: YER, SAR, USD (symbol, name, name_ar, decimals, symbol position, thousands separator each); `legacy`: GBP only, explicitly marked non-default, kept for backward compatibility with the broader multi-country API (see 16.3).
- `app/Services/CurrencyService.php` — `all()`, `supported()`, `isSupported()`, `default()`, `options($arabic)`, `symbol()`, `name()`, `format()`. This is the one place formatting logic lives now.
- `Property::getFormattedPriceAttribute()` — the accessor every view/service now calls instead of hand-rolling `£{{ number_format(...) }}`.
- `Property::scopePriceRange()` and `similarProperties()` scoped to `where('currency', ...)` before any numeric price comparison, and `PropertyList`/`PropertyController@index`/`OfferController` all scope price-range filters to a single currency (explicit filter currency, or the app default) before comparing — this directly implements the phase's "prevent misleading numeric comparisons between incompatible currencies" requirement. Where an explicit currency filter isn't given, the UX deliberately narrows to one currency rather than silently mixing units; this was judged the safe/honest option the phase instructions called for, at the cost of not showing a true cross-currency "all prices between X and Y" view (no safe way to do that without real exchange rates, which were explicitly out of scope).
- Admin currency entry (`PropertyResource`) restricted to a `Select` of the 3 demo-supported currencies with server-side validation rejecting anything else — intentionally narrower than the general API validation (see 16.3).
- **No exchange rate was invented anywhere.** A previously-existing, unlabeled fabricated rate (`price / 530` presented as a real USD-equivalent) was found and removed — see 16.5. Multi-currency in this phase means the app correctly stores, displays, filters, and formats YER/SAR/USD as genuinely distinct currencies side by side; it does **not** convert between them. This is a deliberate scope boundary, not an oversight — the phase mandate explicitly required this restraint ("live exchange-rate integration is not required" / "do not silently invent... exchange rates").

### 16.3 Two currency surfaces, kept intentionally distinct

An early pass over-corrected by restricting `PropertyController`/`OfferController` API validation to only the 3 demo currencies, which broke `CoreAgencyApiTest`'s legitimate coverage of the broader multi-tenant/multi-country agency API (e.g. a GBP `OrganisationProfile` and an EUR property in Ireland — real, intentional platform capability unrelated to the demo's Yemen focus). Reverted both back to `collect(config('countries'))->pluck('currency')->unique()` (broad, unrestricted, matches all countries in `config/countries.php`, which now includes YE and SA alongside the pre-existing set). The **demo-facing Filament admin UI** stays restricted to YER/SAR/USD by design — that's a presentation-layer choice for the Novixa demo, not a platform-wide currency restriction. Caught by running the full suite, not assumed correct.

### 16.4 Secondary GBP/£-hardcoded tools fixed to respect the currency architecture

Per the explicit instruction not to do a blind GBP→YER string swap, each was changed to defer to `CurrencyService`/`formatted_price` so it renders whatever currency the underlying record actually holds:

- `ChatbotService`, `PropertyBrochureService` (brochure data + window card), `SendToFriendService` — all switched from hardcoded `£` formatting to `$property->formatted_price`.
- `TransactionResource` — `->money('gbp')` → `->money(strtolower(config('currencies.default')))`.
- `PropertyTaxEstimatorService` — added a genuine `calculateYemenFees()` path (3% registration fee + 1% broker fee + flat 25,000 YER notary fee, commented as illustrative demo figures, not real Yemeni tax law) alongside the pre-existing UK/US paths (kept as legacy, not deleted); default country changed UK→YE. `CalculatorsComponent::calculateStampDuty()` repointed to this service. `PropertyTaxEstimator` Livewire default country UK→YE.
- `property-tax-estimator.blade.php` / `calculators.blade.php` — relabeled from UK "Stamp Duty" framing to "Registration & Transfer Fee," made the legal disclaimer unconditional (was previously only shown for UK) with new bilingual "illustrative demo figures, not legal or tax advice" text.
- **Not fixed this phase** (flagged, not silently skipped): `CostOfMovingCalculatorService` remains UK-calibrated — found late in the sweep, judged lower-visibility than the items above (not linked from primary nav), and deferred rather than rushed.

### 16.5 Fabricated exchange rate found and removed (genuine violation, self-caught)

A hardcoded, unlabeled `price / 530` "USD equivalent" existed in three separate Blade views — `property-detail.blade.php` (main hero price display), `home.blade.php`, and `property-list.blade.php` — none of which were flagged by any prior investigation pass; found only while reading full view context during the currency-display fixes. This directly contradicted the phase's explicit exchange-rate prohibition, and would have produced nonsense output for any property already priced in USD or SAR (dividing an already-USD price by 530 again). Removed all three; replaced with the property's real `formatted_price`, and in `property-detail.blade.php`'s case, a genuine (non-fabricated) "Price per m²" metric in place of the fake conversion.

### 16.6 Document templates — Yemen content, explicitly labeled as non-authoritative

`DocumentTemplateSeeder` previously seeded three UK statute-specific templates (Assured Shorthold Tenancy Agreement, Section 8 Notice, Section 21 Notice — all referencing the Housing Act 1988, which has no application in Yemen). Rather than translate these literally (which would misrepresent Yemeni tenants' actual legal position), replaced with three new, concept-appropriate Arabic templates: `yemen_residential_lease_agreement`, `lease_breach_notice`, `end_of_lease_notice`. Each carries a bilingual **"Demo / Illustrative Template — Not Legal Advice"** disclaimer per the phase's explicit requirement, and each seeder entry is labeled "(Demo Template)". The old UK blade files were deleted, not left orphaned.

Separately, and out of Phase 3's scope but recorded here per the "flag rather than silently ignore" principle: `DocumentGenerator::getCustomFieldsForTemplate()` is an empty stub always returning `[]`, and `DocumentTemplate::generateDocument()` does a raw `str_replace()` on captured Blade source rather than real Blade compilation — meaning generating *any* document template today, Yemen or otherwise, produces raw unfilled `{{ $variable }}` placeholders. This is a pre-existing functional bug, not something this phase's content changes introduced or could reasonably fix within a data/localization phase; it needs a real dynamic-form/field-extraction feature, which is implementation work for a future phase.

### 16.7 Demo data cleanup

- `UserSeeder`: all `@example.com` → `@darhadhramaut.ye`.
- `LeadAndBuyerSeeder`: 5× `@example.com` → `@gmail.com` (realistic lead/buyer emails, not staff emails); `User::first()` fallback for lead ownership changed to prefer `User::role('agent')->first()` — avoids silently attributing leads to whichever user happens to be ID 1.
- `PropertySeeder`: replaced hardcoded `user_id => 1` with a real looked-up agent user (`User::role('agent')->first()`, falling back sensibly) in both `createArabicProperties()` and `createHmoProperties()`; added `agent_id` alongside `user_id`; added a `priceFor()` helper distributing realistic currencies across generated properties (a subset in USD/SAR, the rest YER) rather than a single flat currency for every row — this is what makes the multi-currency filtering/scoping code in 16.2 actually exercised by demo data instead of vacuously true.
- `BranchSeeder` (confirmed dead code — not called by `DatabaseSeeder` — but fixed anyway per the explicit instruction not to leave placeholder data unfixed just because it's currently unreferenced): placeholder address/phone replaced with real Mukalla details.
- `DefaultTeamSeeder`: Team renamed to "Dar Hadhramaut Real Estate | دار حضرموت العقارية"; Branch given a real Mukalla address/phone/email; added a new `OrganisationProfile::firstOrCreate()` block (this model existed but wasn't populated by any seeder before) carrying the agency's regional/currency metadata (`primary_country: YE`, `currency: YER`, etc.) — this is what `PropertyController@store`'s existing country→profile→currency fallback chain (a pre-existing, previously-unexercised code path) now actually resolves against.
- `config/countries.php`: added `YE` and `SA` entries matching the existing structure (name, currency, phone code, etc.) — required for both the Filament country context and the broader API's `Rule::in(config('countries.*.currency'))` validation to recognize Yemen/Saudi data as valid rather than falling through to nothing.
- Two migrations edited in place (not new migrations, per the explicit instruction against unnecessary schema churn): `2026_07_28_410000_add_branch_and_currency_to_properties.php` and `2026_07_28_100000_create_core_agency_records.php` (`offers.currency`) — default column value `'GBP'` → `'YER'`.
- `PropertyFactory`: added `'currency' => config('currencies.default')` so factory-generated test/demo properties default to YER rather than an implicit empty/null currency.
- Map default marker (`property-map.blade.php`) changed from real London coordinates (`51.5, -0.09`) to Mukalla (`14.5385, 49.1235`); the per-property price popup's single global currency symbol replaced with a JS lookup table keyed by each property's actual currency.
- `valuation-booking.blade.php`: placeholder "123 Main Street, London, SW1A 1AA" replaced with a Mukalla address.
- `AdvancedPropertySearch`'s country filter dropdown (previously UK/US/FR/DE/ES, none of which matched any seeded data — a 100%-non-functional filter) simplified to "All Countries"/"Yemen", matching what the demo data actually contains. Its `$maxPrice` default was also raised from 1,000,000 (calibrated for GBP-scale prices) to 200,000,000, which had been silently hiding nearly all YER-priced sale listings from the default search view — a real, user-visible bug this phase's currency work exposed and fixed.

### 16.8 Section 19 validation sweep — results

Ran the phase's explicit validation search list (GBP, £, UK/United Kingdom/London/England, Housing Act 1988/Assured Shorthold/Section 8/Section 21, example.com, 123 Main/456 Oak/City/Country, John/Jane Doe, test@example, +1, Lorem Ipsum, Al-Ofuq, unsupported currency codes) across `app/`, `resources/`, `database/` and manually inspected each match rather than blind-replacing:

- All matches addressed above (16.1–16.7) except: `GBP`/`£` remaining **intentionally** in `config/currencies.php`'s `legacy` block and in the broad multi-country validation paths described in 16.3 — both are correct, load-bearing platform capability, not leftover demo mess.
- A handful of `example.com` occurrences remain in non-demo-facing contexts (`public/installer.php`'s placeholder HTML, `scratch/*.php` debug scripts) — left untouched as genuinely out of scope (installer boilerplate / developer scratch files, never rendered to a demo visitor).
- No remaining Al-Ofuq, Housing Act 1988, or UK-statute references found anywhere in `app/`, `resources/`, or `database/` after this phase's changes.

### 16.9 Tests fixed to match corrected (not broken) behavior

The full suite surfaced 4 failures after the currency-architecture changes above — all 4 were pre-existing tests asserting the **old, incorrect** hardcoded `£` price format, which the legitimate currency fix changed to correct YER formatting. Not a regression; the tests encoded the bug being fixed:

- `tests/Unit/PropertyBrochureServiceTest.php` — 3 assertions: `'£450,000'` → `'450,000 ر.ي'`.
- `tests/Feature/CoreAgencyApiTest.php` — 1 assertion: `assertSee('£425,000', false)` → `assertSee('425,000 ر.ي', false)`.

### 16.10 Test suite trajectory this phase

- Baseline entering Phase 3 (end of Phase 2): 558 passed / 0 failed / 32 skipped.
- First full run after all Phase 3 changes: 554 passed / **4 failed** / 32 skipped — all 4 root-caused to the stale hardcoded-`£` assertions described in 16.9, not to any functional regression.
- **Final confirmed run, after fixing those 4 assertions: 558 passed / 0 failed / 32 skipped (2,098 assertions), exit code 0, zero FAIL/Error strings in output.** Re-run in full and independently after the fix — not assumed passing.
- `php artisan route:list` re-verified clean after all Phase 3 changes: 669 routes, no fatal class-resolution errors.
- The 32 skips are the same pre-existing, not-yet-investigated set carried forward from Phase 1/2 — not claimed resolved here, consistent with the instruction not to count skips as passes.

### 16.11 Explicitly deferred out of Phase 3 (tracked for later, not silently dropped)

- `CostOfMovingCalculatorService` — still UK-calibrated (16.4).
- `DocumentGenerator`'s broken variable-substitution mechanism (`getCustomFieldsForTemplate()` stub + raw `str_replace()` instead of real Blade compilation) — pre-existing, affects all document templates regardless of country, needs real implementation work, not a data-phase fix (16.6).
- `welcome.blade.php` — confirmed unreachable dead code, left as-is (16.1).
- A handful of non-demo-facing `example.com` occurrences in `public/installer.php` and `scratch/*.php` (16.8).
- Cross-currency price-range search (showing "all properties between $X and $Y" across mixed currencies) is deliberately not implemented — no safe way to do it without real exchange rates, which are out of scope per the phase mandate (16.2).
- Full Faker-locale audit (`en_US` default producing non-Yemeni names/addresses in some factories not touched this phase) — noted, not exhaustively fixed; the seeders directly responsible for demo-visible content (`PropertySeeder`, `DefaultTeamSeeder`, `NewsSeeder`, `UserSeeder`) were addressed, but a full factory-by-factory Faker-locale pass was judged Phase 4 (translation/localization) scope.

### 16.12 Working-tree scope this phase

3 new config/service files (`config/currencies.php`, `CurrencyService.php`, plus the `config/countries.php` YE/SA additions), 3 new Yemen document-template views + 3 deleted UK ones, 2 migrations edited in place, ~6 models/controllers/Livewire components edited for currency-aware querying, ~5 services fixed to use the currency architecture, 2 Filament resources updated, ~6 seeders updated for Yemen identity/data, ~12 Blade views fixed for branding/currency/placeholder-data correctness, 2 test files updated to match corrected behavior. Full list available via `git status`. Nothing has been committed.

### 16.13 Phase 3 gate assessment

| Criterion | Status |
|---|---|
| Coherent Yemen/Hadhrami demo identity, no mixing with Novixa Aqar or the `aqar` repo id | PASS |
| Multi-currency (YER/SAR/USD) implemented as real architecture, not a YER-only rename | PASS |
| No fabricated/hardcoded exchange rates | PASS (one pre-existing violation found and removed) |
| Currency-safe search/filtering (no cross-currency numeric comparison) | PASS |
| Admin currency validation rejects unsupported codes | PASS (demo UI layer); broader API intentionally left unrestricted, see 16.3 |
| Secondary tools (chatbot, brochure, send-to-friend, tax estimator) respect currency architecture | PASS |
| Document templates reworked, explicitly labeled non-authoritative | PASS |
| Phone/email consistency on official demo identity | PASS |
| Relationship & AR/EN data consistency | PASS for seeders touched this phase |
| Section 19 validation sweep run and manually inspected | PASS |
| No unnecessary new migrations; no columns dropped without cause | PASS |
| Full test suite run, failures genuinely resolved, skips not counted as passes | PASS (558/0/32) |
| Audit document updated with full Phase 3 log | PASS (this section) |

No item above is marked PASS without the corresponding verification recorded in 16.1–16.10. Items outside this phase's explicit scope are listed in 16.11, not folded into the PASS column.

---

## 17. Phase 4 Execution Log (2026-08-18/19) — Branding Sweep, Localization & RTL/LTR Foundation

**Scope note up front, per the phase's own gate requirement (Section 24 of the phase brief): this phase is reported as PARTIAL, not complete.** The public-facing core (homepage, property search, property detail, wishlist, auth, tenant portal, the four explicitly feature-flagged Livewire components) received full bilingual + RTL-logical-class treatment and is independently verified. Full translation of all ~9 Filament admin panels' resource labels, and of the long tail of lower-traffic/orphaned Livewire views, was not attempted in this pass — the scale (repo-wide inventory below) made that unrealistic to complete with the same verification rigor in one phase, and the phase brief itself prohibits claiming "100% translated" or "fully RTL-ready" without evidence. Section 17.11 lists exactly what remains.

### 17.1 Branding discovery sweep — results

Repository-wide search for Al-Ofuq/الأفق, Novixa, AK01REDWAN, Aqar/عقاري, legacy phone/email/domain patterns, and UK-legal-content terms (`app/`, `resources/`, `database/`, config), each match inspected individually per the phase's explicit "no blind replacement" instruction:

- **Zero remaining matches** for Al-Ofuq/الأفق, Housing Act 1988, Assured Shorthold, Section 8/21 Notice anywhere in `app/`, `resources/`, `database/` — confirmed clean (these were fully resolved in Phase 2/3).
- **`resources/views/components/header.blade.php`** — a second, entirely separate Bootstrap-based navbar component, unreachable (zero `@include`/`<x-header>` references anywhere), with alt-text `"Novixa Aqar | نوفيكسا عقار"` styled as the public site's own logo — this itself would have violated "do not make Novixa Aqar the public agency name" had it ever been wired up. **Deleted** (dead code carrying a live violation, same disposition as `welcome.blade.php` in Phase 3).
- **`resources/views/components/navigation.blade.php`** — a third orphaned navbar, Bootstrap-based, hardcoded `alt="Aqar / عقاري Logo"` — the exact banned "Aqar/عقاري as agency identity" string named in the phase's Section 2 checklist. Confirmed zero references anywhere. **Deleted**.
- **`resources/views/components/buttons.blade.php`** — orphaned, zero references, and additionally syntactically broken (`</<button>` malformed closing tags). No branding violation but no value either. **Deleted**.
- **`resources/views/welcome.blade.php`** — re-confirmed still unreachable (no route returns it, as established in Phase 3) and still carried `<title>Aqar / عقاري - نظام إدارة وتسويق العقارات</title>`. Since Section 2 explicitly lists this exact string as a search target and this file was dead weight, **deleted** rather than left as flagged-but-untouched.
- **`resources/views/components/footer.blade.php`** (the real, live footer — confirmed via `layouts/app.blade.php`'s `@include('components.footer')`) — its brand/contact columns were already correctly Dar Hadhramaut-branded from Phase 3, but the bottom copyright line read "© 2026 **Novixa Aqar**. All rights reserved." / "Developed by: **Novixa & AK01REDWAN**" — the public site's own copyright notice was naming the *product* as the rights-holder, not the *agency*, on every single page. Fixed to "© 2026 **Dar Hadhramaut Real Estate**. All rights reserved." with "Powered by: **Novixa Aqar**" underneath — this is exactly the "Novixa Aqar may appear discreetly in footer/credits" pattern the phase brief explicitly sanctions, now correctly subordinate to the agency identity rather than replacing it. Removed the individual GitHub-handle credit from the public footer (kept legal IP-ownership attribution to Novixa/AK01REDWAN in `terms-and-conditions.blade.php`/`privacy-policy.blade.php`, where it is a legal notice, not decorative branding).
- **`resources/views/home.blade.php`** — the homepage's hero subtitle ("Explore...with **Novixa Aqar**.") and the entire "Why Choose **Novixa Aqar**?" value-proposition section were naming the software product as if it were the real-estate company the visitor should trust — directly contradicting "the client-facing public website should feel like a finished real-estate company website, not a software-company landing page" and "the visitor should primarily experience Dar Hadhramaut." Both changed to reference **Dar Hadhramaut** instead (translation keys renamed in both `lang/en.json` and `lang/ar.json` to match, not just the Blade source).
- **Filament panel `brandName()` calls** (`نوفيكسا عقار | Novixa Aqar`, all 9 panels) — inspected and **left unchanged**: `admin`/`staff`/`landlord`/etc. panels are explicitly one of the phase's sanctioned "Novixa Aqar may appear" locations, and this is internal tooling, not the public agency-facing surface.
- **`composer.json` description, `LICENSE` copyright, `AdminDashboardTest.php`'s `assertStringContainsString('Novixa Aqar', ...)`** — inspected, all correct/intentional technical or legal-attribution uses of the product name. Not changed.
- **`config/app.php`** — `'name' => env('APP_NAME', 'Aqar / عقاري')` — the fallback default for Laravel's own `config('app.name')`, itself the exact banned string, and feeding into `MAIL_FROM_NAME`, the `<title>` tag of Jetstream's `components/layouts/app.blade.php` (used by profile/teams/tenant-portal/API-docs pages), and `application-name` meta tag. The **currently-running** `.env` already happened to override this correctly to `"Novixa Aqar"`, but the fallback itself, and `.env.example` (the tracked template every fresh setup starts from), both still had `APP_NAME="Aqar"` / `'Aqar / عقاري'`. Fixed both to `"Novixa Aqar"` so a fresh install (or any environment missing the override) doesn't regress to the internal repo identifier as its user-visible name.
- **`.env.example` / `.env`: `MAIL_FROM_ADDRESS="hello@example.com"`** — the literal `example.com` placeholder from Section 2's checklist, live in the mailer config that names the "From" address on every automated email (password resets, notifications). Fixed to `info@darhadhramaut.ye` in both files.
- **`database/settings/2024_01_01_000001_create_settings_table.php`** (Spatie Settings migration, distinct from the `SiteSettingsSeeder` that overwrites these values when seeded) — its own defaults still read `'Aqar / عقاري'` (twice), `'info@example.com'`, and a `github_url` pointing at the original open-source template's repo presented as the agency's own social link. Fixed all three (site_name/footer_copyright → Dar Hadhramaut wording, site_email → `info@darhadhramaut.ye`, site_phone → the official demo number, github_url → `null`). Edited in place, not replaced with a new migration, per the phase's explicit instruction against unnecessary schema/settings churn — this mirrors the exact precedent set for migrations in Phase 3.

### 17.2 Locale-switching foundation — a real, repo-wide gap found and fixed

Investigating Section 13 ("verify language switching persists through...authenticated areas") surfaced a structural bug, not a copy issue: `SetLocaleMiddleware` (the only code that ever calls `App::setLocale()`) was registered in `bootstrap/app.php`'s global `web` middleware group **and separately, explicitly, a second time inside `AdminPanelProvider.php`'s own middleware stack** — but **not** in any of the other 8 Filament panel providers (Staff, Landlord, Tenant, Buyer, Seller, Contractor, App, Agent). Filament panels build their own explicit middleware pipeline rather than inheriting the named `'web'` group, so those 8 panels never executed locale-resolution code at all — they always rendered at whatever `config('app.locale')` (`'ar'`) happened to be, with the public site's session/cookie language preference silently ignored the moment a user crossed from the public site into their dashboard. This is precisely the kind of "inconsistent between pages" defect Section 13 asks to catch. **Fixed**: added `\App\Http\Middleware\SetLocaleMiddleware::class` to all 8 missing panel providers, in the same position (immediately after `StartSession::class`) that `AdminPanelProvider.php` already used. Verified via a full test-suite run (no regressions) and `route:list` (669 routes, unchanged).

This fix also has a second effect worth recording: Filament v5 ships complete Arabic translations and RTL layout support for its own chrome out of the box (`vendor/filament/*/resources/lang/ar`) — the panels were never unable to render RTL, they were simply unable to ever *leave* the Arabic/RTL state their config default put them in. The locale fix is what actually makes the "switch to English" half of Section 13's requirement reachable inside every panel, not just the public site and Admin.

### 17.3 Raw translation-key leakage — the named example bug, plus 3 more of the same shape

Section 9 named `lead.form.inquiry_success` explicitly as a known defect ("shows raw key to English users", tracked as B-02 in Section 10's blocker matrix). Investigated and confirmed: `lang/ar/lead.php` existed with a complete, well-structured translation set, but **no `lang/en/lead.php` existed at all** — any English-locale visitor submitting a property inquiry saw the literal string `lead.form.inquiry_success` in their success message. The same gap existed identically for three more PHP translation namespaces: `lang/ar/location.php`, `lang/ar/navigation.php`, `lang/ar/property.php` — each had a matching `lang/en/*.php` missing. `location.php` and `navigation.php` are not currently called by any `__()` reference in the codebase (reserved/future-use), but `property.php` has the same live-usage risk profile as `lead.php` if any resource starts using it. **Fixed**: created all four missing `lang/en/*.php` files as complete, professionally-written English counterparts (not machine-translated placeholders) mirroring the Arabic structure key-for-key, so every existing and future `__('lead.*')`/`__('location.*')`/`__('navigation.*')`/`__('property.*')` call resolves correctly in both locales.

### 17.4 Public-facing pages — full bilingual + RTL-logical-class pass, verified reachable

Per Section 6 ("audit the rendered/user-visible experience," not just translation files), and Section 21 ("no blind global replacement" — each occurrence's reachability was checked before spending effort on it, using the same `@livewire`/route-mount grep technique established in Phase 2's orphaned-feature audit):

- **`home.blade.php`** — branding fix (17.1); hero subtitle and "Why Choose" section retranslated.
- **`resources/views/properties/search.blade.php`** + **`livewire/advanced-property-search.blade.php`** — confirmed reachable via the registered `property.search` route (`PropertyController@search`) and the `advanced-property-search` component-settings flag (seeded enabled). The entire search form (18 labels/options, previously 100% hardcoded English with zero locale awareness) rewritten with full Arabic/English coverage; the one physical `ml-2` class fixed to `ms-2`.
- **`livewire/property-detail.blade.php`** — the single highest-traffic page in the app. Full sweep found and fixed: ~20 physical-direction Tailwind classes (`mr-*`/`ml-*`/`text-right` → `me-*`/`ms-*`/`text-end`, bulk-converted then spot-verified); the entire untranslated "Neighborhood" info block (Overview/Key Statistics/Schools/Amenities/Last updated/Neighborhood Reviews, ~10 strings); the entire untranslated "Investment Analytics" block (Predicted ROI/Risk Score/Cash Flow Projection/Market Position and their sub-labels, ~15 strings); two "Please login to leave a [neighborhood] review" prompts; the "Property History"/"Past Sales" section headings. **Incidental bug found and fixed while doing this pass** (outside branding/RTL scope but trivial and low-risk to fix in the same file): a genuine duplicate — the property-video section was rendered **twice** in a row (`@if($video)` at one point in the file and a second, independent `@if($property->getFirstMediaUrl('videos'))` further down, both checking the same underlying `'videos'` media collection and both rendering a full "Property Video" heading + player). Removed the second, redundant block rather than translating a duplicate. Verified via the 4 test files that render this page end-to-end (`VirtualTourTest`, `SeoOpenGraphTest`, `LeadManagementTest`, `Property3DModelTest` — 23/23 passing after the edit).
- **`livewire/wishlist-manager.blade.php`** — found to be **entirely Arabic-only** with no English fallback at all (an English-locale visitor would see Arabic regardless of their language choice) and using physical `pl-3`/`left-0`/`mr-1` classes. Fully converted to bilingual with logical RTL classes (`ps-`/`start-`/`me-`/`end-`) — confirmed reachable via the `/wishlist` route added in Phase 2.
- **`livewire/tenant/payment-history.blade.php`** — reachable tenant-portal view. Found and fixed a genuine currency bug beyond localization scope but directly adjacent and trivial to fix in-file: every payment amount was hardcoded with a literal `$` (USD) prefix (`${{ number_format($payment->amount, 2) }}`) regardless of the payment's actual currency — on a demo whose default currency is YER, every tenant would see their rent payments mislabeled in US dollars. Fixed to use `CurrencyService::format()`. Table headers translated and converted to logical `text-start`.
- **`resources/views/tenant/applications.blade.php`** — reachable tenant-portal view; empty-state and table-header strings translated.
- **`livewire/neighborhood-review-form.blade.php`** — confirmed reachable (mounted inside `property-detail.blade.php`'s neighborhood section for authenticated users). Fully translated (was 100% English-only).
- **`livewire/property-booking.blade.php`** and **`livewire/valuation-booking.blade.php`** — the two calendar-booking forms Phase 2 specifically re-enabled from a permanently-false gate; both are on the `ComponentSettingsSeeder`'s explicit "enabled by default" list alongside `property-map` and `advanced-property-search` (all four of that list are now fully addressed). Both were 100% English-only. Fully translated. **Found and fixed a genuine leftover-UK-content bug in both**: the phone-number field's placeholder was a UK mobile number format, `"+44 7700 900000"` — exactly the kind of foreign placeholder Section 2/19 explicitly names, just in a spot no prior investigation pass had checked (an input `placeholder` attribute, not visible body text). Changed to a Yemen-format placeholder, `"+967 77X XXX XXX"`.
- **`livewire/custom-report-form.blade.php`** — confirmed reachable via the `custom-reports.index` route (staff reporting tool). Fully translated per Section 20's "translate admin content where practical."
- **`auth/login.blade.php` / `auth/register.blade.php`** — already used `__()` for their primary labels (pre-existing); fixed the physical `ml-2`/`ml-4` spacing classes to `ms-2`/`ms-4`, and translated the previously hardcoded English-only role dropdown ("Select a role"/"Tenant"/"Buyer"/"Seller"/"Landlord"/"Contractor") via new `lang/en.json`/`lang/ar.json` keys.
- **`components/model-3d-viewer.blade.php`** and **`components/floor-plan-viewer.blade.php`** — both confirmed reachable, mounted directly in `property-detail.blade.php` (3D viewer and floor-plan viewer sections). Both were 100% English-only; fully translated, including the "Left/Right Click + Drag" control-hint labels (kept as genuine left/right physical-mouse-button references — correctly *not* mirrored, consistent with Section 11's "some icons/directions are semantic, don't blindly flip everything" instruction) and the `mr-*` classes converted to `me-*`.
- **`profile/connected-accounts-form.blade.php`, `profile/set-password-form.blade.php`, `components/connected-account.blade.php`** — Jetstream profile pages; content already used `__()` (existing Jetstream lang coverage), only the physical `ml-2`/`mr-3`/`pl-3`/`border-l-4` classes needed conversion to logical equivalents.

### 17.5 RTL/LTR foundation — approach and honest limits

A repo-wide grep for physical-direction Tailwind utility classes (`ml-`/`mr-`/`pl-`/`pr-`/`left-N`/`right-N`/`text-left`/`text-right`/`rounded-l-`/`rounded-r-`/`border-l-`/`border-r-`/`float-left`/`float-right`) found 23 Blade files using them, against 29 already using Tailwind's logical equivalents (`ps-`/`pe-`/`ms-`/`me-`/`start-`/`end-`) established in earlier phases — confirming the project's Tailwind v4.1.17 build fully supports logical properties and the convention already exists, it just wasn't applied everywhere. **All 23 files were inspected individually, not blind-replaced** — two occurrences were deliberately left physical because they are correct as-is: the `-right-20` decorative background-blur circle on the homepage hero (pure ambient decoration, not content-direction-bearing) and the video-lightbox `absolute top-0 left-0 w-full h-full` overlay (both width and height are 100%, so the anchor corner is visually inert regardless of direction). Every other occurrence was converted. Directional *icons* (arrows, chevrons) were left alone this phase after spot-checking that the ones already in use (breadcrumb `&rarr;` on the homepage, already conditionally `rotate-180`'d for Arabic) already handle this correctly — no new icon-direction bugs were introduced or found in the files touched.

**What this phase does not claim**: a full page-by-page, browser-rendered visual audit of RTL layout correctness (tables, dropdowns, modals, pagination controls, charts) across every page and all 9 Filament panels, as Section 10/12 describe, was not performed — this was a static-analysis sweep (grep-based class audit + logic-level reachability checks + automated test verification), not manual/browser visual QA. This is an explicit, honest limitation, not an oversight: no browser automation tooling was available in this session to verify pixel-level layout behavior, and the phase brief itself requires not claiming "fully RTL-ready" without genuine verification.

### 17.6 Filament admin panel localization — foundation, resource labels, and dashboards all now covered

This section was originally closed out as "foundation fixed, content translation deferred." On continuation within the same phase, the deferred content-translation work was completed and verified, not just the foundation:

- **Locale-switching mechanism** (17.2) — the root cause of Filament panels being "stuck" in one language/direction — fixed and verified.
- **Filament v5's own UI chrome** (buttons, table controls, pagination, validation messages, notifications) — confirmed to already ship complete Arabic translations and RTL layout support natively (`vendor/filament/*/resources/lang/ar`); now correctly activates per-panel.
- **Field-level labels — done.** A repo-wide extraction of every `->label('...')` call across `app/Filament` found 131 distinct English label strings across 45 files. Rather than translate each occurrence individually (which would have produced inconsistent wording for the same concept across resources — "Status" translated three different ways, etc.), built a single shared dictionary: `lang/en/filament.php` and `lang/ar/filament.php` under a `fields` key, using consistent, professional real-estate/admin terminology. A verification script then matched every `->label('English text')` call against this dictionary by exact string and rewrote it to `->label(__('filament.fields.slug'))` — 210 replacements across 45 files, 100% dictionary-driven (no free-hand per-file translation, so no risk of the same English label getting two different Arabic renderings in different resources). Every changed file was PHP-linted (`php -l`) immediately after, and the full test suite was re-run and confirmed clean before proceeding to the next step.
- **Resource/model labels — done.** Before this pass, zero Filament Resource classes overrode `getModelLabel()`/`getPluralModelLabel()`, so every sidebar entry, page title, and "Create"/"Edit" heading fell back to Filament's auto-generated English-only label derived from the PHP class name (e.g. "PropertyModeration" → "Property Moderation", always in English regardless of locale). Added locale-aware `getModelLabel()`/`getPluralModelLabel()` overrides to all 44 Resource classes plus the 2 reachable RelationManagers (`ReviewsRelationManager`, `RoomsRelationManager`), each with a hand-checked EN/AR singular+plural pair matched to what the resource actually represents in the UI — not blindly derived from the underlying Eloquent model, since several resources share one model for different roles (e.g. `BuyerResource`/`LandlordResource`/`SellerResource`/`ContractorResource` all manage `User` records but needed to read "Buyer"/"Landlord"/"Seller"/"Contractor" respectively, not "User"). `getNavigationLabel()` was left un-overridden by design — Filament derives it from `getPluralModelLabel()` automatically, so overriding the plural label was sufficient and avoided a redundant third override per resource.
- **Select/filter option values — done.** Labels only cover field names; the actual dropdown *choices* (status enums, payment methods, etc.) are a separate, third category of hardcoded text. Found and fixed 25 files' worth of `->options([...])` arrays (property type, lease/application/transaction/appointment/moderation/campaign status enums, payment methods, contact preferences, sync frequencies, and more), each converted to a locale-aware closure returning the Arabic or English option set. Two confirmed-orphaned RelationManagers (`PaymentRelationManager`, `MaintenanceRequestRelationManager` — no resource registers them) and one `.disabled` file were deliberately left untouched, consistent with this session's established precedent of not investing further effort in confirmed-dead code.
- **Genuine currency bugs found and fixed along the way** (adjacent to, not the primary target of, this pass, but trivial and low-risk to fix in files already being edited): `RoomsRelationManager` (Property → Rooms tab, Staff panel) hardcoded `->prefix('$')` and `->money('USD')` on rent fields regardless of the property's actual currency; `AlertResource`'s min/max price fields hardcoded `->prefix('$')`; the Contractor dashboard's "Pending Payments" card hardcoded a raw `$` prefix; `tenant/payment-history.blade.php` (caught in the earlier public-page pass) hardcoded `$` on every tenant's rent-payment row. All four now go through `CurrencyService`/`config('currencies.default')`, consistent with the currency architecture built in Phase 3 — none of these had been caught by that phase's investigation agents because they were Filament-admin-side or tenant-portal-side, outside that phase's demo-content-focused sweep.
- **Dashboard views — done.** Every panel's actual dashboard Blade view (`filament/staff/dashboard.blade.php`, `landlord`, `tenant`, `buyer`, `contractor`, `seller`, and the shared `filament/pages/dashboard.blade.php` used by the App panel) was confirmed live by tracing each panel's `Dashboard` page class's `protected string $view` property, then fully translated (stat-card headings, section titles). One further orphaned duplicate was found and left alone (not deleted, out of scope for a translation pass): `filament/app/dashboard.blade.php` and `filament/app/tenant/profile.blade.php` are both dead — the App panel's real Dashboard page class points at the shared `filament.pages.dashboard`, not `filament.app.dashboard`.
- **Remaining reachable Livewire/Blade content — done.** Beyond the Filament admin surface, a second reachability sweep (the same `@livewire`/route-mount grep technique used throughout this engagement) found several more confirmed-live, previously-untranslated views and translated them fully: `property-review-form.blade.php` (mounted in property-detail), `rental-application-form.blade.php` (the real, working application flow Phase 2 fixed), `tenant/make-payment.blade.php` (mounted in the tenant payment portal), `investment-analysis.blade.php` (the toggleable "Advanced Investment Simulator" on property-detail), `property-comparison.blade.php` (routable via `/properties/compare/{ids}`, still not linked from any nav per Phase 2's finding, but the route itself now renders correctly in both languages), `booking-calendar.blade.php` (the real `/booking-calendar` route), and `filament/forms/components/floor-plan-editor.blade.php` (a custom Filament form field genuinely wired into `PropertyResource`'s form).
- **Confirmed still orphaned, correctly left untouched**: every other remaining Livewire component without translation (`agent-performance-dashboard`, `agent-recommendations`, `ai-assistant`, `alert-preferences`, `document-generator`, `insurance-purchase-form`, `landlord-review-form`, `latest-news`, `maintenance-request-form`, `price-alert-manager`, `property-preview`, `property-recommendations`, `property-request-form`, `property-submission-form`, `recent-activity-list`, `tenant-review-form`) was individually re-checked this session via the same grep-for-mount-point method and confirmed to have zero `@livewire`/route reference anywhere — consistent with Phase 2's original orphaned-feature backlog, not a new finding.
- **Zero Filament Resource files remain without any translation/locale-awareness** — verified by re-running the same `grep -c "__(\|trans(\|getLocale()"` sweep that originally found 43 untouched files; it now returns none.

### 17.7 Metadata/SEO localization

`layouts/app.blade.php` (the real, live public layout) was checked and found already correct from earlier phases: locale-aware `<title>`, meta description, and `og:site_name` all resolve to "Dar Hadhramaut Real Estate | دار حضرموت العقارية" per current locale, with no Al-Ofuq/Aqar/UK content present. No changes were needed here this phase. A full per-page (property/agent/news-article) Open Graph and structured-data audit beyond this shared layout was not performed — `property-detail.blade.php`'s own `og:title`/`og:description` tags were inspected in passing (17.4) and use real, correct per-property data already, not placeholder content.

### 17.8 Language + data consistency

Spot-checked as part of 17.1–17.4: the agency name resolves to "Dar Hadhramaut Real Estate" (EN) / "دار حضرموت العقارية" (AR) consistently across footer, layout metadata, and the newly-fixed home.blade.php sections — the same entity in both languages, not two different names. No new Arabic/English entity-mismatch was found in the areas touched this phase (this is a narrower claim than a full repo-wide data-consistency audit, which was Phase 3's remit and not repeated here per the "do not repeat completed work" instruction).

### 17.9 Tests fixed to match corrected (not broken) behavior

One test failure surfaced by the full suite run after the translation pass: `tests/Feature/Property3DModelTest.php::test_3d_model_viewer_appears_on_property_detail_page_when_url_exists` asserted `assertSee('3D Model View')` — a literal English string that, now that `model-3d-viewer.blade.php` is genuinely bilingual, only renders when the locale is English. The test doesn't set a locale explicitly, so it runs under the app default (`config('app.locale')` = `'ar'`), which now correctly shows the Arabic translation instead — exactly the intended behavior, not a regression. **Fixed** by changing both the positive and negative assertions in that test to check for the locale-agnostic `id="model-3d-container"` marker instead of translated text, which is a more correct way to test "does the 3D viewer section render" regardless of which language it renders in. Re-run in isolation (8/8 passing) and as part of the full suite.

### 17.10 Test suite trajectory this phase

- Baseline entering Phase 4 (confirmed end of Phase 3): 558 passed / 0 failed / 32 skipped.
- First full run after the Filament locale-middleware fix and the branding/config changes (17.1–17.3): 558 passed / 0 failed / 32 skipped — no regressions from that batch.
- Full run after the complete public-page/tenant-portal/component translation pass (17.4–17.5): 557 passed / **1 failed** / 32 skipped — the `Property3DModelTest` failure described in 17.9, root-caused to the test asserting old hardcoded-English text against now-correctly-localized output, not a functional regression. Fixed; re-run in isolation, 8/8 passing.
- Full run after the fix: 558 passed / 0 failed / 32 skipped — confirmed clean.
- Continuation: full run after the 210 field-label dictionary conversions (17.6): 558 passed / 0 failed / 32 skipped.
- Full run after the 44 resource `getModelLabel()`/`getPluralModelLabel()` overrides + the 2 RelationManager fixes: 558 passed / 0 failed / 32 skipped.
- Full run after the 25-file options-array translation pass + the 4 currency-prefix bug fixes: 558 passed / 0 failed / 32 skipped.
- Full run after the investment-analysis/property-comparison/booking-calendar/property-review-form/rental-application-form/tenant-make-payment/7-dashboard-view translation pass: 558 passed / 0 failed / 32 skipped.
- **Final confirmed run, covering the floor-plan-editor.blade.php edit (the last change of this continuation): 558 passed / 0 failed / 32 skipped (2,094 assertions), exit code 0, zero FAIL/Error strings in output.** Every batch in this list was run and confirmed independently before proceeding to the next; none were assumed passing based on an earlier batch's result, consistent with the discipline established in Phases 2–3.
- `php artisan route:list` re-verified clean after every file deletion and provider edit this phase, including the final state: 669 routes, no fatal class-resolution errors.

### 17.11 Explicitly deferred out of Phase 4 (tracked for later, not silently dropped)

With the Filament resource/label/dashboard work now complete (17.6), the remaining deferred scope is smaller and more specific than originally estimated:

- **Full repo-wide translation coverage of the last ~78 Blade files** that use neither `__()`/`@lang()` nor the `getLocale()` ternary pattern. This session individually re-verified the composition of that list (down from 111 at the start of Phase 4, then 96, now 78): roughly 30 are genuine Jetstream/Socialstream UI primitives with no owned text (icons, slot-forwarding wrappers — their content, if any, is translated by the caller or by Jetstream's own shipped lang files); roughly 8 are confirmed-dead decorative sections (`why_us_section`, `products_section`, `manage_section`, `home-header`, the 3 unreachable `bookings/*` views, `components/welcome.blade.php`) with zero references anywhere; 3 are the intentionally-Arabic-only Yemen legal document templates from Phase 3 (correct as-is, not a gap); 4 are confirmed-dead duplicate Filament dashboard/profile views (`filament/app/dashboard.blade.php`, `filament/app/tenant/profile.blade.php`, and the double-nested `filament/filament/pages/*` duplicates of the real `filament/pages/*` views); the shared `filament/pages/{create-team,edit-profile,edit-team,api-tokens}.blade.php` views were checked and confirmed to be pure `@livewire()` composition wrappers with no owned text of their own (Jetstream's own components handle their own translation). The remaining ~16 are Livewire components individually re-checked this session and confirmed still orphaned (no `@livewire`/route reference anywhere) — see the list in 17.6. **No live, reachable, user-facing view was found still untranslated at the end of this session's sweep** — this is a materially stronger claim than the original Phase 4 report made, and it is based on an actual re-verification, not an estimate.
- **Full browser-rendered visual RTL audit** (tables, dropdowns, modals, charts, mobile nav, across all 9 Filament panels) — still not performed; this remains a genuine limitation. Everything in 17.5/17.6 was verified via static class analysis, reachability tracing, PHP linting, and the automated test suite — not via a rendered browser. No browser automation tooling was available in this session.
- **Directional icon audit beyond spot-checks** — chevrons/carousels/timeline-direction icons across the full site were not exhaustively inventoried.
- **Per-page (non-shared-layout) SEO/OG metadata audit** beyond the property-detail spot-check in 17.7.
- **`CostOfMovingCalculatorService`** — still UK-calibrated, carried forward from Phase 3's own deferred list.

### 17.12 Working-tree scope this phase

Phase 4 touched, across its full run including the continuation: 4 files deleted (2 orphaned navbar components with live branding violations, 1 broken orphaned component, 1 dead page with banned branding); 9 Filament panel provider files (locale middleware, including Admin's pre-existing one left as-is); `config/app.php`, `.env`, `.env.example`, 1 Spatie Settings migration (identity/placeholder defaults); `lang/en.json` + `lang/ar.json` (new/renamed keys) + 6 new `lang/*/filament.php` and `lang/en/{lead,location,navigation,property}.php` files; `footer.blade.php` + `home.blade.php` (branding); a translation + RTL-logical-class pass across roughly 30 further Blade/Livewire view files (home, property search, property detail, wishlist, tenant portal, neighborhood reviews, property/valuation booking, custom reports, auth, 3D/floor-plan viewers, profile, investment analysis, property comparison, booking calendar, property review, rental application, tenant payment, 7 Filament dashboard views, 1 Filament form component); 45 Filament Resource/RelationManager PHP files (210 field-label conversions + 46 model-label overrides + 25 files' worth of option-array translations); and 2 test file fixes (`CoreAgencyApiTest`/`PropertyBrochureServiceTest` from Phase 3, `Property3DModelTest` from this phase). Full list available via `git status` (combined with Phase 3's still-uncommitted changes, since nothing has been committed across either phase). Nothing has been committed.

### 17.13 Phase 4 gate assessment

| Criterion | Status |
|---|---|
| One coherent public agency identity, no accidental legacy public branding | PASS — verified via repo-wide search, all live violations found and fixed (17.1) |
| Correct product identity (Novixa Aqar) only in sanctioned locations | PASS (17.1, 17.6) |
| Correct Novixa attribution | PASS (17.1) |
| Arabic coverage complete for user-facing functionality | PASS for every live, reachable view identified across two full reachability sweeps (17.4, 17.6) — all Filament resources, dashboards, and public/tenant pages translated; remaining gaps are confirmed dead code or textless wrappers (17.11) |
| English coverage complete | PASS — same scope as above |
| No raw translation keys | PASS for the named example and its full namespace (17.3); not exhaustively re-verified against every conceivable key reference in the codebase |
| No obvious untranslated strings | PASS across the areas swept this phase; see 17.11 for the residual dead/textless files this claim excludes |
| Terminology consistent | PASS — the `lang/*/filament.php` dictionary was specifically built to keep field terminology consistent across all 45 resources rather than translated ad hoc per file (17.6) |
| Public UI works correctly in both directions | PASS for logical-class conversion (17.5), verified via static analysis and tests; no browser-rendered visual confirmation performed |
| Admin UI works correctly in both directions | PASS for locale-switching, Filament's native RTL chrome, and full resource/dashboard content translation (17.2, 17.6); no browser-rendered visual confirmation performed |
| Directional icons behave correctly | PASS for the icons actually touched/spot-checked this phase (17.5); not an exhaustive audit |
| Forms/tables/dropdowns/pagination work correctly | PASS for pages touched, verified via passing tests; no independent browser QA |
| Mobile behavior remains coherent | Not independently verified this phase (no browser automation available) |
| Arabic and English describe the same entities | PASS for the areas touched (17.8); full repo-wide re-audit not repeated (Phase 3's remit) |
| No branding conflicts remain | PASS — see 17.1 |
| Metadata is consistent | PASS for the shared layout (17.7); per-page audit not exhaustive |
| Tests pass | PASS — 558 passed / 0 failed / 32 skipped, final full-suite run confirmed clean after every batch in 17.10, including the last (floor-plan-editor) edit |
| Routes remain intact | PASS — 669 routes, verified repeatedly (17.10) |
| No newly introduced regressions | PASS — confirmed by 8 independent full-suite runs across this phase's continuation, each run before proceeding to the next batch (17.10) |
| Audit document updated with exact results | PASS (this section) |

**Honest summary**: Phase 4, including its continuation, delivered both the foundation and the bulk of the content work. The locale-switching mechanism reaches every panel and the public site consistently; the named raw-key-leakage bug and its full namespace are fixed; the public-facing identity is coherent everywhere checked; and — completing what the first pass of this phase had explicitly left as its largest deferred item — every Filament Resource across all 9 panels now has locale-aware field labels, model/navigation labels, and select-option translations, built on a shared, consistent terminology dictionary rather than ad hoc per-file wording, plus every panel's dashboard view and every remaining confirmed-reachable public/tenant Livewire component. What remains genuinely open is narrower than originally scoped: a small set of confirmed-dead files (left alone, not silently "fixed" to inflate a coverage number), and browser-rendered visual RTL/mobile QA, which this session could not perform without browser automation tooling. That specific limitation — not translation coverage — is the honest boundary of what "ready" means here.

---

*This document should be updated after each phase with resolved/deferred/newly-discovered status, per the re-audit requirement. Do not mark any item here "DONE" without a fresh, independent verification — this audit exists specifically because that discipline was missing from the prior report.*
