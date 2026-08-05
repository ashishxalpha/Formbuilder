<?php

namespace App\Services;

use App\Models\ActivityLog;

class ActivityLogService
{
    public function log(int $userId, ?int $formId, string $action, ?array $oldValues = null, ?array $newValues = null, ?string $ip = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $userId,
            'form_id' => $formId,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip' => $ip,
        ]);
    }

    public function logCreate(int $userId, int $formId, array $newValues, ?string $ip = null)
    {
        return $this->log($userId, $formId, 'create', null, $newValues, $ip);
    }

    public function logUpdate(int $userId, int $formId, array $oldValues, array $newValues, ?string $ip = null)
    {
        return $this->log($userId, $formId, 'update', $oldValues, $newValues, $ip);
    }
}
