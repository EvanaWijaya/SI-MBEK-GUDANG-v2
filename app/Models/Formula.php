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
        'kode_formula',
        'nama_formula',
        'deskripsi',
        'created_by',
        'is_active',
    ];

    /**
     * Admin pembuat formula
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Detail bahan baku dalam formula
     */
    public function formulaMaterials(): HasMany
    {
        return $this->hasMany(FormulaMaterial::class);
    }

    /**
     * Relasi langsung ke material (many-to-many)
     */
    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(
            Material::class,
            'formula_materials'
        )->withPivot('persentase')
            ->withTimestamps();
    }

    public function productions(): HasMany
    {
        return $this->hasMany(Production::class);
    }
}
