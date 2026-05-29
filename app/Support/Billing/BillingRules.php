<?php

namespace App\Support\Billing;

use App\Enums\CobrancaStatus;
use App\Enums\ParcelaStatus;
use Carbon\CarbonInterface;

class BillingRules
{
    public function parcelaStatus(CarbonInterface $vencimento, ?CarbonInterface $pagaEm = null): ParcelaStatus
    {
        if ($pagaEm !== null) {
            return ParcelaStatus::Paga;
        }

        $diasAtraso = $vencimento->copy()->startOfDay()->diffInDays(now()->startOfDay(), false);

        return match (true) {
            $diasAtraso >= 30 => ParcelaStatus::EmNegativacao,
            $diasAtraso > 0 => ParcelaStatus::EmAtraso,
            default => ParcelaStatus::Pendente,
        };
    }

    public function cobrancaStatus(CarbonInterface $vencimento, bool $paga = false): CobrancaStatus
    {
        if ($paga) {
            return CobrancaStatus::Paga;
        }

        $diasAtraso = $vencimento->copy()->startOfDay()->diffInDays(now()->startOfDay(), false);
        $diasAteVencer = now()->startOfDay()->diffInDays($vencimento->copy()->startOfDay(), false);

        return match (true) {
            $diasAtraso >= 30 => CobrancaStatus::Cobranca30Dias,
            $diasAtraso >= 10 => CobrancaStatus::Cobranca10Dias,
            $diasAtraso >= 5 => CobrancaStatus::Cobranca5Dias,
            $diasAteVencer <= 5 => CobrancaStatus::Preventiva,
            default => CobrancaStatus::Enviada,
        };
    }

    public function linhaDigitavelDemo(int $numero): string
    {
        $base = str_pad((string) $numero, 3, '0', STR_PAD_LEFT);

        return "00190.00009 01234.{$base}009 56789.000001 1 00000000000000";
    }

    public function codigoBarrasDemo(int $numero): string
    {
        return '0019100000000000000001234'.str_pad((string) $numero, 12, '0', STR_PAD_LEFT);
    }
}
