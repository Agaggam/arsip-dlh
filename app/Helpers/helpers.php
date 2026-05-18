<?php

use App\Models\User;

if (!function_exists('log_activity')) {
    function log_activity(?User $user, string $activity, ?string $description = null): void
    {
        $user_id = $user ? $user->id : null;
        \App\Models\ActivityLog::create([
            'user_id'     => $user_id,
            'causer_name'  => $user?->name,
            'causer_email' => $user?->email,
            'activity'    => $activity,
            'description' => $description,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }
}