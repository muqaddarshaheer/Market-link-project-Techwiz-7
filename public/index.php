<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$autoload = __DIR__.'/../vendor/autoload.php';
if (! is_file($autoload)) {
    require __DIR__.'/../bootstrap/missing-vendor.php';
    ml_missing_vendor_page(dirname(__DIR__));
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $autoload;

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
