<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SerasaEtapa: string implements HasColor, HasLabel
{
    case Notificacao = 'NOTIFICACAO';
    case Inclusao = 'INCLUSAO';
    case NegativacaoFormal = 'NEGATIVACAO_FORMAL';

    public function getLabel(): string
    {
        return match ($this) {
            self::Notificacao => 'Notificação prévia',
            self::Inclusao => 'Inclusão / remessa',
            self::NegativacaoFormal => 'Negativação formal',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Notificacao => 'warning',
            self::Inclusao => 'info',
            self::NegativacaoFormal => 'danger',
        };
    }
}
