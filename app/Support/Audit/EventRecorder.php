<?php

namespace App\Support\Audit;

use App\Models\Boleto;
use App\Models\BoletoEvento;
use App\Models\Cobranca;
use App\Models\CobrancaEvento;
use App\Models\Parcela;
use App\Models\ParcelaEvento;
use Illuminate\Support\Facades\Auth;

class EventRecorder
{
    public function cobranca(Cobranca $cobranca, string $tipo, array $payload = []): CobrancaEvento
    {
        return CobrancaEvento::create($this->payload('cobranca_id', $cobranca->id, $tipo, $payload));
    }

    public function parcela(Parcela $parcela, string $tipo, array $payload = []): ParcelaEvento
    {
        return ParcelaEvento::create($this->payload('parcela_id', $parcela->id, $tipo, $payload));
    }

    public function boleto(Boleto $boleto, string $tipo, array $payload = []): BoletoEvento
    {
        return BoletoEvento::create($this->payload('boleto_id', $boleto->id, $tipo, $payload));
    }

    private function payload(string $key, string $id, string $tipo, array $payload): array
    {
        $occurredAt = now();
        $base = [
            $key => $id,
            'user_id' => Auth::id(),
            'tipo' => $tipo,
            'payload' => $payload,
            'occurred_at' => $occurredAt,
        ];

        $base['checksum_sha256'] = hash('sha256', json_encode([
            $key => $id,
            'tipo' => $tipo,
            'payload' => $payload,
            'occurred_at' => $occurredAt->toJSON(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $base;
    }
}
