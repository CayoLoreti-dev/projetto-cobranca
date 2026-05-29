<?php

namespace App\Policies;

use App\Models\Tarefa;
use App\Models\User;

class TarefaPolicy
{
    public function viewAny(User $user): bool { return $user->can('tarefas.view'); }
    public function view(User $user, Tarefa $tarefa): bool
    {
        return $user->can('tarefas.view') || $tarefa->assigned_to_id === $user->id;
    }
    public function update(User $user, Tarefa $tarefa): bool
    {
        return $user->can('tarefas.update') || $tarefa->assigned_to_id === $user->id;
    }
}
