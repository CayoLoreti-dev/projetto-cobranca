<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CobrancaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'cliente_id' => $this->cliente_id,
            'categoria' => $this->categoria,
            'tipo' => $this->tipo?->value ?? $this->tipo,
            'status' => $this->status?->value ?? $this->status,
            'valor_total' => (string) $this->valor_total,
            'data_emissao' => $this->data_emissao?->toDateString(),
            'data_vencimento_principal' => $this->data_vencimento_principal?->toDateString(),
            'parcelas' => ParcelaResource::collection($this->whenLoaded('parcelas')),
        ];
    }
}
