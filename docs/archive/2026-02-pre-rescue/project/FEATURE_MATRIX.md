# Aqar Feature Matrix (TASK-002)

| Feature | Exists Upstream? | Code Location | Status | MVP Action | Notes |
|---|---|---|---|---|---|
| Property Listings & Management | Yes | `app/Models/Property.php`, `app/Filament/Resources/PropertyResource.php` | VERIFIED | KEEP / EXTEND | Core model for properties |
| Property Image Uploads & Media | Yes | `app/Models/Image.php` | VERIFIED | KEEP | Multi-photo support |
| Property Search & Filtering | Yes | `app/Http/Controllers/API/` & Livewire components | VERIFIED | EXTEND | Add Yemen city/district filters |
| Virtual Staging Tool | Yes | `app/Services/VirtualStagingService.php` | VERIFIED | KEEP | 8 design styles available |
| 3D & AR Viewer | Yes | `docs/3D_PROPERTY_VIEWER.md` | VERIFIED | KEEP | Interactive models |
| User & Role Security | Yes | Spatie Shield & Filament | VERIFIED | KEEP | Admin, Agent, Client roles |
| Arabic Translation / Localization | Partial | `lang/` directory | VERIFIED | EXTEND (TASK-004) | Full Arabic sales flow |
| RTL CSS Layout | Partial | Tailwind / Blade layouts | VERIFIED | EXTEND (TASK-005) | Structural RTL enforcement |
| Yemen Multi-Currency (YER/SAR/USD) | No | `config/` | PLANNED | ADD (TASK-006) | YER default currency |
| WhatsApp & Call CTAs | No | Public views | PLANNED | ADD (TASK-008) | Direct WhatsApp link generator |
| Lead & Inquiry Management | Partial | `buyers`/`sellers` models | VERIFIED | EXTEND (TASK-009) | Simple lead status pipeline |
| Owner Property Submission | No | Public forms | PLANNED | ADD (TASK-010) | Enters 'Pending Review' |
| Property Request Form | No | Public views | PLANNED | ADD (TASK-011) | Client property requests |
| SEO & OpenGraph Meta | Partial | Layout headers | VERIFIED | EXTEND (TASK-012) | Dynamic property tags |
| Al-Mukalla Demo Data | No | `database/seeders/` | PLANNED | ADD (TASK-013) | Fictional Al-Mukalla properties |
