<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'phone', 'nationality', 'passport_number', 'birth_date', 'gender', 'account_type', 'shelter_point'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    public const SHELTER_POINTS = [
        'tawau' => ['name' => 'Tawau', 'fee' => 30],
        'sandakan' => ['name' => 'Sandakan', 'fee' => 30],
        'kinabalu_papar' => ['name' => 'Kinabalu (Papar)', 'fee' => 55],
        'kinabalu_menggatal' => ['name' => 'Kinabalu (Menggatal)', 'fee' => 50],
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole('admin');
    }

    public function isDeportation(): bool
    {
        return $this->account_type === 'deportation';
    }

    public function getShelterFeeAttribute(): int
    {
        return self::SHELTER_POINTS[$this->shelter_point]['fee'] ?? 0;
    }

    public function getShelterPointNameAttribute(): ?string
    {
        return self::SHELTER_POINTS[$this->shelter_point]['name'] ?? null;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function passengerProfiles(): HasMany
    {
        return $this->hasMany(PassengerProfile::class);
    }

    public function boardingLogs(): HasMany
    {
        return $this->hasMany(BoardingLog::class, 'validated_by');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class, 'processed_by');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function deportationManifests(): HasMany
    {
        return $this->hasMany(DeportationManifest::class, 'officer_id');
    }
}
