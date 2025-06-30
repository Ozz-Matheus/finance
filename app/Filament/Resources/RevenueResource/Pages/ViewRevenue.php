<?php

namespace App\Filament\Resources\RevenueResource\Pages;

use App\Filament\Resources\RevenueResource;
use Filament\Resources\Pages\ViewRecord;

class ViewRevenue extends ViewRecord
{
    protected static string $resource = RevenueResource::class;

    protected static string $view = 'filament.resources.revenue-resource.pages.view-revenue';

    public function getFormattedDateProperty(): string
    {
        return \Carbon\Carbon::parse($this->record->date)->translatedFormat('F Y');
    }

    public function getGroupedCategoriesProperty()
    {
        return $this->record->bills()
            ->where('type', 'Gasto')
            ->with('category')
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
