<?php

namespace App\Policies;

use App\Models\Parcela;
use App\Models\User;

class ParcelaPolicy
{
    public function viewAny(User $user): bool { return $user->can('parcelas.view'); }
    public function view(User $user, Parcela $parcela): bool { return $user->can('parcelas.view'); }
    public function update(User $user, Parcela $parcela): bool { return $user->can('parcelas.update'); }
    public function baixar(User $user, Parcela $parcela): bool { return $user->can('parcelas.pay'); }
}
