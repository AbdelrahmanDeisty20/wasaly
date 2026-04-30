<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['full_name', 'email', 'phone', 'password', 'avatar', 'type', 'is_active', 'email_verified_at', 'provider', 'provider_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole(['admin', 'sub_admin']);
    }
    /** @use HasFactory<UserFactory> */

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

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function providers()
    {
        return $this->hasMany(Provider::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function fcmTokens()
    {
        return $this->hasMany(UserFcmToken::class);
    }

    public function notifications()
    {
        return $this->hasMany(AppNotification::class);
    }

    public function otps()
    {
        return $this->hasMany(Otp::class);
    }
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    public function getAvatarAttribute($value)
    {
        if (!$value) return null;
        
        // If it's already a full URL (social login), return it as is
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        return asset('storage/users/avatars/' . $value);
    }
}
