<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QcIndicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_critical',
        'is_active',
    ];

    protected $casts = [
        'is_critical' => 'boolean',
        'is_active' => 'boolean',
    ];

    /* =====================
     | SCOPES
     ===================== */

    /**
     * Active indicators
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Critical indicators
     */
    public function scopeCritical($query)
    {
        return $query->where('is_critical', true);
    }

    /**
     * Non-critical indicators
     */
    public function scopeNonCritical($query)
    {
        return $query->where('is_critical', false);
    }
}