# 21 — Media, Graphics & Asset Audit Report

**Product**: Aqar (عقاري) — Novixa Private Edition  

---

## 1. Asset Directory Inventory

Audited [`public/`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/public) and [`resources/`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/resources).

| Asset Path / Type | Purpose | Current Branding | Action Required |
|---|---|---|---|
| `public/build/assets/` | Compiled Vite CSS & JS bundles | Neutral / Tailwind | Recompiled on `npm run build` |
| `public/favicon.ico` | Web browser favicon | Default Laravel favicon | Replace with Novixa / Aqar brand icon |
| `resources/images/` | Site logo & background graphics | Aqar Real Estate | Configured |
| `public/sitemap.xml` | Generated XML Sitemap | Dynamic Aqar routes | Generated dynamically via `sitemap:generate` |

---

## 2. Recommended Asset Replacement Checklist

1. **Favicon Replacement**: Provide brand-specific icons:
   - `favicon.ico`
   - `apple-touch-icon.png` (180x180)
   - `favicon-32x32.png`
2. **Social Preview Image**: Provide a high-resolution default social card image (`og-preview.jpg`, 1200x630) for Facebook and Twitter sharing previews.
