<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Revenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount', 'date', 'extra',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Accesores / Métodos útiles
    |--------------------------------------------------------------------------
    */

    public function getFormattedDateAttribute(): string
    {
        return ucfirst(Carbon::parse($this->date)->translatedFormat('F \d\e Y'));
    }
}
