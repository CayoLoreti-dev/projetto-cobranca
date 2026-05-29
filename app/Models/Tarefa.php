<?php

namespace App\Models;

use App\Enums\TarefaStatus;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tarefa extends Model
{
    use SoftDeletes;
    use UsesUuid;

    protected $fillable = [
        'cliente_id', 'cobranca_id', 'assigned_to_id', 'tipo', 'titulo', 'descricao',
        'prioridade', 'status', 'vence_em', 'iniciada_em', 'concluida_em', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => TarefaStatus::class,
            'vence_em' => 'datetime',
            'iniciada_em' => 'datetime',
            'concluida_em' => 'datetime',
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

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }
}
