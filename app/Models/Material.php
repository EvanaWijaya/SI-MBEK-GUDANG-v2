<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_name',
        'category',
        'unit',
        'stock',
        'average_usage',
        'lead_time',
        'safety_stock',
        'description',
    ];

    protected $casts = [
        'average_usage' => 'float',
        'lead_time' => 'integer',
        'safety_stock' => 'integer',
        'stock' => 'integer',
    ];

    /**
     * Purchase Order Items
     */
    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Reorder Point (ROP)
     */
    public function getReorderPointAttribute(): float
    {
        return ($this->average_usage * $this->lead_time)
            + $this->safety_stock;
    }

    /**
     * Check if stock is below ROP
     */
    public function isBelowReorderPoint(): bool
    {
        return $this->stock <= $this->reorder_point;
    }

    public function scopeBelowReorderPoint($query)
    {
        return $query->whereRaw(
            'stock <= (average_usage * lead_time + safety_stock)'
        );
    }

    public function formulaMaterials(): HasMany
    {
        return $this->hasMany(FormulaMaterial::class);
    }

    public function formulas(): BelongsToMany
    {
        return $this->belongsToMany(
            Formula::class,
            'formula_materials'
        )->withPivot('percentage')
            ->withTimestamps();
    }

    public function productions(): HasMany
    {
        return $this->hasMany(Production::class, 'material_id');
    }

    public function disposals(): MorphMany
    {
        return $this->morphMany(Disposal::class, 'disposable');
    }

    public function materialStocks(): HasMany
    {
        return $this->hasMany(MaterialStock::class);
    }

    public function stockMovements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, 'stockable');
    }

    public function getAveragePriceAttribute()
    {
        // 🔥 Hitung rata-rata HANYA dari Purchase Order yang statusnya 'received' (Diterima)
        $avgPrice = $this->purchaseOrderItems()
            ->whereHas('purchaseOrder', function ($query) {
                $query->where('status', 'received');
            })
            ->avg('unit_price');

        return $avgPrice ?? 0;
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'feed' => 'Pakan',
            'medicine' => 'Obat',
            default => $this->category,
        };
    }
}