<?php

namespace App\Enums;

enum ClienteTipo: string
{
    case PessoaFisica = 'PF';
    case PessoaJuridica = 'PJ';
    case Condominio = 'CONDOMINIO';
}
