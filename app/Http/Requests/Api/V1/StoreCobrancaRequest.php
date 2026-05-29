<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreCobrancaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('cobrancas.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => ['required', new \App\Rules\Uuid(), 'exists:clientes,id'],
            'categoria' => ['nullable', 'string', 'max:80'],
            'tipo' => ['required', 'in:AVISTA,PARCELADO'],
            'valor_total' => ['required', 'numeric', 'gt:0'],
            'status' => ['sometimes', 'in:RASCUNHO,EMITIDA,ENVIADA,PREVENTIVA,COBRANCA_5_DIAS,COBRANCA_10_DIAS,COBRANCA_30_DIAS,NEGATIVACAO,PAGA,CANCELADA,ARQUIVADA'],
            'data_emissao' => ['required', 'date'],
            'data_vencimento_principal' => ['required', 'date'],
            'responsavel_id' => ['nullable', new \App\Rules\Uuid(), 'exists:users,id'],
            'prioridade' => ['sometimes', 'integer', 'between:1,4'],
            'proxima_acao' => ['nullable', 'string', 'max:255'],
            'data_proxima_acao' => ['nullable', 'date'],
            'observacoes' => ['nullable', 'string', 'max:2000'],
            'gerar_boletos' => ['sometimes', 'boolean'],
            'parcelas' => ['required', 'array', 'min:1'],
            'parcelas.*.numero' => ['required', 'integer', 'min:1'],
            'parcelas.*.valor' => ['required', 'numeric', 'gt:0'],
            'parcelas.*.vencimento' => ['required', 'date'],
            'parcelas.*.status' => ['sometimes', 'in:PENDENTE,ENVIADA,EM_ATRASO,EM_NEGATIVACAO,PAGA,CANCELADA,ARQUIVADA'],
            'parcelas.*.observacoes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
