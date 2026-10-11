# 03 · Features & Sections

## A. Public website

Everything here works without signing in; signing in links bookings,
favourites, applications and reviews to the user's account.

### Home `/`
- Hero search: keyword (property, area, neighbourhood) + purpose (all / for
  sale / for rent) + type (apartment, villa, land, commercial, building).
- **Featured properties**: hand-picked, verified listings with price, beds,
  baths and area.
- Quick links into every section.

### Properties `/properties` · Advanced search `/properties/search`
- Filters: keyword, price range, bedrooms, bathrooms, area, property type,
  status (sale/rent), amenities, year built, energy rating, featured only,
  country/location, and **radius search** around a point on the map.
- Sorting by date, price and more; paginated results.
- **Compare** several properties side by side
  (`/properties/compare/{ids}`).

### Property page `/properties/{id}`
- **Media:** photo gallery, videos, **floor plans**, **virtual tour** link,
  and a **3D model** viewer when a model is attached.
- Key facts: asking price, price per m², bedrooms, bathrooms, area,
  category, features and amenities, branch/team.
- **Location map** with nearby places (schools, daily errands, transport).
- **Investment analytics:** estimated annual rent, estimated expenses, cash
  flow projection, expected 5-year return, market position.
- **Property history:** price changes and past sales timeline.
- **Neighbourhood reviews** and property reviews.
- **Actions:** book a viewing, schedule a live virtual tour, apply for
  tenancy, request a free valuation, send an enquiry, call the office, chat on
  WhatsApp, add to wishlist.

### Booking & calendar
- **Book a viewing** `/properties/{id}/book` and the **booking calendar**
  `/booking-calendar`; signed-in users see their bookings at `/bookings`.
- Every booking and appointment downloads as a calendar invite (`.ics`).

### Rentals
- **Apply to rent** `/apply/{property}` (rental application form);
  applicants track applications at `/applications`.

### Submit & request
- **Submit a property** `/submit-property`: owners list a property for sale or
  rent; it enters the **moderation queue** before going live.
- **Request a property** `/property-request`: describe what you are looking
  for, and the office follows up.

### Wishlist `/wishlist`
Saved properties for the signed-in user.

### Calculators & financial tools `/calculators`
- **Mortgage / financing calculator** (loan amount, term, rate → payment).
- **Purchase costs & fees** estimate (transfer/stamp-duty style fees).
- **Property tax estimator.**
- **Investment analysis** and rental-yield tools (also on property pages).

### News `/news`
Market news and articles, with detail pages and latest-news blocks.

### Assistant & contact
- **Chat assistant** on every page: recognises common questions (search,
  booking, renting, pricing, contact) in Arabic and English, answers them
  and points to the right page, and can **escalate to a human**: the
  conversation appears in the Staff panel's *Chat conversations*.
- **Contact form** `/contact-us`: messages land in the admin panel's
  *Contact messages*.

### Site-wide
- **Arabic ⇄ English** switcher (`/lang/{locale}`), RTL/LTR layouts.
- **Light / dark mode.**
- **Responsive** on phone, tablet and desktop.
- About, services, terms, privacy pages; **sitemap.xml** for search engines.
- Sign in / register, including social sign-in when OAuth providers are
  configured.

---

## B. Panels (signed-in workspaces)

Sections per panel are listed in [02 · Users & Roles](02-USERS-AND-ROLES.md).
Shared capabilities:

| Capability | Where |
|---|---|
| Dashboards with stats and recent activity | every panel |
| Tables with search, filters, sorting, bulk actions | every resource |
| Forms with validation and media upload | create/edit screens |
| Profile editing | every role panel |
| Moderation of properties and reviews | Staff |
| Leads, appointments, transactions, agent performance | Staff, Agent |
| Leases, payments, rental applications | Staff, Landlord, Tenant |
| Maintenance requests, end to end | Tenant → Staff → Contractor |
| Documents and document templates | Staff |
| Email campaigns, news, communication hub, chat | Staff |
| Custom reports, Excel and PDF export | `/custom-reports` |
| Users, teams, roles, settings, menus, portals | Admin |

---

## C. Integrations

The code ships with connectors; each switches on when its credentials are
configured, and each fails safe when they are not.

| Integration | Purpose | State without credentials |
|---|---|---|
| OpenAI | AI-generated property descriptions and AI investment insights | AI features hidden; everything else works |
| Stripe | Online payments (`/payments/*`) | Payments off |
| DocuSign | Digital-signature service layer (for agreements) | Off |
| Google/other OAuth (Socialstream) | Social sign-in | Email/password only |
| Rightmove / Zoopla / OnTheMarket / Jupix | Listing syndication to portals | Off |
| Walk Score | Walk/transit/bike scores | Hidden ("not available"); the provider does not cover Yemen |
| Neighbourhood data, postcode, credit/background check, insurance, accounting (QuickBooks), Plaid | Optional enrichment | Off; no fabricated data |
| Object storage (S3-compatible) | Media at scale | Local storage |
| Email (SMTP/Resend) | Notifications | Logged instead of sent on the demo |
