<?php

namespace Database\Factories;

use App\Enums\ParcelaStatus;
use App\Models\Cobranca;
use App\Models\Parcela;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Parcela> */
class ParcelaFactory extends Factory
{
    protected $model = Parcela::class;

    public function definition(): array
    {
        return [
            'cobranca_id' => Cobranca::factory(),
            'numero' => 1,
            'valor' => fake()->randomFloat(2, 300, 20000),
            'vencimento' => now()->addDays(10)->toDateString(),
            'status' => ParcelaStatus::Pendente->value,
        ];
    }
}
