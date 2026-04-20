<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PortfolioController extends Controller
{
    public function index(): View
    {
        return view('welcome', [
            'themeStyle' => 'gold',
        ]);
    }
}
