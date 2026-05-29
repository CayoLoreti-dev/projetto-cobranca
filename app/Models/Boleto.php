<?php

namespace App\Models;

use App\Enums\BoletoStatus;
use App\Models\Concerns\UsesUuid;
use Database\Factories\BoletoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Boleto extends Model
{
    /** @use HasFactory<BoletoFactory> */
    use HasFactory;

    use SoftDeletes;
    use UsesUuid;

    protected $fillable = [
        'parcela_id', 'cobranca_id', 'pdf_file_id', 'linha_digitavel', 'codigo_barras',
        'valor', 'vencimento', 'status', 'gerado_em', 'enviado_em', 'lido_em',
        'recebido_em', 'pago_em', 'pdf_url', 'pdf_path', 'pdf_original_name',
        'observacoes', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => BoletoStatus::class,
            'valor' => 'decimal:2',
            'vencimento' => 'date',
            'gerado_em' => 'datetime',
            'enviado_em' => 'datetime',
            'lido_em' => 'datetime',
            'recebido_em' => 'datetime',
            'pago_em' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function parcela()
    {
        return $this->belongsTo(Parcela::class);
    }

    public function cobranca()
    {
        return $this->belongsTo(Cobranca::class);
    }

    public function arquivoPdf()
    {
        return $this->belongsTo(Arquivo::class, 'pdf_file_id');
    }

    public function eventos()
    {
        return $this->hasMany(BoletoEvento::class);
    }

    public function ddaControle()
    {
        return $this->hasOne(BoletoDdaControle::class, 'boleto_id');
    }

    public function notaFiscal()
    {
        return $this->hasOne(NotaFiscal::class, 'boleto_id');
    }
}
