<?php

namespace App\Policies;

use App\Models\SerasaOcorrencia;
use App\Models\User;

class SerasaOcorrenciaPolicy
{
    public function viewAny(User $user): bool { return $user->can('serasa.view'); }
    public function view(User $user, SerasaOcorrencia $record): bool { return $user->can('serasa.view'); }
    public function create(User $user): bool { return $user->can('serasa.update'); }
    public function update(User $user, SerasaOcorrencia $record): bool { return $user->can('serasa.update'); }
    public function delete(User $user, SerasaOcorrencia $record): bool { return $user->can('serasa.update'); }
}
