<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cost',
        'date',
        'type',
        'category_id',
        'revenue_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saving(function (Bill $bill) {
            if (! $bill->revenue_id && $bill->date) {
                $revenue = Revenue::whereYear('date', $bill->date->year)
                    ->whereMonth('date', $bill->date->month)
                    ->first();

                if ($revenue) {
                    $bill->revenue()->associate($revenue);
                }
            }
        });
    }

    public function revenue()
    {
        return $this->belongsTo(Revenue::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    const TYPE_GASTO = 'Gasto';

    const TYPE_AHORRO = 'Ahorro';

    const TYPE_EXTRA = 'Extra';
}
