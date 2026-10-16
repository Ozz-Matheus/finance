<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ImportBillsRequest;
use App\Models\Bill;
use App\Models\Revenue;
use App\Services\BillImportService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BillImportController extends Controller
{
    public function preview(ImportBillsRequest $request, BillImportService $service): JsonResponse
    {
        return response()->json($service->preview($request->validated()['items']));
    }

    public function store(ImportBillsRequest $request, BillImportService $service): JsonResponse
    {
        $items = $request->validated()['items'];

        try {
            $result = DB::transaction(function () use ($items, $service): array {
                $plan = $service->preview($items);

                $blockingStatuses = ['conflict', 'missing_revenue', 'repeated_in_batch'];
                foreach ($blockingStatuses as $status) {
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
        } catch (UniqueConstraintViolationException $exception) {
            // Another request inserted the same source/external_id concurrently.
            return response()->json([
                'message' => 'Conflicto de importación. Revisa la vista previa e inténtalo de nuevo.',
            ], 409);
        }

        if ($result['blocked']) {
            return response()->json([
                'message' => 'No se importó ningún gasto. Revisa los movimientos marcados.',
                ...$result['plan'],
            ], 422);
        }

        return response()->json([
            'message' => 'Importación procesada.',
            'created' => $result['created'],
            'skipped_duplicates' => $result['skipped_duplicates'],
        ], $result['created'] > 0 ? 201 : 200);
    }
}
