# Aqar Content, Marketing & Media System — Plan & Task Tracker

**Status of this document:** planning/tracking scaffold only. **No phase below has started.** This file exists so any future session (or the current one, later) can pick up exactly where things stopped, without re-reading the original brief from chat history.

**Source brief:** a 49-section brief from the user (2026-08-28) commissioning a complete, professional, reusable content/marketing/media production system for Novixa Aqar — brand strategy, platform strategy, video/script/hook/CTA libraries, a repository-based content operations system (`content/`), an initial prioritized content backlog, flagship production-ready content, and a 30/60/90-day roadmap. Full section-by-section mapping is in §Phase breakdown below. The brief is long and detailed by design — do not paraphrase it away when executing; each phase below references the exact source sections so nothing gets lost.

---

## ⏸ Where We Stopped / Next Action

**Phase 0 is substantively done (2026-08-29).** Real files exist now, not just this tracker — see the Phase 0 checklist below for exactly what and the new files it produced.

**Next action when resuming: start Phase 1 (Strategic Foundation)** — 1.1 through 1.7. Read `content/research/RESEARCH_NOTES.md` first; its single biggest finding (domestic-Yemen vs. Gulf-diaspora audiences likely need two different platform strategies) directly shapes 1.2 (platform tiering) and should not be re-derived from scratch. Also re-read the **Open Questions** section below before finalizing 1.2/1.7 — question 4 in particular is now sharpened by the research and worth actually asking the user rather than assuming an answer.

