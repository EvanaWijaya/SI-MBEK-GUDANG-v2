<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_code',
        'supplier_id',
        'type',
        'order_date',
        'status',

        'ordered_by_id',
        'ordered_by_type',

        'recorded_by_id',
        'recorded_by_type',

        'notes',

        'approved_date',
        'received_date',
    ];

    /**
     * Admin/Owner who ordered the PO
     */
    public function orderedBy(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Admin/Owner who recorded the PO
     */
    public function recordedBy(): MorphTo
    {
        return $this->morphTo(
            __FUNCTION__,
            'recorded_by_type',
            'recorded_by_id'
        );
    }

    /**
     * Supplier
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Purchase Order Items
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function stockMovements(): MorphMany
    {
        return $this->morphMany(
            StockMovement::class,
            'stockable'
        );
    }
}