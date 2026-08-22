# Aqar Rescue — Execution Plan

A phase is complete only when its acceptance criteria are verified, not merely when code was changed. Live status: `docs/AQAR_PROGRESS.md`. Task-level detail: `docs/AQAR_TASKS.md`.

### Phase 0 — Baseline & Safety — ✅ done 2026-08-20
Repo/branch/commit/env verified (`feature/aqar-productization`, PHP 8.4.16 via `herd/bin/php84`, MySQL up, 558/0/32 test baseline confirmed twice). Doc system created (this file + `CLAUDE.md`, `AQAR_RULES.md`, `AQAR_TASKS.md`, `AQAR_PROGRESS.md`, `AQAR_DECISIONS.md`).

### Phase 1 — Critical Runtime Stabilization
Fix P0-3A/B/C (6 panels crashing on login) and P0-2 (IDOR) first — cheapest, highest-impact, and a precondition for meaningfully testing anything else in those panels. Then P0-1 (authorization), P0-7 (branding), P0-4 (unrouted MVP forms), P0-6/P0-8/P1-21 (environment). **Acceptance:** every seeded persona logs in without a fatal error; full test suite still green; each fix individually browser-verified.

### Phase 2 — Functional Completion
Anything P0-3A/B/C unblocks: give Landlord/Buyer/Seller real resources (not just a Dashboard), close P1-6/P1-7 (maintenance-request staff/landlord UI), decide P1-5 (lease template regression) with product. **Acceptance:** each of the 9 panels has enough real functionality that a demo persona isn't a dead end.

### Phase 3 — UI/UX & Responsive QA
Systematic RTL/LTR check inside panels (tables, dropdowns, modals, pagination), mobile viewport pass beyond the homepage, P2-13 (`space-x-*`→`gap-*`) and `ml-`/`mr-`→`ms-`/`me-` sweep. **Acceptance:** no layout breakage in either direction on any panel or public page, at one desktop and one mobile viewport.

### Phase 4 — Data & Demo Realism
Audit current seed data for placeholder/nonsense content; strengthen Yemeni-market realism per *reference removed — document no longer exists*. **Acceptance:** a cold demo walkthrough doesn't visibly show `Test User 1`/`Lorem Ipsum Property`-class content.

### Phase 5 — Security & Integrity
P0-1/P0-2 land here structurally even though fixed earlier for urgency; this phase is the sweep pass — P1-1 (rate limiting), P1-3 (e-signature CRUD exposure), P2-5/P2-9 (CSP, `/metrics`). **Acceptance:** `docs/AQAR_RULES.md` fully checked.

### Phase 6 — Automated Testing
P1-15/16 (Payments/Stripe, auth tests), regression tests for every P0/P1 fix in this pass. **Acceptance:** suite count grows from the 558 baseline with every fix that lacked coverage; no regressions.

### Phase 7 — Production Hardening
`.env.example` fixes (P1-17, P2-8, P2-16/17/19), `public/build` gitignore decision, CI PHP-version alignment. **Acceptance:** a fresh clone + `.env.example` boots without manual correction.

### Phase 8 — Final Browser QA
Full persona × page × language × viewport matrix from `docs/TEST_USERS.md`.

### Phase 9 — Release Candidate
Freeze scope; run `docs/AQAR_TASKS.md` end to end.

### Phase 10 — Final Readiness Audit
Regenerate `docs/AQAR_PROGRESS.md` with an explicit GO/NO-GO verdict.

---

**Current phase:** Phase 1, in progress.