**Not yet done within Phase 0** (honest gaps, not silently skipped): a live visual/content audit of competitor social accounts (blocked by bot protection on direct fetch this pass — see `content/research/COMPETITOR_ANALYSIS.md`'s "Next step" note); a dedicated marketing-lens screenshot/asset survey of the product (judged as more of a Phase 2/7 production concern than a Phase-0 understanding one, and the understanding itself is now captured in `FEATURE_REALITY_CLASSIFICATION.md` + `PERSONAS.md`); deeper SEO-keyword-specific research (reserved for Phase 4.4, general content-marketing SEO advice was captured but not Arabic-keyword-specific data).

**Still true from before:** every later phase depends on Phase 0 being real, not skipped — don't jump ahead to content production before Phase 1 (strategy) is done too.

---

## Non-Negotiable Rules (apply across every phase, not just one)

Carried directly from the source brief — do not let these erode as work proceeds across sessions:

1. **Never fabricate.** No invented customers, partnerships, transactions, adoption/user/revenue numbers, integrations, certifications, awards, testimonials, or success stories. If data doesn't exist, say so or placeholder it explicitly — never invent it. (Brief §38)
2. **Classify every feature before marketing it**: Available now / Partially available / Demo-prototype / Planned / Conceptual. Only market what's actually usable now, unless explicitly and visibly labeled as future/roadmap. (Brief §39)
3. **Avoid the generic-AI-startup look.** No meaningless gradients, floating-UI-mockup clichés, generic stock imagery, hollow "innovative solution" copy, shallow AI-toned posts, fake-looking AI people, cinematic effects with no purpose, or content with no real value. Aqar's content identity must be recognizable, credible, and product-authentic — real UI, real workflows, real value. (Brief §37)
4. **Arabic-first, native-quality localization** — not literal translation. Every language decision (MSA vs. conversational, Yemen/GCC/broader-Arab-market adaptation, what's AR-only vs EN-only vs bilingual) needs a stated reason. (Brief §6)
5. **Respect the existing Novixa Aqar brand identity** — don't invent a new visual language; extend the one that exists. (Brief §14)
6. **Every content idea needs a stated reason to exist** — objective, funnel stage, KPI. No content for content's sake. (Brief §8, §44)
7. **Don't rush into production.** Understand the product and do the external research before writing a single script or post. (Brief §1, §2, §47)

---

## Known Product Facts (carried over from this session — use as a Phase-0 head start, not a substitute for it)

This session did extensive live QA/UX work on Aqar immediately before this content initiative was requested. Re-derive/expand via the browser during Phase 0, but don't start from zero:

- **Product:** Novixa Aqar (نوفيكسا عقار) — bilingual (Arabic-first, RTL default) real-estate platform, owned by Novixa. Demo brand persona used in seed data: "Dar Hadhramaut Real Estate | دار حضرموت العقارية" — a Yemen-based agency (Mukalla/Hadhramaut HQ, Sana'a + Aden branches).
- **Primary market:** Yemen (YER-primary pricing, Yemeni cities/governorates); architecture doesn't preclude broader GCC expansion later, but Yemeni demo experience is the current focus.
- **Stack:** Laravel 13.24 (PHP 8.4), Filament 5.7.6, Livewire 4.3.5, Tailwind 4, MySQL. ~103 Eloquent models, ~686 routes, 9 Filament panels.
- **9 personas/panels:** Admin, Staff, Agent, Buyer, Seller, Landlord, Tenant, Contractor, and a general "App" panel (open self-registration — the entry point for any new user with no pre-assigned role).
- **Real, working features** (confirmed live this session, not assumed): property search/filter (type, price, beds/baths, purpose), property detail pages with neighborhood stats, walkability scores, investment analytics (ROI/risk/cash-flow projections), virtual tours, 3D model viewer, floor-plan editor, booking/appointment scheduling, news/market-reports section, mortgage/tax calculators, contact forms, role-specific dashboards (revenue, transactions, maintenance requests, leases, work orders), multi-currency support (YER/USD/SAR seen in data).
- **Known NOT-real / mock / inactive** (do not market as real capabilities): WalkScore walkability numbers are algorithmic mock data, not live API data (no real key configured — and WalkScore's actual coverage of Yemen is itself unverified). 12 third-party integrations (RightMove, OnTheMarket, Zoopla, DocuSign, Xero, Sage, CRM sync, Jupix, Let's Safe, digital signature, Socialstream OAuth providers) are wired in code but entirely unconfigured/inactive — do not present as live capabilities.
- **Translation architecture:** Property/News *content* (titles, descriptions, article bodies) is single-language per row (whatever the author typed) — does not auto-translate on locale switch. UI chrome (nav, buttons, labels) is fully bilingual. Relevant for content-language planning: demo listings are Arabic-authored by design (matches the Yemen-first positioning), not a bug.
- **Current readiness status:** DEMO READY (verified, pushed to `main` as of commit `b1a6b3f`, 2026-08-28). Not yet production-hardened for a real launch (see `docs/DEPLOYMENT-ENVIRONMENT-INVENTORY.md` for what's still open — storage persistence, real mail provider, etc.). This matters for content claims: don't market production-scale guarantees that aren't true yet.
- **Where to look for more, before re-deriving from scratch:** `CLAUDE.md` (project identity/architecture), `docs/AQAR_PROGRESS.md` (full chronological build history), `docs/FINAL_PRE_DEPLOYMENT_AUDIT_REPORT.md` + its two addenda (extremely detailed live-browser findings across every persona/page/breakpoint), `docs/TEST_USERS.md` (all 8 persona logins, password `password` for all — useful for screen-recording/demo-capture work later).

---

## Phase Breakdown

Each phase lists its concrete deliverables and the source-brief sections it covers. Status legend: `NOT STARTED` / `IN PROGRESS` / `BLOCKED` / `DONE`.

### Phase 0 — Product & Market Understanding
**Status:** SUBSTANTIALLY DONE (2026-08-29) — see gaps noted above
**Brief sections:** §1, §2, §36, §39

- [x] 0.1 Product inspection with a marketing lens — done via synthesis of this session's extensive prior QA work (not a fresh dedicated screenshot pass; judged sufficient for *understanding*, asset capture deferred to production phases). Encoded into 0.4/0.5 outputs below.
- [x] 0.2 Deep external research — done (11 WebSearch queries + 2 successful WebFetch deep-dives across proptech/SaaS/real-estate marketing, MENA/GCC platform strategy, Yemen-specific digital penetration, diaspora/remittance economics, WhatsApp conversion behavior, YouTube/Reels algorithm mechanics, content repurposing frameworks). → `content/research/SOURCES.md` (12 sourced entries) + `content/research/RESEARCH_NOTES.md` (synthesis). SEO-keyword-specific and paid-media-platform-specific research still open, reserved for Phases 4.4/2.11.
- [x] 0.3 Competitor/reference analysis — done at the company-positioning level (Bayut, OpenSooq, Aqarmap) with sourced findings and a clear differentiation recommendation. → `content/research/COMPETITOR_ANALYSIS.md`. Live visual/content audit of their actual social accounts blocked (bot protection on direct fetch) — flagged as an honest open gap in that file, not silently dropped.
- [x] 0.4 Feature-reality classification — done, comprehensive (Available now / Partially available / Demo-prototype / Planned / Conceptual), sourced from this project's own verified live QA history. → `content/research/FEATURE_REALITY_CLASSIFICATION.md`. **This is the standing reference every future content piece must be checked against — treat it as load-bearing, keep it in sync with the product.**
- [x] 0.5 Audience personas — done for all 10 brief-named angles plus one research-driven addition (Gulf-based Yemeni diaspora investor, elevated to its own persona based on the 0.2 diaspora/remittance findings — a genuine strategic call, not busywork padding). → `content/audience/PERSONAS.md`.

### Phase 1 — Strategic Foundation
**Status:** NOT STARTED
**Brief sections:** §3, §5, §6, §7, §8, §9, §37

- [ ] 1.1 Define the full content ecosystem intent (awareness / education / product education / demonstration / entertainment / trust / conversion) — not just "social posts."
- [ ] 1.2 Multi-platform strategy: explicit Primary / Secondary / Experimental tiering with reasoning, plus the adaptation system (master concept → per-platform variant) rather than one-size-fits-all posting.
- [ ] 1.3 Bilingual language strategy (AR/EN) — what's AR-only, EN-only, bilingual, or separately localized, and why. MSA vs. conversational, Yemen/GCC/broader-Arab-market calibration.
- [ ] 1.4 Content pillar framework — research-derived, not the brief's example list applied blindly. Each pillar documented: purpose, audience, business objective, tone, formats, example topics/hooks/CTAs, platform fit, frequency, production complexity, expected value, metrics.
- [ ] 1.5 Content funnel mapping (TOFU/MOFU/BOFU/Retention) — objective, content type, message, CTA, platform, audience, KPI per stage.
- [ ] 1.6 Content Strategy Matrix: Audience × Problem × Pillar × Format × Platform × Funnel Stage × Goal × CTA × KPI. One of the core reference documents.
- [ ] 1.7 Distinctive Aqar content identity statement — the explicit anti-generic-AI-startup guardrails made concrete for this brand (ties to Non-Negotiable Rule #3).

### Phase 2 — Production Systems & Standards
**Status:** NOT STARTED
**Brief sections:** §4, §10, §11, §12, §13, §14, §15, §16, §17, §18, §19, §20

- [ ] 2.1 Full content-type taxonomy: short-form video, long-form video, static/carousel/infographic, image content, written content, audio — with concrete repeatable formats under each (per brief §4's extensive example list).
- [ ] 2.2 Video strategy: short-form hook/pacing/caption/CTA principles; long-form structure/chapters/retention/thumbnail/title principles. Documented as repeatable templates, not one-offs.
- [ ] 2.3 Script template library (Problem→Pain→Solution→Aqar, Hook→Fact→Explanation→CTA, Myth→Reality, Before→Aqar→After, feature-demo template, 30/60/90-second pitch variants, YouTube 5–8min and 8–15min templates, etc. — only the ones research actually supports).
- [ ] 2.4 Hook library (curiosity, problem, surprise, FOMO, mistake, myth, challenge, question, responsible-controversy, educational, story, transformation, direct-benefit, comparison) — Aqar-specific examples, no trust-damaging clickbait.
- [ ] 2.5 CTA library — varied by objective (awareness, discovery, signup, list-a-property, find-a-property, visit site, watch next, follow, share, comment, save, contact, partnership, demo request). No repetitive "visit our website" everywhere.
- [ ] 2.6 Visual content system: brand visual language, typography, spacing, iconography, photography direction, UI/screenshot treatment, motion/animation principles, thumbnail style, carousel design, subtitle/lower-third conventions, logo usage/safe zones — extending the existing Novixa Aqar identity.
- [ ] 2.7 Production specs per aspect ratio (9:16, 16:9, 1:1, 4:5): resolution, framing, text-safe areas, export/bitrate recommendations, thumbnail dimensions, filename conventions.
- [ ] 2.8 AI production tool workflow — researched and current (not assumed from training data), covering image gen, video gen, voice/narration, subtitles, editing, screen recording, thumbnails, design, copy, scheduling, analytics, asset management. Each entry: why useful, strengths/weaknesses, when (not) to use, free-tier availability.
- [ ] 2.9 End-to-end production pipeline documented (Research→Idea→Brief→Hook→Script→Visual plan→Asset creation→Voice→Editing→Captions→Thumbnail→QA→Localization→Export→Publish→Distribution→Analytics→Learnings→Iteration) with a pre-publish checklist.
- [ ] 2.10 Content QA checklist (factual accuracy, grammar AR+EN, brand consistency, product accuracy, UI accuracy, CTA correctness, visual quality, subtitle timing, audio quality, copyright risk, misleading-claims check, platform compliance, accessibility). Ties directly to Non-Negotiable Rules #1–2.
- [ ] 2.11 Paid/performance creative strategy — awareness/traffic/conversion/retargeting/demo/problem-solution/feature/lead-gen ad variants, plus a creative testing framework (Hook × Creative × Offer × CTA × Audience) with a stated test-first priority order.
- [ ] 2.12 Analytics framework — video metrics, social metrics, website metrics, business metrics (CAC, conversion, qualified leads, activation, retention) — and a stated process for turning results into iteration, not just dashboards.

### Phase 3 — Repository & Governance Architecture
**Status:** NOT STARTED
**Brief sections:** §26, §27, §28, §40, §41, §42

- [ ] 3.1 Finalize and create the `content/` repository structure (research-adjusted version of the brief's proposed layout — see reference structure below; improve rather than copy blindly).
- [ ] 3.2 Content-item folder template (`content/content-items/NNN-slug/` with brief/research/strategy/scripts-AR-EN/captions-AR-EN/hooks/cta/storyboard/shot-list/prompts/seo/publishing/analytics + assets subfolders).
- [ ] 3.3 Content item README template (ID, title, campaign, series, pillar, audience, funnel stage, objective, platform, format, language, duration, CTA, status, priority, dependencies, required assets, production notes, publishing notes, sources, post-publish performance).
- [ ] 3.4 Governance system: naming conventions, versioning, status labels (`IDEA → RESEARCH → BRIEF → SCRIPT → PRODUCTION → REVIEW → APPROVED → PUBLISHED → ANALYZING → ITERATION → ARCHIVED`), approval process, asset organization, source tracking, publishing checklist, archive rules, localization process, outdated-content handling.
- [ ] 3.5 Content ID system (e.g. `AQ-VID-0001`, `AQ-IMG-0001`, `AQ-AD-0001`, `AQ-BLOG-0001`, `AQ-CAMP-0001` — or a better scheme if research suggests one) that stays manageable as the library grows.
- [ ] 3.6 Master content index (Markdown/CSV/JSON — whichever proves more maintainable) tracking ID, title, type, pillar, audience, platform, language, campaign, status, priority, owner, dates, asset location, performance, notes.

### Phase 4 — Calendar, Campaigns, Series & SEO
**Status:** NOT STARTED
**Brief sections:** §21, §22, §23, §24, §25

- [ ] 4.1 Reusable content calendar structure (daily/weekly/monthly/quarterly/campaign/launch/event/seasonal/reactive) with a *realistic* cadence — consistency over unsustainable volume.
- [ ] 4.2 Campaign frameworks (Product Launch, Major Feature Launch, Brand Awareness, "What is Aqar?", "Why Aqar?", Find/List Your Property, For Agents/Owners/Businesses, Educational, Trust, User Story, New Market, Website Launch, Beta/Demo, Community — research-refined list, not the brief's draft taken verbatim).
- [ ] 4.3 Recurring content series (research-named, not necessarily the brief's placeholder names) — each with purpose, audience, format, visual identity, episode structure, frequency, and 10+ seeded episode ideas.
- [ ] 4.4 SEO + content strategy: topic clusters, pillar pages, supporting articles, FAQ opportunities, and explicit links from SEO topics into social/video topics (one integrated system, not two separate ones).
- [ ] 4.5 Content repurposing engine: documented pattern for turning 1 major idea into its full platform-native variant set (YouTube video, 3–8 Shorts/Reels, carousel, static graphics, LinkedIn/X/Facebook posts, blog article, FAQ, email, stories) — native adaptations, not copy-paste.

### Phase 5 — Libraries
**Status:** NOT STARTED
**Brief sections:** §31, §32

- [ ] 5.1 Prompt library (image gen, video gen, voice/narration, thumbnails, B-roll/background visuals, editing, captions, social copy, ad copy, SEO content, localization, research, ideation) — each prompt documented with purpose, inputs, expected output, usage instructions, variables.
- [ ] 5.2 Research source library — source, URL, topic, what was learned, why it matters, how it shaped the strategy. Real insights, not a bare link list.

### Phase 6 — Strategy Documents
**Status:** NOT STARTED
**Brief section:** §33 (list below is the brief's starting point — adjust after Phases 0–5 make the real structure clear)

- [ ] `content/README.md`
- [ ] `content/strategy/CONTENT_STRATEGY.md`
- [ ] `content/strategy/BRAND_POSITIONING.md`
- [ ] `content/strategy/AUDIENCE_STRATEGY.md`
- [ ] `content/strategy/CONTENT_FUNNEL.md`
- [ ] `content/strategy/PLATFORM_STRATEGY.md`
- [ ] `content/strategy/LANGUAGE_STRATEGY.md`
- [ ] `content/strategy/VIDEO_STRATEGY.md`
- [ ] `content/strategy/ADVERTISING_STRATEGY.md`
- [ ] `content/strategy/SEO_CONTENT_STRATEGY.md`
- [ ] `content/strategy/REPURPOSING_SYSTEM.md`
- [ ] `content/strategy/ANALYTICS_FRAMEWORK.md`
- [ ] `content/strategy/PRODUCTION_WORKFLOW.md`
- [ ] `content/guidelines/CONTENT_QA.md`
- [ ] `content/guidelines/VISUAL_CONTENT_GUIDELINES.md`
- [ ] `content/guidelines/VIDEO_PRODUCTION_GUIDELINES.md`
- [ ] `content/prompts/PROMPT_LIBRARY.md`
- [ ] `content/scripts/SCRIPT_LIBRARY.md`
- [ ] `content/hooks/HOOK_LIBRARY.md`
- [ ] `content/copy/CAPTION_LIBRARY.md`
- [ ] `content/ads/AD_CREATIVE_LIBRARY.md`
- [ ] `content/calendar/CONTENT_CALENDAR.md`
- [ ] `content/research/RESEARCH_NOTES.md`
- [ ] `content/research/SOURCES.md`
- [ ] `content/content-items/README.md`

### Phase 7 — Initial Content Backlog & Flagship Production Pack
**Status:** NOT STARTED
**Brief sections:** §29, §30, §44, §45

- [ ] 7.1 Large prioritized backlog (P0/P1/P2/Experimental) spanning awareness, education, product, entertainment, conversion, trust, company, real-estate knowledge, SEO, ads, video (short+long), carousels, graphics — quality-prioritized, not padded for volume.
- [ ] 7.2 Flagship production-ready pack — genuinely finished drafts, not filler, for at minimum: brand introduction ("What is Aqar?"), product explainer, user-focused ("how to find a property"), owner-focused (why list), agent-focused (why use it), one educational piece (common property-search mistakes), several short-form concepts, several long-form YouTube concepts, carousel concepts, and one initial launch/awareness campaign.
- [ ] 7.3 "What to create first" tiering (Tier 1 create immediately / Tier 2 create next / Tier 3 test / Tier 4 avoid for now) with value/effort/cost/impact/scalability/reuse reasoning per item.

### Phase 8 — Roadmap
**Status:** NOT STARTED
**Brief section:** §43

- [ ] 8.1 Days 1–30: foundation, positioning, launch content, awareness, educational basics, product introduction, initial platform presence, initial testing.
- [ ] 8.2 Days 31–60: optimization, recurring series, deeper product education, community, SEO, creative testing, ad experiments.
- [ ] 8.3 Days 61–90: scale winners, deeper content, partnerships, stronger campaigns, retargeting, authority-building, data-driven optimization.

### Phase 9 — Skills & Tooling
**Status:** NOT STARTED
**Brief sections:** §34, §35

- [ ] 9.1 Review available skills; install/use only ones with a stated reason (research, content strategy, copywriting, marketing, visual design, video, image generation, social, SEO, browser inspection/screenshots, asset generation, doc organization). Document what was installed and why.
- [ ] 9.2 Use the browser throughout Phase 0/1/2 research rather than relying on assumptions wherever a real check is possible (competitor sites, platform spec pages, current examples).

### Phase 10 — Final QA, Cleanup & Report
**Status:** NOT STARTED
**Brief sections:** §48, §49

- [ ] 10.1 Independent self-review from each named lens (CMO, Creative Director, Social Media Director, Senior Copywriter, Video Producer, Brand Strategist, Product Marketer, SEO strategist) — the explicit test: "would this compete with professional companies, or does it still look like an AI-generated project?"
- [ ] 10.2 Repository cleanup pass: remove redundant files, generic filler, duplicate ideas, weak strategies, unsupported claims, unnecessary complexity.
- [ ] 10.3 Final report to the user covering sections A–Q from the brief's §49 (what was researched, what was discovered about Aqar, strategic decisions, architecture created, folder/file structure, skills used, pillars, platform/video/ad/SEO strategy, initial backlog, highest-priority content, 30/60/90 plan, assumptions, limitations/missing info, what the user still needs to provide manually).

---

## Reference: Proposed Repository Structure

Starting point from the brief (§26) — confirm/adjust once Phase 0–3 clarify the real shape needed. Not yet created.

```text
content/
├── PLAN.md                 ← this file
├── README.md
├── strategy/
├── research/
├── brand/
├── audience/
├── personas/
├── positioning/
├── pillars/
├── platforms/
├── seo/
├── campaigns/
├── series/
├── calendar/
├── templates/
├── scripts/
├── hooks/
├── captions/
├── copy/
├── ads/
├── production/
├── prompts/
├── analytics/
├── guidelines/
├── references/
└── content-items/
    └── 001-slug/
        ├── README.md
        ├── brief.md
        ├── research.md
        ├── strategy.md
        ├── script-ar.md
        ├── script-en.md
        ├── caption-ar.md
        ├── caption-en.md
        ├── hooks.md
        ├── cta.md
        ├── storyboard.md
        ├── shot-list.md
        ├── prompts.md
        ├── seo.md
        ├── publishing.md
        ├── analytics.md
        └── assets/
            ├── images/
            ├── video/
            ├── audio/
            ├── voice/
            ├── subtitles/
            ├── thumbnails/
            └── exports/
```

---

## Open Questions for the User

Not blockers to *starting* Phase 0, but will materially shape Phases 1+ — worth answering before or during execution rather than assumed:

1. **Team/production reality:** who's actually producing this content day-to-day (just AI-assisted solo work, a small team, freelancers)? Changes the realistic cadence in Phase 8's 30/60/90 plan.
2. **Existing social presence:** does Aqar/Novixa Aqar already have any live accounts (Instagram/TikTok/YouTube/etc.), or is this a from-zero launch? Changes Phase 1.2's platform prioritization and Phase 4.2's campaign framing ("launch" vs. "relaunch").
3. **Budget for paid media:** is Phase 2.11 (ads strategy) meant to be built for an actual near-term ad spend, or purely prepared-and-shelved until later? Changes how much production effort to put into ad-specific creative now vs. deferring.
4. **Target market precision — sharpened by research, worth a direct answer before Phase 1 locks in:** the brief says "Yemen primary, GCC/broader-Arab possible later," but Phase 0.2's research found that domestic Yemen has only ~17.7% internet penetration while the Gulf-based Yemeni diaspora (~1.27M people) is both more digitally reachable *and* already a demonstrated real-estate buyer (diaspora-connected areas show measurably inflated property prices — see `content/research/RESEARCH_NOTES.md` §1). Should the diaspora audience be treated as a **co-primary** target from day one rather than a "later" expansion? This measurably changes Phase 1.2's platform tiering (WhatsApp/Instagram-led for diaspora vs. Facebook-led for domestic) and could change near-term paid-media targeting (Phase 2.11).
5. **Any existing brand assets** beyond what's in the repo (logo files, brand guideline doc, photography, prior marketing materials) that should be inventoried in Phase 0 rather than assumed not to exist?

---

## Change Log

- **2026-08-28** — File created. Full 49-section brief received and mapped into Phases 0–10 above. No execution started yet.
- **2026-08-29** — Phase 0 substantively completed. 5 new files created: `content/research/SOURCES.md`, `content/research/RESEARCH_NOTES.md`, `content/research/COMPETITOR_ANALYSIS.md`, `content/research/FEATURE_REALITY_CLASSIFICATION.md`, `content/audience/PERSONAS.md`. Key finding: domestic-Yemen and Gulf-diaspora audiences likely need distinct platform strategies (Open Question 4 sharpened accordingly — worth the user's direct input before Phase 1 locks in positioning). Next action: Phase 1 (Strategic Foundation).
