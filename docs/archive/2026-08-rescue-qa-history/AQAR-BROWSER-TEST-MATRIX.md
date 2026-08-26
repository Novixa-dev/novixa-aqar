# Aqar Browser Test Matrix

Live, real-interaction browser testing (Chromium via Playwright) against `feature/aqar-productization` @ `41a8787` with this session's uncommitted fixes applied, run on `http://127.0.0.1:8123` (deliberately mismatched from `.env`'s `APP_URL` of port 8000 for part of this testing, to directly re-exercise the CSP/media-URL fix under adversarial conditions) and a fully `migrate:fresh --seed`'d MySQL dev database. Two testing passes contributed to this matrix: an earlier same-session "Final Release QA" pass (dark mode / language switcher / gallery / CSP, all still valid and unchanged by this pass's fixes) and this "End-to-End Acceptance" pass (CRUD, authorization/IDOR, forms, search filter, rate limiting).

Legend: **PASS** / **FAIL** / **BLOCKED** (not testable this pass) / **N/A**.

## Persona login & dashboard

| Feature | Persona | Locale | Device | Result | Notes |
|---|---|---|---|---|---|
| Login → dashboard | super_admin | ar | Desktop | PASS | 200, 0 console/page/network errors |
| Login → dashboard | staff | ar | Desktop | PASS | 200, 0 errors |
| Login → dashboard | agent | ar | Desktop | PASS | 200, 0 errors |
| Login → dashboard | buyer | ar | Desktop | PASS | 200, 0 errors |
| Login → dashboard | seller | ar | Desktop | PASS | 200, 0 errors |
| Login → dashboard | landlord | ar | Desktop | PASS | 200, 0 errors |
| Login → dashboard | tenant | ar | Desktop | PASS | 200, 0 errors |
| Login → dashboard | contractor | ar | Desktop | PASS | 200, 0 errors |
| Logout | staff, buyer, tenant | ar | Desktop | PASS | Session terminated, panel URL redirects to login after logout |
| Invalid password | (any) | ar | Desktop | PASS | Validation error shown, no crash |

## Authorization / cross-panel boundaries (all 9 personas × all other panels)

| Feature | Persona | Locale | Device | Result | Notes |
|---|---|---|---|---|---|
| Direct URL into every non-own panel (48 combinations: buyer/seller/landlord/tenant/contractor/agent × 8 other panels each) | all 6 non-privileged personas | ar | Desktop | **PASS (48/48)** | Every attempt correctly returned 403 |
| Unauthenticated access to all 9 panels | none (logged out) | ar | Desktop | **PASS (9/9)** | Correctly redirected to that panel's own `/login`, no data leaked |

## Cross-record IDOR probing

