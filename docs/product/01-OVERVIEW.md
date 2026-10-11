# 01 · Overview & Benefits

## What Novixa Aqar is

A complete real-estate platform delivered as one product:

- **A public property website** where visitors search, compare and book
  viewings, apply to rent, submit or request a property, use financial
  calculators, read market news and talk to an assistant.
- **Eight role-based workspaces (panels)**, one per kind of user, so each
  person sees only the tools and data that belong to them: system
  administration, the real-estate office, agents, landlords, tenants, buyers,
  sellers and maintenance contractors.

It is built to be **reused and resold** (a product, not a one-off website):
branding, contact details, menus, categories and settings are configurable
from the admin panel, and a team (multi-office) model is built in.

## Who it is for

| Audience | What they get |
|---|---|
| **Real-estate offices & brokers** | One system for listings, leads, appointments, bookings, leases, documents, transactions, staff performance and marketing |
| **Landlords** | Their properties, tenants, leases, rent payments and rental applications in one place |
| **Tenants** | Their lease, payment history and maintenance requests, with status tracking |
| **Buyers** | Saved favourites, bookings and activity, on top of the full public search |
| **Sellers** | Their own listings and enquiries |
| **Maintenance contractors** | Assigned work orders, job status and payments |
| **Agents** | Their listings, open leads and upcoming viewings |
| **Visitors** | A fast, bilingual property search with rich property pages and tools |

## Market fit

- **Arabic-first, fully bilingual.** Arabic (RTL) is the default; English
  (LTR) is one click away. Layouts mirror correctly in both directions.
- **Built for Yemen.** Governorates and cities, Yemeni Rial (YER) as the
  primary currency with SAR and USD supported, local addresses and phone
  formats, realistic Yemeni demo content.
- **Ready for the region.** Country configuration and multi-currency support
  leave room for GCC expansion without changing the core.

## Benefits

**For the business**
- One platform instead of a website plus spreadsheets plus WhatsApp threads:
  listings, CRM, rentals, maintenance and documents share one database.
- Every role self-serves (tenants report maintenance, landlords check
  payments, buyers book viewings), which takes routine work off office staff.
- Management visibility: dashboards for property stats, transactions, top
  properties and agent performance; custom reports exported to Excel/PDF.
- Moderation built in: submitted properties and reviews are approved before
  they appear publicly.

**For each user**
- A focused panel per role, so nobody has to dig through screens that aren't
  theirs, and role-based access control keeps data scoped to its owner.
- Works on phone, tablet and desktop, in light or dark mode.
- Calendar (.ics) invitations for bookings and appointments.

**For trust and quality**
- No fabricated data: integrations without a configured provider (for
  example walkability scores, which their provider does not offer in Yemen)
  show a clear "not available" state instead of invented numbers.
- Authorisation is enforced per panel (a tenant cannot open the admin
  panel), and production builds never expose debug details.

## At a glance

| | |
|---|---|
| Panels | 9 (Admin, Staff, App, Agent, Landlord, Tenant, Buyer, Seller, Contractor) |
| Demo personas | 8, one per role, all `@novixa.dev` |
| Languages | Arabic (default, RTL), English (LTR) |
| Currencies | YER (primary), SAR, USD |
| Data model | ~100 entities: properties, media, leases, payments, maintenance, leads, bookings, documents, reviews, news … |
| Automated tests | 600+ |
