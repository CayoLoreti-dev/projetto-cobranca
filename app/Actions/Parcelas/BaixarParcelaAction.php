<?php

namespace App\Actions\Parcelas;

use App\Enums\CobrancaStatus;
use App\Enums\ParcelaStatus;
use App\Models\Parcela;
use App\Support\Audit\EventRecorder;
use Illuminate\Support\Facades\DB;

class BaixarParcelaAction
{
    public function __construct(private readonly EventRecorder $events)
    {
        //
    }

    public function execute(Parcela $parcela, array $data = []): Parcela
    {
        return DB::transaction(function () use ($parcela, $data) {
            $parcela->update([
                'status' => ParcelaStatus::Paga,
                'paga_em' => $data['paga_em'] ?? now(),
                'valor_pago' => $data['valor_pago'] ?? $parcela->valor,
                'forma_pagamento' => $data['forma_pagamento'] ?? null,
                'metadata' => array_merge($parcela->metadata ?? [], $data['metadata'] ?? []),
            ]);

            $this->events->parcela($parcela, 'parcela.baixada', [
                'valor_pago' => $parcela->valor_pago,
                'forma_pagamento' => $parcela->forma_pagamento,
            ]);

            $cobranca = $parcela->cobranca()->with('parcelas')->first();

            if ($cobranca && $cobranca->parcelas->every(fn (Parcela $item) => $item->status === ParcelaStatus::Paga)) {
                $cobranca->update(['status' => CobrancaStatus::Paga]);
                $this->events->cobranca($cobranca, 'cobranca.paga', ['motivo' => 'todas_parcelas_pagas']);
            }

            return $parcela->refresh();
        });
    }
}
