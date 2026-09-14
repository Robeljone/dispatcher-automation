<?php

namespace App\Casts;

use App\Enums\ActivityAction;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements CastsAttributes<ActivityAction, ActivityAction|string|null>
 */
class ActivityActionCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ActivityAction
    {
        if ($value instanceof ActivityAction) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            return ActivityAction::tryFrom($value) ?? ActivityAction::Unknown;
        }

        return ActivityAction::Unknown;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if ($value instanceof ActivityAction) {
            return $value->value;
        }

        if (is_string($value) && $value !== '') {
            return ActivityAction::tryFrom($value)?->value ?? ActivityAction::Unknown->value;
        }

        return ActivityAction::Unknown->value;
    }
}
