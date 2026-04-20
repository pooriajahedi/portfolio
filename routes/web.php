<?php

use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PortfolioController::class, 'index']);
Route::get('/portfolio/{slug}', [PortfolioController::class, 'index'])
    ->where('slug', '[A-Za-z0-9\-]+');
Route::get('/blog/{slug}', [PortfolioController::class, 'index'])
    ->where('slug', '[A-Za-z0-9\-]+');
