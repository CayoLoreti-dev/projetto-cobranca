<?php

namespace App\Policies;

use App\Models\Cobranca;
use App\Models\User;

class CobrancaPolicy
{
    public function viewAny(User $user): bool { return $user->can('cobrancas.view'); }
    public function view(User $user, Cobranca $cobranca): bool { return $user->can('cobrancas.view'); }
    public function create(User $user): bool { return $user->can('cobrancas.create'); }
    public function update(User $user, Cobranca $cobranca): bool { return $user->can('cobrancas.update'); }
    public function delete(User $user, Cobranca $cobranca): bool { return $user->can('cobrancas.archive'); }
}
