<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 30;

    public function __construct(
        public readonly ?int    $userId,
        public readonly ?string $causerName,
        public readonly ?string $causerEmail,
        public readonly string  $activity,
        public readonly ?string $description,
        public readonly ?string $ipAddress,
        public readonly ?string $userAgent,
    ) {}

    public function handle(): void
    {
        ActivityLog::create([
            'user_id'      => $this->userId,
            'causer_name'  => $this->causerName,
            'causer_email' => $this->causerEmail,
            'activity'     => $this->activity,
            'description'  => $this->description,
            'ip_address'   => $this->ipAddress,
            'user_agent'   => $this->userAgent,
        ]);
    }
}
