<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Goat extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'name', 'age', 'weight', 'health_status', 'for_sale','harga'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
