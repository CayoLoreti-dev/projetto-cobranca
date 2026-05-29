<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CobrancaStatus;
use App\Enums\ParcelaStatus;
use App\Http\Controllers\Controller;
use App\Support\Reports\BillingReportService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RelatorioController extends Controller
{
    public function __construct(private readonly BillingReportService $reports)
    {
        //
    }

    public function resumo(Request $request)
    {
        $this->authorizeReport($request);

        return response()->json(['data' => $this->reports->resumo($this->filters($request))]);
    }

    public function inadimplencia(Request $request)
    {
        $this->authorizeReport($request);

        return response()->json($this->reports->inadimplencia($this->filters($request)));
    }

    public function previsaoRecebimento(Request $request)
    {
        $this->authorizeReport($request);

        return response()->json(['data' => $this->reports->previsaoRecebimento($this->filters($request))]);
    }

    public function produtividade(Request $request)
    {
        $this->authorizeReport($request);

        return response()->json(['data' => $this->reports->produtividade($this->filters($request))]);
    }

    public function evolucaoTemporal(Request $request)
    {
        $this->authorizeReport($request);

        return response()->json(['data' => $this->reports->evolucaoTemporal($this->filters($request))]);
    }

    private function authorizeReport(Request $request): void
    {
        abort_unless($request->user()?->can('relatorios.view'), 403);
    }

    /**
     * @return array<string, mixed>
     */
    private function filters(Request $request): array
    {
        return $request->validate([
            'de' => ['sometimes', 'date'],
            'ate' => ['sometimes', 'date', 'after_or_equal:de'],
            'cliente_id' => ['sometimes', 'uuid', 'exists:clientes,id'],
            'usuario_id' => ['sometimes', 'integer', 'exists:users,id'],
            'categoria' => ['sometimes', 'string', 'max:100'],
            'parcela_status' => ['sometimes', Rule::in(array_column(ParcelaStatus::cases(), 'value'))],
            'cobranca_status' => ['sometimes', Rule::in(array_column(CobrancaStatus::cases(), 'value'))],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);
    }
}
