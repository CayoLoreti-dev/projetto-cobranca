<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('clientes.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'min:3'],
            'tipo' => ['required', 'in:PF,PJ,CONDOMINIO'],
            'documento' => ['required', 'string', 'max:32', 'unique:clientes,documento'],
            'responsavel_financeiro' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'telefone' => ['required', 'string', 'min:8', 'max:32'],
            'whatsapp' => ['nullable', 'string', 'max:32'],
            'endereco' => ['nullable', 'string', 'max:1000'],
            'observacoes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
