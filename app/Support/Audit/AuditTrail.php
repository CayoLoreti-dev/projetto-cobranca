<?php

namespace App\Support\Audit;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class AuditTrail
{
    public function record(Model $model, string $action, ?array $before = null, ?array $after = null, ?string $reason = null): AuditLog
    {
        $occurredAt = now();

        $auditId = (string) Str::uuid();

        $payload = [
            'audit_id' => $auditId,
            'model' => $model::class,
            'auditable_id' => (string) $model->getKey(),
            'action' => $action,
            'before' => $before,
            'after' => $after,
            'occurred_at' => $occurredAt->toJSON(),
        ];

        $attributes = [
            'id' => $auditId,
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
            'occurred_at' => $occurredAt,
        ];

        $databaseAttributes = $attributes;
        $databaseAttributes['before'] = $before === null ? null : json_encode($before, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $databaseAttributes['after'] = $after === null ? null : json_encode($after, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        DB::table('audit_logs')->insert($databaseAttributes);

        return tap(new AuditLog($attributes), function (AuditLog $auditLog): void {
            $auditLog->exists = true;
        });
    }
}
