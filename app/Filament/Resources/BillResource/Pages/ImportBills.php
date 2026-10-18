<?php

namespace App\Filament\Resources\BillResource\Pages;

use App\Filament\Resources\BillResource;
use App\Models\Bill;
use App\Models\Revenue;
use App\Services\BillCsvService;
use App\Services\BillImportService;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class ImportBills extends Page
{
    protected static string $resource = BillResource::class;

    protected static string $view = 'filament.resources.bill-resource.pages.import-bills';

    protected static ?string $title = 'Importar gastos CSV';

    public ?int $revenueId = null;
    public ?string $revenuePeriod = null;
    public ?string $csvPath = null;
    public ?string $csvHash = null;
    public ?array $previewSummary = null;
    public array $previewRows = [];
    public array $problems = [];

    public function mount(): void
    {
        Gate::authorize('create', Bill::class);

        if (request()->query('revenue_id') !== null) {
            $revenue = Revenue::query()->findOrFail(request()->integer('revenue_id'));
            $this->revenueId = $revenue->getKey();
            $this->revenuePeriod = $revenue->date->format('Y-m');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('uploadCsv')
                ->label('Seleccionar CSV y validar')
                ->icon('heroicon-o-document-arrow-up')
                ->form([
                    FileUpload::make('csv')
                        ->label('Archivo CSV')
                        ->disk('local')
                        ->directory('finance-csv-imports')
                        ->visibility('private')
                        ->acceptedFileTypes([
                            'text/csv',
                            'text/plain',
                            'application/vnd.ms-excel',
                        ])
                        ->maxSize(2048)
                        ->required(),
                ])
                ->action(fn (array $data) => $this->loadPreview($data['csv'])),

            Actions\Action::make('confirmCsv')
                ->label('Confirmar importación')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->visible(fn (): bool => $this->csvPath !== null && ! $this->hasBlockers())
                ->requiresConfirmation()
                ->modalHeading('¿Importar los gastos revisados?')
                ->modalDescription('Se registrarán los nuevos movimientos y se omitirán los duplicados.')
                ->action(fn () => $this->confirmImport()),
        ];
    }

    public function loadPreview(string $path): void
    {
        Gate::authorize('create', Bill::class);
        $this->clearPreview();

        try {
            $fullPath = $this->privatePath($path);
            $items = app(BillCsvService::class)->read($fullPath);
            $this->validateRevenuePeriod($items);
            $preview = app(BillImportService::class)->preview($items);

            $this->csvPath = $path;
            $this->csvHash = hash_file('sha256', $fullPath);
            $this->previewSummary = $preview['summary'];

            foreach (array_slice($preview['items'], 0, 20) as $row) {
                $item = $items[$row['index']];
                $this->previewRows[] = [
                    'line' => $row['index'] + 2,
                    'date' => $item['date'],
                    'name' => $item['name'],
                    'cost' => $item['cost'],
                    'status' => $row['status'],
                ];
            }

            if ($this->hasBlockers()) {
                Notification::make()
                    ->title('Archivo con movimientos por resolver')
                    ->body('No se guardó ningún gasto. Revisa los conflictos y meses sin ingreso.')
                    ->warning()
                    ->send();
            } else {
                Notification::make()
                    ->title('Vista previa preparada')
                    ->body('Revisa el resumen y confirma cuando quieras guardar los gastos.')
                    ->success()
                    ->send();
            }
        } catch (ValidationException $exception) {
            $this->problems = array_slice($exception->validator->errors()->all(), 0, 15);
            Notification::make()->title('El CSV tiene errores de validación')->danger()->send();
        } catch (InvalidArgumentException $exception) {
            $this->problems = [$exception->getMessage()];
            Notification::make()->title('CSV no válido')->danger()->send();
        }
    }

    public function confirmImport(): void
    {
        Gate::authorize('create', Bill::class);

        if ($this->csvPath === null || $this->csvHash === null || $this->hasBlockers()) {
            return;
        }

        try {
            $path = $this->privatePath($this->csvPath);

            if (! hash_equals($this->csvHash, hash_file('sha256', $path))) {
                throw new InvalidArgumentException('El archivo cambió. Genera otra vista previa.');
            }

            $items = app(BillCsvService::class)->read($path);
            $this->validateRevenuePeriod($items);
            $result = app(BillImportService::class)->import($items);

            if ($result['blocked']) {
                $this->loadPreview($this->csvPath);
                return;
            }

            $created = $result['created'];
            $duplicates = $result['skipped_duplicates'];

            Storage::disk('local')->delete($this->csvPath);
            $this->clearPreview();

            Notification::make()
                ->title("Gastos importados: {$created}")
                ->body("Duplicados omitidos: {$duplicates}")
                ->success()
                ->send();
        } catch (UniqueConstraintViolationException $exception) {
            Notification::make()
                ->title('Conflicto de importación')
                ->body('Otro proceso registró uno de los movimientos. Vuelve a validar.')
                ->danger()
                ->send();
        } catch (ValidationException | InvalidArgumentException $exception) {
            Notification::make()
                ->title('No se importó el archivo')
                ->body('Vuelve a cargar y validar el CSV.')
                ->danger()
                ->send();

            $this->clearPreview();
        }
    }

    /**
     * When launched from a Revenue, reject the entire CSV if even one row
     * belongs to another month. The global Bills importer remains unrestricted.
     */
    private function validateRevenuePeriod(array $items): void
    {
        if ($this->revenueId === null) {
            return;
        }

        $revenue = Revenue::query()->findOrFail($this->revenueId);
        $period = $revenue->date->format('Y-m');

        foreach ($items as $index => $item) {
            if (substr($item['date'], 0, 7) !== $period) {
                $line = $index + 2;

                throw new InvalidArgumentException(
                    "Fila {$line}: fecha {$item['date']} fuera del mes {$period}. ".
                    'No se importó ningún movimiento.'
                );
            }
        }
    }

    private function hasBlockers(): bool
    {
        foreach (['conflict', 'missing_revenue', 'repeated_in_batch'] as $status) {
            if (($this->previewSummary[$status] ?? 0) > 0) {
                return true;
            }
        }

        return false;
    }

    private function privatePath(string $path): string
    {
        if (! str_starts_with($path, 'finance-csv-imports/') ||
            str_contains($path, '..') ||
            ! Storage::disk('local')->exists($path)) {
            throw new InvalidArgumentException('Archivo no disponible. Vuelve a subirlo.');
        }

        return Storage::disk('local')->path($path);
    }

    private function clearPreview(): void
    {
        $this->csvPath = null;
        $this->csvHash = null;
        $this->previewSummary = null;
        $this->previewRows = [];
        $this->problems = [];
    }
}
