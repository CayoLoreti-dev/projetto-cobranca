<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Cobrancas\CreateCobrancaAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCobrancaRequest;
use App\Http\Resources\V1\CobrancaResource;
use App\Models\Cobranca;

class CobrancaController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Cobranca::class);

        $cobrancas = Cobranca::query()
            ->with('cliente')
            ->when(request('status'), fn ($query, $status) => $query->where('status', $status))
            ->when(request('cliente_id'), fn ($query, $clienteId) => $query->where('cliente_id', $clienteId))
            ->latest()
            ->paginate(request('per_page', 20));

        return CobrancaResource::collection($cobrancas);
    }

    public function store(StoreCobrancaRequest $request, CreateCobrancaAction $action)
    {
        $cobranca = $action->execute($request->validated() + [
            'created_by_id' => $request->user()->id,
            'updated_by_id' => $request->user()->id,
        ], $request->boolean('gerar_boletos', true));

        return CobrancaResource::make($cobranca)->response()->setStatusCode(201);
    }

    public function show(Cobranca $cobranca)
    {
        $this->authorize('view', $cobranca);

        return CobrancaResource::make($cobranca->load('parcelas'));
    }
}
