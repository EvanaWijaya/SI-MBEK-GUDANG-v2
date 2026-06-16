<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'product_name',
        'description',
        'selling_price',
        'stock',
        'reorder_point',
        'formula_id',
        'category',
        'source',
        'created_by',
    ];
    protected $with = ['primaryImage'];

    public static function generateProductCode(string $category): string
    {
        $prefix = match ($category) {
            'pakan' => 'PKN',
            'obat' => 'OBT',
            default => 'PRD',
        };

        $lastProduct = self::where('product_code', 'like', $prefix . '-%')
            ->lockForUpdate()
            ->orderByDesc('id')
            ->first();

        if (!$lastProduct) {
            return $prefix . '-0001';
        }

        $lastNumber = (int) substr($lastProduct->product_code, -4);

        return $prefix . '-' . str_pad(
            $lastNumber + 1,
            4,
            '0',
            STR_PAD_LEFT
        );
    }

    /**
     * Formula used for this product
     */
    public function formula(): BelongsTo
    {
        return $this->belongsTo(Formula::class);
    }

    /**
     * Product productions
     */
    public function productions(): HasMany
    {
        return $this->hasMany(Production::class);
    }

    /**
     * Admin who created the product
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Product allocations
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(ProductAllocation::class);
    }

    /**
     * Check if stock is below reorder point
     */
    public function isBelowReorderPoint(): bool
    {
        return $this->stock <= $this->reorder_point;
    }

    public function stockMovements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, 'stockable');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }

    public function getImageUrlAttribute(): string
    {
        return $this->primaryImage
            ? $this->primaryImage->url
            : asset('images/default-product.png');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function primaryImage(): MorphOne
    {
        return $this->morphOne(Media::class, 'mediable')
            ->where('type', 'image')
            ->where('is_primary', true);
    }

    public function images()
    {
        return $this->media()
            ->where('type', 'image')
            ->orderBy('sort_order');
    }
}