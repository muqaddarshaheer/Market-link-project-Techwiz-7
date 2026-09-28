# MarketLink — Complete Project Documentation (A1)

**Project:** MarketLink — Farmers’ Market Pickup Platform  
**Event:** Aptech TechWiz 7 · End-to-End Web Solutions  
**Author:** Muqaddar Shaheer  
**Stack:** Laravel 11 · PHP 8.2+ · MySQL/MariaDB · Blade · Bootstrap 5.3  
**Repository:** https://github.com/muqaddarshaheer/Market-link-project-Techwiz-7

> **Business rule:** No courier delivery. No online payment. Customers reserve produce, pick up at the stall, and pay the farmer in person (Rs).

---

## Table of contents

1. [Project overview](#1-project-overview)
2. [Problem & solution](#2-problem--solution)
3. [Roles & user journeys](#3-roles--user-journeys)
4. [Feature catalogue](#4-feature-catalogue)
5. [Technology stack](#5-technology-stack)
6. [Architecture](#6-architecture)
7. [Database design](#7-database-design)
8. [Development steps (build log)](#8-development-steps-build-log)
9. [Installation & setup](#9-installation--setup)
10. [Demo accounts](#10-demo-accounts)
11. [Dark / light theme](#11-dark--light-theme)
12. [Order chat & guest checkout](#12-order-chat--guest-checkout)
13. [Security](#13-security)
14. [API endpoints](#14-api-endpoints)
15. [Testing checklist](#15-testing-checklist)
16. [Folder map](#16-folder-map)
17. [Submission notes](#17-submission-notes)

---

## 1. Project overview

MarketLink is a full-stack web application that connects **approved farmers’ market stalls** with **customers** who want to **pre-order produce for pickup**.

| Actor | What they do |
|--------|----------------|
| **Guest** | Browse, add to guest cart, checkout without account, get Order ID + chat/slip |
| **Customer** | Account, cart, checkout, order history, favourites, reviews, in-app chat |
| **Farmer** | Stall products, slots, accept/ready/complete orders, chat, weather, crop tools |
| **Admin** | Approve farmers, catalog, announcements, FAQs, reports, settings |

**Core promise:** Reserve online → collect at market → pay at stall.

---

## 2. Problem & solution

| Real-world pain | MarketLink response |
|-----------------|---------------------|
| Stock sold out before a customer arrives | Pre-order locks quantity until cutoff |
| Farmer does not know weekend demand | Order board: Placed → Accepted → Ready → Completed |
| Hard to coordinate pickup details | Call / WhatsApp + **Order ID chat** + printable slip |
| Farmer guesses crop profit | Crop Calculator + Smart Crop Guide + Crop Health |
| Weather planning is separate | Live weather strip on farmer dashboard |
| ZIP installs fail on other PCs | `vendor/` in repo, `import-db.php`, UTF-8 SQL, `fix-all-issues.php` |

---

## 3. Roles & user journeys

### Guest

```text
Browse products → Add to guest cart → Guest checkout (3 steps)
  → Confirmation page with Order ID
  → Order chat + Order slip + Call/WhatsApp farmer
```

### Customer (logged in)

```text
Register/Login → Browse → Cart → Checkout → Order page (#order-chat)
  → Farmer reply notifications → Pickup & pay → Review
```

### Farmer

```text
Login → Dashboard → New order notification
  → Accept / Decline → Mark ready → Complete
  → Open chat / Order slip / Call customer
  → Products, lands, expenses, weather, crop tools
```

### Admin

```text
Login → Approve farmers → Manage products/markets
  → Announcements → Chatbot FAQs → Reports/CSV → Settings
```

---

## 4. Feature catalogue

### Public site
- Home hero, featured markets / farmers / produce
- Markets, farmers, products (live filters)
- Global search + suggestions
- HarvestWise (produce guide, EN/UR)
- About, Contact, Privacy, Terms, Sitemap
- FAQ chatbot (voice optional)
- **Dark / light mode** (persisted)
- PWA manifest
- Guest basket + stepped guest checkout

### Customer panel
- Dashboard, orders, favourites, reviews, notifications, profile
- Cart / checkout with pickup slots
- Order timeline, change/cancel before cutoff
- Reorder, invoice/slip
- **Order chat** with farmer

### Farmer panel
- Dashboard KPIs, weather, tips, land shortcuts
- Products CRUD + templates
- Orders workflow + unread chat badges
- Pickup slots, sales/insights, expenses
- My Land, Crop Calculator, Smart Crop Guide, Crop Health
- English / Urdu UI toggle
- Order chat + slip

### Admin panel
- Users, farmers (approve/suspend), products, markets, categories
- Orders, reviews moderation, announcements
- Smart Crops CRUD, chatbot FAQs
- Reports + CSV, settings

---

## 5. Technology stack

| Layer | Choice |
|--------|--------|
| Backend | Laravel 11, PHP 8.2+ |
| Database | MySQL 8 / MariaDB (XAMPP) |
| Views | Blade templates |
| CSS/JS | Bootstrap 5.3, Bootstrap Icons, Alpine.js, custom `marketlink.css` |
| Maps | Leaflet + OpenStreetMap |
| Charts | Chart.js |
| Auth | Session + bcrypt, role middleware |
| Assets | `public/` (no Node build required for default UI) |

---

## 6. Architecture

```text
Browser (Blade + CSS/JS)
        │
        ▼
routes/web.php  (+ api.php)
        │
        ▼
Controllers  →  Services (OrderService, NotificationService, …)
        │
        ▼
Eloquent Models  →  MySQL (marketlink)
```

**Important services**
- `OrderService` — cart/guest place order, stock decrement, cutoff
- `NotificationService` — in-app notifications (orders, chat)

**Layouts**
- `layouts/app` — public site
- `layouts/customer` · `layouts/farmer` · `layouts/admin` — panels
- `layouts/guest` — login/register

---

## 7. Database design (high level)

| Table | Purpose |
|--------|---------|
| `users` | Auth + role (admin/farmer/customer) |
| `farmer_profiles` | Stall, phone, cutoff, weather location |
| `markets`, `categories`, `products` | Catalog |
| `carts`, `cart_items` | Logged-in cart |
| `orders`, `order_items` | Pre-orders (guest fields nullable `customer_id`) |
| `order_messages` | Per-order chat (supports guest via nullable `user_id` + `guest_name`) |
| `reviews`, `favorites`, `notifications` | Engagement |
| `farmer_lands`, `farmer_expenses` | Farm ops |
| `smart_crops`, `chatbot_faqs`, `announcements`, `settings` | Content & config |
| `migrations` | Laravel migration history |

Full dump for judges/ZIP: `database/marketlink.sql` (and root `marketlink.sql`).

---

## 8. Development steps (build log)

This section documents the **actual build path** used for TechWiz delivery.

### Phase A — Core platform
1. Scaffold Laravel 11 app and MarketLink domain models.
2. Seed markets, farmers, products, demo users.
3. Public browse pages (home, markets, farmers, products).
4. Auth (register/login), role middleware (customer / farmer / admin).
5. Customer cart → checkout → order lifecycle.
6. Farmer product + order Accept/Ready/Complete.
7. Admin approvals, catalog, announcements, reports.

### Phase B — Farmer tools & UX
8. Weather strip + speak helper.
9. Crop Calculator, Smart Crop Guide, Crop Health.
10. Farmer lands & expenses.
11. Farmer EN/UR panel language toggle.
12. HarvestWise / produce guide for public.
13. Contact actions (Call / WhatsApp) on orders.

### Phase C — Install reliability (ZIP / any PC)
14. Commit `vendor/` so GitHub ZIP runs without Composer on judge PCs.
15. `import-db.php` + UTF-8 SQL (fix phpMyAdmin UTF-16 corruption).
16. `CREATE DATABASE IF NOT EXISTS` in SQL dump.
17. `setup.bat` / `setup-cli.php` / `fix-all-issues.php` (bcrypt, lands columns, quality).
18. `missing-vendor.php` guard + installer flow.
19. Compact fixed navbar with icons + labels (no overflow).
20. Idempotent seed so re-import does not duplicate fatally.

### Phase D — Order ID + chat + guest UX
21. Migration `order_messages` — stored chat per order.
22. Customer + farmer order chat UI (`partials/order-chat`).
23. Order slip/invoice for both parties.
24. After confirm → redirect to order page `#order-chat` (not homepage).
25. Guest confirmation route + session-gated access.
26. Guest messages (`user_id` nullable + `guest_name`).
27. Stepped innovative guest checkout (You → Pickup → Confirm).
28. Unread chat badges on farmer orders list.

### Phase E — Polish for submission
29. Fix navbar search width; product Add/View layout.
30. Shop cards visible by default (no invisible cards waiting on JS).
31. Unified `theme.js` — dark/light across all layouts.
32. Interactive borders (hover/focus glow) for cards, inputs, buttons.
33. This **A1 documentation** file.

---

## 9. Installation & setup

### A) GitHub ZIP / XAMPP (recommended for judges)

1. Unzip into `C:\xampp\htdocs\`
2. Start **Apache + MySQL**
3. Prefer PHP **8.2 or 8.3**
4. Open: `http://localhost/Your-Folder-Name/`  
   — or — `http://localhost/Your-Folder-Name/import-db.php`
5. Import creates DB `marketlink` + demo data

**Manual SQL:** phpMyAdmin → Import → `database/marketlink.sql` (charset **utf-8**)

If anything breaks once:

```bat
C:\xampp\php\php.exe fix-all-issues.php
C:\xampp\php\php.exe artisan cache:clear
C:\xampp\php\php.exe artisan view:clear
```

See also: `SETUP.txt`

### B) Developer clone

```bash
git clone https://github.com/muqaddarshaheer/Market-link-project-Techwiz-7.git
cd Market-link-project-Techwiz-7
composer install
copy .env.example .env
php artisan key:generate
# configure DB_* in .env
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

---

## 10. Demo accounts

| Role | Email | Password |
|------|--------|----------|
| Admin | `admin@marketlink.com` | `Admin@123` |
| Farmer | `farmer@marketlink.com` | `Farmer@123` |
| Customer | `customer@marketlink.com` | `Customer@123` |

Other farmers (same password pattern): e.g. `priya@marketlink.com`

---

## 11. Dark / light theme

### Behaviour
- Toggle button (moon / sun) on public navbar and all panels.
- Preference saved in `localStorage` key **`ml-theme`** (`light` | `dark`).
- Applied on every page via `public/js/theme.js` before paint.
- Updates `data-theme` on `<html>`, `theme-color` meta, and toggle icon.
- If no saved preference, follows `prefers-color-scheme`.

### Implementation
- CSS variables on `:root` and `[data-theme="dark"]` in `public/css/marketlink.css`.
- Body background uses `var(--ml-bg)` so light/dark switch cleanly.
- Interactive borders: cards, filters, forms, outline buttons glow on hover/focus.

### How to test
1. Open home → click theme toggle → page goes dark.
2. Refresh → stays dark.
3. Open farmer/customer/admin panel → same theme.
4. Toggle again → light mode restores.

---

## 12. Order chat & guest checkout

### Logged-in customer
- After checkout → `customer/orders/{id}#order-chat`
- Messages stored in `order_messages`
- Farmer notified; unread badges on farmer order list
- Order slip: print-friendly invoice

### Guest
- Stepped checkout: **You → Pickup → Confirm**
- After place → `guest/orders/{id}#order-chat` (session stores allowed order IDs)
- Guest can message farmer; slip available without login
- Call/WhatsApp prefilled with **Order ID**

### Farmer
- Orders → **Open chat** / **Order slip**
- Full order show page with chat thread

---

## 13. Security

- Role middleware (`customer`, `farmer`, `admin`)
- CSRF on all state-changing forms
- Bcrypt passwords; locked mass-assignment on sensitive fields
- Throttling on checkout, chat, guest order
- Guest order pages gated by session order IDs
- Upload mime/size limits where applicable
- Security headers via app middleware (where configured)

---

## 14. API endpoints

| Method | Path | Notes |
|--------|------|--------|
| GET | `/api/markets` | Markets list |
| GET | `/api/products` | Products list |

(Additional web routes power the Blade UI; see `routes/web.php`.)

---

## 15. Testing checklist

- [ ] Home loads; search suggests products/stalls/markets
- [ ] Light ↔ Dark toggle works and persists
- [ ] Guest: add product → checkout steps → land on chat + Order ID (not home)
- [ ] Guest: send chat message; farmer sees unread
- [ ] Customer login → cart → checkout → `#order-chat`
- [ ] Farmer: accept → ready → complete
- [ ] Order slip opens/print
- [ ] Call / WhatsApp opens with Order ID text
- [ ] Admin: approve farmer, post announcement
- [ ] ZIP path: import SQL + open site without Composer

---

## 16. Folder map

```text
app/Http/Controllers/     Controllers (Order, OrderChat, Farmer, Admin, …)
app/Models/               Eloquent models
app/Services/             OrderService, NotificationService, …
database/migrations/      Schema history
database/marketlink.sql   Full dump for phpMyAdmin / import-db
public/css/marketlink.css Main design system + theme
public/js/theme.js        Dark/light controller
public/js/shop.js         Filters, ajax cart, shop reveal
resources/views/          Blade UI
routes/web.php            Web routes
import-db.php             One-click DB import
fix-all-issues.php       Repair helper
SETUP.txt                 Judge quick start
DOCUMENTATION.md          This file
README.md                 Short project summary
```

---

## 17. Submission notes

1. Prefer opening the **GitHub** project folder or ZIP that includes `vendor/` and `database/marketlink.sql`.
2. Use PHP **≥ 8.2**.
3. Import SQL with charset **utf-8**.
4. Demo farmer login is enough to show orders, chat, weather, and crop tools.
5. Theme and interactive borders: use the moon/sun button on the top bar.

---

## Document control

| Field | Value |
|--------|--------|
| Document | MarketLink A1 Project Documentation |
| Version | 1.0 |
| Date | 2026-09-28 |
| Status | Final for TechWiz 7 submission |

**End of documentation.**
