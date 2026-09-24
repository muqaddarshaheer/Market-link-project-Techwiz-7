# MarketLink - Farmers Market Platform

MarketLink connects local farmers-market growers with customers. Customers browse markets, search produce, save favorites, and place pickup pre-orders. Farmers manage stall profiles, stock, and incoming orders. Admins approve farmers, moderate listings, and review platform reports. Payment happens in person at pickup. There is no delivery and no online payment.

## Features

- Customer pre-order system with stock checks and a cutoff window
- Farmer inventory, weekly stock template, and order workflow
- Admin dashboard with Chart.js analytics and CSV export
- Leaflet.js and OpenStreetMap maps (no API key)
- In-app notifications that refresh every 30 seconds
- Dark mode stored in the browser
- FAQ chatbot with keyword matching
- Responsive Bootstrap 5 layout and a printable invoice
- Small read-only JSON API under `/api/markets` and `/api/products`
- Installable PWA manifest and a simple service worker

## Installation

1. Clone the repository: `git clone <repo-url>`
2. Navigate to the project: `cd marketlink`
3. Copy the environment file: `cp .env.example .env` (on Windows: `copy .env.example .env`)
4. Configure the MySQL database name, user, and password in `.env`
5. Install dependencies: `composer install`
6. Generate the app key: `php artisan key:generate`
7. Create the database, then run migrations and seeders: `php artisan migrate --seed`
8. Link public storage: `php artisan storage:link`
9. Start the server: `php artisan serve`
10. Open http://127.0.0.1:8000

This project targets PHP 8.2+ and Laravel 11. Serve it behind HTTPS in production (`APP_URL` with `https://`, trusted proxy, and TLS terminated at the web server). Session cookies should be marked secure once HTTPS is on.

A seeded SQL export is in `database/marketlink.sql`.

## Demo credentials

- Admin: admin@marketlink.com / Admin@123
- Farmer: farmer@marketlink.com / Farmer@123
- Customer: customer@marketlink.com / Customer@123

Priya Shah (`priya@marketlink.com` / Farmer@123) is a pending farmer and cannot list products until an admin approves the stall.

Password reset emails use the `log` mailer. The reset link is written to `storage/logs/laravel.log`.

## Tech stack

- Laravel 11.x
- MySQL 8.x (MariaDB 10.4 from XAMPP is compatible)
- Bootstrap 5.3
- Alpine.js
- Leaflet.js
- Chart.js

## Screenshots

Capture the homepage, product filters, a market map, the customer order timeline, the farmer order queue, and the admin charts after you walk through the demo accounts.

## Video demo

See `docs/DEMO_VIDEO_SCRIPT.md` for a shot list. Add the MP4 link here when the recording is ready.

## Documentation

The report outline is in `docs/PROJECT_REPORT.md`.
