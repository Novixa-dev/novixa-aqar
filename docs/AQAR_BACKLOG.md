# Novixa Aqar — Open Backlog (single list)

Every item still open, collected from the "carried forward" sections of
`AQAR_TASKS.md` (Phases 13–35) so nothing is lost in the phase history.
Phase detail and verification stay in `AQAR_TASKS.md`; this file is the
to-do list. Last consolidated: **2026-11-13**.

Legend: **Owner** = needs the product owner (access, credentials or a
decision); **Eng** = engineering work that can be picked up directly.

## Needs the owner

| ID | Item | What is needed |
|---|---|---|
| P34-12 / P28-28 | demo2 (RUSU) presents itself as a **Saudi** company (Riyadh address, placeholder phone `+966 55 000 0000`, "since 2009 in Saudi Arabia", Riyadh project cards) while its brand description and every listing are **Yemeni** | Decide: is RUSU Saudi or Yemeni? Then either Yemeni contact details in the footer/home, or real Saudi contact details instead of the placeholder phone |
| P23-11 | Transactional mail domain not verified | DNS records on `novixa.dev` (records listed in `AQAR_KEYS_AND_SERVICES_AR.md`) |
| P23-12 | No continuous uptime monitoring | An UptimeRobot (or similar) check on `https://aqar-demo.novixa.dev/up` and `aqar-demo2` |
| — | Docker Hub image publishing is skipped (no `DOCKERHUB_USERNAME`/`DOCKERHUB_TOKEN` secrets) | Add the secrets if a public image is wanted; otherwise nothing to do (deploys build their own image) |
| — | novixa.dev company site says "7 roles" while the product has 9 panels | Push access to the company-site repo, or confirm the wording to use |

## Engineering

| ID | Item | Notes |
|---|---|---|
| P35-2 | `ui-improves` dependency lock lags `main` by ~86 packages (including major bumps: docusign 9, spatie/permission 8) | Port main's `composer.json`/`composer.lock` to `ui-improves` in its own PR with a full test run; security patches were already applied in PR #111 |
| P29-20 | Property-detail page has 15+ stacked sections | Tabs or anchor navigation; also RSS feed and categories for news |
| P32 | Full WCAG pass | Contrast ratios, heading hierarchy, keyboard traps in modals, screen-reader pass |
| P28-27 / P29-19 | Seeded photo pool is ~16 images reused across ~28 listings | Larger curated pool (external URLs, per the "no binary assets" convention) |
| P28-29 | No `hreflang` | Locale is a session value on the same URL; needs `/en/...` URLs (or a `?lang=` canonical scheme) before hreflang is possible |
| P28-26 | No long-lived cache headers for `/build/*` at the edge | Nginx/Cloudflare config on the VPS, outside the app |
| P28-25 | File cache driver | Fine for one instance; decide before scaling out Octane workers across hosts |
| P18-7 | Password reset shows no feedback when mail sending throws | Catch and show a localized error |
| P13-11 | `PermissionsSeeder` takes ~7 minutes | Not demo-visible; batch the permission inserts |

## Recently closed (for context)

- P33-2 saved searches + alerts — PR #112.
- P29-21 CI on PRs into `ui-improves` — PR #111.
- P28-24 / P29-18 Organization/WebSite JSON-LD, P13-5 maintenance-API write IDOR, PHP Security Checker red on main — PR #110.
- P33-3 verified-listing badge — declined (every public listing is already approved; the badge would mislead).
