<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            static::logActivity('created', $model);
        });

        static::updated(function ($model) {
            static::logActivity('updated', $model);
        });

        static::deleted(function ($model) {
            static::logActivity('deleted', $model);
        });
    }

    protected static function logActivity($action, $model)
    {
        $oldValues = null;
        $newValues = null;

        if ($action === 'created') {
            $newValues = $model->getAttributes();
            unset($newValues['password'], $newValues['remember_token']);
        } elseif ($action === 'updated') {
            $dirtyKeys = array_keys($model->getDirty());
            $oldValues = array_intersect_key($model->getOriginal(), array_flip($dirtyKeys));
            $newValues = $model->getDirty();
            
            unset($oldValues['password'], $oldValues['remember_token'], $newValues['password'], $newValues['remember_token']);
            
            if (empty($newValues)) {
                return;
            }
        } elseif ($action === 'deleted') {
            $oldValues = $model->getAttributes();
            unset($oldValues['password'], $oldValues['remember_token']);
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
