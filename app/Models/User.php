<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'role',
        'status',
        'last_login_at',
        'avatar',
        'phone',
        'notes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
    ];

    /**
     * Cek apakah user memiliki role tertentu.
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        return $this->role === $roles;
    }

    /**
     * Cek apakah user aktif.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get distinct service managers in kontrak matching/containing the user's name.
     */
    public function getSupervisedServiceManagers(): array
    {
        $name = strtoupper($this->name);
        return \App\Models\Kontrak::distinct()
            ->whereNotNull('service_manager')
            ->pluck('service_manager')
            ->filter(function($sm) use ($name) {
                return str_contains(strtoupper($sm), $name);
            })
            ->toArray();
    }
}
