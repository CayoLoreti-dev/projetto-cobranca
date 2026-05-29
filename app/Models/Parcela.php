<?php

namespace App\Models;

use App\Enums\ParcelaStatus;
use App\Models\Concerns\UsesUuid;
use Database\Factories\ParcelaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parcela extends Model
{
    /** @use HasFactory<ParcelaFactory> */
    use HasFactory;
    use SoftDeletes;
    use UsesUuid;

    protected $fillable = [
        'cobranca_id', 'numero', 'valor', 'vencimento', 'status', 'paga_em',
        'valor_pago', 'forma_pagamento', 'observacoes', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => ParcelaStatus::class,
            'valor' => 'decimal:2',
            'valor_pago' => 'decimal:2',
            'vencimento' => 'date',
            'paga_em' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function cobranca()
    {
        return $this->belongsTo(Cobranca::class);
    }

    public function boleto()
    {
        return $this->hasOne(Boleto::class);
    }

    public function eventos()
    {
        return $this->hasMany(ParcelaEvento::class);
    }

    public function popChecklists()
    {
        return $this->hasMany(PopFinanceiroChecklist::class);
    }
}
