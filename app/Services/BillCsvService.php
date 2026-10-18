<?php

namespace App\Services;

use App\Support\BillImportRules;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class BillCsvService
{
    public const HEADERS = [
        'source', 'external_id', 'name', 'cost', 'date', 'type',
        'category_id', 'payment_method_id',
    ];

    public const MAX_ROWS = 500;

    public function read(string $filename): array
    {
        $handle = @fopen($filename, 'rb');

        if (! $handle) {
            throw new InvalidArgumentException('No se pudo abrir el archivo CSV.');
        }

        try {
            $firstLine = fgets($handle);
            if ($firstLine === false) {
                throw new InvalidArgumentException('El CSV está vacío.');
            }

            $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);
            $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';
            $headers = str_getcsv(rtrim($firstLine, "\r\n"), $delimiter, '"', '');

            if ($headers !== self::HEADERS) {
                throw new InvalidArgumentException(
                    'Las columnas deben ser, en este orden: '.implode(', ', self::HEADERS)
                );
            }

            $items = [];
            $line = 1;

            while (($row = fgetcsv($handle, null, $delimiter, '"', '')) !== false) {
                $line++;

                if ($row === [null]) {
                    continue;
                }

                if (count($row) !== count(self::HEADERS)) {
                    throw new InvalidArgumentException("Fila {$line}: número de columnas incorrecto.");
                }

                $items[] = array_combine(self::HEADERS, array_map(
                    fn ($value) => trim((string) $value),
                    $row
                ));

                if (count($items) > self::MAX_ROWS) {
                    throw new InvalidArgumentException(
                        'Máximo '.self::MAX_ROWS.' movimientos por archivo CSV.'
                    );
                }
            }

            Validator::make(['items' => $items], BillImportRules::rules(self::MAX_ROWS))->validate();

            return $items;
        } finally {
            fclose($handle);
        }
    }

    public function write($output, Builder $query): void
    {
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, self::HEADERS, ',', '"', '');

        foreach ($query->orderBy('id')->cursor() as $bill) {
            fputcsv($output, [
                $this->safeCell($bill->source),
                $this->safeCell($bill->external_id),
                $this->safeCell($bill->name),
                $bill->cost,
                $bill->date->format('Y-m-d'),
                $bill->type,
                $bill->category_id,
                $bill->payment_method_id,
            ], ',', '"', '');
        }
    }

    private function safeCell(?string $value): string
    {
        $value ??= '';

        // Prevent CSV formula injection in spreadsheet viewers.
        return preg_match('/^\s*[=+\-@]/u', $value) ? "'".$value : $value;
    }
}
