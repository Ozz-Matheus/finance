<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Revenue;
use App\Services\BillCsvService;
use App\Services\BillImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Tests\TestCase;

class BillCsvImportTest extends TestCase
{
    use RefreshDatabase;

    private function sample(): array
    {
        $category = Category::create(['name' => 'Mercado', 'amount' => 1000]);
        $method = PaymentMethod::create(['name' => 'Nu Débito']);
        Revenue::create(['date' => '2026-09-01', 'amount' => 12000]);

        return [
            'source' => 'nu-debito',
            'external_id' => 'sep-2026-001',
            'name' => 'Abarrotes la Esperanza',
            'cost' => '249.90',
            'date' => '2026-09-12',
            'type' => 'Gasto',
            'category_id' => $category->id,
            'payment_method_id' => $method->id,
        ];
    }

    private function csv(array $rows, bool $bom = true, string $delimiter = ','): string
    {
        $file = tempnam(sys_get_temp_dir(), 'fin-csv-');
        $stream = fopen($file, 'wb');

        if ($bom) {
            fwrite($stream, "\xEF\xBB\xBF");
        }

        fputcsv($stream, BillCsvService::HEADERS, $delimiter, '"', '');

        foreach ($rows as $row) {
            fputcsv($stream, array_values($row), $delimiter, '"', '');
        }

        fclose($stream);

        return $file;
    }

    public function test_csv_preview_then_import_is_idempotent(): void
    {
        $file = $this->csv([$this->sample()]);

        try {
            $items = app(BillCsvService::class)->read($file);
            $service = app(BillImportService::class);

            $this->assertSame(1, $service->preview($items)['summary']['ready']);
            $this->assertDatabaseCount('bills', 0);

            $this->assertSame(1, $service->import($items)['created']);
            $this->assertSame(1, $service->import($items)['skipped_duplicates']);
            $this->assertDatabaseCount('bills', 1);
            $this->assertEquals('249.90', Bill::first()->cost);
        } finally {
            unlink($file);
        }
    }

    public function test_semicolon_delimiter_is_supported(): void
    {
        $file = $this->csv([$this->sample()], false, ';');

        try {
            $this->assertCount(1, app(BillCsvService::class)->read($file));
        } finally {
            unlink($file);
        }
    }

    public function test_csv_rejects_invalid_category(): void
    {
        $row = $this->sample();
        $row['category_id'] = 999999;
        $file = $this->csv([$row]);

        try {
            $this->expectException(ValidationException::class);
            app(BillCsvService::class)->read($file);
        } finally {
            unlink($file);
        }
    }

    public function test_csv_rejects_bad_header(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'fin-csv-');
        file_put_contents($file, "name,cost\nabc,100\n");

        try {
            $this->expectException(InvalidArgumentException::class);
            app(BillCsvService::class)->read($file);
        } finally {
            unlink($file);
        }
    }

    public function test_csv_rejects_duplicate_references_within_batch_without_writing(): void
    {
        $row = $this->sample();
        $file = $this->csv([$row, $row]);

        try {
            $items = app(BillCsvService::class)->read($file);
            $result = app(BillImportService::class)->import($items);

            $this->assertTrue($result['blocked']);
            $this->assertSame(1, $result['plan']['summary']['repeated_in_batch']);
            $this->assertDatabaseCount('bills', 0);
        } finally {
            unlink($file);
        }
    }

    public function test_export_is_not_paginated_and_can_be_limited_to_revenue_month(): void
    {
        $september = $this->sample();
        $service = app(BillImportService::class);
        $service->import([$september]);

        $anotherSeptember = array_merge($september, [
            'external_id' => 'sep-2026-002',
            'date' => '2026-09-22',
        ]);
        $service->import([$anotherSeptember]);

        Revenue::create(['date' => '2026-10-01', 'amount' => 12000]);
        $october = array_merge($september, [
            'external_id' => 'oct-2026-001',
            'date' => '2026-10-02',
        ]);
        $service->import([$october]);

        $septemberRevenue = Revenue::query()->whereYear('date', 2026)->whereMonth('date', 9)->firstOrFail();
        $stream = fopen('php://temp', 'w+');

        try {
            app(BillCsvService::class)->write(
                $stream,
                Bill::query()->where('revenue_id', $septemberRevenue->id)
            );
            rewind($stream);
            $csv = stream_get_contents($stream);
            $this->assertStringContainsString('sep-2026-001', $csv);
            $this->assertStringContainsString('sep-2026-002', $csv);
            $this->assertStringNotContainsString('oct-2026-001', $csv);
            $this->assertCount(3, explode("\n", trim($csv)));
        } finally {
            fclose($stream);
        }
    }

    public function test_export_preserves_identifiers_and_round_trips(): void
    {
        $item = $this->sample();
        app(BillImportService::class)->import([$item]);

        $stream = fopen('php://temp', 'w+');
        app(BillCsvService::class)->write($stream, Bill::query());
        rewind($stream);

        $file = tempnam(sys_get_temp_dir(), 'fin-export-');
        file_put_contents($file, stream_get_contents($stream));
        fclose($stream);

        try {
            $rows = app(BillCsvService::class)->read($file);
            $this->assertSame('sep-2026-001', $rows[0]['external_id']);
            $this->assertSame(1, app(BillImportService::class)->import($rows)['skipped_duplicates']);
        } finally {
            unlink($file);
        }
    }
}
