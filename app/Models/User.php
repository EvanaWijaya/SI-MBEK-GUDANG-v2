<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\CustomResetPassword;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /* ==========================
     | RELATIONSHIPS
     ========================== */

    public function kambings()
    {
        return $this->hasMany(Kambing::class);
    }

    public function dombas()
    {
        return $this->hasMany(Domba::class);
    }

    /* ==========================
     | NOTIFICATIONS
     ========================== */

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

    /* ==========================
     | MASS ASSIGNABLE
     ========================== */

    protected $fillable = [
        'name',
        'email',
        'province',
        'city',
        'address',
        'phone_number',
        'profile_picture',
        'password',
    ];

    /* ==========================
     | HIDDEN
     ========================== */

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /* ==========================
     | CASTS
     ========================== */

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}