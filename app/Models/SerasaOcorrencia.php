<?php

namespace App\Models;

use App\Enums\SerasaEtapa;
use App\Enums\SerasaStatus;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class SerasaOcorrencia extends Model
{
    use UsesUuid;

    protected $fillable = [
        'cobranca_id', 'responsavel_id', 'etapa', 'status', 'protocolo',
        'documento_devedor', 'valor_negativado', 'data_limite_regularizacao',
        'agendado_para', 'executado_em', 'data_baixa', 'motivo_baixa',
        'observacoes', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'etapa' => SerasaEtapa::class,
            'status' => SerasaStatus::class,
            'valor_negativado' => 'decimal:2',
            'data_limite_regularizacao' => 'date',
            'agendado_para' => 'datetime',
            'executado_em' => 'datetime',
            'data_baixa' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function cobranca()
    {
        return $this->belongsTo(Cobranca::class);
    }

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }
}
