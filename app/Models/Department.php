<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    // Relasi: Satu bidang punya banyak user
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Relasi: Satu bidang punya banyak arsip
    public function archives()
    {
        return $this->hasMany(Archive::class);
    }
}
