<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'status',
        'department_id',
        // [LOW-02] email_otp_code dan email_otp_expires_at dihapus dari fillable
        // Gunakan direct assignment: $user->email_otp_code = ...; $user->save();
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_otp_code',       // [LOW-02] Jangan ekspos OTP code
        'email_otp_expires_at', // [LOW-02] Jangan ekspos expiry OTP
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * RELASI
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * FUNGSI CEK HAK AKSES (CUSTOM METHODS)
     */
    public function isPureSuperAdmin(): bool
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }

        if (!$this->role || $this->role->name !== 'super_admin') {
            return false;
        }

        if (!$this->relationLoaded('department')) {
            $this->load('department');
        }

        if (!$this->department) {
            return false;
        }

        return (bool) ($this->department->is_system || $this->department->name === 'System' || $this->department->slug === 'system');
    }

    public function isAdmin(): bool
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }

        return (bool) ($this->role && $this->role->name === 'admin');
    }

    public function isUser(): bool
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }

        return (bool) ($this->role && $this->role->name === 'user');
    }

    public function isFromDepartment(int $department_id): bool
    {
        return $this->department_id === $department_id;
    }
}
