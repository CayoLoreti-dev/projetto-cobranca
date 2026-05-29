<?php

namespace Database\Factories;

use App\Enums\ClienteStatus;
use App\Enums\ClienteTipo;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Cliente> */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'tipo' => fake()->randomElement(ClienteTipo::cases())->value,
            'documento' => fake()->unique()->numerify('###########'),
            'responsavel_financeiro' => fake()->name(),
            'email' => fake()->safeEmail(),
            'telefone' => fake()->phoneNumber(),
            'whatsapp' => fake()->phoneNumber(),
            'status' => ClienteStatus::Ativo->value,
        ];
    }
}
