<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UniqueRevenueMonth implements Rule
{
    protected ?int $ignoreId = null;

    public function __construct(?int $ignoreId = null)
    {
        $this->ignoreId = $ignoreId;
    }

    public function passes($attribute, $value): bool
    {
        $normalized = Carbon::parse($value)->format('Y-m-01');

        return ! DB::table('revenues')
            ->whereDate('date', $normalized)
            ->when($this->ignoreId, fn ($q) => $q->where('id', '!=', $this->ignoreId))
            ->exists();
    }

    public function message(): string
    {
        return 'Ya existe un ingreso registrado para ese mes.';
    }
}
