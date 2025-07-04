<?php

namespace App\Rules;

use App\Models\Revenue;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Carbon;

class RevenueExistsForMonth implements Rule
{
    public function passes($attribute, $value): bool
    {
        try {
            $date = Carbon::parse($value);
        } catch (\Exception $e) {
            return false;
        }

        return Revenue::whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->exists();
    }

    public function message(): string
    {
        return 'Primero debes registrar un ingreso para este mes.';
    }
}
