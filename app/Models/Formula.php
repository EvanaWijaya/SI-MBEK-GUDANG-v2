<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Formula extends Model
{
    use HasFactory;

    protected $fillable = [
        'formula_code',
        'formula_name',
        'description',
        'created_by',
        'is_active',
    ];

    /**
     * Admin who created the formula
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Formula material details
     */
    public function formulaMaterials(): HasMany
    {
        return $this->hasMany(FormulaMaterial::class);
    }

    /**
     * Direct many-to-many relationship with materials
     */
    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(
            Material::class,
            'formula_materials'
        )->withPivot('percentage')
            ->withTimestamps();
    }

    /**
     * Productions using this formula
     */
    public function productions(): HasMany
    {
        return $this->hasMany(Production::class);
    }
}