<?php

namespace App\Http\Controllers\WebPembeli;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function home(): View
    {
        return view('pembeli.home');
    }

    public function produk(): View
    {
        return view('pembeli.produk');
    }

    public function comingSoon(): View
    {
        return view('pembeli.coming-soon');
    }
}
