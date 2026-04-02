<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id', // Tambahkan ini
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
