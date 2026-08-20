# 19 — Pages & Route Inventory Report

**Product**: Aqar (عقاري) — Novixa Private Edition  

---

## 1. Public Pages Inventory

| Route Path | Named Route | Controller / Livewire | Access Control | SEO Ready | UX Status |
|---|---|---|---|---|---|
| `/` | `home` | `HomeController@index` | Public | **Yes** | Excellent RTL layout & hero search |
| `/properties` | `property.list` | `App\Livewire\PropertyList` | Public | **Yes** | Live filters, YER pricing, badges |
| `/properties/{propertyId}` | `property.detail` | `App\Livewire\PropertyDetail` | Public | **Yes** | Image gallery, key specs grid, WhatsApp CTA |
| `/contact` | `contact.show` | `ContactController@show` | Public | **Yes** | Contact form & WhatsApp action |
| `/news` | `news.list` | `App\Livewire\NewsList` | Public | **Yes** | Property market news listing |
| `/news/{slug}` | `news.detail` | `App\Livewire\NewsDetail` | Public | **Yes** | Real estate news article view |
| `/calculators` | `calculators` | `App\Livewire\CalculatorsComponent` | Public | **Yes** | Mortgage & affordability calculators |
| `/about` | `about` | `PageController@about` | Public | **Yes** | Company info & vision |
| `/terms-and-conditions` | `termsandconditions` | `PageController@terms` | Public | **Yes** | Terms of service |
| `/privacy` | `privacypolicy` | `PageController@privacy` | Public | **Yes** | Privacy policy |

---

## 2. Protected User & Tenant Pages

| Route Path | Named Route | Component | Access Control | Status |
|---|---|---|---|---|
| `/wishlist` | `wishlist` | `App\Livewire\WishlistManager` | `auth` | **VERIFIED** |
| `/custom-reports` | `custom-reports.index` | `CustomReportController` | `auth`, `verified` | **VERIFIED** |
| `/booking-calendar` | `booking.calendar` | `App\Livewire\BookingCalendar` | `auth` | **VERIFIED** |
| `/tenant` | `filament.tenant.pages.dashboard` | Filament Tenant Panel | `auth` (`tenant` role) | **VERIFIED** |
| `/app` | `filament.app.pages.dashboard` | Filament Staff Panel | `auth` (`staff` / `admin` role) | **VERIFIED** |
| `/admin` | `filament.admin.pages.dashboard` | Filament Admin Panel | `auth` (`admin` / `super_admin` role) | **VERIFIED** |
