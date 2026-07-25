<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function index(): View
    {
        $packages = Package::query()
            ->active()
            ->ordered()
            ->get();

        return view('public.pricing.index', compact('packages'));
    }
}
