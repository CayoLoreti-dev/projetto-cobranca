<?php

namespace App\Enums;

enum DdaStatus: string
{
    case PendenteVerificacao = 'PENDENTE_VERIFICACAO';
    case Confirmado = 'CONFIRMADO';
    case NaoEncontrado = 'NAO_ENCONTRADO';
}
