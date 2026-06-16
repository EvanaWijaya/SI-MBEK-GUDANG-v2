<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MaterialStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id',
        'quantity',
        'received_date',
        'expiration_date',
        'price_per_unit',
        'created_by',
    ];

    protected $casts = [
        'received_date' => 'date',
        'expiration_date' => 'date',
        'price_per_unit' => 'decimal:2',
    ];

    /**
     * Related material
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Admin who recorded the stock
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Related disposals
     */
    public function disposals(): MorphMany
    {
        return $this->morphMany(Disposal::class, 'disposable');
    }
}