<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class PresupuestoRestanteWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected function getCards(): array
    {
        $cards = [];

        $categories = Category::with(['budgets', 'bills' => function ($query) {
            $query->whereMonth('date', now()->month)
                ->whereYear('date', now()->year);
        }])->get();

        foreach ($categories as $category) {
            $latestBudget = $category->budgets->sortByDesc('created_at')->first();
            $budgetAmount = $latestBudget?->amount ?? 0;

            $spent = $category->bills->sum('cost');
            $remaining = max($budgetAmount - $spent, 0);

            $cards[] = Card::make($category->name, 'Disponible: $'.number_format($remaining, 2))
                ->description('Total: $'.number_format($spent, 2).' de el Presupuesto: $'.number_format($budgetAmount, 2))
                ->color($remaining > 0 ? 'success' : 'danger');
        }

        return $cards;
    }
}
