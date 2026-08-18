<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Pegawai extends Model
{
    protected $table = 'pegawais';

    protected $fillable = [
        'nik',
        'nip_nrk',
        'nama_lengkap',
        'jenis_kelamin',
        'kategori',
        'pangkat_golongan',
        'jabatan',
        'unit_kerja',
        'tmt_sk',
        'masa_kontrak',
        'jam_kerja_mingguan',
        'pendidikan_terakhir',
        'no_hp',
        'email',
        'status',
    ];

    protected $casts = [
        'tmt_sk'              => 'date',
        'jam_kerja_mingguan'  => 'decimal:1',
    ];

    // ====================================================
    // LOCAL QUERY SCOPES
    // ====================================================

    /**
     * Scope utama untuk filter multi-parameter dari Controller.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        // Filter kategori
        if (!empty($filters['kategori'])) {
            $query->where('kategori', $filters['kategori']);
        }

        // Filter unit kerja
        if (!empty($filters['unit_kerja'])) {
            $query->where('unit_kerja', 'LIKE', '%' . $filters['unit_kerja'] . '%');
        }

        // Filter status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter rentang TMT SK
        if (!empty($filters['tmt_dari'])) {
            $query->whereDate('tmt_sk', '>=', $filters['tmt_dari']);
        }
        if (!empty($filters['tmt_sampai'])) {
            $query->whereDate('tmt_sk', '<=', $filters['tmt_sampai']);
        }

        // Global search: NIK, NIP/NRK, Nama, Jabatan
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('nik', 'LIKE', "%{$search}%")
                  ->orWhere('nip_nrk', 'LIKE', "%{$search}%")
                  ->orWhere('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('jabatan', 'LIKE', "%{$search}%");
            });
        }

        return $query;
    }

    // ====================================================
    // HELPER METHODS & ACCESSORIES
    // ====================================================

    /**
     * Nama lengkap jenis kelamin.
     */
    public function getJenisKelaminLabelAttribute(): string
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    /**
     * Warna badge status.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Aktif'         => 'emerald',
            'Cuti'          => 'amber',
            'Tugas Belajar' => 'blue',
            'Non-Aktif'     => 'slate',
            default         => 'slate',
        };
    }

    /**
     * Warna badge kategori.
     */
    public function getKategoriColorAttribute(): string
    {
        return match ($this->kategori) {
            'ASN (PNS)'        => 'indigo',
            'P3K Penuh Waktu'  => 'violet',
            'P3K Paruh Waktu'  => 'purple',
            'BSN / Non-ASN'    => 'orange',
            default            => 'slate',
        };
    }

    // ====================================================
    // STATIC CONSTANTS
    // ====================================================

    public static function kategoriList(): array
    {
        return ['ASN (PNS)', 'P3K Penuh Waktu', 'P3K Paruh Waktu', 'BSN / Non-ASN'];
    }

    public static function statusList(): array
    {
        return ['Aktif', 'Cuti', 'Tugas Belajar', 'Non-Aktif'];
    }

    public static function pendidikanList(): array
    {
        return ['SD', 'SMP', 'SMA/SMK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'];
    }
}
