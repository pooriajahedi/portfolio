<?php

use App\Http\Controllers\Api\ContactRequestApiController;
use App\Http\Controllers\Api\PublicContentController;
use Illuminate\Support\Facades\Route;

Route::get('/site', [PublicContentController::class, 'site'])->name('api.public.site');
Route::get('/resume-items', [PublicContentController::class, 'resume'])->name('api.public.resume');
Route::get('/portfolio', [PublicContentController::class, 'portfolio'])->name('api.public.portfolio');
Route::get('/blog-posts', [PublicContentController::class, 'blogPosts'])->name('api.public.blog-posts');
Route::post('/contact-requests', [ContactRequestApiController::class, 'store'])
    ->middleware('throttle:4,1')
    ->name('api.public.contact-requests.store');
