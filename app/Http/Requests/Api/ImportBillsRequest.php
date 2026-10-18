<?php

namespace App\Http\Requests\Api;

use App\Support\BillImportRules;
use Illuminate\Foundation\Http\FormRequest;

class ImportBillsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return BillImportRules::rules();
    }
}
