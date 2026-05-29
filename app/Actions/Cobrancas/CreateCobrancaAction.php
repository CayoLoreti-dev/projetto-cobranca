<?php

namespace App\Actions\Cobrancas;

use App\Enums\CobrancaTipo;
use App\Jobs\GerarBoletoDemoJob;
use App\Models\Cobranca;
use App\Support\Audit\EventRecorder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateCobrancaAction
{
    public function __construct(private readonly EventRecorder $events)
    {
        //
    }

    public function execute(array $data, bool $gerarBoletos = true): Cobranca
    {
        $parcelas = $data['parcelas'] ?? [];
        unset($data['parcelas']);

        $this->validateParcelas($data['tipo'], (float) $data['valor_total'], $parcelas);

        return DB::transaction(function () use ($data, $parcelas, $gerarBoletos) {
            $data['codigo'] ??= 'COB-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
            $cobranca = Cobranca::create($data);

            $this->events->cobranca($cobranca, 'cobranca.criada', [
                'valor_total' => $cobranca->valor_total,
                'parcelas' => count($parcelas),
            ]);

            foreach ($parcelas as $parcelaData) {
                $parcela = $cobranca->parcelas()->create($parcelaData);
                $this->events->parcela($parcela, 'parcela.criada', [
                    'numero' => $parcela->numero,
                    'valor' => $parcela->valor,
                    'vencimento' => $parcela->vencimento?->toDateString(),
                ]);

                if ($gerarBoletos) {
                    GerarBoletoDemoJob::dispatch($parcela->id)->afterCommit();
                }
            }

            return $cobranca->load('cliente', 'parcelas');
        });
    }

    private function validateParcelas(string|CobrancaTipo $tipo, float $valorTotal, array $parcelas): void
    {
        $tipo = $tipo instanceof CobrancaTipo ? $tipo->value : $tipo;

        if ($tipo === CobrancaTipo::Avista->value && count($parcelas) !== 1) {
            throw ValidationException::withMessages(['parcelas' => 'Cobranca a vista deve ter exatamente uma parcela.']);
        }

        if ($tipo === CobrancaTipo::Parcelado->value && count($parcelas) < 2) {
            throw ValidationException::withMessages(['parcelas' => 'Cobranca parcelada deve nascer com todas as parcelas.']);
        }

        $soma = array_sum(array_map(fn (array $parcela) => (float) $parcela['valor'], $parcelas));

        if (abs($soma - $valorTotal) > 0.01) {
            throw ValidationException::withMessages(['valor_total' => 'A soma das parcelas precisa bater com o valor total.']);
        }
    }
}
