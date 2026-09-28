MARKETLINK — SOURCE CODE PACKAGE
=================================
TechWiz 7 · End-to-End Web Solutions
Author: Muqaddar Shaheer

This folder contains the full human-written source code of the MarketLink
Laravel application, exactly as built for the TechWiz 7 submission.

WHAT IS INCLUDED
----------------
app/          Controllers, Models, Services, Middleware, Providers
bootstrap/    Framework bootstrap files
config/       Application configuration
database/     Migrations and seeders (schema history)
public/       Front-end assets: css/, js/, images/, index.php, manifest.json
resources/    Blade views (all pages, layouts, partials for every role)
routes/       web.php, api.php, console.php
artisan, composer.json, composer.lock, package.json, phpunit.xml, vite.config.js
.env.example  Sample environment configuration
README.md, DOCUMENTATION.md, SETUP.txt
import-db.php, fix-all-issues.php, marketlink.sql

WHAT IS EXCLUDED (intentionally)
---------------------------------
- vendor/          Third-party Composer packages (install via `composer install`)
- node_modules/    Third-party NPM packages (install via `npm install` if used)
- .git/            Version control history
- storage/framework, storage/logs  Runtime cache/log files (not source code)

This keeps the package focused on code written and maintained by the
developer, which is what reviewers evaluate for a TechWiz submission.

HOW TO RUN THIS SOURCE CODE
----------------------------
1. Copy this folder's contents into a fresh Laravel 11 skeleton, OR use the
   full project ZIP (with vendor/ included) from the GitHub repository.
2. composer install
3. copy .env.example to .env and set database credentials
4. php artisan key:generate
5. Import marketlink.sql into a MySQL database named `marketlink`
   (or run `php artisan migrate --seed`)
6. php artisan serve   (or use XAMPP/Apache with the public/ folder as web root)

Full step-by-step installation instructions are in DOCUMENTATION.md and
SETUP.txt included in this same folder.
