# MarketLink

Farmers-market platform for Techwiz 7. Customers browse markets and produce, place **pickup pre-orders**, and pay the farmer **in person**. Farmers manage stalls, stock, and orders. Admins approve farmers and keep the catalog clean.

There is **no delivery** and **no online payment**.

**Repo:** [muqaddarshaheer/Market-link-project-Techwiz-7](https://github.com/muqaddarshaheer/Market-link-project-Techwiz-7)

---

## What it does

### Public site
- Home, markets, farmers, products, search (with live suggestions)
- **HarvestWise** (`/produce-guide`) — fruit & vegetable benefits / cautions, EN + اردو in the popup
- FAQ chatbot (keyword FAQs + optional speech)
- Guest cart / quick order flow
- Dark mode (saved in the browser)
- Installable PWA (`manifest` + service worker)

### Customer
- Cart, checkout, order timeline, cancel / reorder, printable invoice
- Favorites, reviews, notifications (poll refresh)

### Farmer panel
- Dashboard with weather helper (speak endpoint)
- Products, weekly stock template, pickup slots
- Order workflow: accept → ready → complete (or decline)
- Insights, reviews reply, stall profile, account settings
- **Crop Calculator** — land / cost / harvest estimate, crop tips, export & WhatsApp share
- English / Urdu UI toggle for the farmer panel

### Admin
- Users, farmers (approve / suspend), markets, categories, products
- Orders, reviews moderation, announcements, chatbot FAQs
- Reports with Chart.js + CSV export
- Settings; sidebar **MarketLink** brand opens the public site

### Small JSON API
- `GET /api/markets`
- `GET /api/products`

---

## Tech stack

| Layer | Choice |
|--------|--------|
| Backend | PHP 8.2+, Laravel 11 |
| Database | MySQL 8 / MariaDB (XAMPP-friendly) |
| Front end | Blade, Bootstrap 5.3, Alpine.js |
| Maps | Leaflet.js + OpenStreetMap (no API key) |
| Charts | Chart.js |

---

## Requirements

- PHP 8.2+ with common extensions (mbstring, openssl, pdo_mysql, tokenizer, xml, ctype, json, fileinfo)
- Composer
- MySQL or MariaDB
- Optional: Node is **not** required for the default Blade UI

---

## Setup

```bash
git clone https://github.com/muqaddarshaheer/Market-link-project-Techwiz-7.git
cd Market-link-project-Techwiz-7

composer install
copy .env.example .env          # Windows
# cp .env.example .env          # macOS / Linux

php artisan key:generate
```

Edit `.env`:

```env
APP_NAME=MarketLink
APP_URL=http://127.0.0.1:8000
DB_DATABASE=marketlink
DB_USERNAME=root
DB_PASSWORD=
```

Create the empty MySQL database `marketlink`, then:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000).

### XAMPP note

You can also point Apache at the `public/` folder (e.g. `http://127.0.0.1/marketlink-techwiz-7/public`). Keep `APP_URL` matching how you open the site.

### Optional SQL dump

A seeded export lives at `database/marketlink.sql` if you prefer importing instead of `migrate --seed`.

### Production

Serve behind HTTPS (`APP_URL` with `https://`, TLS at the web server, secure session cookies). Run `php artisan config:cache` and `route:cache` after deploy.

---

## Demo logins

Passwords come from `database/seeders/DatabaseSeeder.php` (run after a fresh migrate/seed):

| Role | Email | Password |
|------|--------|----------|
| Admin | `admin@marketlink.com` | `Admin@123` |
| Farmer (approved) | `farmer@marketlink.com` | `1111` |
| Customer | `customer@marketlink.com` | `2222` |

Other seeded farmers (also password `1111`) include accounts such as `priya@marketlink.com`.

Password-reset emails use the `log` mailer — the link is written to `storage/logs/laravel.log`.

---

## Useful URLs

| Page | Path |
|------|------|
| HarvestWise | `/produce-guide` |
| Farmer calculator | `/farmer/crop-calculator` |
| Farmer dashboard | `/farmer/dashboard` |
| Admin dashboard | `/admin/dashboard` |
| Customer dashboard | `/customer/dashboard` |

---

## Project docs

- `docs/PROJECT_REPORT.md` — report outline
- `docs/DEMO_VIDEO_SCRIPT.md` — demo video shot list

---

## License

Academic / Techwiz project use unless otherwise stated by the authors.
