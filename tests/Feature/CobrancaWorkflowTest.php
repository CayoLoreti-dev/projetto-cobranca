<?php

namespace Tests\Feature;

use App\Actions\Cobrancas\CreateCobrancaAction;
use App\Actions\Parcelas\BaixarParcelaAction;
use App\Enums\CobrancaStatus;
use App\Enums\CobrancaTipo;
use App\Enums\ParcelaStatus;
use App\Jobs\GerarBoletoDemoJob;
use App\Models\Cliente;
use App\Models\CobrancaEvento;
use App\Models\Parcela;
use App\Models\ParcelaEvento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CobrancaWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_cria_cobranca_com_parcelas_eventos_auditoria_e_job_de_boleto(): void
    {
        Queue::fake();
        $user = User::factory()->create();
        $cliente = Cliente::factory()->create();

        $this->actingAs($user);

        $cobranca = app(CreateCobrancaAction::class)->execute([
            'cliente_id' => $cliente->id,
            'tipo' => CobrancaTipo::Avista->value,
            'valor_total' => 500,
            'status' => CobrancaStatus::Emitida->value,
            'data_emissao' => now()->toDateString(),
            'data_vencimento_principal' => now()->addDays(10)->toDateString(),
            'parcelas' => [
                ['numero' => 1, 'valor' => 500, 'vencimento' => now()->addDays(10)->toDateString(), 'status' => ParcelaStatus::Pendente->value],
            ],
        ]);

        $this->assertDatabaseHas('cobrancas', ['id' => $cobranca->id]);
        $this->assertDatabaseHas('parcelas', ['cobranca_id' => $cobranca->id, 'numero' => 1]);
        $this->assertDatabaseHas('cobranca_eventos', ['cobranca_id' => $cobranca->id, 'tipo' => 'cobranca.criada']);
        $this->assertDatabaseHas('audit_logs', ['auditable_type' => $cobranca::class, 'auditable_id' => $cobranca->id, 'action' => 'created']);

        Queue::assertPushed(GerarBoletoDemoJob::class);
    }

    public function test_baixa_parcela_e_fecha_cobranca_quando_todas_estao_pagas(): void
    {
        $cliente = Cliente::factory()->create();
        $cobranca = app(CreateCobrancaAction::class)->execute([
            'cliente_id' => $cliente->id,
            'tipo' => CobrancaTipo::Avista->value,
            'valor_total' => 700,
            'status' => CobrancaStatus::Emitida->value,
            'data_emissao' => now()->toDateString(),
            'data_vencimento_principal' => now()->addDays(10)->toDateString(),
            'parcelas' => [
                ['numero' => 1, 'valor' => 700, 'vencimento' => now()->addDays(10)->toDateString(), 'status' => ParcelaStatus::Pendente->value],
            ],
        ], gerarBoletos: false);

        $parcela = Parcela::where('cobranca_id', $cobranca->id)->firstOrFail();

        app(BaixarParcelaAction::class)->execute($parcela, ['valor_pago' => 700, 'forma_pagamento' => 'PIX']);

        $this->assertDatabaseHas('parcelas', ['id' => $parcela->id, 'status' => ParcelaStatus::Paga->value]);
        $this->assertDatabaseHas('cobrancas', ['id' => $cobranca->id, 'status' => CobrancaStatus::Paga->value]);
        $this->assertDatabaseHas('parcela_eventos', ['parcela_id' => $parcela->id, 'tipo' => 'parcela.baixada']);
        $this->assertDatabaseHas('cobranca_eventos', ['cobranca_id' => $cobranca->id, 'tipo' => 'cobranca.paga']);

        $this->assertSame(1, CobrancaEvento::where('cobranca_id', $cobranca->id)->where('tipo', 'cobranca.paga')->count());
        $this->assertSame(1, ParcelaEvento::where('parcela_id', $parcela->id)->where('tipo', 'parcela.baixada')->count());
    }
}
