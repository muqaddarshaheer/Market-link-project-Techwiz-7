# MarketLink — Professional Project Documentation

**Project title:** MarketLink — Farmers’ Market Pickup Platform  
**Competition:** Aptech TechWiz 7 · End-to-End Web Solutions  
**Developer:** Muqaddar Shaheer  
**Document type:** Complete feature & technical documentation (A1)  
**Version:** 2.0 · Date: 28 September 2026  
**Repository:** https://github.com/muqaddarshaheer/Market-link-project-Techwiz-7

---

## Document purpose

This document is written so that a judge, teacher, or client can open it and immediately understand:

- what MarketLink is,
- why it exists,
- every major feature (role by role),
- how to install and test it,
- and why the solution is production-minded for a TechWiz submission.

**Core business rule:** MarketLink does not offer courier delivery and does not take online payments. Customers reserve produce online, collect at the market stall, and pay the farmer in person (Rupees).

---

## Table of contents

1. Executive summary  
2. Problem statement & solution  
3. Project objectives  
4. System roles  
5. Technology stack  
6. System architecture  
7. Feature catalogue — Public website  
8. Feature catalogue — Guest checkout  
9. Feature catalogue — Customer panel  
10. Feature catalogue — Farmer panel  
11. Feature catalogue — Admin panel  
12. Cross-cutting features (theme, chat, search, PWA)  
13. Database design  
14. Security & quality  
15. Installation guide  
16. Demo accounts  
17. Testing checklist  
18. Folder map  
19. Development phases  
20. Submission notes  

---

## 1. Executive summary

MarketLink is a full-stack Laravel web application that connects **approved farmers’ market stalls** with people who want to **pre-order fresh produce for pickup**.

The platform covers the full journey:

- Discover markets, farmers, and seasonal produce  
- Reserve items in a cart (guest or logged-in customer)  
- Confirm a pickup slot and receive a clear **Order ID**  
- Coordinate with the farmer through **in-app chat**, call, or WhatsApp  
- Collect and pay at the stall  
- Leave a review after completion  

Farmers manage stock, slots, expenses, land, weather, and crop planning tools. Admins approve stalls, moderate content, and export reports.

The result is a complete, demo-ready marketplace focused on **trust, pickup logistics, and farmer productivity** — not delivery apps.

---

## 2. Problem statement & solution

| Real-world problem | MarketLink solution |
|--------------------|---------------------|
| Customer arrives and favourite items are sold out | Pre-order locks quantity until the farmer’s cutoff |
| Farmer cannot predict weekend demand | Live order board: Placed → Accepted → Ready → Completed |
| Pickup details are lost in phone calls | Order ID + printable slip + stored chat thread |
| Guests abandon checkout if forced to register | Guest cart + stepped guest checkout + session-gated order page |
| Farmer estimates crop profit by guesswork | Crop Calculator, Smart Crop Guide, Crop Health assistant |
| Weather planning lives outside the app | Live weather strip on the farmer dashboard |
| Judges cannot install Composer-heavy ZIPs | `vendor/` included, one-click `import-db.php`, UTF-8 SQL, repair scripts |

---

## 3. Project objectives

1. Deliver a working multi-role marketplace (Guest, Customer, Farmer, Admin).  
2. Enforce pickup-only commerce with clear Order ID and stall payment.  
3. Provide farmer tools that go beyond simple CRUD (weather, lands, expenses, crop guides).  
4. Support bilingual / accessible UX where relevant (HarvestWise EN/UR, farmer panel language toggle).  
5. Ship reliably on XAMPP for TechWiz evaluation (ZIP-friendly install).  
6. Present a polished UI with dark/light theme and consistent MarketLink branding.

---

## 4. System roles

### 4.1 Guest
Can browse the public site, use guest cart, complete stepped checkout, receive Order ID, open order chat and slip without creating an account (session-protected).

