<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    // ── Phase 1 Roles ───────────────────────────────────────────────
    const ROLE_MANAGER       = 'manager';
    const ROLE_SALES_OFFICER = 'sales_officer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Role Helpers ────────────────────────────────────────────────

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is a manager.
     */
    public function isManager(): bool
    {
        return $this->hasRole(self::ROLE_MANAGER);
    }

    /**
     * Check if user is a sales officer.
     */
    public function isSalesOfficer(): bool
    {
        return $this->hasRole(self::ROLE_SALES_OFFICER);
    }

    /**
     * Get all available roles.
     *
     * @return array<string, string>
     */
    public static function getRoles(): array
    {
        return [
            self::ROLE_MANAGER       => 'Manager',
            self::ROLE_SALES_OFFICER => 'Sales Officer',
        ];
    }

    /**
     * Get the valid role values (for validation rules).
     *
     * @return array<int, string>
     */
    public static function getValidRoles(): array
    {
        return array_keys(self::getRoles());
    }
}
