<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\User;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Hapus akun yang belum verifikasi OTP setelah 24 jam (ghost account cleanup)
Schedule::call(function () {
    $deleted = User::whereNull('email_verified_at')
        ->where('created_at', '<', now()->subHours(24))
        ->delete();

    if ($deleted > 0) {
        \Illuminate\Support\Facades\Log::info("Cleanup: {$deleted} akun tidak terverifikasi dihapus.");
    }
})->daily()->name('cleanup-unverified-accounts')->withoutOverlapping();

// Pembersihan otomatis data tong sampah (soft delete) yang sudah lebih dari 30 hari
Schedule::command('trash:purge --days=30')
    ->daily()
    ->at('01:00')
    ->name('purge-expired-trash')
    ->withoutOverlapping();

