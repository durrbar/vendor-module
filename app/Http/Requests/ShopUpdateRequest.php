<?php

declare(strict_types=1);

namespace Modules\Vendor\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ShopUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'categories' => ['array'],
            'is_active' => ['boolean'],
            'description' => ['nullable', 'string', 'max:10000'],
            'balance' => ['array'],
            'image' => ['nullable', 'array'],
            'cover_image' => ['nullable', 'array'],
            'settings' => ['array'],
            'address' => ['array'],
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
