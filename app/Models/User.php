<?php

namespace App\Models;

use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'profilePhoto',
        'companyName',
        'firstName',
        'lastName',
        'countryCode',
        'dialCode',
        'telephone',
        'email',
        'password',
        'role'
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'emailVerifiedAt' => 'datetime',
        'password' => 'hashed',
    ];

    public function campaign()
    {
        return $this->hasOne(Campaign::class);
    }

    public function opdb()
    {
        return $this->hasOne(Opdb::class);
    }

    public function agent()
    {
        return $this->hasOne(Agent::class);
    }

    public function administration()
    {
        return $this->hasOne(Administration::class);
    }

    public function userInfo()
    {
        return $this->hasOne(UserInfo::class);
    }
}
