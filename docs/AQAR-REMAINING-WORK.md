# Aqar — Remaining Work

Everything left after the 2026-08-25 end-to-end acceptance pass (`docs/AQAR-FINAL-END-TO-END-ACCEPTANCE-REPORT.md`), grouped by urgency. Nothing in this document duplicates what that report already marked FIXED — this is only what's still open.

---

## Production Blockers

Must be resolved before this codebase is deployed to a real, public production domain — not blockers for a local/demo walkthrough, which is otherwise clean.

1. **`README.md`, `k8s/*.yaml` (all 13 files), and `install.php` are entirely the original upstream "Liberu Real Estate" template's content, never adapted for this project.** `README.md` describes a different product, links to a live third-party demo (`agent.liberu.co.uk`), and its own install instructions clone `github.com/liberu-real-estate/real-estate-laravel.git` — not this repository. `k8s/deployment.yaml` (and 2 sibling container specs in the same file) pulls `image: liberu/real-estate:latest` — deploying via these manifests as-written would run someone else's Docker image, not this application, and `k8s/ingress.yaml`'s default host is `agent.liberu.co.uk`. Anyone evaluating this repo via its own front door (README) or its own Kubernetes manifests sees zero mention of Aqar, Novixa, or Dar Hadhramaut. **Recommended action:** either fully rewrite `README.md` and every `k8s/*.yaml` for this project's actual identity and actual container registry, or delete the `k8s/` directory entirely if the documented Docker/`docker-compose.yml` path (already correct, see `docs/PRODUCTION-DEPLOYMENT.md`) is the real intended deployment method and Kubernetes was never actually adopted.
2. **Environment configuration** (`APP_URL`, `APP_ENV=production`, `APP_DEBUG=false`, real `APP_KEY`, production DB credentials, mail provider if needed) — see `docs/PRODUCTION-DEPLOYMENT.md` for the full, itemized checklist. Not a code defect; purely deployment-time configuration.

## Before Public Demo

Not blockers for the demo itself (all confirmed working in this pass), but worth a look before a high-stakes presentation.

1. **Live dev database is literally named `liberu_real_estate`** (confirmed via query logs during this pass). Purely cosmetic — never visible to an end user — but worth renaming to `aqar` for internal consistency the next time the DB is provisioned from scratch, matching the already-correct `.env.example`/`docker-compose.yml` defaults.
2. **`Document::registerMediaCollections()` and `ComplianceDocument::registerMediaCollections()` reference a `'documents'` filesystem disk that isn't configured anywhere** (`Storage::disk('documents')` throws `Disk [documents] does not have a configured driver`). Confirmed both are currently unreachable from any live UI path (`DocumentResource` uses a plain `FileUpload` bound to a `file_path` column instead, bypassing this entirely; `ComplianceDocument` has no Filament resource at all) — so this is latent, not a live bug, but would break instantly the moment either code path is wired up. Fix by either defining a real `'documents'` disk in `config/filesystems.php` or removing the dead `registerMediaCollections()` methods.
3. **Five orphaned Livewire component classes confirmed unreachable this pass**: `About`, `PrivacyPolicy`, `ServicesComponent`, `TermsAndConditions` — the public `/about`, `/privacy-policy`, `/services`, `/terms-and-conditions` routes are actually served by `PageController` returning plain static Blade views, never touching these classes. Safe to delete; duplicated, not missing, functionality. (This is in addition to the ~14 orphaned components already catalogued in the prior audit pass — not re-listed here.)
4. **`RentalCharge` model + controller with no backing database table** (already documented in the prior audit; still unreachable, still zero live risk, still not touched).

## Recommended Improvements

Real, but non-urgent — quality-of-life or professionalism polish, not correctness issues.

1. Accessibility: property-list filter form's inputs lack programmatic `<label for=>` association; property-detail's heading hierarchy is non-strictly-nested in one conditional section (both already documented in the prior audit pass, not re-verified live this pass — carried forward, not re-discovered).
2. SEO: only property-detail has a real per-page meta description and JSON-LD; every other public page uses the generic sitewide fallback.
3. Main JS bundle (`app-*.js`) is ~1.3MB uncompressed / 364KB gzipped, over Vite's 500KB chunk-size warning threshold — needs code-splitting (dynamic imports for Three.js/model-viewer/chatbot).
4. `PermissionsSeeder` takes several minutes on every fresh seed (`shield:generate` called once per panel, 9 times) — a real CI/deploy-pipeline timing concern, not something a demo viewer ever sees. Not attempted this pass or the prior one; would likely require patching or forking the third-party Shield command.

## Future Enhancements

Explicitly out of scope for the current demo — architectural/product-scope items already flagged in prior audit passes, carried forward without re-investigation this pass: locale-prefixed URL routing (hreflang), a true bilingual dual-column content model for property/news descriptions, consolidating the `Lease`/`LeaseAgreement` model duplication, a real multi-provider `PortalSyncService` to replace the three legacy portal-sync services.

## Technical Debt

- `properties.status` still carries two coexisting vocabularies by design (the canonical `for_sale/for_rent/sold/rented` set this pass's fixes normalize toward, and a separate, legitimate UK-portal-style vocabulary used by the V1 Automation API) — this is now a documented, intentional split (see `Property::STATUS_SYNONYMS`'s doc comment), not an accidental inconsistency, but still two systems sharing one column long-term.
- ~14 orphaned Livewire components from the prior audit pass plus the 4-5 newly-confirmed ones in this pass — safe to delete in a dedicated cleanup commit once a maintainer has had a chance to double-check nothing external depends on them.
- 3 orphaned legacy Filament resources under an undiscovered `app/Filament/Resources/` directory (prior audit finding, unchanged).
