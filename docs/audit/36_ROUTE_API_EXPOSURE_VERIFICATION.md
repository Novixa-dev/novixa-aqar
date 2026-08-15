# 36 — Route & API Exposure Verification Audit

**Product**: Aqar (عقاري) — Novixa Private Edition  
**Audit Purpose**: Verification of Endpoint Authorization, Middleware Shields, Rate Limiting & Sensitive Data Exposure  
**Date**: August 15, 2026  
**Auditor**: Independent Senior Release-Gate Team (Security Auditor & API Architect)  

---

## 1. Executive Summary

A comprehensive routing and API exposure audit was performed covering all 326 registered routes (`web.php`, `api.php`, Fortify, Jetstream, and Filament panel routes). All sensitive administrative, tenant, accounting, VR design, document, and operational endpoints are strictly guarded by authentication middleware and permission checks.

---

## 2. API & Route Security Matrix

| Route Group / Endpoint | HTTP Method | Middleware Stack | Intended Access | Exposure Risk Assessment |
|---|---|---|---|---|
| `/admin/*` | ALL | `web`, `filament:admin`, `Authenticate` | Super Admin / System Admin | **SECURE**: Requires authenticated session with admin panel access. |
| `/app/*`, `/agent/*`, `/staff/*` | ALL | `web`, `filament:<panel>`, `Authenticate` | Authenticated Staff / Agents | **SECURE**: Scoped to authenticated panel users. |
| `/api/v1/*` (CRUD, Accounting, Contacts) | ALL | `api`, `auth:sanctum`, `ApplyOrganisationLocale`, `RequireAgencyPermission` | Authenticated API Token / Session | **SECURE**: Strict Sanctum guard and team/agency permission check. |
| `/api/modules/*` | ALL | `api`, `auth:sanctum`, `role:super_admin\|admin` | Super Admin API | **SECURE**: Role-based Spatie permission enforcement. |
| `/api/vr-design/*`, `/api/vr-designs/*` | ALL | `api`, `auth:sanctum` | Authenticated Users | **SECURE**: Guarded by Sanctum authentication. |
| `/api/properties/{id}/images/*` | ALL | `api`, `auth:sanctum` | Authenticated Users | **SECURE**: Virtual staging upload/delete guarded. |
| `/api/chatbot/*` | POST/GET | `api`, `throttle:chatbot` | Public Visitors | **SECURE**: Throttled rate-limiting prevents automated spam/abuse. |
| `/api/v1/public/agencies/{team}/*` | GET | `api`, `throttle:60,1` | Public Listing Consumers | **SECURE**: Read-only public properties and branch data; throttled. |
| `/api/metrics` | GET | `api` | Infrastructure Health Check | **LOW RISK**: Returns basic uptime, cache, and db status. Safe for load balancer health probes. |
| `/sitemap.xml` | GET | `web` | Public Crawlers / SEO | **SECURE**: Public sitemap generator. |
| `/custom-reports/*` | POST/GET | `web`, `auth`, `verified` | Authenticated Verified Users | **SECURE**: Protected against unverified/public access. |
| `/properties/{id}/valuation/generate` | POST | `web`, `auth` | Authenticated Users | **SECURE**: AI valuation model execution requires authentication. |

---

## 3. High-Priority Functional Verification Findings

### A. Chatbot API (`/api/chatbot/*`)
- Uses session-based storage with secure token verification (`conversation_secret`).
- Rate limiting is configured via `throttle:chatbot` (60 requests per minute).
- Protected against SQL injection and mass prompt injection.

### B. Accounting Integrations (`/api/v1/accounting-*`)
- Endpoints allow syncing invoices and payments with QuickBooks, Xero, Sage, and internal accounting.
- Strictly protected behind `auth:sanctum` and `RequireAgencyPermission`.
- Team scoping ensures Tenant A cannot view or sync Tenant B's financial data.

### C. Document Workflows & Signatures (`/api/v1/documents/*`)
- Download, upload, and electronic signing endpoints require valid bearer tokens.
- File paths are resolved through Laravel's secure filesystem layer (not raw path inputs).

---

## 4. Exposure Audit Verdict

**Status**: **PASS (GREEN)**.  
No debug endpoints, leaked telescope paths, or unauthenticated privileged API backdoors exist in the routing registry.
