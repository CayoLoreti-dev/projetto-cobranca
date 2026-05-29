<?php

namespace Database\Factories;

use App\Enums\CobrancaStatus;
use App\Enums\CobrancaTipo;
use App\Models\Cliente;
use App\Models\Cobranca;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Cobranca> */
class CobrancaFactory extends Factory
{
    protected $model = Cobranca::class;

    public function definition(): array
    {
        $emissao = now()->subDays(fake()->numberBetween(1, 20));

        return [
            'cliente_id' => Cliente::factory(),
            'codigo' => 'COB-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
            'categoria' => 'vistoria',
            'tipo' => CobrancaTipo::Avista->value,
            'valor_total' => fake()->randomFloat(2, 300, 20000),
            'status' => CobrancaStatus::Emitida->value,
            'data_emissao' => $emissao->toDateString(),
            'data_vencimento_principal' => $emissao->copy()->addDays(15)->toDateString(),
        ];
    }
}
