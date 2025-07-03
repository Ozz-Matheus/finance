<?php

namespace App\Observers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Revenue;
use Illuminate\Support\Carbon;

class RevenueObserver
{
    public function created(Revenue $revenue): void
    {
        $categories = Category::all();
        $monthStart = Carbon::parse($revenue->date)->startOfMonth();

        foreach ($categories as $category) {
            $alreadyExists = Budget::where('category_id', $category->id)
                ->whereDate('date', $monthStart)
                ->exists();

            if (! $alreadyExists) {
                Budget::create([
                    'category_id' => $category->id,
                    'amount' => $category->amount,
                    'date' => $monthStart,
                ]);
            }
        }
    }
}
