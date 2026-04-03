<?php

declare(strict_types=1);

namespace Modules\Vendor\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BecameSellersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page_options' => ['required', 'array'],
            'commission.*.min_balance' => ['required', 'numeric', 'min:0'],
            'commission.*.max_balance' => ['required'],
            'commission.*.commission' => ['required', 'numeric', 'min:0'],
            'commission.*.level' => ['required', 'string'],
            'commission.*.sub_level' => ['required', 'string'],
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
