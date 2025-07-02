<?php

namespace App\Filament\Resources\RevenueResource\Widgets;

use App\Models\Revenue;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class ResumenFinancieroWidget extends Widget
{
    protected static string $view = 'filament.resources.revenue-resource.widgets.resumen-financiero-widget';

    public Revenue $record;

    protected int|string|array $columnSpan = 'full';

    public function getFormattedDateProperty(): string
    {
        return ucfirst(Carbon::parse($this->record->date)->translatedFormat('F Y'));
    }

    public function getGroupedCategoriesProperty()
    {
        return $this->record->bills()
            ->where('type', 'Gasto')
            ->with('category.budgets')
            ->get()
            ->groupBy(fn ($bill) => $bill->category->name ?? 'Sin categoría');
    }

    public function getExtrasProperty()
    {
        return $this->record->bills()->where('type', 'Extra')->get();
    }

    public function getAhorrosProperty()
    {
        return $this->record->bills()->where('type', 'Ahorro')->get();
    }

    public function getGastosProperty(): float
    {
        return $this->groupedCategories->flatten()->sum('cost');
    }

    public function getGastoExtrasProperty(): float
    {
        return $this->extras->sum('cost');
    }

    public function getGastoAhorrosProperty(): float
    {
        return $this->ahorros->sum('cost');
    }
}
