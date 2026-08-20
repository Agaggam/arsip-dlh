<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengawasan extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'pengawasan';

    protected $keyType    = 'string';
    public    $incrementing = false;

    // Daftar kolom hasil pengawasan — DRY constant
    public const HASIL_COLUMNS = [
        'izin_lingkungan'   => 'Izin Lingk.',
        'wajib_ubah_dok'    => 'Wajib Ubah Dok',
        'oss_rba'           => 'OSS RBA',
        'lap'               => 'LAP',
        'grease_trap'       => 'Grease Trap',
        'ipal'              => 'IPAL',
        'kett_teknis_ipal'  => 'Kett. Teknis IPAL',
        'pantau_ipal'       => 'Pantau IPAL',
        'iplc_pertek'       => 'IPLC/Pertek',
        'pantau_udara'      => 'Pantau Udara',
        'inv_lb3'           => 'INV LB3',
        'tps_b3'            => 'TPS B3',
        'kett_teknis_b3'    => 'Kett. Teknis B3',
        'izin_rintek'       => 'Izin/Rintek',
        'sures_biopori'     => 'Sures/Biopori',
        'pilah_sampah'      => 'Pilah Sampah',
    ];

    public const SKALA_USAHA_OPTIONS = ['SPPL', 'UKL-UPL', 'AMDAL'];

    public const KECAMATAN_OPTIONS = [
        'Kecamatan Batu',
        'Kecamatan Bumiaji',
        'Kecamatan Junrejo',
    ];

    protected $fillable = [
        'batch_id',
        'user_id',
        'tahun',
        'nama_pengawas',
        'kecamatan',
        'jenis_pengawasan',
        'nama_usaha',
        'skala_usaha',
        'waktu_pengawasan',
        // Hasil Pengawasan
        'izin_lingkungan', 'wajib_ubah_dok', 'oss_rba', 'lap',
        'grease_trap', 'ipal', 'kett_teknis_ipal', 'pantau_ipal',
        'iplc_pertek', 'pantau_udara', 'inv_lb3', 'tps_b3',
        'kett_teknis_b3', 'izin_rintek', 'sures_biopori', 'pilah_sampah',
        // Summary
        'jml_v', 'jml_p', 'jml_x',
        'keterangan',
        'status',
        'catatan_admin',
    ];

    protected $casts = [
        'waktu_pengawasan' => 'date',
        'tahun'            => 'integer',
        'jml_v'            => 'integer',
        'jml_p'            => 'integer',
        'jml_x'            => 'integer',
    ];

    // ===========================
    // BOOT — Auto-hitung jml_v, jml_p, jml_x
    // ===========================

    protected static function booted(): void
    {
        static::saving(function (Pengawasan $model) {
            $v = 0; $p = 0; $x = 0;
            foreach (array_keys(self::HASIL_COLUMNS) as $col) {
                match ($model->{$col}) {
                    'v' => $v++,
                    'p' => $p++,
                    'x' => $x++,
                    default => null,
                };
            }
            $model->jml_v = $v;
            $model->jml_p = $p;
            $model->jml_x = $x;
        });
    }

    // ===========================
    // RELASI
    // ===========================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ===========================
    // SCOPE FILTER
    // ===========================

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, fn($q, $v) => $q->where(function ($q) use ($v) {
                $q->where('nama_usaha', 'like', "%{$v}%")
                  ->orWhere('nama_pengawas', 'like', "%{$v}%")
                  ->orWhere('skala_usaha', 'like', "%{$v}%");
            }))
            ->when($filters['kecamatan'] ?? null, fn($q, $v) => $q->where('kecamatan', $v))
            ->when($filters['jenis_pengawasan'] ?? null, fn($q, $v) => $q->where('jenis_pengawasan', $v))
            ->when($filters['tahun'] ?? null, fn($q, $v) => $q->where('tahun', $v))
            ->when($filters['status'] ?? null, fn($q, $v) => $q->where('status', $v));
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

    public function getJenisLabelAttribute(): string
    {
        return match ($this->jenis_pengawasan) {
            'langsung'       => 'Langsung',
            'tidak_langsung' => 'Tidak Langsung',
            default          => '-',
        };
    }
}
