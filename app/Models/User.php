<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\LogsActivity;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getActivityLogName(): string
    {
        return 'portal_settings';
    }

    public function getActivityLogTitle(): string
    {
        return $this->name . " ({$this->email})";
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->hasRole('Super Admin')) {
            return true;
        }

        $panelId = $panel->getId();

        return match ($panelId) {
            'keuangan' => $this->can('module.keuangan'),
            'settings' => $this->can('module.settings'),
            default => $this->can("module.{$panelId}"),
        };
    }

    /**
     * Check if user has permission to access a specific module
     */
    public function canAccessModule(string $moduleId): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->hasRole('Super Admin')) {
            return true;
        }

        return $this->can("module.{$moduleId}");
    }

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
            'last_login_at' => 'datetime',
        ];
    }
}
