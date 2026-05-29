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
            ->orderByDesc('id')
            ->cursorPaginate(request('per_page', 20));

        return response()->json([
            'data' => $tarefas->items(),
            'meta' => [
                'next_cursor' => optional($tarefas->nextCursor())->encode(),
                'prev_cursor' => optional($tarefas->previousCursor())->encode(),
            ],
        ]);
    }
}
