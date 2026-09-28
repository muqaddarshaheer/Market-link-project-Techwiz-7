<?php

/**
 * First-visit guard + auto setup for GitHub / share ZIP on XAMPP.
 */

function ml_needs_setup(string $root): bool
{
    $autoload = $root.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
    $env = $root.DIRECTORY_SEPARATOR.'.env';
    $lock = $root.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'install.lock';

    return ! is_file($autoload) || ! is_file($env) || ! is_file($lock);
}

function ml_php_too_old_page(): void
{
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

function ml_setup_fail_page(string $root, string $error, array $logs = []): void
{
    $folder = basename($root);
    header('HTTP/1.1 503 Service Unavailable');
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>MarketLink — setup</title>';
    echo '<style>body{font-family:Segoe UI,sans-serif;background:#eef2ef;color:#0e3b2e;margin:0;padding:2rem 1rem}';
    echo '.card{max-width:640px;margin:0 auto;background:#fff;border:1px solid #d5e8dc;border-radius:16px;padding:1.4rem 1.5rem;box-shadow:0 10px 24px rgba(14,59,46,.08)}';
    echo 'h1{margin:0 0 .5rem;font-size:1.35rem}.btn{display:inline-block;background:#1f6b45;color:#fff;text-decoration:none;border-radius:10px;padding:.7rem 1.1rem;font-weight:700;margin-right:.5rem}';
    echo 'code{background:#eef4f0;padding:.1rem .35rem;border-radius:6px}.err{background:#fff1f0;border:1px solid #f3d0cc;padding:.8rem 1rem;border-radius:10px;color:#8a2e2e}';
    echo 'pre{background:#0e3b2e;color:#e8f5ee;padding:.8rem;border-radius:10px;overflow:auto;font-size:.75rem;max-height:220px}.muted{color:#5a675f}</style></head><body><div class="card">';
    echo '<h1>Almost ready</h1>';
    echo '<div class="err"><strong>'.htmlspecialchars($error, ENT_QUOTES, 'UTF-8').'</strong></div>';
    echo '<ol class="muted">';
    echo '<li>XAMPP Control Panel → start <strong>Apache</strong> + <strong>MySQL</strong></li>';
    echo '<li>PHP must be <strong>8.2+</strong> (Laravel 11)</li>';
    echo '<li>Open <code>import-db.php</code> to load SQL (skip phpMyAdmin if it errors)</li>';
    echo '<li>Or <code>setup.bat</code> / Retry below</li>';
    echo '</ol>';
    echo '<p><a class="btn" href="./">Retry</a> <a class="btn" href="./import-db.php">Import SQL</a> <a class="btn" href="./install.php">Installer</a></p>';
    if ($logs) {
        echo '<pre>'.htmlspecialchars(implode("\n\n", $logs), ENT_QUOTES, 'UTF-8').'</pre>';
    }
    echo '</div></body></html>';
    exit;
}

/**
 * Auto-install on first site open. Redirects when done; shows help page on failure.
 */
function ml_try_auto_setup(string $root): void
{
    if (defined('PHP_VERSION_ID') && PHP_VERSION_ID < 80200) {
        ml_php_too_old_page();
    }

    @set_time_limit(0);
    @ini_set('max_execution_time', '0');
    @ini_set('memory_limit', '512M');

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' && empty($_GET['ml_setup'])) {
        $folder = basename($root);
        $hasVendor = is_file($root.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php');
        header('Content-Type: text/html; charset=UTF-8');
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
        echo '<meta http-equiv="refresh" content="0;url=?ml_setup=1">';
        echo '<title>MarketLink — installing…</title>';
        echo '<style>body{font-family:Segoe UI,sans-serif;background:#eef2ef;color:#0e3b2e;margin:0;padding:2rem 1rem;text-align:center}';
        echo '.card{max-width:520px;margin:2rem auto;background:#fff;border:1px solid #d5e8dc;border-radius:16px;padding:1.6rem;box-shadow:0 10px 24px rgba(14,59,46,.08)}';
        echo '.spin{width:36px;height:36px;border:3px solid #d5e8dc;border-top-color:#1f6b45;border-radius:50%;margin:0 auto 1rem;animation:s 0.8s linear infinite}@keyframes s{to{transform:rotate(360deg)}}';
        echo '.muted{color:#5a675f}</style></head><body><div class="card">';
        echo '<div class="spin"></div>';
        echo '<h1 style="font-size:1.25rem;margin:0 0 .5rem">Setting up MarketLink…</h1>';
        echo '<p class="muted">Folder: <code>'.htmlspecialchars($folder, ENT_QUOTES, 'UTF-8').'</code></p>';
        if ($hasVendor) {
            echo '<p class="muted">Creating database + demo data (about 10–30 seconds). Keep MySQL ON.</p>';
        } else {
            echo '<p class="muted">First install needs internet (1–3 minutes). Keep Apache + MySQL ON.</p>';
        }
        echo '<p class="muted">Do not close this tab.</p>';
        echo '</div></body></html>';
        exit;
    }

    $setupCli = $root.DIRECTORY_SEPARATOR.'setup-cli.php';
    if (! is_file($setupCli)) {
        ml_setup_fail_page($root, 'setup-cli.php missing from this ZIP.');
    }
    require_once $setupCli;

    $scheme = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    $base = ($base === '/' || $base === '\\') ? '' : $base;
    $detectedUrl = $scheme.'://'.$host.$base;

    $result = ml_run_full_setup($root, $detectedUrl);

    if (! empty($result['ok'])) {
        $target = $result['url'] ?: ($detectedUrl.'/');
        header('Location: '.$target, true, 302);
        exit;
    }

    ml_setup_fail_page($root, (string) ($result['error'] ?? 'Setup failed'), $result['logs'] ?? []);
}

function ml_missing_vendor_page(string $root): void
{
    ml_try_auto_setup($root);
}
