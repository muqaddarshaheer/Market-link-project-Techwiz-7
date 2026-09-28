<?php

/**
 * Full one-shot setup for GitHub ZIP / XAMPP.
 * Run: php setup-cli.php
 * Also used by setup.bat and install.php
 */

declare(strict_types=1);

$root = __DIR__;
$isCli = PHP_SAPI === 'cli';

function ml_out(string $msg, bool $isCli): void
{
    if ($isCli) {
        echo $msg.PHP_EOL;
    }
}

function ml_php_version_id(string $bin): int
{
    if ($bin === '' || ($bin !== 'php' && ! is_file($bin))) {
        return 0;
    }
    $out = [];
    $code = 0;
    @exec(escapeshellarg($bin).' -r "echo PHP_VERSION_ID;"', $out, $code);
    if ($code !== 0 || empty($out[0]) || ! ctype_digit(trim($out[0]))) {
        return 0;
    }

    return (int) trim($out[0]);
}

/**
 * Prefer any PHP CLI >= 8.2 (Laravel 11). Never pick an old XAMPP 8.0 over a newer PATH PHP.
 */
function ml_find_php(): string
{
    $candidates = [];

    foreach ([
        'C:\\xampp\\php\\php.exe',
        'C:\\XAMPP\\php\\php.exe',
        '/opt/lampp/bin/php',
        '/usr/bin/php',
    ] as $bin) {
        if (is_file($bin)) {
            $candidates[] = $bin;
        }
    }

    $where = [];
    if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
        @exec('where php 2>nul', $where);
    } else {
        @exec('command -v php 2>/dev/null', $where);
    }
    foreach ($where as $line) {
        $line = trim($line);
        if ($line !== '' && is_file($line) && ! preg_match('/\.(dll|so)$/i', $line)) {
            $candidates[] = $line;
        }
    }

    if (defined('PHP_BINARY') && PHP_BINARY && is_file(PHP_BINARY) && ! preg_match('/\.(dll|so)$/i', PHP_BINARY)) {
        $candidates[] = PHP_BINARY;
    }

    $candidates[] = 'php';

    $best = null;
    $bestId = 0;
    $seen = [];
    foreach ($candidates as $bin) {
        $key = strtolower($bin);
        if (isset($seen[$key])) {
            continue;
        }
        $seen[$key] = true;
        $id = ml_php_version_id($bin);
        if ($id >= 80200 && $id > $bestId) {
            $bestId = $id;
            $best = $bin;
        }
    }

    if ($best !== null) {
        return $best;
    }

    foreach ($candidates as $bin) {
        if ($bin === 'php' || is_file($bin)) {
            return $bin;
        }
    }

    return 'php';
}

function ml_require_php82_web(): void
{
    if (defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 80200) {
        return;
    }

    header('HTTP/1.1 503 Service Unavailable');
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>MarketLink — PHP upgrade needed</title>';
    echo '<style>body{font-family:Segoe UI,sans-serif;background:#eef2ef;color:#0e3b2e;margin:0;padding:2rem 1rem}';
    echo '.card{max-width:640px;margin:0 auto;background:#fff;border:1px solid #d5e8dc;border-radius:16px;padding:1.4rem 1.5rem}';
    echo 'code{background:#eef4f0;padding:.15rem .4rem;border-radius:6px}.err{color:#8a2e2e}</style></head><body><div class="card">';
    echo '<h1>PHP 8.2+ required</h1>';
    echo '<p class="err">This PC has PHP <strong>'.htmlspecialchars(PHP_VERSION, ENT_QUOTES, 'UTF-8').'</strong>. MarketLink needs <strong>PHP 8.2 or 8.3</strong>.</p>';
    echo '<ol><li>Install XAMPP with PHP 8.2/8.3 from <code>https://www.apachefriends.org</code></li>';
    echo '<li>Restart Apache + MySQL</li>';
    echo '<li>Open this site again</li></ol>';
    echo '</div></body></html>';
    exit;
}


