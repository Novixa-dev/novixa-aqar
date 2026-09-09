# Language Strategy

Phase 1.3. The goal is native-quality localization, not literal translation (source brief §6). Builds directly on `PLATFORM_STRATEGY.md`'s two-track split — language calibration is one of the concrete things that actually differs between Track A (domestic Yemen) and Track B (Gulf diaspora), not just platform mix.

## The core principle

Arabic-first content measurably outperforms translated English in this region — 15–25% higher engagement in KSA per the research, and 35–50% higher engagement for properly localized (not just translated) Arabic vs. translated English in Gulf markets specifically (`content/research/SOURCES.md` #12). This isn't a values statement, it's a measured performance difference. Every content decision should default to Arabic-native creation, with English as a deliberate secondary case, not the reverse.

## Dialect calibration by track

| Track | Register | Why |
|---|---|---|
| **Track A — domestic Yemen** | Yemeni-accented conversational Arabic for spoken/narrated content (video voiceover, captions written to read naturally when spoken); Modern Standard Arabic (MSA) for written educational/authority content (market reports, guides) where a slightly more formal register signals credibility | Matches how the audience actually speaks and consumes information day-to-day; MSA for "serious" written content matches the existing pattern already established in this product's own News/market-report content (already Arabic-authored in a formal register) |
| **Track B — Gulf diaspora** | Yemeni dialect still primary (this is a Yemeni-identity audience, not a generic Gulf one — dialect should signal "this is for you specifically," not blend into generic Gulf Arabic content), with awareness that the audience code-switches between Yemeni dialect, Gulf dialect (from living there), and some English (from Gulf business environments) | The research's "bilingual works best in UAE/Lebanon" finding applies here — but the more important signal for this audience is dialect-authenticity to Yemen, not polish |

**What this means practically:** don't flatten everything into generic MSA "safe" Arabic to cover both tracks with one script. A script written to feel natural to a Yemeni ear (whether in Yemen or Riyadh) will outperform a script that reads as generic pan-Arab content, per the research above.

## What's Arabic-only, English-only, bilingual, or separately localized

| Content type | Language approach | Why |
|---|---|---|
| Social video/posts (both tracks) | **Arabic-only**, native-authored | Matches the audience and the measured engagement advantage; this is not a bilingual-value content category |
| Product UI screen-recordings used in content | Arabic UI (the product's real default locale) | Authenticity — showing the actual Arabic-first product matches the brand, and per `FEATURE_REALITY_CLASSIFICATION.md`, the product genuinely is Arabic-default; showing it that way in content is honest, not a choice of convenience |
| Website/landing content | **Bilingual** (AR primary, EN secondary) | Matches the product itself (already fully bilingual AR/EN per this session's own QA work) — content should mirror that, not diverge from it |
| B2B/LinkedIn content (Developer/Agency/Business-customer personas specifically) | **English-primary, Arabic-available** | LinkedIn is the named GCC B2B platform in the research, and English carries more weight in Gulf B2B/business contexts specifically — the one deliberate exception to the Arabic-first default, scoped narrowly to this platform/persona combination only |
| Internal documentation, this content system itself | English | Matches the existing codebase/docs convention (CLAUDE.md, all prior QA docs) — no change needed here, this is process not audience-facing content |

## What "native-quality localization" means here, concretely

- Not translating an English hook into Arabic word-for-word — writing the hook in Arabic first, for how the idea actually lands in Arabic.
- Not using Gulf-dialect slang to sound "trendy" if it reads as inauthentic coming from a Yemen-rooted brand — authenticity beats borrowed trendiness per the research on trust-driven MENA audiences.
- Captions/subtitles written to match natural spoken rhythm, not a stiff literal transcript — matters doubly for muted-viewing short-form (per `content/research/SOURCES.md` #4).
- When a script or caption library entry (Phase 2.3–2.5, not yet written) needs both AR and EN versions, the EN version should read as a genuine adaptation for an English-reading audience (e.g., an international partner), not a subtitle-style crib of the Arabic.

## What this does not do

- It does not treat English as the "primary" language with Arabic as a checkbox translation — this project's own codebase already got this right (Arabic-first is the enforced default, per `CLAUDE.md`), and content should match that discipline, not regress from it.
- It does not assume one dialect register serves the whole Arab-speaking market — GCC dialect ≠ Yemeni dialect ≠ MSA, and conflating them is exactly the kind of generic-content mistake the research warns against.
