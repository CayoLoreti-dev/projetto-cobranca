<?php

namespace App\Enums;

enum NotaFiscalStatus: string
{
    case PendenteEmissao = 'PENDENTE_EMISSAO';
    case Emitida = 'EMITIDA';
    case Cancelada = 'CANCELADA';
}
