<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SchoolManagementController extends Controller
{
    public function __invoke(): View
    {
        return view('public.services.school-management');
    }
}
