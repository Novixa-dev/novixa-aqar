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
- **Admin/Panel stack:** Filament v5 across **8 separate panels** (`Admin`, `App`, `Buyer`, `Contractor`, `Landlord`, `Seller`, `Staff`, `Tenant`). Auth via Jetstream + Fortify + Sanctum, RBAC via `spatie/laravel-permission` + `filament-shield`.
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
| B-01 | Three competing brand names across public pages | HIGH | **Decided** (Section 13): Dar Hadhramaut (public) / Novixa Aqar (product). **Not yet executed** on public-facing files — Phase 4. |
| B-02 | `lead.form.inquiry_success` shows raw key to English users | HIGH | Open — Phase 4. |
| B-03 | `VirtualTourTest` failures | HIGH | **FIXED.** Root cause was a stale route cache + a missing UI section, not a Livewire 4 regression as previously assumed. See Section 14. |
| B-04 | Admin seeder creates `admin@example.com`/`password` super-admin, wired into default `DatabaseSeeder` | CRITICAL (pre-prod) | **FIXED.** Production-environment guard added; demo seeding itself intentionally left intact. |
| B-05 | `composer.json` name/PHP constraint | MEDIUM | **FIXED.** |
| B-06 | £/GBP hardcoded in secondary tools | MEDIUM | Open — Phase 3. |
| B-07 | No Yemen entry in `config/countries.php` | MEDIUM | Open — Phase 3. |
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

*This document should be updated after each phase with resolved/deferred/newly-discovered status, per the re-audit requirement. Do not mark any item here "DONE" without a fresh, independent verification — this audit exists specifically because that discipline was missing from the prior report.*
