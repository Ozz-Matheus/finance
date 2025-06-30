<?php

namespace App\Models;

use App\Traits\HasFormattedDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    use HasFactory, HasFormattedDate;

    protected $fillable = [
        'amount', 'date', 'extra', 'saving',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}
