<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use UsesUuid;

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'auditable_type', 'auditable_id', 'action', 'before', 'after',
        'reason', 'ip_address', 'user_agent', 'origin', 'checksum_sha256', 'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'before' => 'array',
            'after' => 'array',
            'occurred_at' => 'datetime',
        ];
    }
}
