<?php

namespace App\Policies;

use App\Models\BoletoDdaControle;
use App\Models\User;

class BoletoDdaControlePolicy
{
    public function viewAny(User $user): bool { return $user->can('dda.view'); }
    public function view(User $user, BoletoDdaControle $record): bool { return $user->can('dda.view'); }
    public function create(User $user): bool { return $user->can('dda.update'); }
    public function update(User $user, BoletoDdaControle $record): bool { return $user->can('dda.update'); }
    public function delete(User $user, BoletoDdaControle $record): bool { return $user->can('dda.update'); }
}
