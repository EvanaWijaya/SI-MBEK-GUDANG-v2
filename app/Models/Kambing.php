<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Carbon\Carbon;

class Kambing extends Model
{
    use HasFactory;
    protected $table = 'kambing';
    protected $primaryKey = 'id';


    protected $fillable = [
        'user_id',
        'name',
        'age',
        'age_now',
        'weight_now',
        'image',
        'for_sale',
        'imageCaption',
        'type_goat',
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
    public static function getTipeGoatOptions(): array
    {
        return ['Etawa', 'Boer', 'Skeang', 'Saaren'];
    }

    public function getAgeNowAttribute()
    {
        if (!$this->tanggal_lahir) {
            return null;
        }

        return Carbon::parse($this->tanggal_lahir)->diffInMonths(Carbon::now());
    }

    public function histories()
    {
        return $this->hasMany(KambingHistory::class);
    }
    // Method untuk menghitung umur
    public function hitungUmur($referensi = null)
    {
        $referensi = $referensi ?: now();
        $lahir = Carbon::parse($this->tanggal_lahir);
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

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function primaryImage()
    {
        return $this->morphOne(Media::class, 'mediable')
            ->where('type', 'image')
            ->where('is_primary', true);
    }
}

