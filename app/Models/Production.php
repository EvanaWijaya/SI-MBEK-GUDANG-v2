<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Production extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'formula_id',
        'production_quantity',
        'qc_status',
        'qc_percentage',
        'qc_threshold',
        'production_date',
        'expiration_date',
        'status',
        'created_by',
    ];

    /* =======================
     | RELATIONS
     ======================= */

    public function formula(): BelongsTo
    {
        return $this->belongsTo(Formula::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function disposals(): MorphMany
    {
        return $this->morphMany(Disposal::class, 'disposable');
    }

    /* =======================
     | HELPERS
     ======================= */

    public function isProgress(): bool
    {
        return $this->status === 'progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isQcPassed(): bool
    {
        return $this->qc_status === 'passed';
    }

    public function isQcFailed(): bool
    {
        return $this->qc_status === 'failed';
    }
}