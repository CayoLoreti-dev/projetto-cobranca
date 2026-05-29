<?php

namespace App\Enums;

enum InteracaoCanal: string
{
    case Email = 'EMAIL';
    case WhatsApp = 'WHATSAPP';
    case Ligacao = 'LIGACAO';
    case Sistema = 'SISTEMA';
    case ObservacaoInterna = 'OBSERVACAO_INTERNA';
}
