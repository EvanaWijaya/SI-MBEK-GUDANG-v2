<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ProductStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'source',
        'reference_id',
        'received_date',
        'expiration_date',
        'price_per_unit',
    ];

    protected $casts = [
        'received_date' => 'date',
        'expiration_date' => 'date',
        'price_per_unit' => 'decimal:2',
    ];

    /**
     * Related product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Related production (source = production)
     */
    public function production(): BelongsTo
    {
        return $this->belongsTo(Production::class, 'reference_id');
    }

    /**
     * Related purchase order (source = purchase)
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'reference_id');
    }

    /**
     * Dynamic source reference
     */
    public function getReferenceAttribute()
    {
        return match ($this->source) {
            'production' => $this->production,
            'purchase' => $this->purchaseOrder,
            'manual_adjustment' => null,
            default => null,
        };
    }

    public function disposals(): MorphMany
    {
        return $this->morphMany(Disposal::class, 'disposable');
    }
}