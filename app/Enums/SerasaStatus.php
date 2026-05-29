<?php

namespace App\Enums;

enum SerasaStatus: string
{
    case Pendente = 'PENDENTE';
    case Executado = 'EXECUTADO';
    case Cancelado = 'CANCELADO';
}
