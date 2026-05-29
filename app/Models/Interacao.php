<?php

namespace App\Models;

use App\Enums\InteracaoCanal;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Interacao extends Model
{
    use UsesUuid;

    protected $table = 'interacoes';

    protected $fillable = [
        'cliente_id', 'cobranca_id', 'parcela_id', 'user_id', 'canal', 'resultado',
        'assunto', 'descricao', 'ocorreu_em', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'canal' => InteracaoCanal::class,
            'ocorreu_em' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