### 4.2 Customer
Registered shopper with dashboard, favourites, notifications, full order history, reorder, reviews, and persistent cart.

### 4.3 Farmer
Approved stall operator managing products, pickup slots, order workflow, chat, sales insights, expenses, lands, and smart farming helpers.

### 4.4 Admin
Platform operator who approves farmers, manages markets/categories/products, moderates reviews, posts announcements, maintains chatbot FAQs, and exports reports.

---

## 5. Technology stack

| Layer | Technology |
|-------|------------|
| Backend framework | Laravel 11 |
| Language | PHP 8.2+ |
| Database | MySQL 8 / MariaDB (XAMPP-friendly) |
| Templating | Blade |
| Front-end CSS/JS | Bootstrap 5.3, Bootstrap Icons, custom `marketlink.css`, Alpine.js |
| Maps | Leaflet + OpenStreetMap |
| Charts | Chart.js |
| Auth | Session authentication, bcrypt passwords, role middleware |
| Packaging | Composer; `vendor/` shipped for judge ZIP installs |

---

## 6. System architecture

```text
Browser (Blade UI + CSS/JS)
        │
        ▼
routes/web.php  (+ routes/api.php)
        │
        ▼
Controllers  →  Services (OrderService, NotificationService, …)
        │
        ▼
Eloquent Models  →  MySQL database `marketlink`
```

**Primary layouts**
- `layouts/app` — public pages  
- `layouts/customer` — customer panel  
- `layouts/farmer` — farmer panel  
- `layouts/admin` — admin panel  
- `layouts/guest` — login / register  

**Key services**
- `OrderService` — place order, stock rules, cutoff handling  
- `NotificationService` — in-app notifications for orders and chat  

---

## 7. Feature catalogue — Public website

### 7.1 Home page
- Brand-led hero with MarketLink identity and clear CTA  
- Featured markets, growers, and produce  
- Trust strip (no delivery / pay in person / approved growers)  
- **What’s Growing?** harvest calendar for 2026 seasonal events  
- Farmer story video band and customer review marquee  
- Final CTA to browse markets or create an account  

### 7.2 Markets module
- Market listing with location and schedule context  
- Market detail page showing associated stalls and atmosphere  
- Map support via Leaflet where coordinates are available  

### 7.3 Farmers module
- Directory of approved stalls  
- Farmer profile: phone, market days, product count, produce list  
- Public path into stall products for pickup booking  

### 7.4 Products module
- Catalog with category / stall context  
- Product detail with price, unit, availability  
- Add to cart (customer) or guest cart actions  
- Professional Add / View action layout on cards  

### 7.5 Global search
- Navbar search for produce, stalls, and markets  
- Live suggestions endpoint (`/search/suggest`)  
- Full search results page  

### 7.6 HarvestWise (Produce Guide)
- Interactive guide to fruit and vegetable benefits  
- English / Urdu content support  
- Optional speak / assistant reading for accessibility  
- Linked from main navigation as HarvestWise  

### 7.7 Harvest calendar (What’s Growing?)
- Month grid for harvest / arrival / pickup / season events  
- Search and type filters, grid/list views  
- Coming-up strip and favourites hooks  
- Day modal with benefit facts and stall context when growers match  

### 7.8 Static & trust pages
- About MarketLink  
- Contact form (rate-limited)  
- Privacy Policy  
- Terms of Use  
- Sitemap  

### 7.9 FAQ chatbot
- Floating assistant for common market/pickup questions  
- Admin-managed FAQ bank  
- Optional text-to-speech endpoint for spoken answers  

---

## 8. Feature catalogue — Guest checkout

### 8.1 Guest cart
- Add / update / remove products without login  
- Dedicated guest cart page  

### 8.2 Stepped guest checkout
Three clear steps designed for speed and clarity:

1. **You** — name and contact  
2. **Pickup** — slot / market timing  
3. **Confirm** — review and place reservation  

