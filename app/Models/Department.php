<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Department extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_system'];

    protected $casts = [
        'is_system' => 'boolean',
    ];


    /**
     * Relasi: Satu departemen punya banyak user.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relasi: Satu departemen punya banyak kategori arsip.
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Relasi: Satu departemen punya banyak arsip MELALUI kategori.
     * Alurnya: Department -> Category -> Archive
     */
    public function archives(): HasManyThrough
    {
        return $this->hasManyThrough(
            Archive::class,   // Model tujuan (yang ingin diambil)
            Category::class,  // Model perantara
            'department_id',  // Foreign key pada tabel perantara (categories)
            'category_id',    // Foreign key pada tabel tujuan (archives)
            'id',             // Local key pada tabel ini (departments)
            'id'              // Local key pada tabel perantara (categories)
        );
    }
}