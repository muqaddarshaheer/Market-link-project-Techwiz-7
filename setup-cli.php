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

function ml_find_php(): string
{
    if (defined('PHP_BINARY') && PHP_BINARY && is_file(PHP_BINARY) && stripos(PHP_BINARY, 'php') !== false) {
        // Apache SAPI binary may not be CLI — prefer known XAMPP CLI
        $xampp = [
            'C:\\xampp\\php\\php.exe',
            'C:\\XAMPP\\php\\php.exe',
        ];
        foreach ($xampp as $bin) {
            if (is_file($bin)) {
                return $bin;
            }
        }

        return PHP_BINARY;
    }

    foreach (['C:\\xampp\\php\\php.exe', 'C:\\XAMPP\\php\\php.exe', '/opt/lampp/bin/php', '/usr/bin/php'] as $bin) {
        if (is_file($bin)) {
            return $bin;
        }
    }

    return 'php';
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

        $php = ml_find_php();
        $logs[] = 'PHP: '.$php;

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

        [$code, $out, $err] = ml_run(escapeshellarg($php).' artisan migrate --force --seed', $root);
        $logs[] = trim($out."\n".$err);
        if ($code !== 0) {
            throw new RuntimeException('migrate --seed failed. See log above.');
        }
        $logs[] = 'Database migrated + seeded';

        [$code, $out, $err] = ml_run(escapeshellarg($php).' artisan storage:link', $root);
        $logs[] = trim($out."\n".$err) ?: 'storage:link done';

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
