<?php

/**
 * Friendly guard when GitHub ZIP is opened before full setup.
 */
function ml_missing_vendor_page(string $root): void
{
    $folder = basename($root);
    $installUrl = './install.php';
    header('HTTP/1.1 503 Service Unavailable');
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>MarketLink — setup needed</title>';
    echo '<style>body{font-family:Segoe UI,sans-serif;background:#eef2ef;color:#0e3b2e;margin:0;padding:2rem 1rem}';
    echo '.card{max-width:640px;margin:0 auto;background:#fff;border:1px solid #d5e8dc;border-radius:16px;padding:1.4rem 1.5rem;box-shadow:0 10px 24px rgba(14,59,46,.08)}';
    echo 'h1{margin:0 0 .5rem;font-size:1.35rem}.btn{display:inline-block;background:#1f6b45;color:#fff;text-decoration:none;border-radius:10px;padding:.7rem 1.1rem;font-weight:700}';
    echo 'code{background:#eef4f0;padding:.1rem .35rem;border-radius:6px}ol{line-height:1.55}.muted{color:#5a675f}</style></head><body><div class="card">';
    echo '<h1>Setup needed (one click)</h1>';
    echo '<p class="muted">GitHub ZIP needs a one-time install. It creates <code>vendor/</code>, database <code>marketlink</code>, and demo data.</p>';
    echo '<ol>';
    echo '<li>XAMPP: start <strong>Apache + MySQL</strong></li>';
    echo '<li>Double-click <code>setup.bat</code> in folder <code>'.htmlspecialchars($folder, ENT_QUOTES, 'UTF-8').'</code></li>';
    echo '<li>Or use the web installer below</li>';
    echo '</ol>';
    echo '<p><a class="btn" href="'.htmlspecialchars($installUrl, ENT_QUOTES, 'UTF-8').'">Open install.php</a></p>';
    echo '<p class="muted">No manual migrate / seed. Setup does everything.</p>';
    echo '</div></body></html>';
    exit;
}

function ml_needs_setup(string $root): bool
{
    $autoload = $root.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
    $env = $root.DIRECTORY_SEPARATOR.'.env';
    $lock = $root.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'install.lock';

    return ! is_file($autoload) || ! is_file($env) || ! is_file($lock);
}
