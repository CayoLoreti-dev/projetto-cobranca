<?php

namespace App\Enums;

enum SerasaEtapa: string
{
    case Notificacao = 'NOTIFICACAO';
    case Inclusao = 'INCLUSAO';
    case NegativacaoFormal = 'NEGATIVACAO_FORMAL';
}
