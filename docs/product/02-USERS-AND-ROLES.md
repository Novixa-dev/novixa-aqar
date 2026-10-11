# 02 · Users & Roles

Every signed-in user has one role, and each role has its **own panel** (its
own URL, menu and dashboard). Panels are separate, so the access check runs
at the door: a tenant who opens `/admin` is refused (HTTP 403). Inside a
panel, records are scoped to their owner, so a landlord sees only their
properties and a tenant only their lease.

Demo logins below share the password `password`.

---

## مدير النظام — System administrator · `/admin`
**Demo:** `admin@novixa.dev` · role `super_admin` (all permissions)

Configures and governs the whole platform.

- **Dashboard:** property statistics, latest properties, recent bookings.
- **Users** and **teams/offices**; **branches**.
- **Roles & permissions** (Filament Shield).
- **General settings:** site name, contact email and phone, currency, default
  language, social links, footer.
- **Menus** for the public website.
- **Contact messages** sent from the website.
- **Property-portal settings** (Rightmove, Zoopla, OnTheMarket connectors,
  active only once credentials are added).

## موظف الإدارة — Office staff · `/staff`
**Demo:** `staff@novixa.dev` · view/create/update on most records, no deletes

The office's operational workspace, and the largest panel.

| Area | Sections |
|---|---|
| Listings | Properties, categories, features, templates, images, moderation queue, key locations |
| People & CRM | Leads, buyers, sellers, landlords, tenants, contractors, agent performance |
| Sales & viewings | Appointments, favourites, reviews, transactions, market analyses |
| Rentals | Leases, lease agreements, rental applications, maintenance requests, utility payments, energy consumption |
| Documents | Documents, document templates |
| Communication | Communication hub, chat conversations, email campaigns, news |

Dashboard widgets: property stats, agent-performance overview, recent
transactions, top-performing properties.

## وكيل عقاري — Real-estate agent · `/agent`
**Demo:** `agent@novixa.dev`

A focused daily view for an agent.

- Stats: my listings, active listings, open leads, bookings this month.
- Recent leads (name, interest, status) and recent viewings (customer,
  property, date, status).

## مالك عقار — Landlord · `/landlord`
**Demo:** `landlord@novixa.dev`

- **Properties** I own (rental inventory).
- **Leases** on those properties.
- **Payments** received against them.
- **Rental applications** from prospective tenants.
- Dashboard: total properties, occupied properties, total tenants, recent
  activity.

## مستأجر — Tenant · `/tenant`
**Demo:** `tenant@novixa.dev`

- **My lease** (terms, dates, rent).
- **Payments**: history and status.
- **Maintenance requests**: report an issue, follow its status through to
  completion.
- **Communication hub** with the office.
- Dashboard: stats overview and recent maintenance requests.

## مشتري — Buyer · `/buyer`
**Demo:** `buyer@novixa.dev`

- Dashboard: available properties, active listings, my bookings, recent
  activity.
- Uses the public site for searching, comparing, favourites (wishlist),
  booking viewings and leaving reviews, all tied to the buyer's account.

## بائع — Seller · `/seller`
**Demo:** `seller@novixa.dev`

- **My properties**: create and manage the listings I am selling (new
  submissions go through moderation).
- Dashboard: my properties, active listings, total bookings on them, recent
  activity.

## مقاول صيانة — Maintenance contractor · `/contractor`
**Demo:** `contractor@novixa.dev`

- **Maintenance requests** assigned to me: follow and update their status through to completion.
- **My contractor profile.**
- Dashboard: open jobs, completed jobs, pending payments, recent activity.

## Account & team workspace · `/app`

Available to signed-in users for account-level tasks: profile, team
(office) creation and settings, API tokens, and integrations such as Plaid.

---

## How access is enforced

| Layer | Mechanism |
|---|---|
| Panel entry | Each panel admits only its role(s); others get 403 |
| Permissions | Spatie Permission + Filament Shield, per resource and action |
| Record scope | Queries filtered to the owner (landlord → own properties, tenant → own lease, contractor → assigned jobs) |
| Teams | Office-level separation via `team_id` |

Visitors who are not signed in can use the whole public website (see
[03](03-FEATURES.md)); signing in adds bookings, favourites, applications
and reviews to their account.
