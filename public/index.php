<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// The application sits one level above public/. On the cPanel host it lives in a
// sibling folder instead, next to public_html (see deploy/cpanel.sh).
$base = is_dir(__DIR__.'/../laravel_portfolio_core') ? __DIR__.'/../laravel_portfolio_core' : __DIR__.'/..';

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $base.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $base.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $base.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