### 8.3 Guest confirmation experience
- Redirects to guest order page with **Order ID** (not the homepage)  
- Session stores allowed guest order IDs for security  
- Order chat available for guests (`guest_name` + nullable `user_id`)  
- Printable order slip  
- Call / WhatsApp actions prefilled with Order ID  

### 8.4 Guest quick order
- One-product quick path for fast reservation (`guest/quick/{product}`)  

---

## 9. Feature catalogue — Customer panel

### 9.1 Authentication & account
- Customer registration and login  
- Profile update and password change  
- Secure logout  

### 9.2 Dashboard
- Snapshot of recent orders and activity  
- Shortcuts into favourites, notifications, and reviews  

### 9.3 Cart & checkout
- Persistent cart for logged-in customers  
- Checkout with pickup slot selection  
- Stock and cutoff validation through OrderService  

### 9.4 Orders
- Order history list  
- Order detail with timeline statuses  
- Update / cancel before cutoff (where allowed)  
- Reorder previous items  
- Invoice / slip download or print view  
- **In-order chat** with the farmer (`#order-chat`)  

### 9.5 Favourites
- Save products for later  
- Toggle favourites from browsing flows  

### 9.6 Notifications
- In-app notifications for order and chat events  
- Polling endpoint for near-live updates  
- Mark one / mark all as read  

### 9.7 Reviews
- Leave a review after completed pickup  
- Helpful votes on reviews  
- Reviews surface on home and product/farmer contexts  

---

## 10. Feature catalogue — Farmer panel

### 10.1 Farmer dashboard
- KPI cards for orders and stall health  
- Weather strip with optional speak helper  
- Tips and land shortcuts  
- Fast path into new and pending orders  

### 10.2 Product management
- Create, update, delete stall products  
- Stock and pricing controls  
- Product templates (save / apply) for repeated listings  

### 10.3 Pickup slots
- Configure when customers can collect  
- Aligns with checkout availability  

### 10.4 Order workflow
Full operational board:

1. Receive new order (notification)  
2. **Accept** or **Decline**  
3. Mark **Ready** for pickup  
4. Mark **Complete** after collection and payment  

Additional tools on each order:

- Open chat thread  
- Print / view order slip  
- Call customer  
- WhatsApp with Order ID context  
- Unread chat badges on the orders list  

### 10.5 Sales & insights
- Sales / insights views for stall performance  
- Helps farmers plan stock for the next market day  

### 10.6 Expenses tracker
- Record farm/stall expenses  
- Update and delete expense entries  
- Supports simple profit awareness alongside sales  

### 10.7 My Land
- Register plots / lands  
- Track crop stage and expected harvest window  
- Care tips tied to planted crop context  

### 10.8 Crop Calculator
- Estimate production / planning numbers for a crop  
- Practical helper before committing land and budget  

### 10.9 Smart Crop Guide
- Browse smart crop records maintained by admin  
- Detail pages with growth / harvest guidance  

### 10.10 Crop Health assistant
- Conversational / guided crop health support  
- Message, answer, photo, reset, and speak endpoints  
- Helps farmers act earlier on plant issues  

### 10.11 Farmer profile & account
- Edit stall profile and contact details  
- Account settings area  
- Notifications list shared with panel patterns  

### 10.12 Language toggle
- Farmer panel English / Urdu UI toggle for broader accessibility  

---

## 11. Feature catalogue — Admin panel

### 11.1 Admin dashboard
- Platform overview for operators  
- Entry points into users, farmers, orders, and catalog  

### 11.2 User management
- View users  
- Toggle / set status  
- Optional PIN controls for privileged flows  

### 11.3 Farmer approvals
- Review farmer applications  
- Approve or reject  
- Suspend or remove problematic stalls  
- Update farmer profile fields when needed  

### 11.4 Markets & categories
- CRUD for markets  
- CRUD for product categories  

### 11.5 Product catalog oversight
- Inspect and update products  
- Remove listings that violate rules  

