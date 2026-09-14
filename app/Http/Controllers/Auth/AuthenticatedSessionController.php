<?php

namespace App\Http\Controllers\Auth;

use App\Actions\RecordActivity;
use App\Enums\ActivityAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function store(LoginRequest $request, RecordActivity $recordActivity): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();
        assert($user instanceof User);

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        $recordActivity->handle($user, ActivityAction::LoggedIn, 'Signed in to the panel.');

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request, RecordActivity $recordActivity): RedirectResponse
    {
        $user = $request->user();

        if ($user instanceof User) {
            $recordActivity->handle($user, ActivityAction::LoggedOut, 'Signed out of the panel.');
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
