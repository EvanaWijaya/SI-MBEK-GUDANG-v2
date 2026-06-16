<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Disposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'disposable_id',
        'disposable_type',
        'quantity',
        'reason',
        'notes',
        'created_by',
    ];

    /**
     * Related disposable entity
     */
    public function disposable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Admin who recorded the disposal
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}