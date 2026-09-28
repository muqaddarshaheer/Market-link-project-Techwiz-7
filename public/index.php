<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require __DIR__.'/../bootstrap/missing-vendor.php';
$appRoot = dirname(__DIR__);
if (ml_needs_setup($appRoot)) {
    ml_missing_vendor_page($appRoot);
}
$autoload = $appRoot.'/vendor/autoload.php';

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $autoload;

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
