<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    // Tambahkan department_id karena sekarang kategori dimiliki oleh departemen
    protected $fillable = ['department_id', 'name', 'slug', 'description'];

    /**
     * Relasi: Kategori dimiliki oleh satu Departemen
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relasi: Satu kategori bisa memiliki banyak Arsip
     */
    public function archives(): HasMany
    {
        return $this->hasMany(Archive::class);
    }
}