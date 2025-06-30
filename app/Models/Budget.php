<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount', 'date', 'category_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

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
