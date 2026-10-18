<?php

namespace App\Filament\Resources\BillResource\Pages;

use App\Filament\Resources\BillResource;
use App\Models\Bill;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Revenue;
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
                    Select::make('period')
                        ->label('Mes y año')
                        ->placeholder('Todos los meses')
                        ->options(fn (): array => Revenue::query()
                            ->orderByDesc('date')
                            ->get(['date'])
                            ->mapWithKeys(fn (Revenue $revenue): array => [
                                $revenue->date->format('Y-m') => ucfirst($revenue->date->locale('es')->translatedFormat('F Y')),
                            ])
                            ->all())
                        ->searchable(),
                    DatePicker::make('from')->label('Desde (opcional)')->native(false),
                    DatePicker::make('to')->label('Hasta (opcional)')->native(false)->afterOrEqual('from'),
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

                    $period = $data['period'] ?? null;
                    $query = Bill::query()
                        ->when($period, fn ($query, $value) => $query
                            ->whereYear('date', substr($value, 0, 4))
                            ->whereMonth('date', substr($value, 5, 2)))
                        ->when($data['from'] ?? null, fn ($query, $date) => $query->whereDate('date', '>=', $date))
                        ->when($data['to'] ?? null, fn ($query, $date) => $query->whereDate('date', '<=', $date))
                        ->when($data['category_id'] ?? null, fn ($query, $id) => $query->where('category_id', $id))
                        ->when($data['payment_method_id'] ?? null, fn ($query, $id) => $query->where('payment_method_id', $id));

                    $filename = 'finance-gastos-'.($period ?: now()->format('Ymd-His')).'.csv';

                    return response()->streamDownload(function () use ($query): void {
                        $output = fopen('php://output', 'wb');

                        try {
                            app(BillCsvService::class)->write($output, $query);
                        } finally {
                            fclose($output);
                        }
                    }, $filename, [
                        'Content-Type' => 'text/csv; charset=UTF-8',
                    ]);
                }),
        ];
    }
}
