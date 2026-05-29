<?php

namespace App\Observers;

use App\Enums\AuditAction;
use App\Support\Audit\AuditTrail;
use Illuminate\Database\Eloquent\Model;

class AuditableObserver
{
    public function created(Model $model): void
    {
        app(AuditTrail::class)->record($model, AuditAction::Created->value, null, $this->clean($model->getAttributes()));
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        unset($changes['updated_at']);

        if ($changes === []) {
            return;
        }

        $before = [];
        foreach (array_keys($changes) as $attribute) {
            $before[$attribute] = $model->getOriginal($attribute);
        }

        app(AuditTrail::class)->record($model, AuditAction::Updated->value, $this->clean($before), $this->clean($changes));
    }

    public function deleted(Model $model): void
    {
        app(AuditTrail::class)->record($model, AuditAction::Deleted->value, $this->clean($model->getOriginal()), null);
    }

    private function clean(array $attributes): array
    {
        unset($attributes['password'], $attributes['remember_token']);

        return $attributes;
    }
}
