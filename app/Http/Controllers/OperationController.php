<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class OperationController extends Controller
{
    public function distribution(): Response
    {
        return Inertia::render('Operations/Distribution');
    }

    public function dispatch(): Response
    {
        return Inertia::render('Operations/Dispatch');
    }
}
