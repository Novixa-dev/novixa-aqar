# 06 — Database & Data Layer Audit Report

**Product**: Aqar (عقاري) — Novixa Private Edition  

---

## 1. Schema Overview & Migrations Analysis

The database layer consists of **151 migration files** covering 60+ domain tables.

### Key Data Models & Tables
1. **Properties (`properties`)**: Stores property listings with columns for title, description, location, price, bedrooms, bathrooms, area, status, property_type, latitude, longitude, postal_code, country, neighborhood_id, model_3d_url, virtual_tour_url, ar_tour_enabled.
2. **Leads (`leads`)**: Captures potential buyers/renters with name, email, phone, budget, lead_score, category, property_type, and status.
3. **Buyers (`buyers`)**: Tracks buyer preferences, requirement criteria, budget ranges, and property matching indices.
4. **Neighborhoods (`neighborhoods`)**: Contains localized Yemeni neighborhoods (e.g. Al-Mukalla districts: حي السلام, حي الشرج, حي فوه, حي الديس).
5. **Favorites (`favorites`)**: Pivot table connecting users to favorited properties.

---

## 2. Eager Loading & N+1 Query Audit

### Audit Findings
- **PropertyList Component** ([`app/Livewire/PropertyList.php#L141`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/app/Livewire/PropertyList.php#L141)): Uses `$query->with('features', 'images')` to eager load property images and amenities, preventing N+1 queries.
- **WishlistManager Component** ([`app/Livewire/WishlistManager.php#L61`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/app/Livewire/WishlistManager.php#L61)): Uses `$user->favoriteProperties()->with(['images', 'neighborhood', 'features', 'category'])`, preventing N+1 issues when displaying saved properties.

---

## 3. Indexing & Query Performance Evaluation

| Table | Index Name / Type | Columns | Purpose | Status |
|---|---|---|---|---|
| `properties` | Primary | `id` | PK | **Indexed** |
| `properties` | Index | `status`, `is_featured` | Public filter acceleration | **Indexed** |
| `properties` | Index | `latitude`, `longitude` | Spatial / radius search | **Indexed** |
| `favorites` | Unique | `user_id`, `property_id` | Enforce unique wishlist entries | **Indexed** |
| `leads` | Index | `category`, `lead_score` | Lead filtering & sorting | **Indexed** |
| `neighborhoods` | Index | `name` | Neighborhood lookup | **Indexed** |

---

## 4. Seeder & Production Readiness

- **Development / Seeders**: `PropertySeeder` and `LeadAndBuyerSeeder` seed realistic Yemeni Arabic listings and Yemeni phone numbers (`+967 77x...`).
- **Production Setup**: Running `php artisan migrate --force` builds a clean, empty production schema ready for real business data.
