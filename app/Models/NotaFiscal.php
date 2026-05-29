<?php

namespace App\Models;

use App\Enums\NotaFiscalStatus;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotaFiscal extends Model
{
    use SoftDeletes;
    use UsesUuid;

    protected $table = 'notas_fiscais';

    protected $fillable = [
        'cobranca_id', 'boleto_id', 'numero', 'serie', 'status', 'valor',
        'emitida_em', 'competencia', 'observacoes', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => NotaFiscalStatus::class,
            'valor' => 'decimal:2',
            'emitida_em' => 'datetime',
            'competencia' => 'date',
            'metadata' => 'array',
        ];
    }

    public function cobranca()
    {
        return $this->belongsTo(Cobranca::class);
    }

    public function boleto()
    {
        return $this->belongsTo(Boleto::class);
    }
}
