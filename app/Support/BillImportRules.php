<?php

namespace App\Support;

use App\Models\Bill;
use Illuminate\Validation\Rule;

class BillImportRules
{
    public static function rules(int $maxItems = 100): array
    {
        return [
            'items' => ['required', 'array', 'min:1', 'max:'.$maxItems],
            'items.*.source' => ['required', 'string', 'alpha_dash', 'max:100'],
            'items.*.external_id' => ['required', 'string', 'max:191'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.cost' => ['required', 'numeric', 'gt:0', 'max:9999999999.99', 'decimal:0,2'],
            'items.*.date' => ['required', 'date_format:Y-m-d'],
            'items.*.type' => ['required', Rule::in([Bill::TYPE_GASTO, Bill::TYPE_EXTRA, Bill::TYPE_AHORRO])],
            'items.*.category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'items.*.payment_method_id' => ['required', 'integer', Rule::exists('payment_methods', 'id')],
        ];
    }
}
