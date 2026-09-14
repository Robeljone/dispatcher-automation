<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class HrController extends Controller
{
    public function payroll(): Response
    {
        return Inertia::render('Hr/Payroll');
    }
}
