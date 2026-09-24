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
        'name', 'email', 'phone', 'address', 'password', 'role', 'status', 'avatar',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

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
        return $this->hasMany(AppNotification::class, 'user_id');
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'published_by');
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

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function avatarUrl(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=2d6a4f&color=fff';
    }
}
