<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;

class ReferenceDataController extends Controller
{
    public function categories(): JsonResponse
    {
        return response()->json([
            'data' => Category::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function paymentMethods(): JsonResponse
    {
        return response()->json([
            'data' => PaymentMethod::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
