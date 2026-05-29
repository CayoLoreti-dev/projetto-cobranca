<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PopChecklistStatus: string implements HasColor, HasLabel
{
    case Pendente = 'PENDENTE';
    case Concluido = 'CONCLUIDO';
    case Cancelado = 'CANCELADO';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pendente => 'Pendente',
            self::Concluido => 'Concluído',
            self::Cancelado => 'Cancelado',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pendente => 'warning',
            self::Concluido => 'success',
            self::Cancelado => 'danger',
        };
    }
}
