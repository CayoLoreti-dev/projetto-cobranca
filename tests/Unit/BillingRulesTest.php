<?php

namespace Tests\Unit;

use App\Enums\CobrancaStatus;
use App\Enums\ParcelaStatus;
use App\Support\Billing\BillingRules;
use Tests\TestCase;

class BillingRulesTest extends TestCase
{
    public function test_resolve_status_de_parcela_por_vencimento_e_pagamento(): void
    {
        $rules = new BillingRules();

        $this->assertSame(ParcelaStatus::Paga, $rules->parcelaStatus(now()->subDays(3), now()));
        $this->assertSame(ParcelaStatus::EmNegativacao, $rules->parcelaStatus(now()->subDays(31)));
        $this->assertSame(ParcelaStatus::EmAtraso, $rules->parcelaStatus(now()->subDays(2)));
        $this->assertSame(ParcelaStatus::Pendente, $rules->parcelaStatus(now()->addDays(2)));
    }

    public function test_resolve_status_de_cobranca_por_atraso(): void
    {
        $rules = new BillingRules();

        $this->assertSame(CobrancaStatus::Paga, $rules->cobrancaStatus(now()->subDays(10), true));
        $this->assertSame(CobrancaStatus::Cobranca30Dias, $rules->cobrancaStatus(now()->subDays(30)));
        $this->assertSame(CobrancaStatus::Cobranca10Dias, $rules->cobrancaStatus(now()->subDays(10)));
        $this->assertSame(CobrancaStatus::Cobranca5Dias, $rules->cobrancaStatus(now()->subDays(5)));
        $this->assertSame(CobrancaStatus::Preventiva, $rules->cobrancaStatus(now()->addDays(5)));
    }
}
