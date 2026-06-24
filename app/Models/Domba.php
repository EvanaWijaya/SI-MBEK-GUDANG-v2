<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Domba extends Model
{
    use HasFactory;

    protected $table = 'domba';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'name',
        'age',
        'age_now',
        'weight_now',
        'image',
        'for_sale',
        'type_domba',
        'jenis',
        'jenis_kelamin',
        'weight',
        'faksin_status',
        'healt_status',
        'harga',
        'tanggal_lahir'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function getTipeDombaOptions(): array
    {
        return ['Garut', 'Ekor Gemuk', 'Ekor Tipis', 'Texel', 'Dorper'];
    }

    public function histories()
    {
        return $this->hasMany(KambingHistory::class);
    }
    // Method untuk menghitung umur
    public function hitungUmur($referensi = null)
    {
        $referensi = $referensi ?: now();
        $lahir = \Carbon\Carbon::parse($this->tanggal_lahir);
        $diff = $lahir->diff($referensi);

        $result = [];
        if ($diff->y > 0)
            $result[] = $diff->y . ' tahun';
        if ($diff->m > 0)
            $result[] = $diff->m . ' bulan';
        if ($diff->d > 0)
            $result[] = $diff->d . ' hari';

        return implode(' ', $result) ?: '0 hari';
    }

    // Method untuk umur awal
    public function umurAwal()
    {
        return $this->hitungUmur($this->created_at);
    }

    public function media(): MorphMany
{
    return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
}

// Foto utama (relasi, bukan method biasa)
public function primaryImage(): MorphOne
{
    return $this->morphOne(Media::class, 'mediable')
        ->where('is_primary', true)
        ->orderBy('sort_order');
}

    
}