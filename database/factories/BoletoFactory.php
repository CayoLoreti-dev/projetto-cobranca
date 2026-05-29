<?php

namespace Database\Factories;

use App\Enums\BoletoStatus;
use App\Models\Boleto;
use App\Models\Parcela;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Boleto> */
class BoletoFactory extends Factory
{
    protected $model = Boleto::class;

    public function definition(): array
    {
        $parcela = Parcela::factory()->create();

        return [
            'parcela_id' => $parcela->id,
            'cobranca_id' => $parcela->cobranca_id,
            'linha_digitavel' => fake()->numerify('#####.##### #####.###### #####.###### # ##############'),
            'codigo_barras' => fake()->numerify(str_repeat('#', 44)),
            'valor' => $parcela->valor,
            'vencimento' => $parcela->vencimento,
            'status' => BoletoStatus::Emitido->value,
        ];
    }
}
