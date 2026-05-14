<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_MANAGER       = 'manager';
    const ROLE_SALES_OFFICER = 'sales_officer';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isManager(): bool
    {
        return $this->hasRole(self::ROLE_MANAGER);
    }

    public function isSalesOfficer(): bool
    {
        return $this->hasRole(self::ROLE_SALES_OFFICER);
    }

    public static function getRoles(): array
    {
        return [
            self::ROLE_MANAGER       => 'Manager',
            self::ROLE_SALES_OFFICER => 'Sales Officer',
        ];
    }

    public static function getValidRoles(): array
    {
        return array_keys(self::getRoles());
    }
}
