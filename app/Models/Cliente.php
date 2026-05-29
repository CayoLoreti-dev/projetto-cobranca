<?php

namespace App\Models;

use App\Enums\ClienteStatus;
use App\Enums\ClienteTipo;
use App\Models\Concerns\UsesUuid;
use Database\Factories\ClienteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Cliente extends Model
{
    /** @use HasFactory<ClienteFactory> */
    use HasFactory;
    use SoftDeletes;
    use UsesUuid;

    protected $fillable = [
        'nome', 'tipo', 'documento', 'responsavel_financeiro', 'email', 'telefone',
        'whatsapp', 'endereco', 'status', 'observacoes', 'created_by_id', 'updated_by_id',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'tipo' => ClienteTipo::class,
            'status' => ClienteStatus::class,
            'archived_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Cliente $cliente): void {
            $userId = Auth::id();

            if ($userId !== null) {
                $cliente->created_by_id ??= $userId;
                $cliente->updated_by_id ??= $userId;
            }
        });

        static::updating(function (Cliente $cliente): void {
            $userId = Auth::id();

            if ($userId !== null) {
                $cliente->updated_by_id = $userId;
            }
        });
    }

    public function cobrancas()
    {
        return $this->hasMany(Cobranca::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function tarefas()
    {
        return $this->hasMany(Tarefa::class);
    }

    public function interacoes()
    {
        return $this->hasMany(Interacao::class);
    }

    public function popChecklists()
    {
        return $this->hasMany(PopFinanceiroChecklist::class);
    }
}
