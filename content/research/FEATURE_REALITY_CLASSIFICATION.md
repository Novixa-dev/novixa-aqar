# Feature Reality Classification

Phase 0.4 output. **This is the single most important reference file in the entire content system** — every piece of content, every script, every ad claim must be checked against this before publishing (ties to the Non-Negotiable Rules in `PLAN.md`: never fabricate, never market a feature as live when it isn't). Classification: **Available now** (real, working, verified live) / **Partially available** (real but limited/mocked in part) / **Demo-prototype** (works in the seeded demo, not yet production-hardened) / **Planned** (wired in code, not configured/active) / **Conceptual** (not present at all).

Source: direct, live, verified testing across this project's QA sessions (`docs/AQAR_PROGRESS.md`, `docs/FINAL_PRE_DEPLOYMENT_AUDIT_REPORT.md` + addenda, `docs/DEPLOYMENT-ENVIRONMENT-INVENTORY.md`), not assumption. Re-verify anything marked here before a major campaign if significant time/code changes have passed since 2026-08-29.

---

## Available now — safe to market as real, working functionality

- **Bilingual Arabic (RTL, default) / English (LTR) experience** across the public site and all 9 role panels, with a working live language switcher.
- **Property search & filtering** — type, purpose (sale/rent), price range, bedrooms/bathrooms, location — confirmed live, confirmed to actually narrow results (not cosmetic).
- **Property detail pages** with photo galleries, pricing (multi-currency: YER/USD/SAR seen in real seeded data), neighborhood statistics (population, average household income, nearby amenities/schools), walkability/transit/bike scores *(see "Partially available" — the scores render real but the underlying data source is mock, see below)*.
- **Investment analytics on property pages** — predicted ROI, a risk score, cash-flow projection (estimated rent/expenses/net cash flow/cash-on-cash return), market-position comparison. This is a genuinely differentiating feature (see `COMPETITOR_ANALYSIS.md` — matches Aqarmap's data-led positioning lane) and safe to market as real, present functionality.
- **Virtual tours and 3D model viewer** on property pages where a property has one configured.
- **Interactive floor-plan editor** (upload a floor-plan image, annotate rooms/points of interest) — Staff panel property-creation flow.
- **Booking/appointment scheduling** for property viewings — confirmed to create real appointment records.
- **News / market-reports section** — real, Arabic-authored articles (property market reports, ownership guides, home-design tips, investment strategy pieces).
- **Mortgage and tax-estimate calculators** — confirmed to output real, locale-appropriate (YER, localized) figures.
- **Contact forms** — confirmed to deliver (subject to a real mail provider being configured; see "Partially available").
- **9 role-specific dashboards** (Admin, Staff, Agent, Buyer, Seller, Landlord, Tenant, Contractor, plus a general "App" self-service panel) — each showing real, live data: revenue/transactions, active listings, bookings, leases, maintenance requests/work orders, rental applications.
- **Self-registration** — a new visitor can create an account and reach their own dashboard (this exact flow was found broken and fixed during this session's final release gate — now confirmed working end-to-end).
- **Property image/document upload** — confirmed working via a real, verified upload test (chunked upload, real progress, real preview).
- **Interactive property map** (Leaflet-based) showing listings geographically.

## Partially available — real, but with a caveat that must not be glossed over in content

- **Walkability/transit/bike scores**: the *feature* is real and renders correctly, but the underlying numbers are algorithmically generated mock data — no live WalkScore API key is configured, and WalkScore's actual real-world coverage of Yemen is itself unverified. **Do not market these specific numbers as "real walkability data" or "powered by WalkScore.**" Fine to mention the *feature* exists (a walkability estimate on every listing); not fine to claim the specific figures are externally verified.
- **Contact-form / notification email delivery**: works correctly end-to-end in principle, but depends on a real mail provider being configured in the deployment environment — the local dev/demo environment's mail setup (`mailpit`) doesn't deliver real email outside Docker. Not a product limitation, a deployment-configuration one — but content shouldn't promise "instant email confirmation" until a real provider is live in whatever environment content is pointing people at.
- **Neighborhood data displayed on property pages**: appears to be real, seeded reference data (not mock/random) for the demo's actual Yemeni neighborhoods — but hasn't been independently fact-checked against real-world Yemen demographic/amenity data. Treat as "illustrative demo data," not a verified civic dataset, until confirmed otherwise.

## Demo-prototype — works in the current seeded demo, don't claim production-scale guarantees

- The entire seeded dataset (28 properties, 8 persona accounts, 5 news articles, demo transactions/leases/bookings) is realistic-but-fictional demo content for "Dar Hadhramaut Real Estate" — appropriate to *show* as an example of what the platform does, but must never be presented as real customer/transaction history or real user testimonials (ties directly to the brief's own §38 rule).
- File storage currently writes to local disk, not guaranteed persistent across redeploys in every hosting scenario (see `docs/DEPLOYMENT-ENVIRONMENT-INVENTORY.md` §12) — don't make durability/reliability promises about uploaded content until this is resolved for the actual production deployment target.

## Planned — wired in code, not configured or active; do not market as current capability

- **Third-party portal syndication** (RightMove, OnTheMarket, Zoopla sync) — real scheduled jobs exist in code, but these are UK/international property-portal integrations with zero configured API keys, and are not relevant to the Yemen market as currently scoped. Do not mention.
- **Accounting integrations** (Xero, Sage Online), **CRM sync**, **Jupix property data**, **Let's Safe tenant screening**, **DocuSign e-signature / generic digital-signature integration** — all present in code, all unconfigured/inactive.
- **Social login (OAuth via Google/Facebook/GitHub/LinkedIn/etc.)** — the underlying package is present but zero providers are actually configured; standard email/password registration is the real, working path (correctly, this was fixed to no longer show a broken/empty "or continue with" divider when no providers are configured).
- **Real-time features (Reverb/WebSocket broadcasting)** — package installed, one backend event wired, zero frontend consumer. Nothing to market here at all currently.

## Conceptual — not present in the product; do not reference

- Anything not listed above that might be assumed of a "modern proptech platform" (e.g., blockchain, AI valuation beyond the real investment-analytics feature already listed, VR property tours as distinct from the real 3D-model-viewer/virtual-tour features that do exist) — a prior phase of this project's own history explicitly found and removed fabricated claims of exactly this kind from `README.md` (blockchain/holographic-tours/neural-valuation/VR-design claims that had no basis in the actual codebase). Do not reintroduce anything from that category into marketing content.

---

## How to use this file

Before any script, caption, ad, or landing page ships, cross-check every specific claim against this list. If a claim isn't here, don't assume it's true — verify it live (the way every entry above was verified) before publishing, and add it to this file once confirmed. Update this file whenever the product changes; it should never be allowed to drift out of sync with what's actually shipped.
