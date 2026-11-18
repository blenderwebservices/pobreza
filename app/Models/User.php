<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_RESEARCHER = 'investigador';
    public const ROLE_INDIVIDUAL = 'individuo';

    protected $fillable = [
        'name',
        'email',
        'role',
        'institution',
        'phone',
        'is_active',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public static function roleLabels(): array
    {
        return [
            self::ROLE_ADMIN => 'Administrador General',
            self::ROLE_RESEARCHER => 'Investigador / Institución',
            self::ROLE_INDIVIDUAL => 'Individuo / Hogar',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_active;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isResearcher(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_RESEARCHER], true);
    }

    public function households(): HasMany
    {
        return $this->hasMany(Household::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(PovertyEvaluation::class);
    }

    public function importBatches(): HasMany
    {
        return $this->hasMany(ImportBatch::class);
    }
}
