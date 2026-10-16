<?php

namespace Tests\Feature\Api;

use App\Models\Bill;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Revenue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillImportApiTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;
    private PaymentMethod $method;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('finance.import_token', str_repeat('t', 48));

        $this->category = Category::create(['name' => 'Mercado', 'amount' => 1000]);
        $this->method = PaymentMethod::create(['name' => 'Débito']);

        Revenue::create([
            'amount' => 30000,
            'date' => '2026-10-01',
            'extra' => 0,
            'saving' => 0,
        ]);
    }

    private function item(array $overrides = []): array
    {
        return array_merge([
            'source' => 'banco-debito',
            'external_id' => 'mov-0001',
            'name' => 'Supermercado',
            'cost' => '249.90',
            'date' => '2026-10-14',
            'type' => 'Gasto',
            'category_id' => $this->category->id,
            'payment_method_id' => $this->method->id,
        ], $overrides);
    }

    public function test_api_rejects_missing_or_invalid_tokens(): void
    {
        $this->getJson('/api/categories')->assertUnauthorized();

        $this->withToken('invalid')->getJson('/api/categories')->assertUnauthorized();
    }

    public function test_reference_data_requires_token_and_returns_choices(): void
    {
        $this->withToken(str_repeat('t', 48))
            ->getJson('/api/categories')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Mercado');

        $this->getJson('/api/payment-methods')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Débito');
    }

    public function test_preview_does_not_write_and_checks_missing_month(): void
    {
        $this->withToken(str_repeat('t', 48))
            ->postJson('/api/bills/import/preview', [
                'items' => [$this->item(), $this->item([
                    'external_id' => 'mov-0002',
                    'date' => '2026-11-01',
                ])],
            ])
            ->assertOk()
            ->assertJsonPath('summary.ready', 1)
            ->assertJsonPath('summary.missing_revenue', 1);

        $this->assertDatabaseCount('bills', 0);
    }

    public function test_import_preserves_cents_and_is_idempotent(): void
    {
        $payload = ['items' => [$this->item()]];

        $this->withToken(str_repeat('t', 48))
            ->postJson('/api/bills/import', $payload)
            ->assertCreated()
            ->assertJsonPath('created', 1);

        $this->assertDatabaseHas('bills', [
            'source' => 'banco-debito',
            'external_id' => 'mov-0001',
            'cost' => 249.90,
        ]);

        $this->postJson('/api/bills/import', $payload)
            ->assertOk()
            ->assertJsonPath('created', 0)
            ->assertJsonPath('skipped_duplicates', 1);

        $this->assertDatabaseCount('bills', 1);
        $this->assertNotNull(Bill::first()->revenue_id);
    }

    public function test_conflicts_and_repeated_ids_block_the_whole_batch(): void
    {
        $this->withToken(str_repeat('t', 48))
            ->postJson('/api/bills/import', ['items' => [$this->item()]])
            ->assertCreated();

        $this->postJson('/api/bills/import', [
            'items' => [
                $this->item(['name' => 'Otro concepto']),
                $this->item(['external_id' => 'mov-0002']),
            ],
        ])
            ->assertUnprocessable()
            ->assertJsonPath('summary.conflict', 1);

        $this->postJson('/api/bills/import', [
            'items' => [
                $this->item(['external_id' => 'mov-0003']),
                $this->item(['external_id' => 'mov-0003']),
            ],
        ])
            ->assertUnprocessable()
            ->assertJsonPath('summary.repeated_in_batch', 1);

        $this->assertDatabaseCount('bills', 1);
    }

    public function test_invalid_references_and_missing_revenue_are_rejected(): void
    {
        $this->withToken(str_repeat('t', 48))
            ->postJson('/api/bills/import', ['items' => [
                $this->item(['category_id' => 999999]),
            ]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('items.0.category_id');

        $this->postJson('/api/bills/import', ['items' => [
            $this->item(['date' => '2026-11-01']),
        ]])
            ->assertUnprocessable()
            ->assertJsonPath('summary.missing_revenue', 1);

        $this->assertDatabaseCount('bills', 0);
    }

    public function test_same_external_id_can_be_used_by_another_source(): void
    {
        $this->withToken(str_repeat('t', 48))
            ->postJson('/api/bills/import', ['items' => [
                $this->item(),
                $this->item(['source' => 'otra-cuenta']),
            ]])
            ->assertCreated()
            ->assertJsonPath('created', 2);

        $this->assertDatabaseCount('bills', 2);
    }
}