### 11.6 Smart Crops CMS
- Create / update / delete smart crop guide entries used by farmers  

### 11.7 Orders & payments (admin view)
- Inspect all orders  
- Optional payment status marking for admin records  

### 11.8 Reviews moderation
- Approve / moderate / delete reviews  

### 11.9 Announcements
- Publish site announcements for customers and farmers  

### 11.10 Chatbot FAQ management
- Maintain question/answer pairs powering the public chatbot  

### 11.11 Reports
- Reports screen  
- CSV export for offline analysis  

### 11.12 Settings
- Platform settings update screen  

---

## 12. Cross-cutting features

### 12.1 Dark / light theme
- Moon / sun toggle on public and panel layouts  
- Preference stored in `localStorage` key `ml-theme`  
- Applied early via `public/js/theme.js` (`data-theme` on `<html>`)  
- Falls back to `prefers-color-scheme` when unset  
- Dark-mode contrast tuned for cards, hero phone mockup, forms, and tables  

### 12.2 Interactive UI polish
- Consistent MarketLink design tokens in `marketlink.css`  
- Hover / focus borders on cards, inputs, and outline buttons  
- Responsive navbar with icons + labels  

### 12.3 Order chat system
- Table: `order_messages`  
- Supports customer, farmer, and guest senders  
- Guest messages use nullable `user_id` + `guest_name`  
- Notifications and unread badges keep farmers responsive  

### 12.4 Order ID & slip
- Every reservation gets a clear Order ID  
- Printable slip / invoice for customer, guest, and farmer  
- Call / WhatsApp deep links include Order ID text  

### 12.5 PWA readiness
- Web app manifest for installable shortcut on supported browsers  

### 12.6 Public API
- `GET /api/markets` — markets list  
- `GET /api/products` — products list  

---

## 13. Database design (high level)

| Table / area | Purpose |
|--------------|---------|
| `users` | Authentication and role (admin / farmer / customer) |
| `farmer_profiles` | Stall metadata, phone, cutoff, weather location |
| `markets`, `categories`, `products` | Public catalog |
| `carts`, `cart_items` | Logged-in shopping cart |
| `orders`, `order_items` | Pickup reservations (guest orders allowed) |
| `order_messages` | Stored chat per order |
| `reviews`, `favorites`, `notifications` | Engagement loop |
| `farmer_lands`, `farmer_expenses` | Farm operations |
| `smart_crops` | Smart crop guide content |
| `chatbot_faqs`, `announcements`, `settings` | Content & configuration |
| `migrations` | Laravel schema history |

**Delivery dumps:** `database/marketlink.sql` (and root `marketlink.sql` where provided).

---

## 14. Security & quality

- Role middleware for customer, farmer, and admin routes  
- CSRF protection on state-changing requests  
- Bcrypt password hashing  
- Rate limiting on login, checkout, chat, guest order, and contact  
- Guest order pages gated by session-allowed order IDs  
- Upload constraints where media is accepted  
- Mass-assignment protection on sensitive model fields  
- Repair utilities (`fix-all-issues.php`) for demo environment resilience  

---

## 15. Installation guide

### 15.1 GitHub ZIP / XAMPP (recommended for judges)

1. Unzip into `C:\xampp\htdocs\`  
2. Start Apache and MySQL in XAMPP  
3. Prefer PHP 8.2 or 8.3  
4. Open `http://localhost/Your-Folder-Name/` or run `import-db.php`  
5. Import creates database `marketlink` with demo data  

**Manual SQL:** phpMyAdmin → Import → `database/marketlink.sql` with charset **utf-8**.

If something fails once:

```bat
C:\xampp\php\php.exe fix-all-issues.php
C:\xampp\php\php.exe artisan cache:clear
C:\xampp\php\php.exe artisan view:clear
```

Also see `SETUP.txt`.

### 15.2 Developer clone

