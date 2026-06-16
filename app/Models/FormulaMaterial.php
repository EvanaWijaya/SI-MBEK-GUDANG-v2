<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormulaMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'formula_id',
        'material_id',
        'percentage',
    ];

    /**
     * Related formula
     */
    public function formula(): BelongsTo
    {
        return $this->belongsTo(Formula::class);
    }

    /**
     * Related material
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}