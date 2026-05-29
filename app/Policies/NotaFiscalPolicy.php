<?php

namespace App\Policies;

use App\Models\NotaFiscal;
use App\Models\User;

class NotaFiscalPolicy
{
    public function viewAny(User $user): bool { return $user->can('notas_fiscais.view'); }
    public function view(User $user, NotaFiscal $record): bool { return $user->can('notas_fiscais.view'); }
    public function create(User $user): bool { return $user->can('notas_fiscais.create'); }
    public function update(User $user, NotaFiscal $record): bool { return $user->can('notas_fiscais.update'); }
    public function delete(User $user, NotaFiscal $record): bool { return $user->can('notas_fiscais.update'); }
}
