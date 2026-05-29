<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CobrancaValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejects_non_uuid_cliente_id()
    {
        $user = User::factory()->create();
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'cobrancas.create']);
        $user->givePermissionTo('cobrancas.create');
        $this->actingAs($user);

        $payload = [
            'cliente_id' => 'not-a-uuid',
            'tipo' => 'AVISTA',
            'valor_total' => 100,
            'data_emissao' => now()->toDateString(),
            'data_vencimento_principal' => now()->addDays(10)->toDateString(),
            'parcelas' => [
                ['numero' => 1, 'valor' => 100, 'vencimento' => now()->addDays(10)->toDateString(), 'status' => 'PENDENTE'],
            ],
        ];

        $response = $this->postJson('/api/v1/cobrancas', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['cliente_id']);
    }

    public function test_rejects_too_long_observacoes()
    {
        $user = User::factory()->create();
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'cobrancas.create']);
        $user->givePermissionTo('cobrancas.create');
        $cliente = Cliente::factory()->create();

        $this->actingAs($user);

        $payload = [
            'cliente_id' => $cliente->id,
            'tipo' => 'AVISTA',
            'valor_total' => 100,
            'data_emissao' => now()->toDateString(),
            'data_vencimento_principal' => now()->addDays(10)->toDateString(),
            'observacoes' => str_repeat('a', 2500),
            'parcelas' => [
                ['numero' => 1, 'valor' => 100, 'vencimento' => now()->addDays(10)->toDateString(), 'status' => 'PENDENTE'],
            ],
        ];

        $response = $this->postJson('/api/v1/cobrancas', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['observacoes']);
    }
}
