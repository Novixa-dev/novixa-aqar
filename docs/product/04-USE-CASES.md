# 04 · Use Cases

End-to-end scenarios, each followable on the demo with the accounts in
[05](05-DEMO-GUIDE-AR.md). "→" marks a hand-off between roles.

### UC-1 · A visitor finds a home and books a viewing
1. Visitor searches on the home page or `/properties/search` (city, type,
   price, bedrooms, radius on the map).
2. Compares two or three options at `/properties/compare/…`.
3. Opens a property: photos, floor plan, virtual tour, map, costs and
   investment estimates.
4. Books a viewing (signs in or registers as **buyer**), and downloads the
   `.ics` invite.
5. → **Agent / staff** see the booking in their viewings and confirm it.

### UC-2 · An owner lists a property
1. Owner signs in as **seller** (or uses `/submit-property`).
2. Adds the property with details, photos, floor plans and a virtual-tour
   link.
3. → **Staff** review it in *Property moderation* and approve it.
4. The listing appears publicly; the seller follows bookings on it from the
   seller dashboard.

### UC-3 · A tenant reports a fault and a contractor fixes it
1. **Tenant** opens *Maintenance requests* → new request (issue,
   priority, details).
2. → **Staff** assign it to a **contractor**.
3. **Contractor** sees the job in their panel and updates its status
   through to completion.
4. **Tenant** follows the status from their dashboard.

### UC-4 · A landlord runs their rentals
1. **Landlord** reviews *Rental applications* from prospective tenants.
2. Checks *Leases* (dates, rent, tenant) on each property.
3. Tracks *Payments* received and outstanding.
4. Dashboard shows occupancy and total tenants at a glance.

### UC-5 · A renter applies online
1. Visitor opens a for-rent property → **Apply for tenancy**.
2. Fills the rental application (`/apply/{property}`) and tracks it at
   `/applications`.
3. → **Landlord / staff** review and decide.

### UC-6 · A buyer shortlists and reviews
1. **Buyer** adds properties to the wishlist (`/wishlist`).
2. Leaves a property or neighbourhood review.
3. → **Staff** moderate reviews before they are public.

### UC-7 · The office manages leads and performance
1. Enquiries sent from a property page become **leads** in the Staff
   panel; contact-form messages reach *Contact messages*, and escalated
   chats reach *Chat conversations*.
2. Staff assign and follow leads; appointments and transactions are logged.
3. Managers review *Agent performance* and dashboard widgets (top
   properties, recent transactions) and export custom reports to Excel or
   PDF.

### UC-8 · A buyer evaluates affordability
1. Visitor opens `/calculators`.
2. Runs the mortgage/financing calculator, purchase-cost and tax estimates.
3. Compares with the investment analytics on a property page (rent,
   expenses, 5-year return).

### UC-9 · The administrator configures the platform
1. **Admin** sets site name, contact details, currency and default language
   in *General settings*.
2. Manages menus, users, teams/branches and roles/permissions.
3. Reads website *Contact messages*.
4. Connects optional services (portals, payments, AI) by adding their keys.

### UC-10 · Bilingual, any device
1. Any user switches Arabic ⇄ English; layouts flip RTL/LTR.
2. Switches light/dark mode.
3. Uses the same flows on phone, tablet and desktop.
