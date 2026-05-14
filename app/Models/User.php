<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

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
            'is_active' => 'boolean',
        ];
    }

    /* ── Role Helpers ── */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isFinance(): bool
    {
        return in_array($this->role, ['admin', 'finance']);
    }

    public function isCashier(): bool
    {
        return in_array($this->role, ['admin', 'cashier']);
    }

    public function isInventory(): bool
    {
        return in_array($this->role, ['admin', 'inventory']);
    }

    public function isHR(): bool
    {
        return in_array($this->role, ['admin', 'hr']);
    }

    public function isManager(): bool
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    public function hasRole(string ...$roles): bool
    {
        return $this->role === 'admin' || in_array($this->role, $roles);
    }

    public function getRoleLabel(): string
    {
        return match ($this->role) {
            'admin'     => 'Administrator',
            'finance'   => 'Finance',
            'cashier'   => 'Cashier',
            'inventory' => 'Inventory Staff',
            'hr'        => 'Human Resources',
            'manager'   => 'Manager',
            default     => ucfirst($this->role),
        };
    }

    /* ── Relationships ── */

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
