<?php

namespace App\Models;

use App\Notifications\AdminResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
        'must_change_password' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Check if admin is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if admin is regular admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Send password reset notification
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPasswordNotification($token));
    }

    /**
     * Purchase Orders ordered by this admin
     */
    public function orderedPurchaseOrders(): MorphMany
    {
        return $this->morphMany(PurchaseOrder::class, 'ordered_by');
    }

    /**
     * Purchase Orders recorded by this admin
     */
    public function recordedPurchaseOrders(): MorphMany
    {
        return $this->morphMany(PurchaseOrder::class, 'recorded_by');
    }

    /**
     * Productions created by this admin
     */
    public function productions(): HasMany
    {
        return $this->hasMany(Production::class, 'created_by');
    }

    /**
     * Activity logs
     */
    public function activities(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'actor');
    }
}