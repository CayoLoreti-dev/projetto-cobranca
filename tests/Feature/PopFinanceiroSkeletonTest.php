<?php

namespace Tests\Feature;

use App\Actions\NotasFiscais\RegistrarNotaFiscalAction;
use App\Enums\BoletoStatus;
use App\Enums\CobrancaStatus;
use App\Enums\CobrancaTipo;
use App\Enums\ParcelaStatus;
use App\Models\Boleto;
use App\Models\Cliente;
use App\Models\Cobranca;
use App\Models\Parcela;
use App\Support\Billing\PopFinanceiroService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PopFinanceiroSkeletonTest extends TestCase
{
    use RefreshDatabase;

    public function test_registra_nota_fiscal_com_regra_de_valor_igual_ao_boleto(): void
    {
        $cliente = Cliente::factory()->create();
        $cobranca = Cobranca::factory()->create([
            'cliente_id' => $cliente->id,
            'tipo' => CobrancaTipo::Avista->value,
            'status' => CobrancaStatus::Emitida->value,
            'valor_total' => 100,
        ]);

        $parcela = Parcela::factory()->create([
            'cobranca_id' => $cobranca->id,
            'numero' => 1,
            'valor' => 100,
            'status' => ParcelaStatus::Pendente->value,
            'vencimento' => now()->addDays(5)->toDateString(),
        ]);

        $boleto = Boleto::create([
            'parcela_id' => $parcela->id,
            'cobranca_id' => $cobranca->id,
            'valor' => 100,
            'vencimento' => $parcela->vencimento,
            'status' => BoletoStatus::Emitido->value,
        ]);

        $this->expectException(ValidationException::class);

        app(RegistrarNotaFiscalAction::class)->execute([
            'cobranca_id' => $cobranca->id,
            'boleto_id' => $boleto->id,
            'valor' => 99,
            'status' => 'PENDENTE_EMISSAO',
        ]);
    }

    public function test_pop_financeiro_service_cria_checklist_serasa_e_controle_dda_em_modo_skeleton(): void
    {
        $cliente = Cliente::factory()->create();
        $cobranca = Cobranca::factory()->create([
            'cliente_id' => $cliente->id,
            'tipo' => CobrancaTipo::Avista->value,
            'status' => CobrancaStatus::Emitida->value,
            'valor_total' => 100,
        ]);

        $parcela = Parcela::factory()->create([
            'cobranca_id' => $cobranca->id,
            'numero' => 1,
            'valor' => 100,
            'status' => ParcelaStatus::EmAtraso->value,
            'vencimento' => now()->subDays(12)->toDateString(),
        ]);

        $boleto = Boleto::create([
            'parcela_id' => $parcela->id,
            'cobranca_id' => $cobranca->id,
            'valor' => 100,
            'vencimento' => $parcela->vencimento,
            'status' => BoletoStatus::Emitido->value,
        ]);

        $summary = app(PopFinanceiroService::class)->runDaily(now());

        $this->assertGreaterThan(0, $summary['parcelas_processadas']);
        $this->assertDatabaseHas('boleto_dda_controles', [
            'boleto_id' => $boleto->id,
            'status' => 'PENDENTE_VERIFICACAO',
        ]);
        $this->assertDatabaseHas('pop_financeiro_checklists', [
            'parcela_id' => $parcela->id,
            'etapa' => 'COBRANCA_10_DIAS',
            'status' => 'PENDENTE',
        ]);
        $this->assertDatabaseHas('serasa_ocorrencias', [
            'cobranca_id' => $cobranca->id,
            'etapa' => 'NOTIFICACAO',
            'status' => 'PENDENTE',
        ]);
    }
}
