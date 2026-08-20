# 28 — License & Attribution Deep Audit

**Product**: Aqar (عقاري) — Novixa Private Edition  
**Audit Purpose**: Intellectual Property, Open-Source License Compliance & Attribution Verification  
**Date**: August 15, 2026  
**Auditor**: Independent Senior Release-Gate Team (Open-Source & IP Compliance)  

> [!NOTE]
> This audit provides engineering and open-source licensing analysis based on industry-standard software compliance practices. It does not constitute formal legal counsel. Formal legal review is recommended prior to commercial distribution.

---

## 1. Executive Summary

The project is derived from the open-source repository `liberu-real-estate/real-estate-laravel`, which was published under the **MIT License**. Under the terms of the MIT license, derivative works, substantial modifications, rebranding, and proprietary commercial use/private deployment are fully permitted, provided that the original copyright notice and permission notice are preserved in source code files or an accompanying attribution notice.

Currently, there is no standalone `LICENSE` or `ATTRIBUTION.md` file in the root directory.

---

## 2. Dependency License Matrix

### A. Backend PHP Packages (Composer)

| Package | Declared License | Compatibility | Attribution Requirement |
|---|---|---|---|
| `laravel/framework` | MIT | Permissive / Fully Compatible | Standard MIT notice |
| `filament/filament` | MIT | Permissive / Fully Compatible | Standard MIT notice |
| `filament/spatie-laravel-media-library-plugin` | MIT | Permissive / Fully Compatible | Standard MIT notice |
| `bezhansalleh/filament-shield` | MIT | Permissive / Fully Compatible | Standard MIT notice |
| `bursteri/socialstream` | MIT | Permissive / Fully Compatible | Standard MIT notice |
| `livewire/livewire` | MIT | Permissive / Fully Compatible | Standard MIT notice |
| `spatie/laravel-medialibrary` | MIT | Permissive / Fully Compatible | Standard MIT notice |
| `spatie/laravel-permission` | MIT | Permissive / Fully Compatible | Standard MIT notice |
| `spatie/laravel-sitemap` | MIT | Permissive / Fully Compatible | Standard MIT notice |
| `docusign/esign-client` | MIT / Apache-2.0 | Permissive / Fully Compatible | Standard Apache/MIT notice |
| `stripe/stripe-php` | MIT | Permissive / Fully Compatible | Standard MIT notice |
| `spiral/roadrunner-cli` | MIT | Permissive / Fully Compatible | Standard MIT notice |

### B. Frontend JavaScript & CSS Libraries (NPM)

| Library | Declared License | Compatibility | Attribution Requirement |
|---|---|---|---|
| `tailwindcss` | MIT | Permissive / Fully Compatible | Included in minified bundle |
| `alpinejs` | MIT | Permissive / Fully Compatible | Included in bundle |
| `flowbite` | MIT | Permissive / Fully Compatible | Included in bundle |
| `leaflet` | BSD-2-Clause | Permissive / Fully Compatible | BSD 2-Clause notice preserved |
| `three` (Three.js) | MIT | Permissive / Fully Compatible | MIT notice preserved |
| `@google/model-viewer` | Apache-2.0 | Permissive / Fully Compatible | Apache 2.0 license notice |
| `preline` | MIT | Permissive / Fully Compatible | MIT notice preserved |

### C. Typography & Assets

| Asset Category | Source | License | Status |
|---|---|---|---|
| **Cairo Font** | Google Fonts (Mohamed Gaber) | SIL Open Font License (OFL 1.1) | Free for commercial & web use |
| **Tajawal Font** | Google Fonts (Boutros Fonts) | SIL Open Font License (OFL 1.1) | Free for commercial & web use |
| **Inter Font** | Google Fonts (Rasmus Andersson) | SIL Open Font License (OFL 1.1) | Free for commercial & web use |
| **Heroicons** | Tailwind Labs | MIT | Free for commercial & web use |

---

## 3. What Can Be Rebranded vs What Must Remain

| Component / Artifact | Can It Be Rebranded to Novixa? | Must It Remain? | Guidance / Rationale |
|---|---|---|---|
| **Product Name & UI** | **YES** | NO | Rebrand all UI, titles, headers, logos, and metadata to "Novixa Aqar / عقاري". |
| **Domain & Routes** | **YES** | NO | Use Novixa domain names and routing structures. |
| **Database Schemas & Data** | **YES** | NO | Remove upstream database names and replace demo records with Novixa data. |
| **Composer Package Name** | **YES** | NO | Change `"liberu-real-estate/real-estate-laravel"` to `"novixa/aqar"`. |
| **Upstream Git Remote** | **YES** | NO | Disconnect remote and initialize private Novixa repository. |
| **Original MIT Copyright Notice** | **NO** (Must be acknowledged) | **YES** | Retain credit in an `ATTRIBUTION.md` or `THIRD_PARTY_NOTICES.md` file acknowledging `Liberu Real Estate (Curtis Delicata)` as upstream base. |
| **Vendor Package Headers** | **NO** | **YES** | Leave third-party `vendor/` and `node_modules/` headers intact. |

---

## 4. IP Compliance Action Plan

1. **Add `ATTRIBUTION.md`**:
   Document upstream lineage:
   > *"Novixa Aqar is built upon foundational code from Liberu Real Estate (MIT License, Copyright (c) Curtis Delicata and contributors), heavily customized, localized, and extended by Novixa for the Yemeni and Arab real-estate markets."*
2. **Add Private/Proprietary `LICENSE`**:
   Add Novixa's commercial/proprietary software license for Novixa Private Edition code.
3. **Verify Clean Asset Provenance**:
   Ensure placeholder property images in demo seeders use royalty-free Unsplash/Pexels licenses or Novixa proprietary photography.
