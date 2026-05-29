<?php

namespace App\Models;

use App\Enums\DdaStatus;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class BoletoDdaControle extends Model
{
    use UsesUuid;

    protected $table = 'boleto_dda_controles';

    protected $fillable = [
        'boleto_id', 'status', 'apareceu_no_dda', 'verificado_em', 'ultimo_retorno', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => DdaStatus::class,
            'apareceu_no_dda' => 'boolean',
            'verificado_em' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function boleto()
    {
        return $this->belongsTo(Boleto::class);
    }
}
