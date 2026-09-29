<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    /**
     * Catat log aktivitas sistem ke database.
     */
    protected function logActivity(
        string $action,
        string $module,
        ?string $recordId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ) {
        return ActivityLog::log($action, $module, $recordId, $oldValues, $newValues, $description);
    }
}
