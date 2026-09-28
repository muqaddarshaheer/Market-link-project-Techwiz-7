# MarketLink — all known fixes (step by step)

Run from project root (XAMPP MySQL ON):

```bat
C:\xampp\php\php.exe fix-all-issues.php
```

Or open: `http://localhost/Your-Folder/import-db.php`

---

## ISSUE 1 — SQL UTF-8 (no BOM)

**Already fixed in repo.** Fresh `database/marketlink.sql` and `marketlink.sql` are UTF-8.

Verify (PowerShell):

```powershell
Format-Hex database\marketlink.sql -Count 4
```

Expect `2D 2D 20 4D` (`-- M`), **not** `FF FE`.

Skip if hex starts with `2D 2D`.

Manual: VS Code → Save with Encoding → UTF-8  
Or: `php fix-all-issues.php` (rebuilds dump)

---

## ISSUE 2 — CREATE DATABASE / USE (shared hosting)

**Already fixed in dump.** Those lines are omitted/commented.

phpMyAdmin:

1. Create/select your DB (e.g. `marketlink`)
2. Import → choose `database/marketlink.sql`
3. Charset: **utf-8**

Skip if import works after selecting DB first.

---

## ISSUE 3 — `products.quality` missing

```sql
ALTER TABLE `products`
  ADD COLUMN `quality` ENUM('premium','fresh','standard')
  NOT NULL DEFAULT 'fresh' AFTER `unit`;
```

Verify:

```sql
SHOW COLUMNS FROM products LIKE 'quality';
```

Skip if row returned.

---

## ISSUE 4 — Bcrypt password

```sql
ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL;
```

Reset demo farmer (or run `php fix-all-issues.php`):

```sql
-- Prefer generating live hash:
-- php -r "echo password_hash('Farmer@123', PASSWORD_BCRYPT, ['cost'=>12]);"
```

```bat
php artisan tinker
>>> echo Hash::make('Farmer@123');
```

Then:

```sql
UPDATE users SET password = '<paste-60-char-hash>'
WHERE email = 'farmer@marketlink.com';
```

Verify:

```sql
SELECT email, LENGTH(password) AS hash_length, LEFT(password, 7) AS prefix
FROM users WHERE email = 'farmer@marketlink.com';
```

Expect: `hash_length=60`, prefix `$2y$12$` or `$2y$08$` / `$2y$10$` (all valid bcrypt).

Login: `farmer@marketlink.com` / `Farmer@123`

Skip if login already works.

---

## ISSUE 5 — `farmer_lands` missing

**Correct schema** (matches `FarmerLand` model — not the guessed address/lat columns):

```sql
CREATE TABLE IF NOT EXISTS `farmer_lands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `farmer_id` bigint(20) unsigned NOT NULL,
  `name` varchar(120) NOT NULL,
  `area_amount` decimal(10,2) DEFAULT NULL,
  `area_unit` varchar(30) NOT NULL DEFAULT 'kanal',
  `crop_name` varchar(120) DEFAULT NULL,
  `crop_key` varchar(60) DEFAULT NULL,
  `planted_on` date DEFAULT NULL,
  `expected_harvest` date DEFAULT NULL,
  `stage` varchar(30) NOT NULL DEFAULT 'empty',
  `soil_type` varchar(60) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `farmer_lands_farmer_id_is_active_index` (`farmer_id`,`is_active`),
  CONSTRAINT `farmer_lands_farmer_id_foreign`
    FOREIGN KEY (`farmer_id`) REFERENCES `farmer_profiles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Verify:

```sql
SHOW TABLES LIKE 'farmer_lands';
```

Skip if table exists.

Or:

```bat
php artisan migrate
```

---

## After any fix

```bat
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan storage:link
```

Test: `http://localhost/Market-link-project-Techwiz-7-main/`  
Login farmer dashboard.
