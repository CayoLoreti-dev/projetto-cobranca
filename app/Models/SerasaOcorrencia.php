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
        'cobranca_id', 'responsavel_id', 'etapa', 'status', 'agendado_para',
        'executado_em', 'observacoes', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'etapa' => SerasaEtapa::class,
            'status' => SerasaStatus::class,
            'agendado_para' => 'datetime',
            'executado_em' => 'datetime',
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
