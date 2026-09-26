<?php

/**
 * XAMPP entry: http://localhost/marketlink-techwiz-7/
 * Uses PHP and the MySQL database named marketlink.
 */

$publicPath = __DIR__.DIRECTORY_SEPARATOR.'public';
$uri = urldecode(parse_url(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH));
$base = rtrim(str_replace('\\', '/', dirname(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '')), '/');
if ($base !== '' && $base !== '/' && strpos($uri, $base) === 0) {
    $uri = substr($uri, strlen($base)) ?: '/';
}

$publicRoot = realpath($publicPath);
$candidate = $publicRoot ? realpath($publicPath.str_replace('/', DIRECTORY_SEPARATOR, $uri)) : false;
if ($uri !== '/' && $candidate && $publicRoot && strpos($candidate, $publicRoot) === 0 && is_file($candidate)) {
    $types = [
        'css' => 'text/css; charset=UTF-8',
        'js' => 'application/javascript; charset=UTF-8',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'json' => 'application/json',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
    ];
    $ext = strtolower(pathinfo($candidate, PATHINFO_EXTENSION));
    if (isset($types[$ext])) {
        header('Content-Type: '.$types[$ext]);
    }
    readfile($candidate);
    return;
}

$_SERVER['SCRIPT_NAME'] = ($base === '' || $base === '/' ? '' : $base).'/index.php';
$_SERVER['SCRIPT_FILENAME'] = __FILE__;
$_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];

require $publicPath.DIRECTORY_SEPARATOR.'index.php';
