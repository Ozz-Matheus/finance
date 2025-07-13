<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\RevenueResource;
use App\Models\Bill;
use App\Models\Revenue;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class IngresosVsGastosWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getCards(): array
    {
        $month = now()->month;
        $year = now()->year;

        $ingresos = Revenue::whereMonth('date', $month)->whereYear('date', $year)->sum('amount');
        $gastos = Bill::whereMonth('date', $month)->whereYear('date', $year)->sum('cost');

        $currentRevenue = Revenue::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->first();

        return [
            Card::make('Ingresos', '$'.number_format($ingresos, 2))
                ->description('Este mes')
                ->color('success')
                ->url(RevenueResource::getUrl('view', ['record' => $currentRevenue]))
                ->openUrlInNewTab()
                ->color('primary')
                ->extraAttributes(['class' => 'cursor-pointer']),

            Card::make('Total', '$'.number_format($gastos, 2))
                ->description('Este mes')
                ->color('danger'),

            Card::make('Balance', '$'.number_format($ingresos - $gastos, 2))
                ->description('Este mes')
                ->color($ingresos - $gastos >= 0 ? 'success' : 'danger'),
        ];
    }
}
