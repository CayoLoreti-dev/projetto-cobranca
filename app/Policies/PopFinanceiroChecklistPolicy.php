<?php

namespace App\Policies;

use App\Models\PopFinanceiroChecklist;
use App\Models\User;

class PopFinanceiroChecklistPolicy
{
    public function viewAny(User $user): bool { return $user->can('pop_financeiro.view'); }
    public function view(User $user, PopFinanceiroChecklist $record): bool { return $user->can('pop_financeiro.view'); }
    public function create(User $user): bool { return $user->can('pop_financeiro.update'); }
    public function update(User $user, PopFinanceiroChecklist $record): bool { return $user->can('pop_financeiro.update'); }
    public function delete(User $user, PopFinanceiroChecklist $record): bool { return $user->can('pop_financeiro.update'); }
}
