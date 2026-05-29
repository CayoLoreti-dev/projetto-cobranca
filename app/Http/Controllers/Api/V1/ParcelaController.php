<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Parcelas\BaixarParcelaAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BaixarParcelaRequest;
use App\Http\Resources\V1\ParcelaResource;
use App\Models\Parcela;

class ParcelaController extends Controller
{
    public function baixar(BaixarParcelaRequest $request, Parcela $parcela, BaixarParcelaAction $action)
    {
        $this->authorize('baixar', $parcela);

        return ParcelaResource::make($action->execute($parcela, $request->validated()));
    }
}
