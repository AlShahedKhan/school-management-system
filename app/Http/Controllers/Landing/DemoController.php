<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DemoController extends Controller
{
    public function index(): View
    {
        return view('public.demo.index');
    }
}
