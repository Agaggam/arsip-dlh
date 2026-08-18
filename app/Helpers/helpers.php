<?php

use App\Models\User;
use App\Jobs\LogActivityJob;

if (!function_exists('log_activity')) {
    /**
     * Catat aktivitas user secara asinkron via Queue.
     * Tidak memblokir response — diproses di background.
     */
    function log_activity(?User $user, string $activity, ?string $description = null): void
    {
        LogActivityJob::dispatch(
            userId:      $user?->id,
            causerName:  $user?->name,
            causerEmail: $user?->email,
            activity:    $activity,
            description: $description,
            ipAddress:   request()->ip(),
            userAgent:   request()->userAgent(),
        );
    }
}