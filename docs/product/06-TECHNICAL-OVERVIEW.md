# 06 · Technical Overview

## Stack

| Layer | Technology |
|---|---|
| Language / framework | PHP 8.4–8.5, Laravel 13 |
| Admin & role panels | Filament 5 (9 panels), Livewire 4 |
| Front end | Blade, Livewire, Alpine.js, Tailwind CSS 4, Vite |
| Auth | Jetstream + Fortify, Socialstream (OAuth), Sanctum (API tokens) |
| Authorisation | Spatie Permission + Filament Shield |
| Media | Spatie Media Library (images, videos, floor plans; S3-compatible storage optional) |
| Settings | Spatie Laravel Settings (site name, contact, currency, language …) |
| App server | Laravel Octane on RoadRunner |
| Database | MySQL 8 (SQLite for tests) |
| Optional | Redis + Horizon (queues), Reverb (websockets), Stripe, DocuSign, OpenAI |

## Structure

```
app/Filament/{Admin,Staff,App,Agent,Landlord,Tenant,Buyer,Seller,Contractor}/
    Pages/ Resources/ Widgets/         one directory per panel
app/Providers/Filament/*PanelProvider  panel registration (path, auth, pages)
app/Livewire/                          public-site interactive components
app/Services/                          domain services and integrations
app/Support/DemoAccounts.php           the demo personas (single source)
database/seeders/                      realistic Yemeni demo data
lang/{ar,en}.json, lang/{ar,en}/*.php  translations (Arabic default)
deploy/aqar-demo/                      VPS deploy kit (Docker, nginx, scripts)
```

## Key design points

- **Panel per role.** Each panel has its own path and admits only its role,
  which keeps menus small and authorisation simple to reason about.
- **Teams.** Most records carry `team_id` (an office). A `tenant` *role*
  (renter) is a separate concept from teams; tenant-related columns differ
  per table, so queries are written against the actual relationship.
- **Localisation.** Arabic is the default locale; `<html dir>` follows the
  locale; logical Tailwind utilities (`ms-`/`me-`) keep RTL and LTR correct.
- **Fail-safe integrations.** Optional services return "not available"
  without credentials and never invent data. Outbound calls use short
  timeouts so a slow provider can't stall a page.
- **Moderation.** User-submitted properties and reviews are held for staff
  approval.

## Quality

- 600+ automated tests (unit + feature), run on every push in CI.
- `php artisan demo:audit` exercises a running deployment over real HTTP:
  public pages, language direction, content, forms, a login as every demo
  persona, authorisation denials, and error handling.

## Deployment (demo environments)

Covered in detail in [`deploy/aqar-demo/README.md`](../../deploy/aqar-demo/README.md).

| Site | Branch | Workflow |
|---|---|---|
| https://aqar-demo.novixa.dev | `main` | `.github/workflows/deploy-aqar-demo.yml` |
| https://aqar-demo2.novixa.dev | `ui-improves` | `.github/workflows/deploy-aqar-demo2.yml` |

- GitHub Actions runs the tests, builds the Docker image, streams it to the
  server over SSH, and runs `deploy.sh`.
- **Zero-downtime releases:** the new release starts beside the old one and
  only receives traffic once its pages answer 200. A failure at any step
  leaves the old release serving.
- **Zero-downtime demo reset:** bumping `DEMO_SEED_VERSION` seeds a fresh
  database beside the live one and switches to it.
- Served through the server's existing nginx (shared with the mail server)
  behind Cloudflare, with a Cloudflare Origin certificate.

## Security notes

- Demo accounts share a public password and exist only in non-production
  environments; `UserSeeder` refuses to run in production.
- Secrets live only on the server (`/opt/<site>/secrets.env`, `db.env`) and
  in GitHub Actions secrets, never in the repository.
- The application container runs as an unprivileged user; MySQL sits on an
  internal-only Docker network.
- `APP_DEBUG=false` on deployed environments; the audit checks that no debug
  details leak.
