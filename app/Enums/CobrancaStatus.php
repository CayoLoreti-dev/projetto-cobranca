<?php

namespace App\Enums;

enum CobrancaStatus: string
{
    case Rascunho = 'RASCUNHO';
    case Emitida = 'EMITIDA';
    case Enviada = 'ENVIADA';
    case Preventiva = 'PREVENTIVA';
    case Cobranca5Dias = 'COBRANCA_5_DIAS';
    case Cobranca10Dias = 'COBRANCA_10_DIAS';
    case Cobranca30Dias = 'COBRANCA_30_DIAS';
    case Negativacao = 'NEGATIVACAO';
    case Paga = 'PAGA';
    case Cancelada = 'CANCELADA';
    case Arquivada = 'ARQUIVADA';
}
