<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        assert($user instanceof User);

        $recentActivities = Activity::query()
            ->with('user:id,name,email')
            ->when(! $user->isAdmin(), fn ($query) => $query->whereBelongsTo($user))
            ->latest('id')
            ->limit(8)
            ->get()
            ->map(fn (Activity $activity): array => $this->activityPayload($activity));

        return Inertia::render('Dashboard', [
            'stats' => [
                'users' => User::query()->count(),
                'admins' => User::query()->where('role', UserRole::Admin)->count(),
                'active' => User::query()->where('is_active', true)->count(),
            ],
            'recentUsers' => $user->isAdmin()
                ? User::query()
                    ->latest('id')
                    ->limit(5)
                    ->get(['id', 'name', 'email', 'role', 'is_active', 'created_at'])
                : [],
            'recentActivities' => $recentActivities,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function activityPayload(Activity $activity): array
    {
        return [
            'id' => $activity->id,
            'action' => $activity->action->label(),
            'description' => $activity->description,
            'user' => $activity->user?->name ?? 'System',
            'created_at' => $activity->created_at?->diffForHumans(),
        ];
    }
}
