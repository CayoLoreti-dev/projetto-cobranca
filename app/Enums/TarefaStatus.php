<?php

namespace App\Enums;

enum TarefaStatus: string
{
    case Aberta = 'ABERTA';
    case EmAndamento = 'EM_ANDAMENTO';
    case Concluida = 'CONCLUIDA';
    case Cancelada = 'CANCELADA';
    case Atrasada = 'ATRASADA';
    case Arquivada = 'ARQUIVADA';
}
