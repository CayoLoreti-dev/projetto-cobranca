<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class BoletoEvento extends Model
{
    use UsesUuid;

    public $timestamps = false;

    protected $fillable = ['boleto_id', 'user_id', 'tipo', 'payload', 'checksum_sha256', 'occurred_at'];

    protected function casts(): array
    {
        return ['payload' => 'array', 'occurred_at' => 'datetime'];
    }
}