```bash
git clone https://github.com/muqaddarshaheer/Market-link-project-Techwiz-7.git
cd Market-link-project-Techwiz-7
composer install
copy .env.example .env
php artisan key:generate
# set DB_* in .env
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

---

## 16. Demo accounts

| Role | Email | Password |
|------|--------|----------|
| Admin | admin@marketlink.com | Admin@123 |
| Farmer | farmer@marketlink.com | Farmer@123 |
| Customer | customer@marketlink.com | Customer@123 |

Additional seeded farmers follow the same password style where documented in the seeders.

---

## 17. Testing checklist

- [ ] Home loads with featured markets, farmers, and produce  
- [ ] Search suggestions return products / stalls / markets  
- [ ] Dark ↔ light theme toggles and persists after refresh  
- [ ] Harvest calendar months and event modals work  
- [ ] HarvestWise opens and switches language where available  
- [ ] Guest: add to cart → stepped checkout → land on Order ID + chat (not home)  
- [ ] Guest: send chat; farmer sees unread indicator  
- [ ] Customer: cart → checkout → order page `#order-chat`  
- [ ] Farmer: Accept → Ready → Complete  
- [ ] Order slip opens and prints  
- [ ] Call / WhatsApp includes Order ID  
- [ ] Farmer tools: weather, lands, expenses, crop calculator, smart guide, crop health  
- [ ] Admin: approve farmer, manage catalog, announcement, FAQ, CSV export  
- [ ] ZIP path works with `import-db.php` without Composer on the judge PC  

---

## 18. Folder map

```text
app/Http/Controllers/     Web controllers (Order, OrderChat, Farmer, Admin, …)
app/Models/               Eloquent models
app/Services/             Business services
database/migrations/      Schema history
database/marketlink.sql   Full database dump
public/css/marketlink.css Design system + dark/light theme
public/js/theme.js        Theme controller
public/js/home.js         Home + harvest calendar behaviour
public/js/shop.js         Shop filters / cart helpers
resources/views/          Blade templates
routes/web.php            Primary web routes
import-db.php             One-click DB import for judges
fix-all-issues.php       Environment repair helper
SETUP.txt                 Quick start for evaluators
DOCUMENTATION.md          Source of this Word document
MarketLink-Documentation.docx   Word export for submission
README.md                 Short project summary
```

---

## 19. Development phases (build log)

### Phase A — Core platform
Laravel scaffold, domain models, seeding, public browse, auth & roles, customer cart/checkout, farmer order board, admin approvals.

### Phase B — Farmer productivity
Weather, Crop Calculator, Smart Crop Guide, Crop Health, lands, expenses, EN/UR panel toggle, HarvestWise, Call/WhatsApp actions.

### Phase C — ZIP reliability
Vendor included, UTF-8 SQL, import script, setup helpers, navbar compactness, idempotent seed behaviour.

### Phase D — Order ID, chat, guest UX
`order_messages`, customer/farmer/guest chat, slips, post-confirm redirect to chat, stepped guest checkout, unread badges.

### Phase E — Submission polish
Search/card layout fixes, unified theme, interactive borders, dark-mode readability, and this professional documentation pack.

---

## 20. Submission notes

1. Submit / open the GitHub project (or ZIP) that includes `vendor/` and `database/marketlink.sql`.  
2. Use PHP 8.2+.  
3. Import SQL as **utf-8**.  
4. Demo farmer login is enough to demonstrate orders, chat, weather, and crop tools.  
5. Theme toggle is on the top bar (moon / sun).  
6. Open `MarketLink-Documentation.docx` for the printable professional brief; keep `DOCUMENTATION.md` as the editable source.

---

## Document control

| Field | Value |
|-------|--------|
| Document name | MarketLink Professional Project Documentation |
| Version | 2.0 |
| Author | Muqaddar Shaheer |
| Event | Aptech TechWiz 7 |
| Status | Final for evaluation |

**End of documentation.**
