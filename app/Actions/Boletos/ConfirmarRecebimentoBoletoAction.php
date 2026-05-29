<?php

namespace App\Actions\Boletos;

use App\Enums\BoletoStatus;
use App\Models\Boleto;
use App\Support\Audit\EventRecorder;

class ConfirmarRecebimentoBoletoAction
{
    public function __construct(private readonly EventRecorder $events)
    {
        //
    }

    public function execute(Boleto $boleto, bool $lido = false): Boleto
    {
        $boleto->update([
            'status' => $lido ? BoletoStatus::Lido : BoletoStatus::Recebido,
            'lido_em' => $lido ? now() : $boleto->lido_em,
            'recebido_em' => $lido ? $boleto->recebido_em : now(),
        ]);

        $this->events->boleto($boleto, $lido ? 'boleto.lido' : 'boleto.recebido');

        return $boleto->refresh();
    }
}
