<?php

namespace App\Models;

use App\Notifications\OwnerResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Owner extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'owner';

    protected $fillable = [
        'name',
        'email',
        'password',
        'must_change_password',
        'phone',
        'profile_picture',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'must_change_password' => 'boolean',
    ];

    /**
     * Send password reset notification
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new OwnerResetPasswordNotification($token));
    }

    /**
     * Purchase orders ordered by owner
     */
    public function orderedPurchaseOrders(): MorphMany
    {
        return $this->morphMany(PurchaseOrder::class, 'ordered_by');
    }

    /**
     * Activity logs
     */
    public function activities(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'actor');
    }

     public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }

    public function primaryImage(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Media::class, 'mediable')->where('is_primary', true);
    }
}