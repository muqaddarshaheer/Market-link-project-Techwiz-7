<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'avatar', 'password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function farmerProfile(): HasOne
    {
        return $this->hasOne(FarmerProfile::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class, 'customer_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'customer_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'customer_id');
    }

    public function appNotifications(): HasMany
    {
        return $this->hasMany(AppNotification::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isFarmer(): bool
    {
        return $this->role === 'farmer';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /** Create user with role/status (not mass-assignable). */
    public static function registerAccount(array $attributes): self
    {
        $role = $attributes['role'];
        $status = $attributes['status'] ?? 'active';
        $verified = $attributes['email_verified_at'] ?? null;
        unset($attributes['role'], $attributes['status'], $attributes['email_verified_at']);

        $user = new static($attributes);
        $user->role = $role;
        $user->status = $status;
        $user->email_verified_at = $verified;
        $user->save();

        return $user;
    }

    public function setAccountStatus(string $status): void
    {
        abort_unless(in_array($status, ['active', 'inactive', 'pending', 'suspended'], true), 422);
        $this->forceFill(['status' => $status])->save();
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'admin' => route('admin.dashboard'),
            'farmer' => route('farmer.dashboard'),
            default => route('customer.dashboard'),
        };
    }
}
