<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function getBudgetAmount($date): float
    {
        return $this->budgets()
            ->where('category_id', $this->id)
            ->whereDate('date', $date)
            ->value('amount') ?? 0;
    }
}
