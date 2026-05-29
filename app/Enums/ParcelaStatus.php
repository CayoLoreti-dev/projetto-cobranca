<?php

namespace App\Enums;

enum ParcelaStatus: string
{
    case Pendente = 'PENDENTE';
    case Enviada = 'ENVIADA';
    case EmAtraso = 'EM_ATRASO';
    case EmNegativacao = 'EM_NEGATIVACAO';
    case Paga = 'PAGA';
    case Cancelada = 'CANCELADA';
    case Arquivada = 'ARQUIVADA';
}
