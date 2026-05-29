<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreClienteRequest;
use App\Http\Resources\V1\ClienteResource;
use App\Models\Cliente;

class ClienteController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Cliente::class);

        $clientes = Cliente::query()
            ->when(request('busca'), function ($query, $busca) {
                $query->where(function ($subQuery) use ($busca) {
                    $subQuery->where('nome', 'ilike', "%{$busca}%")
                        ->orWhere('documento', 'ilike', "%{$busca}%");
                });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->cursorPaginate(request('per_page', 20));

        return ClienteResource::collection($clientes);
    }

    public function store(StoreClienteRequest $request)
    {
        $cliente = Cliente::create($request->validated() + [
            'created_by_id' => $request->user()->id,
            'updated_by_id' => $request->user()->id,
        ]);

        return ClienteResource::make($cliente)->response()->setStatusCode(201);
    }

    public function show(Cliente $cliente)
    {
        $this->authorize('view', $cliente);

        return ClienteResource::make($cliente);
    }
}
