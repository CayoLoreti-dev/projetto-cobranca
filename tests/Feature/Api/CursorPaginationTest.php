<?php

namespace Tests\Feature\Api;

use App\Enums\CobrancaStatus;
use App\Enums\CobrancaTipo;
use App\Models\Cliente;
use App\Models\Cobranca;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CursorPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_clientes_index_returns_next_cursor(): void
    {
        Permission::findOrCreate('clientes.view');
        $user = User::factory()->create();
        $user->givePermissionTo('clientes.view');

        Cliente::factory()->create(['nome' => 'Cliente A']);
        Cliente::factory()->create(['nome' => 'Cliente B']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/clientes?per_page=1');

        $response->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta']);

        $this->assertNotNull(data_get($response->json(), 'links.next'));
    }

    public function test_cobrancas_index_returns_next_cursor(): void
    {
        Permission::findOrCreate('cobrancas.view');
        $user = User::factory()->create();
        $user->givePermissionTo('cobrancas.view');

        $cliente = Cliente::factory()->create();

        Cobranca::create([
            'cliente_id' => $cliente->id,
            'codigo' => 'CB-001',
            'tipo' => CobrancaTipo::Avista->value,
            'valor_total' => 1000,
            'status' => CobrancaStatus::Emitida->value,
            'data_emissao' => now()->toDateString(),
            'data_vencimento_principal' => now()->addDays(5)->toDateString(),
        ]);

        Cobranca::create([
            'cliente_id' => $cliente->id,
            'codigo' => 'CB-002',
            'tipo' => CobrancaTipo::Avista->value,
            'valor_total' => 2000,
            'status' => CobrancaStatus::Emitida->value,
            'data_emissao' => now()->toDateString(),
            'data_vencimento_principal' => now()->addDays(10)->toDateString(),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/cobrancas?per_page=1');

        $response->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta']);

        $this->assertNotNull(data_get($response->json(), 'links.next'));
    }
}
