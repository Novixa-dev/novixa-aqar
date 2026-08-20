# 07 — API Architecture & Inventory Report

**Product**: Aqar (عقاري) — Novixa Private Edition  

---

## 1. API Route Overview

The application features **607 total routes**, including a comprehensive RESTful API under `/api/v1/` protected by `auth:sanctum` and rate limiters (`throttle:60,1`).

---

## 2. API Endpoint Inventory Matrix

| Endpoint Path | Method | Controller & Action | Auth Requirement | Purpose | Status |
|---|---|---|---|---|---|
| `/api/v1/public/agencies/{team}/properties` | `GET` | `ApiPublicWebsiteController@properties` | None (Public Rate Limited) | Public property listings for agency | `COMPLETE` |
| `/api/v1/public/agencies/{team}/properties/{property}` | `GET` | `ApiPublicWebsiteController@property` | None (Public Rate Limited) | Public property detail payload | `COMPLETE` |
| `/api/chatbot/start` | `POST` | `ChatbotController@startConversation` | Rate-limited (throttle:chatbot) | Initialize AI assistant conversation | `COMPLETE` |
| `/api/chatbot/message` | `POST` | `ChatbotController@sendMessage` | Bearer Token / Session | Send message to AI chatbot | `COMPLETE` |
| `/api/v1/properties` | `GET/POST` | `ApiPropertyController` | `auth:sanctum` | CRM property CRUD operations | `COMPLETE` |
| `/api/v1/leads` | `GET/POST` | `ApiLeadController` | `auth:sanctum` | Lead management API | `COMPLETE` |
| `/api/v1/buyers` | `GET/POST` | `ApiBuyerController` | `auth:sanctum` | Buyer requirements API | `COMPLETE` |
| `/api/v1/documents` | `GET/POST` | `ApiDocumentController` | `auth:sanctum` | Compliance document workflow | `COMPLETE` |
| `/api/v1/vr-designs` | `GET/POST` | `VRPropertyDesignController` | `auth:sanctum` | VR 3D room design management | `COMPLETE` |

---

## 3. Security, Authentication & Rate Limiting

- **Sanctum Authentication**: Protected API endpoints enforce `Bearer` token verification.
- **Throttling**: Public endpoints enforce `throttle:60,1` (60 requests per minute). Chatbot endpoints enforce custom `throttle:chatbot` middleware.
- **Resource JSON Format**: API responses follow standardized JSON structures via Laravel API Resources.
