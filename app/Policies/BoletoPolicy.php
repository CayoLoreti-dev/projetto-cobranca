<?php

namespace App\Policies;

use App\Models\Boleto;
use App\Models\User;

class BoletoPolicy
{
    public function viewAny(User $user): bool { return $user->can('boletos.view'); }
    public function view(User $user, Boleto $boleto): bool { return $user->can('boletos.view'); }
    public function update(User $user, Boleto $boleto): bool { return $user->can('boletos.update'); }
    public function upload(User $user, Boleto $boleto): bool { return $user->can('boletos.upload'); }
}
