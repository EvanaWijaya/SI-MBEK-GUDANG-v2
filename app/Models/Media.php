<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'mediable_id',
        'mediable_type',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'type',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'file_size' => 'integer',
    ];

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * URL lengkap file
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}