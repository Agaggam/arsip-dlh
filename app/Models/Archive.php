<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Archive extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'file_path',
        'file_type',
        'file_size',
        'category_id',
        'user_id',
        'archive_date',
        'download_count',
        'description',
        'hash_token',
        'delete_reason',
    ];

    /**
     * Relasi ke Kategori
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke User (Pengunggah)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Aksesor opsional: Jika kamu ingin memanggil $archive->department
     * Kamu bisa mendapatkan datanya lewat kategori.
     */
    public function department()
    {
        return $this->category->department();
    }
}