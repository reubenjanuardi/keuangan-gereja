<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function (Model $model) {
            $logName = method_exists($model, 'getActivityLogName') ? $model->getActivityLogName() : 'default';
            $title = method_exists($model, 'getActivityLogTitle') ? $model->getActivityLogTitle() : ($model->nama ?? $model->name ?? $model->no_bukti ?? $model->kode_akun ?? '#' . $model->getKey());
            $modelName = class_basename($model);

            ActivityLog::log(
                description: "Menambahkan {$modelName} [{$title}]",
                logName: $logName,
                subject: $model,
                properties: [
                    'attributes' => $model->attributesToArray(),
                ]
            );
        });

        static::updated(function (Model $model) {
            $logName = method_exists($model, 'getActivityLogName') ? $model->getActivityLogName() : 'default';
            $title = method_exists($model, 'getActivityLogTitle') ? $model->getActivityLogTitle() : ($model->nama ?? $model->name ?? $model->no_bukti ?? $model->kode_akun ?? '#' . $model->getKey());
            $modelName = class_basename($model);

            $dirty = $model->getDirty();
            $old = array_intersect_key($model->getOriginal(), $dirty);

            // Jangan catat jika hanya updated_at atau remember_token
            unset($dirty['updated_at'], $old['updated_at'], $dirty['remember_token'], $old['remember_token']);

            if (empty($dirty)) {
                return;
            }

            ActivityLog::log(
                description: "Mengubah {$modelName} [{$title}]",
                logName: $logName,
                subject: $model,
                properties: [
                    'old' => $old,
                    'attributes' => $dirty,
                ]
            );
        });

        static::deleted(function (Model $model) {
            $logName = method_exists($model, 'getActivityLogName') ? $model->getActivityLogName() : 'default';
            $title = method_exists($model, 'getActivityLogTitle') ? $model->getActivityLogTitle() : ($model->nama ?? $model->name ?? $model->no_bukti ?? $model->kode_akun ?? '#' . $model->getKey());
            $modelName = class_basename($model);

            ActivityLog::log(
                description: "Menghapus {$modelName} [{$title}]",
                logName: $logName,
                subject: $model,
                properties: [
                    'old' => $model->attributesToArray(),
                ]
            );
        });
    }
}
