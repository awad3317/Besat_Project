<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        // 'email',
        'image',
        'password',
        'phone', 
        'whatsapp_number', 
        'type',
        'fcm_token',
        'is_banned',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'password' => 'hashed',
        ];
    }

    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }
    
    public function usedDiscountCodes()
    {
        return $this->belongsToMany(DiscountCode::class, 'discount_code_user')
                    ->withPivot('usage_count');

    }

     public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
    // In App\Models\Customer.php
public function getInitials(): string
{
    $words = explode(' ', $this->name);
    $initials = '';
    if (isset($words[0])) {
        $initials .= mb_substr($words[0], 0, 1);
    }
    if (isset($words[1])) {
        $initials .= mb_substr($words[1], 0, 1);
    }
    return mb_strtoupper($initials);
}
}
