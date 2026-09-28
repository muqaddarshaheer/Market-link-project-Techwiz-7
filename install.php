<?php

/**
 * One-time XAMPP / ZIP setup for MarketLink.
 * Open: http://localhost/Your-Folder-Name/install.php
 */

declare(strict_types=1);

$root = __DIR__;
$vendorAutoload = $root.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
$composerJson = $root.DIRECTORY_SEPARATOR.'composer.json';
$envPath = $root.DIRECTORY_SEPARATOR.'.env';
$envExample = $root.DIRECTORY_SEPARATOR.'.env.example';
$composerPhar = $root.DIRECTORY_SEPARATOR.'composer.phar';
$lockFile = $root.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'install.lock';

header('Content-Type: text/html; charset=UTF-8');

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function findPhp(): string
{
    if (defined('PHP_BINARY') && PHP_BINARY && is_file(PHP_BINARY)) {
        return PHP_BINARY;
    }

    $candidates = [
        'C:\\xampp\\php\\php.exe',
        'C:\\XAMPP\\php\\php.exe',
        '/opt/lampp/bin/php',
        '/usr/bin/php',
    ];
    foreach ($candidates as $bin) {
        if (is_file($bin)) {
            return $bin;
        }
    }

    return 'php';
}

function runCmd(string $cmd, string $cwd): array
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
    $code = proc_close($proc);

    return [$code, (string) $out, (string) $err];
}

$alreadyReady = is_file($vendorAutoload) && is_file($envPath) && is_file($lockFile);
$logs = [];
$error = null;
$ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_setup'])) {
    try {
        if (! is_file($composerJson)) {
            throw new RuntimeException('composer.json missing. Wrong folder?');
        }

        @mkdir($root.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'framework', 0775, true);
        @mkdir($root.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'logs', 0775, true);
        @mkdir($root.DIRECTORY_SEPARATOR.'bootstrap'.DIRECTORY_SEPARATOR.'cache', 0775, true);

        if (! is_file($envPath) && is_file($envExample)) {
            if (! @copy($envExample, $envPath)) {
                throw new RuntimeException('Could not create .env from .env.example');
            }
            $logs[] = 'Created .env';
        }

        $php = findPhp();
        if (! is_file($vendorAutoload)) {
            if (! is_file($composerPhar)) {
                $composerInstaller = @file_get_contents('https://getcomposer.org/installer');
                if ($composerInstaller === false) {
                    throw new RuntimeException('Could not download Composer. Check internet, then run setup.bat');
                }
                $installerPath = $root.DIRECTORY_SEPARATOR.'composer-setup.php';
                file_put_contents($installerPath, $composerInstaller);
                [$code, $out, $err] = runCmd(escapeshellarg($php).' '.escapeshellarg($installerPath), $root);
                @unlink($installerPath);
                $logs[] = trim($out."\n".$err);
                if ($code !== 0 || ! is_file($composerPhar)) {
                    throw new RuntimeException('Composer download failed. Use setup.bat or install Composer manually.');
                }
                $logs[] = 'Downloaded composer.phar';
            }

            [$code, $out, $err] = runCmd(
                escapeshellarg($php).' '.escapeshellarg($composerPhar).' install --no-interaction --prefer-dist',
                $root
            );
            $logs[] = trim($out."\n".$err);
            if ($code !== 0 || ! is_file($vendorAutoload)) {
                throw new RuntimeException('composer install failed. Open setup.bat or run: php composer.phar install');
            }
            $logs[] = 'Dependencies installed (vendor/)';
        } else {
            $logs[] = 'vendor/ already present';
        }

        // Generate APP_KEY if empty
        $envContents = is_file($envPath) ? (string) file_get_contents($envPath) : '';
        if ($envContents !== '' && (preg_match('/^APP_KEY=\s*$/m', $envContents) || ! preg_match('/^APP_KEY=.+/m', $envContents))) {
            [$code, $out, $err] = runCmd(escapeshellarg($php).' artisan key:generate --force', $root);
            $logs[] = trim($out."\n".$err);
            if ($code !== 0) {
                throw new RuntimeException('key:generate failed. Run: php artisan key:generate');
            }
            $logs[] = 'APP_KEY generated';
        }

        file_put_contents($lockFile, date('c')."\n");
        $ok = true;
        $logs[] = 'Setup complete';
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$folder = basename($root);
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
        pre { background: #0e3b2e; color: #e8f5ee; padding: .8rem; border-radius: 10px; overflow: auto; font-size: .78rem; }
        ol { padding-left: 1.2rem; }
    </style>
</head>
<body>
<div class="card">
    <h1>MarketLink — first-time setup</h1>
    <p class="muted">GitHub ZIP does not include <code>vendor/</code>. Run setup once, then open the site.</p>

    <?php if ($ok): ?>
        <div class="ok">
            <strong>Ready.</strong>
            <p style="margin:.4rem 0 0">Create MySQL database <code>marketlink</code> in phpMyAdmin, then run migrate:</p>
            <pre>php artisan migrate --seed</pre>
            <p><a class="btn" href="./">Open MarketLink</a></p>
        </div>
    <?php elseif ($alreadyReady): ?>
        <div class="ok">
            <strong>Already installed.</strong>
            <p style="margin:.5rem 0 0"><a class="btn" href="./">Open MarketLink</a></p>
        </div>
    <?php else: ?>
        <?php if ($error): ?><div class="err"><strong>Setup failed:</strong> <?= h($error) ?></div><?php endif; ?>
        <ol class="muted">
            <li>Start <strong>Apache + MySQL</strong> in XAMPP</li>
            <li>Click <strong>Run setup</strong> (needs internet once)</li>
            <li>Create DB <code>marketlink</code>, then <code>php artisan migrate --seed</code></li>
        </ol>
        <form method="post" style="margin-top:1rem">
            <button type="submit" name="run_setup" value="1">Run setup</button>
        </form>
        <p class="muted" style="margin-top:1rem">Or double-click <code>setup.bat</code> in folder <code><?= h($folder) ?></code></p>
    <?php endif; ?>

    <?php if ($logs): ?>
        <h2 style="font-size:1rem;margin-top:1.2rem">Log</h2>
        <pre><?= h(implode("\n\n", $logs)) ?></pre>
    <?php endif; ?>
</div>
</body>
</html>
