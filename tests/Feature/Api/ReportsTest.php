<?php

namespace Tests\Feature\Api;

use App\Enums\CobrancaStatus;
use App\Enums\CobrancaTipo;
use App\Enums\ParcelaStatus;
use App\Models\Cliente;
use App\Models\Cobranca;
use App\Models\Parcela;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_bloqueia_relatorio_sem_permissao(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/relatorios/resumo')
            ->assertForbidden();
    }

    public function test_resumo_de_relatorio_calcula_valores_centrais(): void
    {
        Permission::findOrCreate('relatorios.view');

        $user = User::factory()->create();
        $user->givePermissionTo('relatorios.view');

        $cliente = Cliente::factory()->create();
        $cobranca = Cobranca::factory()->create([
            'cliente_id' => $cliente->id,
            'responsavel_id' => $user->id,
            'tipo' => CobrancaTipo::Parcelado->value,
            'status' => CobrancaStatus::Cobranca30Dias->value,
            'prioridade' => 4,
            'valor_total' => 1000,
        ]);

        Parcela::factory()->create([
            'cobranca_id' => $cobranca->id,
            'numero' => 1,
            'valor' => 400,
            'vencimento' => now()->subDays(10)->toDateString(),
            'status' => ParcelaStatus::EmAtraso->value,
        ]);

        Parcela::factory()->create([
            'cobranca_id' => $cobranca->id,
            'numero' => 2,
            'valor' => 600,
            'valor_pago' => 600,
            'vencimento' => now()->subDay()->toDateString(),
            'paga_em' => now(),
            'status' => ParcelaStatus::Paga->value,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/relatorios/resumo');

        $response->assertOk();
        $this->assertEquals(1, $response->json('data.cobrancas.total'));
        $this->assertEquals(1, $response->json('data.cobrancas.casos_criticos'));
        $this->assertEquals(400.0, $response->json('data.valores.em_aberto'));
        $this->assertEquals(400.0, $response->json('data.valores.em_atraso'));
        $this->assertEquals(600.0, $response->json('data.valores.pago'));
    }
}