function ml_run(string $cmd, string $cwd): array
{
    $descriptor = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];
    $proc = proc_open($cmd, $descriptor, $pipes, $cwd, null, ['bypass_shell' => false]);
    if (! is_resource($proc)) {
        return [1, '', 'Could not start process'];
    }
    fclose($pipes[0]);
    $out = stream_get_contents($pipes[1]);
    $err = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);

    return [proc_close($proc), (string) $out, (string) $err];
}

function ml_find_mysql(): ?string
{
    foreach ([
        'C:\\xampp\\mysql\\bin\\mysql.exe',
        'C:\\XAMPP\\mysql\\bin\\mysql.exe',
        '/opt/lampp/bin/mysql',
        '/usr/bin/mysql',
    ] as $bin) {
        if (is_file($bin)) {
            return $bin;
        }
    }

    $where = [];
    if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
        @exec('where mysql 2>nul', $where);
    } else {
        @exec('command -v mysql 2>/dev/null', $where);
    }
    foreach ($where as $line) {
        $line = trim($line);
        if ($line !== '' && is_file($line)) {
            return $line;
        }
    }

    return null;
}

/**
 * Import database/marketlink.sql (phpMyAdmin-compatible dump).
 */
function ml_import_sql_dump(string $root, array &$logs): bool
{
    $sqlFile = $root.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'marketlink.sql';
    if (! is_file($sqlFile)) {
        $sqlFile = $root.DIRECTORY_SEPARATOR.'marketlink.sql';
    }
    if (! is_file($sqlFile)) {
        $logs[] = 'SQL dump not found';

        return false;
    }

    $mysql = ml_find_mysql();
    if ($mysql) {
        // Dump already contains CREATE DATABASE + USE marketlink
        $cmd = escapeshellarg($mysql).' -u root --default-character-set=utf8mb4 < '.escapeshellarg($sqlFile);
        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            $cmd = 'cmd /c '.$cmd;
        }
        [$code, $out, $err] = ml_run($cmd, $root);
        $logs[] = trim($out."\n".$err) ?: 'mysql import finished';
        if ($code === 0) {
            $logs[] = 'Imported database/marketlink.sql via mysql';

            return true;
        }
        $logs[] = 'mysql CLI import failed, trying PHP fallback…';
    }

    // PHP fallback: split on ;\n (good enough for our mysqldump)
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
        ]);
        $sql = (string) file_get_contents($sqlFile);
        $sql = preg_replace('/^--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*![0-9]{5}.*?\*\//s', '', $sql);
        $pdo->exec($sql);
        $logs[] = 'Imported database/marketlink.sql via PHP';

        return true;
    } catch (Throwable $e) {
        $logs[] = 'PHP SQL import failed: '.$e->getMessage();

        return false;
    }
}

function ml_set_env_value(string $envPath, string $key, string $value): void
{
    $contents = is_file($envPath) ? (string) file_get_contents($envPath) : '';
    $line = $key.'='.$value;
    if (preg_match('/^'.preg_quote($key, '/').'=.*/m', $contents)) {
        $contents = preg_replace('/^'.preg_quote($key, '/').'=.*/m', $line, $contents, 1);
    } else {
        $contents = rtrim($contents)."\n".$line."\n";
    }
    file_put_contents($envPath, $contents);
}

/**
 * @return array{ok:bool,logs:list<string>,error:?string,url:?string}
 */
