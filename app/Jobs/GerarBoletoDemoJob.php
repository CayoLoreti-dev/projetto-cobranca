<?php

namespace App\Jobs;

use App\Enums\BoletoStatus;
use App\Models\Parcela;
use App\Support\Audit\EventRecorder;
use App\Support\Billing\BillingRules;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GerarBoletoDemoJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $parcelaId)
    {
        //
    }

    public function handle(BillingRules $rules, EventRecorder $events): void
    {
        $parcela = Parcela::query()->with('cobranca')->findOrFail($this->parcelaId);

        $boleto = $parcela->boleto()->firstOrCreate(
            ['parcela_id' => $parcela->id],
            [
                'cobranca_id' => $parcela->cobranca_id,
                'linha_digitavel' => $rules->linhaDigitavelDemo($parcela->numero),
                'codigo_barras' => $rules->codigoBarrasDemo($parcela->numero),
                'valor' => $parcela->valor,
                'vencimento' => $parcela->vencimento,
                'status' => BoletoStatus::Emitido,
                'gerado_em' => now(),
            ],
        );

        $events->boleto($boleto, 'boleto.gerado', ['parcela_id' => $parcela->id]);
    }
}
