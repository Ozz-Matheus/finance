<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBudget extends CreateRecord
{
    protected static string $resource = BudgetResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public static function canCreateAnother(): bool
    {
        return false;
    }
}
