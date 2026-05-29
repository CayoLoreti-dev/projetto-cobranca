<?php

namespace App\Enums;

enum TarefaPrioridade: int
{
    case Baixa = 1;
    case Normal = 2;
    case Alta = 3;
    case Critica = 4;
}
