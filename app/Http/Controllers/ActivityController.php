<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $activities = Activity::query()
            ->with('user:id,name,email')
            ->latest('id')
            ->paginate(15)
            ->through(fn (Activity $activity): array => [
                'id' => $activity->id,
                'action' => $activity->action->label(),
                'description' => $activity->description,
                'user' => $activity->user?->name ?? 'System',
                'email' => $activity->user?->email,
                'ip_address' => $activity->ip_address,
                'created_at' => $activity->created_at?->toDayDateTimeString(),
            ]);

        return Inertia::render('Activity/Index', [
            'activities' => $activities,
        ]);
    }
}
