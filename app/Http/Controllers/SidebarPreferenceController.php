<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SidebarPreferenceController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $open = $request->boolean('open') ? 'true' : 'false';

        return back()->cookie('sidebar_state', $open, 60 * 24 * 365);
    }
}
