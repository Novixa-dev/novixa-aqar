# 26 — Global Original-Project Reference Audit

**Product**: Aqar (عقاري) — Novixa Private Edition  
**Audit Scope**: Repository-Wide Forensic Reference Scan  
**Target Strings**: `liberu`, `liberu-real-estate`, `liberu_real_estate`, `liberu.io`, `agent.liberu.co.uk`, `Curtis Delicata`, `real-estate-laravel`  
**Date**: August 15, 2026  
**Auditor**: Independent Senior Release-Gate Team  

---

## 1. Audit Overview

This audit represents an exhaustive, repository-wide scan covering all file types (PHP, Blade, JS, TS, CSS, JSON, YAML, XML, ENV, Docker, K8s, GitHub Actions, shell scripts, seeders, settings, tests, and documentation) to identify all remaining traces of the upstream open-source project.

---

## 2. Complete Reference Inventory & Classification Matrix

| File Path | Line | Matched Value / String | Context / Category | Safe to Change? | Required Attribution? | Recommended Action |
|---|---|---|---|---|---|---|
| [`composer.json`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/composer.json#L2) | 2 | `"name": "liberu-real-estate/real-estate-laravel"` | Composer Package Identifier | **YES** | NO | Change to `"name": "novixa/aqar"`. |
| [`.env.example`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/.env.example#L16) | 16 | `DB_DATABASE=liberu_real_estate` | Database Name Example | **YES** | NO | Change to `DB_DATABASE=aqar_db`. |
| [`.env.testing`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/.env.testing#L1) | 1 | `APP_NAME="Liberu Real Estate"` | Testing App Name | **YES** | NO | Change to `APP_NAME="Aqar Testing"`. |
| [`.env.testing`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/.env.testing#L17) | 17 | `DB_DATABASE=liberu_real_estate_testing` | Testing Database Name | **YES** | NO | Change to `DB_DATABASE=aqar_testing`. |
| [`database/settings/2024_01_01_000001_create_settings_table.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/database/settings/2024_01_01_000001_create_settings_table.php#L18) | 18 | `'https://www.github.com/liberu-real-estate/real-estate-laravel'` | Settings Table Default URL | **YES** | NO | Change to Novixa repo URL or official website URL. |
| [`app/Filament/App/Widgets/SocialLinksWidget.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/app/Filament/App/Widgets/SocialLinksWidget.php#L40) | 40 | `'https://www.github.com/liberu-genealogy'` | Social Links Widget | **YES** | NO | Update to Novixa GitHub/website URL or remove. |
| [`app/Http/Controllers/API/V1/AccountingIntegrationController.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/app/Http/Controllers/API/V1/AccountingIntegrationController.php#L37) | 37 | `'liberu_accounting'` | Accounting Provider Validation Rule | **YES** (Cautious) | NO | Rename provider key to `'novixa_accounting'` or keep as legacy provider identifier with alias. |
| [`docker-compose.yml`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/docker-compose.yml#L96-L97) | 96–97 | `MYSQL_DATABASE: '${DB_DATABASE:-liberu}'`, `MYSQL_USER: '${DB_USERNAME:-liberu}'` | Docker Compose Defaults | **YES** | NO | Change default fallbacks to `aqar` and `aqar_user`. |
| [`k8s/configmap.yaml`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/k8s/configmap.yaml#L7-L13) | 7, 8, 9, 11, 13 | `app: liberu-real-estate`, `managed-by: liberu-control-panel`, `APP_NAME: "Liberu Real Estate"` | Kubernetes ConfigMap Labels & Env | **YES** | NO | Update labels and APP_NAME to `aqar` / `novixa-aqar`. |
| [`k8s/deployment.yaml`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/k8s/deployment.yaml#L9-L265) | 9, 51, 71, 189, 265 | `image: liberu/real-estate:latest`, `managed-by: liberu-control-panel` | Kubernetes Deployment Specs | **YES** | NO | Update image registry to Novixa container registry (e.g. `ghcr.io/novixa/aqar:latest`). |
| [`k8s/ingress.yaml`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/k8s/ingress.yaml#L28-L31) | 28, 31 | `${DOMAIN:-agent.liberu.co.uk}` | Kubernetes Ingress Host | **YES** | NO | Change default fallback domain to Novixa domain (e.g., `aqar.novixa.ye`). |
| [`k8s/namespace.yaml`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/k8s/namespace.yaml#L6-L13) | 6, 11, 12 | `app: liberu-real-estate`, `liberu.io/repo: "liberu-real-estate/real-estate-laravel"` | Kubernetes Namespace Spec | **YES** | NO | Rename namespace to `novixa-aqar`. |
| [`k8s/secret.yaml`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/k8s/secret.yaml#L11-L21) | 11, 12, 20, 21 | `DB_DATABASE: "liberu"`, `DB_USERNAME: "liberu"` | K8s Secret Example | **YES** | NO | Update placeholders to `aqar` / `aqar_user`. |
| [`k8s/overlays/development/kustomization.yaml`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/k8s/overlays/development/kustomization.yaml#L38) | 38 | `- name: liberu/real-estate` | Kustomize Image Override | **YES** | NO | Update image name to Novixa repo tag. |
| [`k8s/overlays/production/kustomization.yaml`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/k8s/overlays/production/kustomization.yaml#L21) | 21 | `- name: liberu/real-estate` | Kustomize Image Override | **YES** | NO | Update image name to Novixa production registry. |
| [`setup.sh`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/setup.sh#L2-L510) | 2, 510 | `# Setup script for the Liberu Real Estate project.`, `"LIBERU REAL ESTATE - INSTALLER"` | Bash Installer Script | **YES** | NO | Rebrand script header to "NOVIXA AQAR INSTALLER". |
| [`install.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/install.php#L45) | 45 | `'https://github.com/liberu-real-estate/real-estate-laravel.git'` | Legacy Auto-Installer Script | **YES** | NO | Remove legacy install script or update repo link. |
| [`error_log`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/error_log#L1-L4) | 1–4 | `/home/liberu/projects/real-estate-laravel/...` | Legacy Server Error Log | **YES** | NO | Delete obsolete unversioned root `error_log`. |
| [`README.md`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/README.md#L134-L140) | 134–140 | Links to `liberu-control-panel`, `liberu-crm`, `liberu-genealogy`, etc. | Root README Ecosystem Table | **YES** | NO | Replace root README with dedicated Novixa Aqar documentation. |
| [`VIRTUAL_STAGING_SUMMARY.md`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/VIRTUAL_STAGING_SUMMARY.md#L5) | 5 | `"Liberu Real Estate Laravel application"` | Legacy Implementation Doc | **YES** | NO | Move to `docs/legacy/` or rebrand. |
| [`VR_DESIGN_IMPLEMENTATION_SUMMARY.md`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/VR_DESIGN_IMPLEMENTATION_SUMMARY.md#L5) | 5 | `"Liberu Real Estate Laravel application"` | Legacy Implementation Doc | **YES** | NO | Move to `docs/legacy/` or rebrand. |
| [`resources/views/filament/widgets/daboville-report.blade.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/resources/views/filament/widgets/daboville-report.blade.php#L6) | 6 | `<title>Daboville Report</title>` | Orphaned Genealogy Blade View | **YES** | NO | Delete orphaned view. |
| [`resources/views/filament/widgets/descendant-chart.blade.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/resources/views/filament/widgets/descendant-chart.blade.php#L6) | 6 | `<title>Descendant Chart</title>` | Orphaned Genealogy Blade View | **YES** | NO | Delete orphaned view. |
| [`.git/config`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/.git/config) | Remote | `https://github.com/liberu-real-estate/real-estate-laravel.git` | Git Remote Origin | **YES** | NO | Execute `git remote remove origin` and add Novixa remote. |

---

## 3. License Attribution Notice

Under the MIT license of the upstream project, copyright notices and permission notices must be preserved in third-party source files where applicable. However:
- Product naming, database identifiers, K8s labels, docker defaults, and environment templates are operational configurations and **must be changed** to reflect Novixa Aqar.
- Upstream attribution should be formally centralized in an `ATTRIBUTION.md` or `NOTICE.md` file rather than lingering haphazardly across operational configurations.
