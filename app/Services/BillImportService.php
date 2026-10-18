<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Revenue;
use Illuminate\Support\Facades\DB;

class BillImportService
{
    /**
     * Returns an ordered validation plan; it never writes any data.
     *
     * The client supplies a stable external_id scoped to a source (bank/account alias).
     * This preserves repeated purchases that share a date, concept and amount.
     */
    public function preview(array $items): array
    {
        $summary = array_fill_keys([
            'ready', 'duplicate', 'conflict', 'missing_revenue', 'repeated_in_batch',
        ], 0);

        $seen = [];
        $revenueIds = [];
        $results = [];

        foreach ($items as $index => $item) {
            $key = $item['source']."\0".$item['external_id'];
            $month = substr($item['date'], 0, 7);
            $status = 'ready';

            if (isset($seen[$key])) {
                $status = 'repeated_in_batch';
            } else {
                $seen[$key] = true;

                $existing = Bill::query()
                    ->where('source', $item['source'])
                    ->where('external_id', $item['external_id'])
                    ->first();

                if ($existing) {
                    $status = $this->sameMovement($existing, $item) ? 'duplicate' : 'conflict';
                } else {
                    if (! array_key_exists($month, $revenueIds)) {
                        $revenueIds[$month] = Revenue::query()
                            ->whereYear('date', substr($month, 0, 4))
                            ->whereMonth('date', substr($month, 5, 2))
                            ->value('id');
                    }

                    if (! $revenueIds[$month]) {
                        $status = 'missing_revenue';
                    }
                }
            }

            $summary[$status]++;

            $results[] = [
                'index' => $index,
                'source' => $item['source'],
                'external_id' => $item['external_id'],
                'status' => $status,
            ];
        }

        return ['summary' => $summary, 'items' => $results];
    }

    public function import(array $items): array
    {
        return DB::transaction(function () use ($items): array {
            $plan = $this->preview($items);

            foreach (['conflict', 'missing_revenue', 'repeated_in_batch'] as $status) {
                if ($plan['summary'][$status] > 0) {
                    return ['blocked' => true, 'plan' => $plan];
                }
            }

            $created = 0;
            foreach ($plan['items'] as $row) {
                if ($row['status'] === 'duplicate') {
                    continue;
                }
                $item = $items[$row['index']];
                $month = substr($item['date'], 0, 7);
                $revenueId = Revenue::query()
                    ->whereYear('date', substr($month, 0, 4))
                    ->whereMonth('date', substr($month, 5, 2))
                    ->value('id');
                Bill::query()->create([
                    'source' => $item['source'],
                    'external_id' => $item['external_id'],
                    'name' => $item['name'],
                    'cost' => $item['cost'],
                    'date' => $item['date'],
                    'type' => $item['type'],
                    'revenue_id' => $revenueId,
                    'category_id' => $item['category_id'],
                    'payment_method_id' => $item['payment_method_id'],
                ]);
                $created++;
            }

            return [
                'blocked' => false,
                'created' => $created,
                'skipped_duplicates' => $plan['summary']['duplicate'],
            ];
        });
    }

    private function sameMovement(Bill $bill, array $item): bool
    {
        return $bill->name === $item['name']
            && number_format((float) $bill->cost, 2, '.', '') === number_format((float) $item['cost'], 2, '.', '')
            && $bill->date->format('Y-m-d') === $item['date']
            && $bill->type === $item['type']
            && (int) $bill->category_id === (int) $item['category_id']
            && (int) $bill->payment_method_id === (int) $item['payment_method_id'];
    }
}
