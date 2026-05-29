<?php

namespace App\Enums;

enum BoletoStatus: string
{
    case Emitido = 'EMITIDO';
    case Enviado = 'ENVIADO';
    case Lido = 'LIDO';
    case Recebido = 'RECEBIDO';
    case Pago = 'PAGO';
    case Vencido = 'VENCIDO';
    case Cancelado = 'CANCELADO';
    case Arquivado = 'ARQUIVADO';
}
