<?php

namespace App\Enums;

enum PopChecklistStatus: string
{
    case Pendente = 'PENDENTE';
    case Concluido = 'CONCLUIDO';
    case Cancelado = 'CANCELADO';
}
