<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ManagementServiceController extends Controller
{
    public function digitalTransformation(): View
    {
        return view('public.services.digital-transformation');
    }

    public function smartBangladesh(): View
    {
        return view('public.services.smart-bangladesh');
    }

    public function fullAutomation(): View
    {
        return view('public.services.full-automation');
    }

    public function madrasha(): View
    {
        return view('public.services.madrasha-management');
    }

    public function kindergarten(): View
    {
        return view('public.services.kindergarten-management');
    }
}
