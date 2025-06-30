<?php

namespace App\Filament\Resources\RevenueResource\Pages;

use App\Filament\Resources\RevenueResource;
use App\Filament\Resources\RevenueResource\Widgets\ResumenFinancieroWidget;
use Filament\Resources\Pages\ViewRecord;

class ViewRevenue extends ViewRecord
{
    protected static string $resource = RevenueResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            ResumenFinancieroWidget::class,
        ];
    }
}