function ml_run_full_setup(string $root, ?string $detectedBaseUrl = null): array
{
    $logs = [];
    $vendorAutoload = $root.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
    $composerJson = $root.DIRECTORY_SEPARATOR.'composer.json';
    $envPath = $root.DIRECTORY_SEPARATOR.'.env';
    $envExample = $root.DIRECTORY_SEPARATOR.'.env.example';
    $composerPhar = $root.DIRECTORY_SEPARATOR.'composer.phar';
    $lockFile = $root.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'install.lock';

    try {
        if (! is_file($composerJson)) {
            throw new RuntimeException('composer.json missing. Wrong folder?');
        }

        $php = ml_find_php();
        $phpId = ml_php_version_id($php);
        if ($phpId < 80200) {
            throw new RuntimeException(
                'PHP 8.2+ required for setup CLI. Found '.$php.' (version id '.$phpId.'). Install PHP 8.2/8.3 (or newer XAMPP) and ensure it is on PATH, then run setup again.'
            );
        }
        $logs[] = 'PHP: '.$php.' ('.$phpId.')';

        foreach ([
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/framework/testing',
            'storage/logs',
            'storage/app/public',
            'bootstrap/cache',
        ] as $dir) {
            @mkdir($root.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $dir), 0775, true);
        }

        if (! is_file($envPath) && is_file($envExample)) {
            if (! @copy($envExample, $envPath)) {
                throw new RuntimeException('Could not create .env from .env.example');
            }
            $logs[] = 'Created .env';
        }

        $folder = basename($root);
        $appUrl = $detectedBaseUrl ?: ('http://localhost/'.$folder);
        $appUrl = rtrim($appUrl, '/');
        ml_set_env_value($envPath, 'APP_URL', $appUrl);
        ml_set_env_value($envPath, 'DB_DATABASE', 'marketlink');
        ml_set_env_value($envPath, 'DB_USERNAME', 'root');
        ml_set_env_value($envPath, 'DB_PASSWORD', '');
        ml_set_env_value($envPath, 'DB_HOST', '127.0.0.1');
        $logs[] = 'APP_URL='.$appUrl;

        if (! is_file($vendorAutoload)) {
            if (! is_file($composerPhar)) {
                $composerInstaller = @file_get_contents('https://getcomposer.org/installer');
                if ($composerInstaller === false) {
                    throw new RuntimeException('Could not download Composer. Check internet connection.');
                }
                $installerPath = $root.DIRECTORY_SEPARATOR.'composer-setup.php';
                file_put_contents($installerPath, $composerInstaller);
                [$code, $out, $err] = ml_run(escapeshellarg($php).' '.escapeshellarg($installerPath), $root);
                @unlink($installerPath);
                if ($code !== 0 || ! is_file($composerPhar)) {
                    throw new RuntimeException('Composer download failed: '.trim($out."\n".$err));
                }
                $logs[] = 'Downloaded composer.phar';
            }

            [$code, $out, $err] = ml_run(
                escapeshellarg($php).' '.escapeshellarg($composerPhar).' install --no-interaction --prefer-dist --optimize-autoloader',
                $root
            );
            $logs[] = trim($out."\n".$err);
            if ($code !== 0 || ! is_file($vendorAutoload)) {
                throw new RuntimeException('composer install failed. Check internet / PHP zip extension.');
            }
            $logs[] = 'vendor/ installed';
        } else {
            $logs[] = 'vendor/ already present';
        }

        $envContents = (string) file_get_contents($envPath);
        if (preg_match('/^APP_KEY=\s*$/m', $envContents) || ! preg_match('/^APP_KEY=.+/m', $envContents)) {
            [$code, $out, $err] = ml_run(escapeshellarg($php).' artisan key:generate --force', $root);
            $logs[] = trim($out."\n".$err);
            if ($code !== 0) {
                throw new RuntimeException('key:generate failed');
            }
            $logs[] = 'APP_KEY generated';
        }

        // Create MySQL database (XAMPP default: root / empty password)
        try {
            $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            $pdo->exec('CREATE DATABASE IF NOT EXISTS `marketlink` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            $logs[] = 'Database marketlink ready';
        } catch (Throwable $dbEx) {
            throw new RuntimeException(
                'MySQL not reachable. Start MySQL in XAMPP Control Panel, then run setup again. ('.$dbEx->getMessage().')'
            );
        }

        // Prefer ready SQL dump (fast for judges). Fallback: migrate --seed.
        $alreadySeeded = false;
        try {
            $pdoDb = new PDO('mysql:host=127.0.0.1;port=3306;dbname=marketlink', 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            $alreadySeeded = (int) $pdoDb->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='marketlink' AND table_name='users'")->fetchColumn() > 0
                && (int) $pdoDb->query('SELECT COUNT(*) FROM users')->fetchColumn() > 0;
        } catch (Throwable) {
            $alreadySeeded = false;
        }

        if ($alreadySeeded) {
            $logs[] = 'Database already has data — keeping it';
        } elseif (! ml_import_sql_dump($root, $logs)) {
            [$code, $out, $err] = ml_run(escapeshellarg($php).' artisan migrate --force --seed', $root);
            $logs[] = trim($out."\n".$err);
            if ($code !== 0) {
                throw new RuntimeException(
                    'SQL import and migrate --seed both failed. Import database/marketlink.sql in phpMyAdmin or open import-db.php, then Retry.'
                );
            }
            $logs[] = 'Database migrated + seeded (fallback)';
        }

        // Repair common ZIP issues (passwords / missing columns) without reseeding
        try {
            $fix = new PDO('mysql:host=127.0.0.1;port=3306;dbname=marketlink', 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            $hasQuality = (bool) $fix->query("SHOW COLUMNS FROM products LIKE 'quality'")->fetch();
            if (! $hasQuality) {
                $fix->exec("ALTER TABLE `products` ADD COLUMN `quality` ENUM('premium','fresh','standard') NOT NULL DEFAULT 'fresh' AFTER `unit`");
                $logs[] = 'Added products.quality';
            }
            $hasLands = (bool) $fix->query("SHOW TABLES LIKE 'farmer_lands'")->fetch();
            if (! $hasLands) {
                $fix->exec("CREATE TABLE `farmer_lands` (
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
                  CONSTRAINT `farmer_lands_farmer_id_foreign` FOREIGN KEY (`farmer_id`) REFERENCES `farmer_profiles` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                $logs[] = 'Created farmer_lands';
            }
            $fix->exec('ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL');
            $demos = [
                'farmer@marketlink.com' => 'Farmer@123',
                'customer@marketlink.com' => 'Customer@123',
                'admin@marketlink.com' => 'Admin@123',
            ];
            $upd = $fix->prepare('UPDATE users SET password = ? WHERE email = ?');
            foreach ($demos as $email => $plain) {
                $row = $fix->query('SELECT password FROM users WHERE email='.$fix->quote($email))->fetch(PDO::FETCH_ASSOC);
                if (! $row) {
                    continue;
                }
                if (strlen((string) $row['password']) !== 60 || ! password_verify($plain, (string) $row['password'])) {
                    $upd->execute([password_hash($plain, PASSWORD_BCRYPT, ['cost' => 12]), $email]);
                    $logs[] = 'Repaired password for '.$email;
                }
            }
        } catch (Throwable $repairEx) {
            $logs[] = 'Repair note: '.$repairEx->getMessage();
        }

        // Schema-only migrate (never seed again — dump/seed already loaded data)
        [$code, $out, $err] = ml_run(escapeshellarg($php).' artisan migrate --force', $root);
        $logs[] = trim($out."\n".$err) ?: 'migrate check done';
        // Do not fail setup if migrate complains after SQL import
        if ($code !== 0) {
            $logs[] = 'migrate returned non-zero (often OK after SQL import) — continuing';
        }

        [$code, $out, $err] = ml_run(escapeshellarg($php).' artisan storage:link', $root);
        $logs[] = trim($out."\n".$err) ?: 'storage:link done';

        foreach (['cache:clear', 'config:clear', 'view:clear'] as $art) {
            ml_run(escapeshellarg($php).' artisan '.$art, $root);
        }
        $logs[] = 'Caches cleared';

        @file_put_contents($lockFile, date('c')."\n");

        return [
            'ok' => true,
            'logs' => $logs,
            'error' => null,
            'url' => $appUrl.'/',
        ];
    } catch (Throwable $e) {
        return [
            'ok' => false,
            'logs' => $logs,
            'error' => $e->getMessage(),
            'url' => null,
        ];
    }
}

if ($isCli && realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === realpath(__FILE__)) {
    echo "=== MarketLink full setup ===".PHP_EOL.PHP_EOL;
    $result = ml_run_full_setup($root);
    foreach ($result['logs'] as $line) {
        echo $line.PHP_EOL.PHP_EOL;
    }
    if (! $result['ok']) {
        echo 'ERROR: '.$result['error'].PHP_EOL;
        exit(1);
    }
    echo 'DONE. Open: '.$result['url'].PHP_EOL;
    echo 'Demo: farmer@marketlink.com / Farmer@123'.PHP_EOL;
    exit(0);
}
