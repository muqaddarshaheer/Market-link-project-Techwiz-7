<?php

/**
 * One-click DB import (bypasses phpMyAdmin encoding bugs).
 * Open: http://localhost/Your-Folder/import-db.php
 */

declare(strict_types=1);

@set_time_limit(0);
header('Content-Type: text/html; charset=UTF-8');

$root = __DIR__;
$sqlCandidates = [
    $root.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'marketlink.sql',
    $root.DIRECTORY_SEPARATOR.'marketlink.sql',
];

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function find_mysql(): ?string
{
    foreach (['C:\\xampp\\mysql\\bin\\mysql.exe', 'C:\\XAMPP\\mysql\\bin\\mysql.exe', '/opt/lampp/bin/mysql', '/usr/bin/mysql'] as $bin) {
        if (is_file($bin)) {
            return $bin;
        }
    }

    return null;
}

$sqlFile = null;
foreach ($sqlCandidates as $c) {
    if (is_file($c)) {
        $sqlFile = $c;
        break;
    }
}

$logs = [];
$ok = false;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (! $sqlFile) {
            throw new RuntimeException('marketlink.sql not found');
        }

        $raw = file_get_contents($sqlFile);
        if ($raw === false || $raw === '') {
            throw new RuntimeException('SQL file empty / unreadable');
        }

        // If someone saved UTF-16, convert to UTF-8
        if (str_starts_with($raw, "\xFF\xFE") || str_starts_with($raw, "\xFE\xFF")) {
            $raw = mb_convert_encoding($raw, 'UTF-8', 'UTF-16');
            $logs[] = 'Converted UTF-16 -> UTF-8';
        } elseif (substr_count(substr($raw, 0, 200), "\0") > 20) {
            $raw = mb_convert_encoding($raw, 'UTF-8', 'UTF-16LE');
            $logs[] = 'Converted UTF-16LE -> UTF-8';
        }
        $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw) ?? $raw;

        // Normalize to a temp UTF-8 file for mysql CLI
        $tmp = $root.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'import-utf8.sql';
        @mkdir(dirname($tmp), 0775, true);
        file_put_contents($tmp, $raw);

        $mysql = find_mysql();
        if ($mysql) {
            $cmd = 'cmd /c '.escapeshellarg($mysql).' -u root --default-character-set=utf8mb4 < '.escapeshellarg($tmp);
            $descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
            $proc = proc_open($cmd, $descriptors, $pipes, $root);
            if (! is_resource($proc)) {
                throw new RuntimeException('Could not start mysql');
            }
            fclose($pipes[0]);
            $out = stream_get_contents($pipes[1]);
            $err = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $code = proc_close($proc);
            $logs[] = trim((string) $out."\n".$err) ?: 'mysql finished';
            if ($code !== 0) {
                throw new RuntimeException('mysql import failed');
            }
        } else {
            $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
            ]);
            $pdo->exec($raw);
            $logs[] = 'Imported via PHP PDO';
        }

        @unlink($tmp);

        // Ensure .env exists for Laravel
        $env = $root.DIRECTORY_SEPARATOR.'.env';
        $example = $root.DIRECTORY_SEPARATOR.'.env.example';
        if (! is_file($env) && is_file($example)) {
            copy($example, $env);
            $logs[] = 'Created .env';
        }
        if (is_file($env)) {
            $folder = basename($root);
            $contents = (string) file_get_contents($env);
            foreach ([
                'APP_URL' => 'http://localhost/'.$folder,
                'DB_DATABASE' => 'marketlink',
                'DB_USERNAME' => 'root',
                'DB_PASSWORD' => '',
                'DB_HOST' => '127.0.0.1',
            ] as $k => $v) {
                $line = $k.'='.$v;
                if (preg_match('/^'.preg_quote($k, '/').'=.*/m', $contents)) {
                    $contents = preg_replace('/^'.preg_quote($k, '/').'=.*/m', $line, $contents, 1);
                } else {
                    $contents = rtrim($contents)."\n".$line."\n";
                }
            }
            file_put_contents($env, $contents);
        }

        // APP_KEY
        if (is_file($root.DIRECTORY_SEPARATOR.'artisan') && is_file($root.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php')) {
            $php = 'php';
            foreach (['C:\\xampp\\php\\php.exe'] as $bin) {
                // prefer PATH php 8.2+ — quick check via where is heavy; try php
            }
            $phpBin = null;
            $where = [];
            @exec('where php 2>nul', $where);
            foreach ($where as $line) {
                $line = trim($line);
                if ($line && is_file($line)) {
                    $ver = trim((string) shell_exec(escapeshellarg($line).' -r "echo PHP_VERSION_ID;"'));
                    if (ctype_digit($ver) && (int) $ver >= 80200) {
                        $phpBin = $line;
                        break;
                    }
                }
            }
            if ($phpBin) {
                $envContents = (string) file_get_contents($env);
                if (preg_match('/^APP_KEY=\s*$/m', $envContents) || ! preg_match('/^APP_KEY=.+/m', $envContents)) {
                    shell_exec(escapeshellarg($phpBin).' '.escapeshellarg($root.DIRECTORY_SEPARATOR.'artisan').' key:generate --force');
                    $logs[] = 'APP_KEY generated';
                }
            }
        }

        @file_put_contents(
            $root.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'install.lock',
            date('c')."\n"
        );

        $ok = true;
        $logs[] = 'DONE';
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MarketLink — Import DB</title>
    <style>
        body{font-family:Segoe UI,sans-serif;background:#eef2ef;color:#0e3b2e;margin:0;padding:2rem 1rem}
        .card{max-width:560px;margin:0 auto;background:#fff;border:1px solid #d5e8dc;border-radius:16px;padding:1.4rem 1.5rem}
        .ok{background:#e8f6ee;border:1px solid #b7dcc4;padding:.8rem 1rem;border-radius:10px}
        .err{background:#fff1f0;border:1px solid #f3d0cc;padding:.8rem 1rem;border-radius:10px;color:#8a2e2e}
        button,.btn{background:#1f6b45;color:#fff;border:0;border-radius:10px;padding:.7rem 1.1rem;font-weight:700;cursor:pointer;text-decoration:none;display:inline-block}
        .muted{color:#5a675f;font-size:.92rem} code{background:#eef4f0;padding:.1rem .35rem;border-radius:6px}
        pre{background:#0e3b2e;color:#e8f5ee;padding:.8rem;border-radius:10px;font-size:.75rem;overflow:auto}
    </style>
</head>
<body>
<div class="card">
    <h1 style="margin-top:0;font-size:1.35rem">Import database</h1>
    <p class="muted">Do not use phpMyAdmin if it shows encoding errors. This page imports <code>marketlink.sql</code> safely.</p>
    <p class="muted">SQL file: <code><?= h($sqlFile ? basename(dirname($sqlFile)).'/'.basename($sqlFile) : 'MISSING') ?></code></p>

    <?php if ($ok): ?>
        <div class="ok"><strong>Database imported.</strong></div>
        <p style="margin-top:1rem"><a class="btn" href="./">Open MarketLink</a></p>
        <p class="muted">Demo: farmer@marketlink.com / Farmer@123</p>
    <?php else: ?>
        <?php if ($error): ?><div class="err"><?= h($error) ?></div><?php endif; ?>
        <ol class="muted">
            <li>XAMPP: start <strong>MySQL</strong></li>
            <li>Click Import below</li>
        </ol>
        <form method="post" style="margin-top:1rem" onsubmit="this.querySelector('button').disabled=true;this.querySelector('button').textContent='Importing…';">
            <button type="submit">Import marketlink.sql</button>
        </form>
    <?php endif; ?>

    <?php if ($logs): ?><pre><?= h(implode("\n", $logs)) ?></pre><?php endif; ?>
</div>
</body>
</html>
