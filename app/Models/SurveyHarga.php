<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyHarga extends Model
{
    use HasUuids, SoftDeletes; // Auto-generate UUID saat create

    protected $table = 'survey_harga';

    protected $keyType    = 'string'; // Beritahu Laravel bahwa PK bertipe string
    public    $incrementing = false;  // UUID tidak auto-increment

    protected $fillable = [
        'user_id',
        'department_id',
        'kelompok',
        'judul',
        'kode_komponen',
        'kode_rekening',
        'spesifikasi_singkat',
        'spesifikasi_detail',
        'satuan',
        'harga_usulan',
        'nama_toko_1', 'harga_toko_1', 'gambar_toko_1', 'link_belanja_1',
        'nama_toko_2', 'harga_toko_2', 'gambar_toko_2', 'link_belanja_2',
        'nama_toko_3', 'harga_toko_3', 'gambar_toko_3', 'link_belanja_3',
        'status',
        'catatan_admin',
    ];

    protected $casts = [
        'harga_usulan' => 'integer',
        'harga_toko_1' => 'integer',
        'harga_toko_2' => 'integer',
        'harga_toko_3' => 'integer',
    ];

    // ===========================
    // RELASI
    // ===========================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // ===========================
    // SCOPE FILTER
    // ===========================

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['kelompok'] ?? null, fn($q, $v) => $q->where('kelompok', $v))
            ->when($filters['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->when($filters['department_id'] ?? null, fn($q, $v) => $q->where('department_id', $v))
            ->when($filters['search'] ?? null, fn($q, $v) => $q->where(function ($q) use ($v) {
                $q->where('judul', 'like', "%{$v}%")
                  ->orWhere('kode_komponen', 'like', "%{$v}%")
                  ->orWhere('kode_rekening', 'like', "%{$v}%");
            }));
    }

    // ===========================
    // ACCESSOR / HELPERS
    // ===========================

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'diajukan'  => 'bg-blue-100 text-blue-700',
            'disetujui' => 'bg-green-100 text-green-700',
            'ditolak'   => 'bg-red-100 text-red-700',
            default     => 'bg-gray-100 text-gray-700',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'diajukan'  => 'Diajukan',
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
            default     => 'Unknown',
        };
    }

    public function getHargaUsulanFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_usulan, 0, ',', '.');
    }
}
