<?php

namespace App\Actions\NotasFiscais;

use App\Models\Boleto;
use App\Models\Cobranca;
use App\Models\NotaFiscal;
use App\Support\Audit\EventRecorder;
use Illuminate\Validation\ValidationException;

class RegistrarNotaFiscalAction
{
    public function __construct(private readonly EventRecorder $events)
    {
        //
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data): NotaFiscal
    {
        $cobranca = Cobranca::query()->findOrFail($data['cobranca_id']);
        $boleto = isset($data['boleto_id']) ? Boleto::query()->findOrFail($data['boleto_id']) : null;

        if ($boleto !== null) {
            if ($boleto->cobranca_id !== $cobranca->id) {
                throw ValidationException::withMessages([
                    'boleto_id' => 'O boleto informado não pertence à cobrança selecionada.',
                ]);
            }

            if (abs((float) $boleto->valor - (float) $data['valor']) > 0.01) {
                throw ValidationException::withMessages([
                    'valor' => 'O valor da nota fiscal deve ser igual ao valor do boleto vinculado.',
                ]);
            }
        }

        $nota = NotaFiscal::create($data);

        $this->events->cobranca($cobranca, 'nota_fiscal.registrada', [
            'nota_fiscal_id' => $nota->id,
            'valor' => $nota->valor,
            'status' => $nota->status?->value,
        ]);

        if ($boleto !== null) {
            $this->events->boleto($boleto, 'boleto.nota_fiscal_vinculada', [
                'nota_fiscal_id' => $nota->id,
                'valor' => $nota->valor,
            ]);
        }

        return $nota->refresh();
    }
}
