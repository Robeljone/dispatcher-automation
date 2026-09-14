<?php

namespace App\Http\Controllers;

use App\Actions\RecordActivity;
use App\Enums\ActivityAction;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        $user = $request->user();
        assert($user instanceof User);

        return Inertia::render('Profile/Edit', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function update(UpdateProfileRequest $request, RecordActivity $recordActivity): RedirectResponse
    {
        $user = $request->user();
        assert($user instanceof User);

        $data = $request->safe()->only(['name', 'email']);

        if ($request->filled('password')) {
            $data['password'] = $request->string('password')->toString();
        }

        $user->update($data);

        $recordActivity->handle($user, ActivityAction::ProfileUpdated, 'Updated their profile.');

        return back()->with('success', 'Profile updated.');
    }
}
