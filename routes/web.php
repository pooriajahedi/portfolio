<?php

use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PortfolioController::class, 'index']);
Route::post('/contact-requests', [PortfolioController::class, 'storeContactRequest'])
    ->middleware('throttle:4,1')
    ->name('contact-requests.store');
