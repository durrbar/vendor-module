<?php

declare(strict_types=1);

namespace Modules\Vendor\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class WithdrawRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shop_id' => ['required', 'exists:Modules\Vendor\Models\Shop,id'],
            'amount' => ['required', 'numeric'],
            'payment_method' => ['nullable', 'string'],
            'details' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
