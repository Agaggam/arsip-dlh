<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Tambahkan ini
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Archive extends Model
{
    use HasFactory, SoftDeletes; // Gunakan trait SoftDeletes

    protected $fillable = [
        'title',
        'file_path',
        'file_type',
        'file_size',
        'category_id',
        'user_id',
        'download_count',
        'description',
        'department_id', // Tambahkan ini
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
     * Relasi ke Department (Bidang)
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}