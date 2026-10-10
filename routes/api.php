<?php

use App\Http\Controllers\Api\PublicContentController;
use Illuminate\Support\Facades\Route;

Route::get('/site', [PublicContentController::class, 'site'])->name('api.public.site');
Route::get('/portfolio', [PublicContentController::class, 'portfolio'])->name('api.public.portfolio');
Route::get('/portfolio/{slug}', [PublicContentController::class, 'portfolioProject'])->name('api.public.portfolio.show');
