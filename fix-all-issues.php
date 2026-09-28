<?php
/**
 * Apply all TechWiz DB + SQL dump fixes for MarketLink.
 * Run: php fix-all-issues.php
 */

declare(strict_types=1);

$roots = [
    __DIR__,
    'C:\\xampp\\htdocs\\Market-link-project-Techwiz-7-main',
    'C:\\xampp\\htdocs\\marketlink-techwiz-7',
];
$roots = array_values(array_unique(array_filter($roots, 'is_dir')));

function out(string $m): void
{
    echo $m.PHP_EOL;
}

function pdo(): PDO
{
    return new PDO('mysql:host=127.0.0.1;port=3306;dbname=marketlink', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
}

out('=== MarketLink fix-all ===');

// ---- ISSUE 3 + 4 + 5 on live DB ----
try {
    $db = pdo();
    out('DB connected: marketlink');

    // ISSUE 3: quality column
    $hasQuality = (bool) $db->query("SHOW COLUMNS FROM products LIKE 'quality'")->fetch();
    if ($hasQuality) {
        out('[SKIP] ISSUE 3: products.quality already exists');
    } else {
        $db->exec("ALTER TABLE `products` ADD COLUMN `quality` ENUM('premium','fresh','standard') NOT NULL DEFAULT 'fresh' AFTER `unit`");
        out('[OK] ISSUE 3: products.quality added');
    }

    // ISSUE 5: farmer_lands (correct schema from migration)
    $hasLands = (bool) $db->query("SHOW TABLES LIKE 'farmer_lands'")->fetch();
    if ($hasLands) {
        out('[SKIP] ISSUE 5: farmer_lands already exists');
    } else {
        $db->exec("CREATE TABLE `farmer_lands` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        out('[OK] ISSUE 5: farmer_lands created');
    }

    // farmer_expenses if missing (related)
    $hasExp = (bool) $db->query("SHOW TABLES LIKE 'farmer_expenses'")->fetch();
    if (! $hasExp) {
        $db->exec("CREATE TABLE `farmer_expenses` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `farmer_id` bigint(20) unsigned NOT NULL,
          `land_id` bigint(20) unsigned DEFAULT NULL,
          `category` varchar(60) NOT NULL DEFAULT 'other',
          `title` varchar(160) NOT NULL,
          `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
          `spent_on` date NOT NULL,
          `notes` text DEFAULT NULL,
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `farmer_expenses_farmer_id_spent_on_index` (`farmer_id`,`spent_on`),
          CONSTRAINT `farmer_expenses_farmer_id_foreign` FOREIGN KEY (`farmer_id`) REFERENCES `farmer_profiles` (`id`) ON DELETE CASCADE,
          CONSTRAINT `farmer_expenses_land_id_foreign` FOREIGN KEY (`land_id`) REFERENCES `farmer_lands` (`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        out('[OK] farmer_expenses created');
    } else {
        out('[SKIP] farmer_expenses exists');
    }

    // ISSUE 4: passwords — ensure bcrypt 60 chars for demo users
    $db->exec("ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL");

    // Generate proper hashes with PHP password_hash (always valid bcrypt)
    $demos = [
        'farmer@marketlink.com' => 'Farmer@123',
        'customer@marketlink.com' => 'Customer@123',
        'admin@marketlink.com' => 'Admin@123',
    ];
    $upd = $db->prepare('UPDATE users SET password = ? WHERE email = ?');
    foreach ($demos as $email => $plain) {
        $row = $db->query("SELECT password FROM users WHERE email=".$db->quote($email))->fetch(PDO::FETCH_ASSOC);
        if (! $row) {
            out("[WARN] user missing: $email");
            continue;
        }
        $hash = $row['password'];
        $needs = strlen($hash) !== 60
            || ! str_starts_with($hash, '$2y$')
            || ! password_verify($plain, $hash);
        if ($needs) {
            $new = password_hash($plain, PASSWORD_BCRYPT, ['cost' => 12]);
            $upd->execute([$new, $email]);
            out("[OK] ISSUE 4: reset password hash for $email (len=".strlen($new).')');
        } else {
            out("[SKIP] ISSUE 4: $email already verifies with bcrypt");
        }
    }

    // Verify
    out('--- verify ---');
    foreach ($db->query("SELECT email, LENGTH(password) AS len, LEFT(password,7) AS prefix FROM users WHERE email LIKE '%@marketlink.com'") as $r) {
        out("  {$r['email']} len={$r['len']} prefix={$r['prefix']}");
    }
    out('  quality: '.($db->query("SHOW COLUMNS FROM products LIKE 'quality'")->fetch() ? 'YES' : 'NO'));
    out('  farmer_lands: '.($db->query("SHOW TABLES LIKE 'farmer_lands'")->fetch() ? 'YES' : 'NO'));
} catch (Throwable $e) {
    out('[FAIL] DB: '.$e->getMessage());
    out('Start MySQL in XAMPP, create DB marketlink, then re-run.');
    exit(1);
}

// ---- ISSUE 1 + 2: rebuild SQL dumps UTF-8, hosting-safe ----
$mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
$tmp = sys_get_temp_dir().DIRECTORY_SEPARATOR.'ml-raw.sql';
if (! is_file($mysqldump)) {
    out('[WARN] mysqldump not found — skip SQL rebuild');
    exit(0);
}

passthru(escapeshellarg($mysqldump).' -u root --single-transaction --quick --skip-routines --skip-triggers --default-character-set=utf8mb4 marketlink --result-file='.escapeshellarg($tmp), $code);
if ($code !== 0 || ! is_file($tmp)) {
    out('[FAIL] mysqldump');
    exit(1);
}

$sql = file_get_contents($tmp);
$sql = preg_replace('/\s*CHECK\s*\(\s*json_valid\s*\([^)]*\)\s*\)/i', '', $sql);
$sql = str_replace("\r\n", "\n", $sql);
$sql = preg_replace('/^\xEF\xBB\xBF/', '', $sql);

// Hosting-safe: NO CREATE DATABASE / USE (commented). Tables only.
$header = "-- MarketLink TechWiz 7 - FULL dump (UTF-8 no BOM)\n"
    ."-- phpMyAdmin: SELECT your database first, then Import this file (charset utf-8)\n"
    ."-- Or open /import-db.php in the project (recommended)\n"
    ."-- Demo: farmer@marketlink.com / Farmer@123\n"
    ."-- CREATE DATABASE and USE are intentionally omitted for shared hosting.\n\n"
    ."SET NAMES utf8mb4;\n"
    ."SET FOREIGN_KEY_CHECKS=0;\n"
    ."SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n";

// Strip any CREATE DATABASE / USE if present
$sql = preg_replace('/^CREATE DATABASE.*$/mi', '-- CREATE DATABASE omitted for hosting', $sql);
$sql = preg_replace('/^USE\s+`?marketlink`?\s*;?\s*$/mi', '-- USE marketlink; omitted for hosting', $sql);

$final = $header.ltrim($sql)."\nSET FOREIGN_KEY_CHECKS=1;\n";

foreach ($roots as $root) {
    $paths = [
        $root.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'marketlink.sql',
        $root.DIRECTORY_SEPARATOR.'marketlink.sql',
    ];
    foreach ($paths as $path) {
        @mkdir(dirname($path), 0775, true);
        file_put_contents($path, $final);
        $hex = bin2hex(substr(file_get_contents($path), 0, 2));
        $nulls = substr_count(file_get_contents($path), "\0");
        out("[OK] ISSUE 1+2: wrote $path (start=$hex nulls=$nulls size=".filesize($path).')');
    }
}

@unlink($tmp);
out('=== DONE ===');
out('Next: php artisan cache:clear && php artisan view:clear');
out('Login: farmer@marketlink.com / Farmer@123');
