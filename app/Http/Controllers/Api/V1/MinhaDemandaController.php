<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tarefa;

class MinhaDemandaController extends Controller
{
    public function index()
    {
        $tarefas = Tarefa::query()
            ->where('assigned_to_id', auth()->id())
            ->orderByDesc('prioridade')
            ->orderBy('vence_em')
            ->paginate(request('per_page', 20));

        return response()->json(['data' => $tarefas->items(), 'meta' => ['total' => $tarefas->total()]]);
    }
}
