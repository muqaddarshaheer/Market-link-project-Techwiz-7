<?php
// Rebuild hosting-safe UTF-8 SQL dump (no CREATE DATABASE / USE).
$tmp = sys_get_temp_dir().DIRECTORY_SEPARATOR.'marketlink-raw.sql';
$mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
$cmd = escapeshellarg($mysqldump)
    .' -u root --single-transaction --quick --skip-routines --skip-triggers'
    .' --default-character-set=utf8mb4 marketlink --result-file='.escapeshellarg($tmp);
passthru($cmd, $code);
if ($code !== 0 || ! is_file($tmp)) {
    fwrite(STDERR, "mysqldump failed\n");
    exit(1);
}
$sql = file_get_contents($tmp);
$sql = preg_replace('/\s*CHECK\s*\(\s*json_valid\s*\([^)]*\)\s*\)/i', '', $sql);
$sql = str_replace("\r\n", "\n", $sql);
$sql = preg_replace('/^\xEF\xBB\xBF/', '', $sql);
$sql = preg_replace('/^CREATE DATABASE.*$/mi', '-- CREATE DATABASE omitted for hosting', $sql);
$sql = preg_replace('/^USE\s+`?marketlink`?\s*;?\s*$/mi', '-- USE marketlink; omitted for hosting', $sql);
$header = "-- MarketLink TechWiz 7 - FULL dump (UTF-8 no BOM)\n"
    ."-- phpMyAdmin: SELECT database first, then Import (charset utf-8)\n"
    ."-- Or use import-db.php / fix-all-issues.php\n"
    ."-- Demo: farmer@marketlink.com / Farmer@123\n\n"
    ."SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\nSET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n";
$final = $header.ltrim($sql)."\nSET FOREIGN_KEY_CHECKS=1;\n";
foreach (['database/marketlink.sql', 'marketlink.sql'] as $path) {
    file_put_contents($path, $final);
    echo $path.' OK size='.filesize($path).' hex='.bin2hex(substr(file_get_contents($path), 0, 4)).PHP_EOL;
}
@unlink($tmp);
