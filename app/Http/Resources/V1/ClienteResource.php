<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'tipo' => $this->tipo?->value ?? $this->tipo,
            'documento' => $this->documento,
            'responsavel_financeiro' => $this->responsavel_financeiro,
            'email' => $this->email,
            'telefone' => $this->telefone,
            'whatsapp' => $this->whatsapp,
            'status' => $this->status?->value ?? $this->status,
            'created_at' => $this->created_at?->toJSON(),
        ];
    }
}
