<?php

namespace App\Models;

use App\Enums\PopChecklistStatus;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class PopFinanceiroChecklist extends Model
{
    use UsesUuid;

    protected $table = 'pop_financeiro_checklists';

    protected $fillable = [
        'reference_date', 'cliente_id', 'cobranca_id', 'parcela_id', 'boleto_id',
        'assigned_to_id', 'etapa', 'status', 'acao_canal', 'escalonamento_nivel',
        'titulo', 'descricao', 'sla_limite_em', 'concluido_em', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'reference_date' => 'date',
            'status' => PopChecklistStatus::class,
            'sla_limite_em' => 'datetime',
            'concluido_em' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function cobranca()
    {
        return $this->belongsTo(Cobranca::class);
    }

    public function parcela()
    {
        return $this->belongsTo(Parcela::class);
    }

    public function boleto()
    {
        return $this->belongsTo(Boleto::class);
    }

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }
}
