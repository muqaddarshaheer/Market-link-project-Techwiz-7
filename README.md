# MarketLink

**Farmers’ market pickup platform** — TechWiz 7 · End-to-End Web Solutions  
**TechWiz 7** · **Muqaddar Shaheer**

[![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)](https://www.php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com)
[![License](https://img.shields.io/badge/License-Academic-1f6b45)](#license)

Customers browse markets and produce, place **pickup pre-orders**, and pay the farmer **at the stall**.  
Farmers manage stock and orders. Admins approve stalls and keep the catalog clean.

> **No delivery. No online payment.** Cash / local payment at pickup only.

---

## About this project

MarketLink connects a real farmers’ market stall with people who want to book produce before market day.

| Problem | What MarketLink does |
|--------|----------------------|
| Customer arrives and stock is gone | Pre-order locks quantity until cutoff |
| Farmer does not know demand | Order board: Accept → Ready → Complete |
| Hard to reach each other | Call + WhatsApp on the order |
| Farmer guesses crop profit | Crop Calculator + 80mm-style bill |
| Weather planning is separate | Weather strip + Smart Crop Guide |

Built for **Aptech TechWiz 7**.

**Repository:** https://github.com/muqaddarshaheer/Market-link-project-Techwiz-7

---

## Features

### Public
- Home, markets, farmers, products, live search
- **HarvestWise** — produce benefits / cautions (English + Urdu)
- FAQ chatbot, dark mode, PWA install
- Guest cart and one-step quick order

### Customer
- Cart, checkout, order timeline, invoice
- Favourites, reviews, notifications
- Call / WhatsApp on orders

### Farmer
- Dashboard, products, slots, stock templates
- Order workflow with Call / WhatsApp
- Weather helper, **Crop Calculator**, **Smart Crop Guide**, **Crop Health**
- English / Urdu panel toggle

### Admin
- Approve / suspend farmers, catalog, Smart Crops
- Orders, reviews, announcements, chatbot FAQs
- Reports + CSV export, settings

### API
- `GET /api/markets`
- `GET /api/products`

---

## Tech stack

| Layer | Choice |
|--------|--------|
| Backend | PHP 8.2+, Laravel 11 |
| Database | MySQL 8 / MariaDB (XAMPP-friendly) |
| UI | Blade, Bootstrap 5.3 |
| Maps | Leaflet + OpenStreetMap |
| Charts | Chart.js |

---

## How the app flows

```text
Guest / Customer          Farmer                    Admin
      |                      |                        |
  Browse & cart      See new pre-orders        Approve stall
      |                      |                        |
  Place pickup       Accept → Ready → Done     Moderate catalog
      |                      |                        |
  Pay at stall       Call / WhatsApp           Reports / CSV
      |                      |                        |
  Leave review       Reply to reviews          Smart Crops CRUD
```

---

## Requirements

- PHP 8.2+ (mbstring, openssl, pdo_mysql, tokenizer, xml, ctype, json, fileinfo)
- Composer
- MySQL or MariaDB
- Node is **not** required for the default Blade UI

---

## Setup

### GitHub ZIP / any PC (XAMPP)

`vendor/` is included in the repo so **Code → Download ZIP** works without Composer.

1. Unzip into `C:\xampp\htdocs\`
2. Start **Apache + MySQL** in XAMPP
3. Open: `http://localhost/Your-Folder-Name/`
4. First visit auto-creates `.env`, database `marketlink`, migrate + seed

Optional: **`SHARE-ZIP.bat`** → Desktop `MarketLink-READY.zip` for WhatsApp sharing.  
If MySQL was offline, start it and click **Retry**. See **SETUP.txt**.

### Git clone / developers

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

Create MySQL database `marketlink`, then:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Open http://127.0.0.1:8000

### XAMPP

Point Apache at the `public/` folder. Keep `APP_URL` matching how you open the site.

Optional SQL dump: `database/marketlink.sql`

### Production tips

- `APP_DEBUG=false`, `APP_ENV=production`, `SESSION_ENCRYPT=true`
- HTTPS + `php artisan config:cache` / `route:cache`

Security in the app: role middleware, CSRF, bcrypt, throttling, locked `role`/`status` mass-assignment, upload mime limits, security headers.

---

## Demo logins

After `php artisan migrate --seed`:

| Role | Email | Password |
|------|--------|----------|
| Admin | `admin@marketlink.com` | `Admin@123` |
| Farmer | `farmer@marketlink.com` | `Farmer@123` |
| Customer | `customer@marketlink.com` | `Customer@123` |

Other farmers (same password): e.g. `priya@marketlink.com`

Password-reset links go to `storage/logs/laravel.log` when mail driver is `log`.

---

## Useful URLs

| Page | Path |
|------|------|
| HarvestWise | `/produce-guide` |
| Farmer dashboard | `/farmer/dashboard` |
| Crop Calculator | `/farmer/crop-calculator` |
| Smart Crop Guide | `/farmer/smart-crop-guide` |
| Crop Health | `/farmer/crop-health` |
| Admin dashboard | `/admin/dashboard` |
| Customer dashboard | `/customer/dashboard` |

---

## Project structure (short)

```text
app/Http/Controllers   # Public, customer, farmer, admin
app/Services            # Orders, weather, crop calculator, smart crop advice
app/Models              # Eloquent models
database/migrations     # Schema
database/seeders        # Demo data + Smart Crops
resources/views         # Blade UI
public/css · public/js  # Front-end assets
docs/                   # Team documentation (Word)
```

---

## License

Academic / TechWiz project use unless the author states otherwise.  
See [LICENSE](LICENSE).

---

**Done by Muqaddar Shaheer**
