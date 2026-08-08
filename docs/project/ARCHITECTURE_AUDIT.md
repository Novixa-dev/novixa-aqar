# Architecture Audit (TASK-002)

## Overview
This audit maps out the domain architecture, data structures, controllers, services, admin panels, and extension points of the Aqar / Liberu Real Estate application.

## Core Models & Database Architecture

| Model | Table | Purpose | Key Relationships |
|---|---|---|---|
| `App\Models\Property` | `properties` | Primary real estate property listing | Belongs to `User` (Agent/Owner), Has many `Image`, `VirtualTour`, `Appointment`, `Offer` |
| `App\Models\Image` | `images` | Property photo assets & Virtual Staging | Belongs to `Property`, Has many `stagedVersions` |
| `App\Models\User` | `users` | Authenticated agents, admins, owners, clients | Has roles/permissions via Spatie Shield |
| `App\Models\Buyer` | `buyers` | Buyer leads & requirements | Belongs to `User` |
| `App\Models\Seller` | `sellers` | Seller / Property owner contacts | Belongs to `User` |
| `App\Models\Landlord` | `landlords` | Property rental landlords | Belongs to `User` |
| `App\Models\Tenant` | `tenants` | Rental property tenants | Belongs to `User` |
| `App\Models\Transaction` | `transactions` | Sales & rental financial agreements | Belongs to `Property`, `Buyer`, `Seller` |
| `App\Models\Appointment` | `appointments` | Viewing appointments & tours | Belongs to `Property`, `User` |
| `App\Models\CommunityEvent` | `community_events` | Local area events & neighborhood info | Belongs to spatial search service |
| `App\Models\VirtualTour` | `virtual_tours` | 360 virtual tour & AR viewer | Belongs to `Property` |

## Key Services Layer
- `App\Services\VirtualStagingService`: AI photo virtual staging with 8 design styles.
- `App\Services\PropertyValuationService`: Automated property market valuation calculation.
- `App\Services\SpatialSearchService` / `RadialSearchService`: Geographic radius and coordinate searching.
- `App\Services\AgentMatchingService`: Automated client-to-agent match score engine.
- `App\Services\CostOfMovingCalculatorService`: Buyer transaction cost calculations.
- `App\Services\DocuSignService`: Digital agreement signature integration.

## Admin Panel (Filament PHP)
Located in `app/Filament/Resources/`:
- `PropertyResource` (`PropertyResource.php`)
- `UserResource` (`UserResource.php`)
- `BuyerResource` (`BuyerResource.php`)
- `SellerResource` (`SellerResource.php`)
- `LandlordResource` (`LandlordResource.php`)
- `TenantResource` (`TenantResource.php`)
- `AppointmentResource` (`AppointmentResource.php`)
- `OfferResource` (`OfferResource.php`)

## Frontend & View Layer
- **Livewire Components**: Reactive property search, interactive floor plans, virtual staging gallery (`VirtualStagingGallery.php`), 3D viewer.
- **Blade Layouts**: `resources/views/layouts/` (App, Guest).
- **Vite & Tailwind Asset Pipeline**: `vite.config.js`, `postcss.config.cjs`.

## Extension & Customization Points for Aqar MVP
1. **Arabic/RTL Localization**: Expose Arabic translation classmaps in `lang/ar/` and configure HTML `dir="rtl"`.
2. **Yemen Currency & Location Specs**: Default currency to `YER` (Yemeni Rial) with multi-currency SAR/USD toggles; seed Al-Mukalla (Hadhramaut) locations (Fuwah, Al-Dais, Al-Sharj, Al-Salam, Buwaysh).
3. **WhatsApp / Direct Call Conversion**: One-click WhatsApp CTA on property pages with pre-filled Arabic message headers.
4. **Owner Property Submission Workflow**: Public form submitting to `Pending Review` status in Filament.
