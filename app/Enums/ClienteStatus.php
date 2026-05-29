<?php

namespace App\Enums;

enum ClienteStatus: string
{
    case Ativo = 'ATIVO';
    case Inativo = 'INATIVO';
    case Arquivado = 'ARQUIVADO';
}
