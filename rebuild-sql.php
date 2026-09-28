<?php
// Rebuild clean UTF-8 (no BOM) SQL dump for phpMyAdmin.
$tmp = sys_get_temp_dir().DIRECTORY_SEPARATOR.'marketlink-raw.sql';
$mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
$cmd = escapeshellarg($mysqldump)
    .' -u root --databases marketlink --single-transaction --quick'
    .' --skip-routines --skip-triggers --default-character-set=utf8mb4'
    .' --result-file='.escapeshellarg($tmp);
passthru($cmd, $code);
if ($code !== 0 || ! is_file($tmp)) {
    fwrite(STDERR, "mysqldump failed\n");
    exit(1);
}

$sql = file_get_contents($tmp);
$sql = preg_replace('/\s*CHECK\s*\(\s*json_valid\s*\([^)]*\)\s*\)/i', '', $sql);
$sql = str_replace("\r\n", "\n", $sql);
$sql = preg_replace('/^\xEF\xBB\xBF/', '', $sql);

$header = "-- MarketLink TechWiz 7 - FULL database\n"
    . "-- phpMyAdmin: Import this file (Character set of file = utf-8)\n"
    . "-- Demo: farmer@marketlink.com / Farmer@123\n\n";

$sql = $header.ltrim($sql);

foreach (['database/marketlink.sql', 'marketlink.sql'] as $path) {
    file_put_contents($path, $sql);
    // Ensure no UTF-16 / BOM
    $bytes = file_get_contents($path, false, null, 0, 3);
    if ($bytes === "\xFF\xFE" || $bytes === "\xFE\xFF" || $bytes === "\xEF\xBB\xBF") {
        fwrite(STDERR, "Bad BOM in $path\n");
        exit(1);
    }
    if (str_contains(file_get_contents($path), "\0")) {
        fwrite(STDERR, "NUL bytes in $path\n");
        exit(1);
    }
    echo $path.' OK size='.filesize($path).' hex='.bin2hex(substr(file_get_contents($path), 0, 8)).PHP_EOL;
}

@unlink($tmp);
