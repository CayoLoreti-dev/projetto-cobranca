<?php

namespace App\Support\Audit;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditTrail
{
    public function record(Model $model, string $action, ?array $before = null, ?array $after = null, ?string $reason = null): AuditLog
    {
        $payload = [
            'model' => $model::class,
            'id' => (string) $model->getKey(),
            'action' => $action,
            'before' => $before,
            'after' => $after,
            'occurred_at' => now()->toJSON(),
        ];

        return AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => $model::class,
            'auditable_id' => (string) $model->getKey(),
            'action' => $action,
            'before' => $before,
            'after' => $after,
            'reason' => $reason ?? Request::header('X-Audit-Reason'),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'origin' => app()->runningInConsole() ? 'console' : 'http',
            'checksum_sha256' => hash('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
            'occurred_at' => now(),
        ]);
    }
}
