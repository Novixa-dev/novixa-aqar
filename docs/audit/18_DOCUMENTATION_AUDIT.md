# 18 — Markdown Documentation Audit Report

**Product**: Aqar (عقاري) — Novixa Private Edition  

---

## 1. Documentation Inventory Overview

The repository contains **87 Markdown (`.md`) files**:
- **30 files** floating in root directory.
- **28 files** in `docs/`.
- **2 files** in `docs/ai/`.
- **8 files** in `docs/project/`.
- **18 files** in `docs/tasks/`.
- **1 file** in `.github/`.

---

## 2. Documentation Restructuring Recommendations

To establish a clean, professional documentation layout for Novixa, the 30 root `.md` files should be migrated into structured subdirectories under `docs/`.

### Recommended Documentation Layout

```
docs/
├── README.md
├── PRODUCT.md
├── ARCHITECTURE.md
├── audit/                             <-- 24 Audit Reports
│   ├── 01_EXECUTIVE_SUMMARY.md
│   ├── 02_PROJECT_OVERVIEW.md
│   └── ...
├── features/                          <-- Migrated feature specs from root
│   ├── 3D_PROPERTY_MODELS.md
│   ├── AR_TOURS.md
│   ├── CHATBOT.md
│   ├── HOLOGRAPHIC_TOURS.md
│   ├── NEURAL_NETWORK_VALUATION.md
│   ├── SMART_CONTRACTS.md
│   └── VIRTUAL_STAGING.md
├── project/
│   ├── WORKFLOW.md
│   └── RELEASE_CHECKLIST.md
└── tasks/                             <-- Task 001 to Task 018 specifications
    ├── TASK-001-BASELINE-ANALYSIS.md
    └── ...
```

---

## 3. Recommended File Actions Matrix

| Current Path | Target Path / Action | Reason |
|---|---|---|
| Root `3D_PROPERTY_MODELS_*.md` | Move to `docs/features/3d-models/` | Clean up root directory clutter |
| Root `AR_TOUR_*.md` | Move to `docs/features/ar-tours/` | Clean up root directory clutter |
| Root `CHATBOT_*.md` | Move to `docs/features/chatbot/` | Clean up root directory clutter |
| Root `HOLOGRAPHIC_*.md` | Move to `docs/features/holographic/` | Clean up root directory clutter |
| Root `VR_DESIGN_*.md` | Move to `docs/features/vr-design/` | Clean up root directory clutter |
| Root `README.md` | Keep in Root & Update branding | Primary repository documentation entrypoint |
