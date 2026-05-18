<?php

namespace App\Exports;

use App\Models\ActivityLog;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ActivityLogsExport implements FromCollection, WithHeadings, WithMapping
{
    protected Collection $logs;

    public function __construct(Collection $logs)
    {
        $this->logs = $logs;
    }

    public function collection()
    {
        return $this->logs;
    }

    public function headings(): array
    {
        return [
            'ID', 'User', 'Aktivitas', 'Deskripsi', 'IP Address', 'User Agent', 'Waktu'
        ];
    }

    public function map($log): array
    {
        return [
            $log->id,
            $log->user ? $log->user->name : 'Guest',
            $log->activity,
            $log->description,
            $log->ip_address,
            $log->user_agent,
            $log->created_at->format('Y-m-d H:i:s'),
        ];
    }
}