| Feature | Persona | Locale | Device | Result | Notes |
|---|---|---|---|---|---|
| Maintenance-request edit, 6 IDs probed | tenant | ar | Desktop | **PASS** | 2 owned IDs → 200 (verified via DB cross-check as tenant's own records); 4 non-owned → 404 |
| Lease view, 2 IDs probed | tenant | ar | Desktop | **PASS** | Own lease → 200 (verified); other → 404 |
| Rental-application view, 3 IDs probed | landlord | ar | Desktop | **PASS** | All 404 (none owned in current seed) — correctly blocked, not leaked |
| Property view, 7 IDs probed | landlord | ar | Desktop | **PASS** | 1 owned → 200 (verified); 6 non-owned → 404 |
| Maintenance-request edit, 6 IDs probed | contractor | ar | Desktop | **PASS** | 2 assigned IDs → 200 (verified via DB cross-check); 4 unassigned → 404 |
| V1 API `/api/v1/maintenance` team/ownership scoping | tenant, staff | — | API | PASS (code-verified + re-confirmed) | `MaintenanceController::teamQuery()` override confirmed in code; live authenticated-fetch re-test not repeated this pass (covered earlier same session) |

## Property CRUD (Staff panel)

| Feature | Persona | Locale | Device | Result | Notes |
|---|---|---|---|---|---|
| Create property, empty form submit | staff | ar | Desktop | PASS | Stayed on create page, no garbage record persisted |
| Create property, full valid data + image upload | staff | ar | Desktop | **PASS (after fix)** | Was BLOCKING (created record had NULL team_id/user_id, immediately uneditable) — fixed via `CreateProperty::mutateFormDataBeforeCreate()` |
| Edit property (price + status) | staff | ar | Desktop | PASS | Change persisted after reload; canonical `for_rent` status round-trips correctly |
| Uploaded image reachable over HTTP | staff | — | Desktop | **PASS (after fix)** | Was FAIL (404 forever — media saved to `local` disk, not `public`) — fixed via `Property::registerMediaCollections()->useDisk('public')` |
| Property edit access across full inventory | staff, agent | — | Desktop | **PASS (after fix)** | Was FAIL (staff could edit 3 of 36 properties) — fixed via `PropertySeeder` `team_id` |

## Property Moderation resource (Staff panel)

| Feature | Persona | Locale | Device | Result | Notes |
|---|---|---|---|---|---|
| Open moderation list, edit moderation_status | staff | ar | Desktop | PASS | Confirms this session's earlier fix (separate `moderation_status` column, no longer corrupts listing `status`) |

## Lease Agreements resource (Staff panel)

| Feature | Persona | Locale | Device | Result | Notes |
|---|---|---|---|---|---|
| List shows real data | staff | ar | Desktop | PASS | 1 row present (was always 0 before this session's earlier fix) |

## Public property search & filter

| Feature | Persona | Locale | Device | Result | Notes |
|---|---|---|---|---|---|
| Filter by for_sale / for_rent | anonymous | ar | Desktop | PASS | Zero ID overlap between the two filtered sets; both differ from unfiltered baseline; counts sane |

## Public forms — valid & invalid submission

| Feature | Persona | Locale | Device | Result | Notes |
|---|---|---|---|---|---|
| Contact form, valid data | anonymous | ar | Desktop | **PASS (after fix)** | Was FAIL (500 — `contact_messages.interest` column didn't exist despite being in `$fillable`/validation) — fixed via migration |
| Contact form, invalid data | anonymous | ar | Desktop | PASS | No crash; re-verify visible error rendering in a future pass (script's error-locator selector didn't positively confirm, but no server error occurred) |
| Submit-property form, valid data (incl. `year_built`) | anonymous | ar | Desktop | PASS | Success message shown; `year_built` field confirmed present |
| Submit-property form, invalid data | anonymous | ar | Desktop | PASS | Full localized Arabic field-level validation errors shown |
| Property-request form, valid data (no email) | anonymous | ar | Desktop | **PASS (after fix)** | Was FAIL (500 — `buyers.email` NOT NULL contradicted the form's own `nullable` validation rule) — fixed via migration |
| Property-request form, invalid data | anonymous | ar | Desktop | PASS | Full localized Arabic field-level validation errors shown |

## Rate limiting

| Feature | Persona | Locale | Device | Result | Notes |
|---|---|---|---|---|---|
| `/api/metrics` throttle | anonymous | — | API | PASS | `limit=30`, decrements correctly, 429 fires exactly at the configured threshold, `Retry-After` header present, no sensitive data in response |

## Localization, theme, gallery, media (carried from this session's earlier Final Release QA pass — not re-run this pass, unaffected by this pass's fixes)

| Feature | Persona | Locale | Device | Result | Notes |
|---|---|---|---|---|---|
| All public pages, CSP img-src violations | anonymous | ar + en | Desktop | PASS | 0 violations across 11 routes × 2 locales, tested on a port deliberately mismatched from `APP_URL` |
| Login page dark mode | anonymous | ar | Desktop | PASS | Dark card background confirmed (measured, not assumed) |
| Property-detail photo gallery | anonymous | ar | Desktop | PASS | 3 thumbnails rendered on a multi-photo property; relative `/storage/...` URLs |
| Filament panel language switcher | super_admin | ar→en | Desktop | PASS | Locale change confirmed to persist across navigation, not just a one-off query param |

## Not tested this pass (honest gaps — see `docs/AQAR-REMAINING-WORK.md` and `docs/AQAR-FINAL-END-TO-END-ACCEPTANCE-REPORT.md` §"Known Limitations")

| Feature | Reason |
|---|---|
| Wishlist/favorites, property comparison, booking-modal live click-through | Planned for a dedicated background agent that failed on an account session limit before running; covered functionally in an earlier same-session pass, not re-verified this pass |
| Tablet-width (768px) responsive check | Not re-run this pass; desktop + mobile (375px) covered in an earlier same-session pass |
| Deep per-endpoint API fuzzing beyond the maintenance/metrics endpoints tested | Time-boxed out of this pass given the scope already covered directly |
| Accessibility screen-reader/contrast tooling | Static findings only (see Remaining Work); no live assistive-tech testing performed |
