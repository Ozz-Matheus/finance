<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ImportBillsRequest;
use App\Services\BillImportService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;

class BillImportController extends Controller
{
    public function preview(ImportBillsRequest $request, BillImportService $service): JsonResponse
    {
        return response()->json($service->preview($request->validated()['items']));
    }

    public function store(ImportBillsRequest $request, BillImportService $service): JsonResponse
    {
        try {
            $result = $service->import($request->validated()['items']);
        } catch (UniqueConstraintViolationException $exception) {
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
