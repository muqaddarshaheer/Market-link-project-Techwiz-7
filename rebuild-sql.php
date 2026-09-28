<?php
// Rebuild UTF-8 SQL dump WITH CREATE DATABASE so phpMyAdmin auto-creates DB.
$tmp = sys_get_temp_dir().DIRECTORY_SEPARATOR.'marketlink-raw.sql';
$mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
$cmd = escapeshellarg($mysqldump)
    .' -u root --databases marketlink --single-transaction --quick --skip-routines --skip-triggers'
    .' --default-character-set=utf8mb4 --result-file='.escapeshellarg($tmp);
passthru($cmd, $code);
if ($code !== 0 || ! is_file($tmp)) {
    fwrite(STDERR, "mysqldump failed — is MySQL running?\n");
    exit(1);
}

$sql = file_get_contents($tmp);
$sql = preg_replace('/\s*CHECK\s*\(\s*json_valid\s*\([^)]*\)\s*\)/i', '', $sql);
$sql = str_replace("\r\n", "\n", $sql);
$sql = preg_replace('/^\xEF\xBB\xBF/', '', $sql);
// Drop dump's own CREATE/USE; we inject a clean IF NOT EXISTS block
$sql = preg_replace('/^CREATE DATABASE.*$/mi', '', $sql);
$sql = preg_replace('/^USE\s+`?marketlink`?\s*;?\s*$/mi', '', $sql);

$header = "-- MarketLink TechWiz 7 - FULL dump (UTF-8 no BOM)\n"
    ."-- phpMyAdmin: Import this file (Character set of the file = utf-8)\n"
    ."-- CREATE DATABASE runs automatically on import (XAMPP / local).\n"
    ."-- Demo: farmer@marketlink.com / Farmer@123\n\n"
    ."CREATE DATABASE IF NOT EXISTS `marketlink` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n"
    ."USE `marketlink`;\n\n"
    ."SET NAMES utf8mb4;\n"
    ."SET FOREIGN_KEY_CHECKS=0;\n"
    ."SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n";

$final = $header.ltrim($sql)."\nSET FOREIGN_KEY_CHECKS=1;\n";

$targets = [
    __DIR__.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'marketlink.sql',
    __DIR__.DIRECTORY_SEPARATOR.'marketlink.sql',
    'C:\\xampp\\htdocs\\Market-link-project-Techwiz-7-main\\database\\marketlink.sql',
    'C:\\xampp\\htdocs\\Market-link-project-Techwiz-7-main\\marketlink.sql',
];

foreach ($targets as $path) {
    $dir = dirname($path);
    if (! is_dir($dir)) {
        continue;
    }
    file_put_contents($path, $final);
    $start = bin2hex(substr(file_get_contents($path), 0, 4));
    $hasCreate = str_contains(file_get_contents($path), 'CREATE DATABASE IF NOT EXISTS');
    echo $path.' OK size='.filesize($path).' hex='.$start.' create='.($hasCreate ? 'YES' : 'NO').PHP_EOL;
}

@unlink($tmp);
