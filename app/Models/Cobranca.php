<?php

namespace App\Models;

use App\Enums\CobrancaStatus;
use App\Enums\CobrancaTipo;
use App\Models\Concerns\UsesUuid;
use Database\Factories\CobrancaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Cobranca extends Model
{
    /** @use HasFactory<CobrancaFactory> */
    use HasFactory;
    use SoftDeletes;
    use UsesUuid;

    protected $fillable = [
        'cliente_id', 'codigo', 'categoria', 'tipo', 'valor_total', 'moeda', 'status',
        'data_emissao', 'data_vencimento_principal', 'responsavel_id', 'prioridade',
        'proxima_acao', 'data_proxima_acao', 'observacoes', 'metadata', 'created_by_id',
        'updated_by_id', 'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'tipo' => CobrancaTipo::class,
            'status' => CobrancaStatus::class,
            'valor_total' => 'decimal:2',
            'data_emissao' => 'date',
            'data_vencimento_principal' => 'date',
            'data_proxima_acao' => 'date',
            'metadata' => 'array',
            'archived_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Cobranca $cobranca): void {
            $userId = Auth::id();

            if ($userId !== null) {
                $cobranca->created_by_id ??= $userId;
                $cobranca->updated_by_id ??= $userId;
            }
        });

        static::updating(function (Cobranca $cobranca): void {
            $userId = Auth::id();

            if ($userId !== null) {
                $cobranca->updated_by_id = $userId;
            }
        });
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function parcelas()
    {
        return $this->hasMany(Parcela::class);
    }

    public function boletos()
    {
        return $this->hasMany(Boleto::class);
    }

    public function eventos()
    {
        return $this->hasMany(CobrancaEvento::class);
    }

    public function notasFiscais()
    {
        return $this->hasMany(NotaFiscal::class);
    }

    public function serasaOcorrencias()
    {
        return $this->hasMany(SerasaOcorrencia::class);
    }

    public function popChecklists()
    {
        return $this->hasMany(PopFinanceiroChecklist::class);
    }
}
