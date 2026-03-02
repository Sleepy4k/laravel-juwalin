<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Package;

class HomeController extends Controller
{
    public function index()
    {
        $packages = Package::active()->get();

        return view('public.home', compact('packages'));
    }

    public function about()
    {
        return view('public.about');
    }

    public function products()
    {
        $packages = Package::active()->get();

        return view('public.products', compact('packages'));
    }

    public function pricing()
    {
        $packages = Package::active()->get();

        return view('public.pricing', compact('packages'));
    }
}
