# MarketLink

**Techwiz 7** — Laravel 11 marketplace connecting local farmers-market farmers with customers. Browse markets, filter produce, place **pickup pre-orders**, manage stock, and run admin reports. **No online payment. No delivery.** Pay in person at the stall.

## Stack

- PHP 8.2+ / Laravel 11.x
- MySQL 8.x
- Bootstrap 5.3, Alpine.js, Leaflet + OpenStreetMap, Chart.js
- Role-based auth (admin / farmer / customer)

## Requirements

- PHP 8.2+ with extensions: `mbstring`, `xml`, `curl`, `zip`, `gd`, `bcmath`, `pdo_mysql`
- Composer 2.x
- MySQL 8.x (or MariaDB 10.4+)
- Node optional (assets use CDN; Vite not required for demo)

## Quick start

```bash
git clone <repo-url> marketlink
cd marketlink
composer install
cp .env.example .env
php artisan key:generate
```

Configure `.env`:

```env
APP_NAME=MarketLink
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marketlink
DB_USERNAME=root
DB_PASSWORD=your_password
```

```bash
# Create the database first, then:
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Open http://127.0.0.1:8000

### SQL dump

A seeded schema dump is included at [`database/marketlink.sql`](database/marketlink.sql).

Import:

```bash
mysql -u root -p marketlink < database/marketlink.sql
```

Re-export after seeding:

```bash
mysqldump -u root -p marketlink > database/marketlink.sql
```

## Demo accounts

| Role     | Email                     | Password     |
|----------|---------------------------|--------------|
| Admin    | admin@marketlink.com      | Admin@123    |
| Farmer   | farmer@marketlink.com     | Farmer@123   |
| Customer | customer@marketlink.com   | Customer@123 |

Additional seeded farmers: `sunrise@marketlink.com` / `herbs@marketlink.com` (password `Farmer@123`).

## Features

- **Customers:** register, browse markets/farmers on Leaflet maps, autocomplete search, filters & sort, cart with stock validation, pre-orders with pickup slots & cutoff modify/cancel, favorites, reviews after completed orders, notifications (30s poll), dark mode
- **Farmers:** stall profile + map picker, admin approval gate, product CRUD, weekly stock copy, order accept/decline/ready/complete, insights charts, review replies
- **Admin:** metrics + Chart.js, approve farmers, markets/categories CRUD, product & review moderation, reports + CSV export, announcements, chatbot FAQs, settings
- **Shared:** FAQ chatbot widget, sitemap, print invoice, Bootstrap dark mode via `localStorage`

## Security notes

- Passwords hashed with bcrypt (Laravel hasher)
- CSRF on all forms; Blade escaping by default
- Role middleware (`role:admin|farmer|customer`) and farmer approval middleware
- Login throttled (`throttle:6,1`)
- Uploads validated for MIME type and size
- Prefer HTTPS in production (`APP_URL=https://...`)

## Project layout

```
app/Http/Controllers/   # Home, Auth, Market, Farmer, Product, Cart, Order, Review, dashboards
app/Models/             # Eloquent models + relationships
app/Http/Middleware/    # RoleMiddleware, EnsureFarmerApproved
database/migrations/    # Full schema
database/seeders/       # Demo data
database/marketlink.sql # SQL export
resources/views/        # Blade UI
public/css|js/          # Theme + maps/chatbot JS
```

## Docs (competition)

User-facing documentation for the competition lives in the Project store:

- Demo video script
- Project report structure

## License

Educational project for Techwiz 7.
