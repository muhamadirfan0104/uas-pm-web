<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class KasirController extends Controller
{
    public function dashboard(): View
    {
        return view('kasir.dashboard');
    }
}
