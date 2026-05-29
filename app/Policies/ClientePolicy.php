<?php

namespace App\Policies;

use App\Models\Cliente;
use App\Models\User;

class ClientePolicy
{
    public function viewAny(User $user): bool { return $user->can('clientes.view'); }
    public function view(User $user, Cliente $cliente): bool { return $user->can('clientes.view'); }
    public function create(User $user): bool { return $user->can('clientes.create'); }
    public function update(User $user, Cliente $cliente): bool { return $user->can('clientes.update'); }
    public function delete(User $user, Cliente $cliente): bool { return $user->can('clientes.archive'); }
    public function restore(User $user, Cliente $cliente): bool { return $user->can('clientes.archive'); }
}
