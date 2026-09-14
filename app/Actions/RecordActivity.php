<?php

namespace App\Actions;

use App\Enums\ActivityAction;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;

class RecordActivity
{
    public function __construct(private Request $request) {}

    public function handle(?User $user, ActivityAction $action, string $description): Activity
    {
        return Activity::query()->create([
            'user_id' => $user?->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => $this->request->ip(),
        ]);
    }
}
