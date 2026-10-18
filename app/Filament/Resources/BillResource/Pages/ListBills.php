<?php

namespace App\Filament\Resources\BillResource\Pages;

use App\Filament\Resources\BillResource;
use App\Models\Bill;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Services\BillCsvService;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Gate;

class ListBills extends ListRecords
{
    protected static string $resource = BillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('importCsv')
                ->label('Importar CSV')
                ->icon('heroicon-o-document-arrow-up')
                ->url(BillResource::getUrl('import'))
                ->visible(fn (): bool => auth()->user()?->can('create', Bill::class) ?? false),

            Actions\Action::make('exportCsv')
                ->label('Exportar CSV')
                ->icon('heroicon-o-document-arrow-down')
                ->visible(fn (): bool => auth()->user()?->can('viewAny', Bill::class) ?? false)
                ->form([
                    DatePicker::make('from')->label('Desde')->native(false),
                    DatePicker::make('to')->label('Hasta')->native(false)->afterOrEqual('from'),
                    Select::make('category_id')
                        ->label('Categoría')
                        ->options(Category::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable(),
                    Select::make('payment_method_id')
                        ->label('Método de pago')
                        ->options(PaymentMethod::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable(),
                ])
                ->action(function (array $data) {
                    Gate::authorize('viewAny', Bill::class);

                    $query = Bill::query()
                        ->when($data['from'] ?? null, fn ($query, $date) => $query->whereDate('date', '>=', $date))
                        ->when($data['to'] ?? null, fn ($query, $date) => $query->whereDate('date', '<=', $date))
                        ->when($data['category_id'] ?? null, fn ($query, $id) => $query->where('category_id', $id))
                        ->when($data['payment_method_id'] ?? null, fn ($query, $id) => $query->where('payment_method_id', $id));

                    return response()->streamDownload(function () use ($query): void {
                        $output = fopen('php://output', 'wb');

                        try {
                            app(BillCsvService::class)->write($output, $query);
                        } finally {
                            fclose($output);
                        }
                    }, 'finance-gastos-'.now()->format('Ymd-His').'.csv', [
                        'Content-Type' => 'text/csv; charset=UTF-8',
                    ]);
                }),
        ];
    }
}
