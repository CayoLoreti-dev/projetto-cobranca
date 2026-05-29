<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParcelaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cobranca_id' => $this->cobranca_id,
            'numero' => $this->numero,
            'valor' => (string) $this->valor,
            'vencimento' => $this->vencimento?->toDateString(),
            'status' => $this->status?->value ?? $this->status,
            'paga_em' => $this->paga_em?->toJSON(),
            'valor_pago' => $this->valor_pago ? (string) $this->valor_pago : null,
        ];
    }
}
