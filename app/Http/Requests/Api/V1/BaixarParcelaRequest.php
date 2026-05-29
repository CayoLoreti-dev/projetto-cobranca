<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaixarParcelaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('parcelas.pay') ?? false;
    }

    public function rules(): array
    {
        return [
            'valor_pago' => ['nullable', 'numeric', 'gt:0'],
            'paga_em' => ['nullable', 'date'],
            'forma_pagamento' => ['nullable', 'string', 'max:80'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
