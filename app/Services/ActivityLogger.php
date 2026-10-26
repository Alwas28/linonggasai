<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Records who approved, rejected, deactivated or changed important data (blueprint 8.3).
 */
class ActivityLogger
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public static function log(string $action, ?Model $subject = null, array $metadata = [], ?User $user = null): ActivityLog
    {
        return ActivityLog::query()->create([
            'user_id' => $user?->id ?? auth()->id(),
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'metadata' => $metadata ?: null,
        ]);
    }
}
