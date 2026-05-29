<?php

namespace Tests\Feature\Api;

use App\Enums\CobrancaStatus;
use App\Enums\CobrancaTipo;
use App\Enums\ParcelaStatus;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ResourceCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_can_create_cliente(): void
    {
        Permission::findOrCreate('clientes.create');
        $user = User::factory()->create();
        $user->givePermissionTo('clientes.create');

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/clientes', [
                'nome' => 'Cliente Teste',
                'tipo' => 'PF',
                'documento' => '12345678901',
                'responsavel_financeiro' => 'Financeiro',
                'email' => 'teste@example.com',
                'telefone' => '+5511999999999',
            ])
            ->assertCreated()
            ->assertJsonPath('data.nome', 'Cliente Teste');

        $this->assertDatabaseHas('clientes', ['nome' => 'Cliente Teste']);
    }

    public function test_api_can_create_cobranca(): void
    {
        Permission::findOrCreate('cobrancas.create');
        $user = User::factory()->create();
        $user->givePermissionTo('cobrancas.create');

        $cliente = Cliente::factory()->create();

        $payload = [
            'cliente_id' => $cliente->id,
            'tipo' => CobrancaTipo::Avista->value,
            'valor_total' => 1500,
            'status' => CobrancaStatus::Emitida->value,
            'data_emissao' => now()->toDateString(),
            'data_vencimento_principal' => now()->addDays(7)->toDateString(),
            'parcelas' => [
                ['numero' => 1, 'valor' => 1500, 'vencimento' => now()->addDays(7)->toDateString(), 'status' => ParcelaStatus::Pendente->value],
            ],
        ];

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cobrancas', $payload)
            ->assertCreated()
            ->assertJsonPath('data.cliente_id', $cliente->id);

        $this->assertDatabaseHas('cobrancas', ['cliente_id' => $cliente->id]);
        $this->assertDatabaseHas('parcelas', ['numero' => 1]);
    }
}
