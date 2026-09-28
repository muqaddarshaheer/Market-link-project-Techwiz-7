<?php

/**
 * Browser installer for MarketLink (GitHub ZIP / XAMPP).
 * Open: http://localhost/Your-Folder-Name/install.php
 */

declare(strict_types=1);

@set_time_limit(0);
@ini_set('max_execution_time', '0');
@ini_set('memory_limit', '512M');

$root = __DIR__;
require $root.DIRECTORY_SEPARATOR.'setup-cli.php';

$vendorAutoload = $root.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
$envPath = $root.DIRECTORY_SEPARATOR.'.env';
$lockFile = $root.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'install.lock';

header('Content-Type: text/html; charset=UTF-8');

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

$folder = basename($root);
$scheme = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$base = ($base === '/' || $base === '\\') ? '' : $base;
$detectedUrl = $scheme.'://'.$host.$base;

$alreadyReady = is_file($vendorAutoload) && is_file($envPath) && is_file($lockFile);
$logs = [];
$error = null;
$ok = false;
$siteUrl = $detectedUrl.'/';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_setup'])) {
    $result = ml_run_full_setup($root, $detectedUrl);
    $logs = $result['logs'];
    $ok = $result['ok'];
    $error = $result['error'];
    if ($result['url']) {
        $siteUrl = $result['url'];
    }
    if ($ok) {
        $alreadyReady = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MarketLink setup</title>
    <style>
        body { font-family: Segoe UI, sans-serif; background: #eef2ef; color: #0e3b2e; margin: 0; padding: 2rem 1rem; }
        .card { max-width: 640px; margin: 0 auto; background: #fff; border: 1px solid #d5e8dc; border-radius: 16px; padding: 1.4rem 1.5rem; box-shadow: 0 10px 24px rgba(14,59,46,.08); }
        h1 { margin: 0 0 .4rem; font-size: 1.4rem; }
        p { line-height: 1.5; }
        .ok { background: #e8f6ee; border: 1px solid #b7dcc4; padding: .8rem 1rem; border-radius: 10px; }
        .err { background: #fff1f0; border: 1px solid #f3d0cc; padding: .8rem 1rem; border-radius: 10px; color: #8a2e2e; }
        .muted { color: #5a675f; font-size: .92rem; }
        button, .btn { display: inline-block; background: #1f6b45; color: #fff; border: 0; border-radius: 10px; padding: .7rem 1.1rem; font-weight: 700; cursor: pointer; text-decoration: none; }
        pre { background: #0e3b2e; color: #e8f5ee; padding: .8rem; border-radius: 10px; overflow: auto; font-size: .78rem; max-height: 280px; }
        ol { padding-left: 1.2rem; }
        code { background: #eef4f0; padding: .1rem .35rem; border-radius: 6px; }
    </style>
</head>
<body>
<div class="card">
    <h1>MarketLink — one-click setup</h1>
    <p class="muted">GitHub ZIP does not include <code>vendor/</code>. This installer builds the site, creates the database, and loads demo data.</p>

    <?php if ($ok): ?>
        <div class="ok">
            <strong>Ready — no extra steps.</strong>
            <p style="margin:.5rem 0 0"><a class="btn" href="<?= h($siteUrl) ?>">Open MarketLink</a></p>
            <p class="muted" style="margin-top:.8rem">Demo: <code>farmer@marketlink.com</code> / <code>Farmer@123</code></p>
        </div>
    <?php elseif ($alreadyReady && ! $error): ?>
        <div class="ok">
            <strong>Already installed.</strong>
            <p style="margin:.5rem 0 0"><a class="btn" href="./">Open MarketLink</a></p>
        </div>
    <?php else: ?>
        <?php if ($error): ?><div class="err"><strong>Setup failed:</strong> <?= h($error) ?></div><?php endif; ?>
        <ol class="muted">
            <li>Start <strong>Apache + MySQL</strong> in XAMPP</li>
            <li>Click <strong>Run full setup</strong> (needs internet once)</li>
            <li>Wait 1–3 minutes — do not close the tab</li>
        </ol>
        <form method="post" style="margin-top:1rem" onsubmit="this.querySelector('button').disabled=true;this.querySelector('button').textContent='Working… please wait';">
            <button type="submit" name="run_setup" value="1">Run full setup</button>
        </form>
        <p class="muted" style="margin-top:1rem">Prefer desktop? Double-click <code>setup.bat</code> in <code><?= h($folder) ?></code></p>
    <?php endif; ?>

    <?php if ($logs): ?>
        <h2 style="font-size:1rem;margin-top:1.2rem">Log</h2>
        <pre><?= h(implode("\n\n", $logs)) ?></pre>
    <?php endif; ?>
</div>
</body>
</html>
