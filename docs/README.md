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
| [`AQAR_UI_COMPARISON_REPORT_AR.md`](AQAR_UI_COMPARISON_REPORT_AR.md) | Arabic: live comparison of the `main`/`ui-improves` demos, with numbers |
| [`THEME_STORE_FEASIBILITY_AR.md`](THEME_STORE_FEASIBILITY_AR.md) | Arabic: feasibility study for an installable UI theme store |

## Product documentation (`product/`)

Audience-facing documentation describing what the product does and who it's
for — not how the code works. Start at [`product/README.md`](product/README.md).

| Document | Purpose |
|---|---|
| [`product/01-OVERVIEW.md`](product/01-OVERVIEW.md) | What Novixa Aqar is and who it's for |
| [`product/02-USERS-AND-ROLES.md`](product/02-USERS-AND-ROLES.md) | The 8 personas and what each can do |
| [`product/03-FEATURES.md`](product/03-FEATURES.md) | Feature-by-feature breakdown |
| [`product/04-USE-CASES.md`](product/04-USE-CASES.md) | Real-world scenarios the product handles |
| [`product/05-DEMO-GUIDE-AR.md`](product/05-DEMO-GUIDE-AR.md) | Arabic: guided demo script |
| [`product/06-TECHNICAL-OVERVIEW.md`](product/06-TECHNICAL-OVERVIEW.md) | Technical overview for a non-code audience |

## QA, audits & operations (Arabic guides)

| Document | Purpose |
|---|---|
| [`AQAR_LIVE_AUDIT_REPORT_AR.md`](AQAR_LIVE_AUDIT_REPORT_AR.md) | Live-site audit findings |
| [`AQAR_BROWSER_TEST_PLAN_AR.md`](AQAR_BROWSER_TEST_PLAN_AR.md) | Final browser-based test plan |
| [`AQAR_STAGING_MANUAL_QA_GUIDE_AR.md`](AQAR_STAGING_MANUAL_QA_GUIDE_AR.md) | Manual QA guide for the staging environment |
| [`AQAR_EXTERNAL_SERVICES_PLAN_AR.md`](AQAR_EXTERNAL_SERVICES_PLAN_AR.md) | Plan for wiring up external/third-party services |
| [`AQAR_KEYS_AND_SERVICES_AR.md`](AQAR_KEYS_AND_SERVICES_AR.md) | Registry of service credentials/keys needed and their status |
| [`STAGING_DEMO_DEPLOYMENT_PROMPT.md`](STAGING_DEMO_DEPLOYMENT_PROMPT.md) | Prompt/checklist for deploying the full demo to staging |
| [`FULL_DEMO_DATA_COMPLETION_REPORT.md`](FULL_DEMO_DATA_COMPLETION_REPORT.md) | Report on completing/fixing seeded demo data |
| [`FINAL_PRE_DEPLOYMENT_AUDIT_REPORT.md`](FINAL_PRE_DEPLOYMENT_AUDIT_REPORT.md) | Final deep audit before a production deploy |
| [`AQAR_QA_REPORT_2026-09-09.md`](AQAR_QA_REPORT_2026-09-09.md) | Dated full QA sweep and environment report |

## Deployment & release

| Document | Purpose |
|---|---|
| [`PRODUCTION-DEPLOYMENT.md`](PRODUCTION-DEPLOYMENT.md) | Production deployment and environment configuration guide |
| [`RELEASE-CHECKLIST.md`](RELEASE-CHECKLIST.md) | Local vs. production QA checklist |
| [`REPOSITORY_RELEASE_AUDIT.md`](REPOSITORY_RELEASE_AUDIT.md) | Repository/git-history/branding release-readiness record |
| [`DEPLOYMENT-ENVIRONMENT-INVENTORY.md`](DEPLOYMENT-ENVIRONMENT-INVENTORY.md) | Inventory of what each deployment environment needs/provides |
| [`../deploy/README.md`](../deploy/README.md) · [`../deploy/aqar-demo/README.md`](../deploy/aqar-demo/README.md) | The two live deploy pipelines: Dexal (cPanel) and the Novixa VPS (Docker) |

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
