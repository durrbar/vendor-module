<?php

declare(strict_types=1);

namespace Modules\Vendor\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Enum;
use Modules\Vendor\Enums\StoreNoticePriority;
use Modules\Vendor\Enums\StoreNoticeType;

class StoreNoticeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'priority' => ['required', 'string', new Enum(StoreNoticePriority::class)],
            'notice' => ['required', 'string'],
            'description' => ['nullable', 'string', 'max:10000'],
            'effective_from' => ['nullable', 'date'],
            'expired_at' => ['required', 'date', 'after:effective_from'],
            'type' => ['required', 'string', new Enum(StoreNoticeType::class)],
            'received_by' => ['array', 'required_if: type,'.StoreNoticeType::SpecificVendor->value.','.StoreNoticeType::SpecificShop->value],
            'received_by.*' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'received_by.required_if' => 'Please! Select at least one Specific receiver.',
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
