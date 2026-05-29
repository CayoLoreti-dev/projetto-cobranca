<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SerasaStatus: string implements HasColor, HasLabel
{
    case Pendente = 'PENDENTE';
    case Executado = 'EXECUTADO';
    case Cancelado = 'CANCELADO';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pendente => 'Pendente',
            self::Executado => 'Executado',
            self::Cancelado => 'Cancelado',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pendente => 'warning',
            self::Executado => 'success',
            self::Cancelado => 'danger',
        };
    }
}
