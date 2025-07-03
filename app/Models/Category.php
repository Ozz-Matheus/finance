<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
    ];

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function getBudgetAmount(string $date): float
    {
        return $this->budgets()
            ->whereDate('date', $date)
            ->value('amount')
            ?? $this->amount
            ?? 0;
    }
}
