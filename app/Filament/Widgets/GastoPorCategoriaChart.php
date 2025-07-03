<?php

namespace App\Filament\Widgets;

use App\Models\Bill;
use Filament\Widgets\BarChartWidget;

class GastoPorCategoriaChart extends BarChartWidget
{
    protected static ?string $heading = 'Top 5 categorías de gasto';

    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $bills = Bill::with('category')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->get()
            ->groupBy('category.name')
            ->map(fn ($items) => $items->sum('cost'))
            ->sortDesc()
            ->take(5);

        return [
            'datasets' => [
                [
                    'label' => 'Monto',
                    'data' => $bills->values()->toArray(),
                ],
            ],
            'labels' => $bills->keys()->toArray(),
        ];
    }
}
