# Documentation Index

Full documentation map for Novixa Aqar. Start at the root [`README.md`](../README.md) for a product overview and local setup; this index is for finding a specific document.

## Getting started & project status

| Document | Purpose |
|---|---|
| [`AQAR_PROGRESS.md`](AQAR_PROGRESS.md) | Live, continuously-updated project status log — the fastest way to understand what's been done and what's current |
| [`AQAR_TASKS.md`](AQAR_TASKS.md) | Live task tracker with root-cause/verification detail per item |
| [`AQAR_DECISIONS.md`](AQAR_DECISIONS.md) | Architectural/product decision log with rationale |
| [`AQAR_RULES.md`](AQAR_RULES.md) | Binding release-gate rules for this project |
| [`TEST_USERS.md`](TEST_USERS.md) | Seeded demo accounts for every role/panel |

## Deployment & release

| Document | Purpose |
|---|---|
| [`PRODUCTION-DEPLOYMENT.md`](PRODUCTION-DEPLOYMENT.md) | Production deployment and environment configuration guide |
| [`RELEASE-CHECKLIST.md`](RELEASE-CHECKLIST.md) | Local vs. production QA checklist |
| [`REPOSITORY_RELEASE_AUDIT.md`](REPOSITORY_RELEASE_AUDIT.md) | Repository/git-history/branding release-readiness record |

## Architecture & API

| Document | Purpose |
|---|---|
| [`MODULAR_ARCHITECTURE.md`](MODULAR_ARCHITECTURE.md) | The `app/Modules/` plugin architecture |
| [`CORE_API_V1.md`](CORE_API_V1.md) | Core REST API (v1) reference |

## Feature reference docs

| Document | Feature |
|---|---|
| [`3D_PROPERTY_MODELS.md`](3D_PROPERTY_MODELS.md) / [`3D_PROPERTY_MODELS_QUICKSTART.md`](3D_PROPERTY_MODELS_QUICKSTART.md) / [`3D_PROPERTY_VIEWER.md`](3D_PROPERTY_VIEWER.md) | Interactive 3D property models |
| [`AGENT_MATCHING_SERVICE.md`](AGENT_MATCHING_SERVICE.md) / [`AGENT_MATCHING_QUICK_START.md`](AGENT_MATCHING_QUICK_START.md) | Buyer↔agent matching |
| [`CHATBOT_FEATURE.md`](CHATBOT_FEATURE.md) / [`CHATBOT_ARCHITECTURE.md`](CHATBOT_ARCHITECTURE.md) / [`CHATBOT_UI_DESIGN.md`](CHATBOT_UI_DESIGN.md) | AI support chatbot |
| [`COMMUNITY_EVENTS.md`](COMMUNITY_EVENTS.md) | Community events calendar on property detail pages |
| [`ENHANCED_SEARCH_FILTERS.md`](ENHANCED_SEARCH_FILTERS.md) | Energy/walkability/transit/bike-score search filters |
| [`INTERACTIVE_FLOOR_PLANS.md`](INTERACTIVE_FLOOR_PLANS.md) / [`INTERACTIVE_FLOOR_PLANS_ARCHITECTURE.md`](INTERACTIVE_FLOOR_PLANS_ARCHITECTURE.md) | Interactive floor plan editor |
| [`INVESTMENT_ANALYTICS.md`](INVESTMENT_ANALYTICS.md) | AI investment analysis on property detail pages |
| [`NEIGHBORHOOD_FEATURE.md`](NEIGHBORHOOD_FEATURE.md) | Neighbourhood insights (demographics, amenities) |
| [`NEWS_FEATURE.md`](NEWS_FEATURE.md) | Market news/CMS |
| [`PROPERTY_HISTORY.md`](PROPERTY_HISTORY.md) | Property price/listing history |
| [`PROPERTY_TAX_ESTIMATOR.md`](PROPERTY_TAX_ESTIMATOR.md) | Registration/transfer fee estimator |
| [`VIRTUAL_TOURS_INTEGRATION.md`](VIRTUAL_TOURS_INTEGRATION.md) / [`VIRTUAL_TOURS_QUICKSTART.md`](VIRTUAL_TOURS_QUICKSTART.md) | Virtual/live property tours |
| [`WALKABILITY_SCORES.md`](WALKABILITY_SCORES.md) / [`WALKABILITY_UI_PREVIEW.md`](WALKABILITY_UI_PREVIEW.md) | Walkability/transit/bike scores |
| [`WISHLIST_API.md`](WISHLIST_API.md) | Wishlist/favorites |

## Historical / archived documentation

Point-in-time audit and QA reports, and superseded process documents, are kept for historical record under [`archive/`](archive/) — each subfolder has its own index explaining what's there and why it was archived:

- [`archive/2026-08-rescue-qa-history/`](archive/2026-08-rescue-qa-history/README.md) — the dated audit/QA reports from the 2026-08 rescue effort (original baseline audit, demo-readiness audit, final release QA, end-to-end acceptance report, browser test matrix, remaining-work snapshot)
- [`archive/2026-02-pre-rescue/`](archive/2026-02-pre-rescue/README.md) — documentation that predates the rescue effort entirely (39 numbered audit iterations, old task-analysis files, old feature-implementation summaries)

**Do not treat anything under `archive/` as a description of current system state** — for that, use `AQAR_PROGRESS.md` and the tables above.
