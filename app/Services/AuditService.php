<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Log an audit event.
     *
     * @param string      $module       e.g. 'journal', 'sale', 'purchase', 'inventory', 'payroll'
     * @param string      $action       e.g. 'create', 'update', 'delete', 'post', 'reverse', 'void'
     * @param object|null $auditable    The model instance being audited
     * @param array|null  $oldValues    Previous values (for updates)
     * @param array|null  $newValues    New values (for creates/updates)
     * @param string|null $description  Human-readable description
     */
    public function log(
        string  $module,
        string  $action,
        ?object $auditable = null,
        ?array  $oldValues = null,
        ?array  $newValues = null,
        ?string $description = null
    ): AuditLog {
        $log = new AuditLog();
        $log->user_id = Auth::id();
        $log->module = $module;
        $log->action = $action;
        $log->auditable_type = $auditable ? get_class($auditable) : null;
        $log->auditable_id = $auditable?->id ?? null;
        $log->old_values = $oldValues;
        $log->new_values = $newValues;
        $log->ip_address = Request::ip();
        $log->description = $description;
        $log->created_at = now();
        $log->save();

        return $log;
    }

    /**
     * Convenience: log a creation event.
     */
    public function logCreation(string $module, object $model, ?string $description = null): AuditLog
    {
        return $this->log($module, 'create', $model, null, $model->toArray(), $description);
    }

    /**
     * Convenience: log an update event with dirty tracking.
     */
    public function logUpdate(string $module, object $model, array $oldValues, ?string $description = null): AuditLog
    {
        return $this->log($module, 'update', $model, $oldValues, $model->toArray(), $description);
    }

    /**
     * Convenience: log a status change (post, reverse, cancel, etc.).
     */
    public function logStatusChange(string $module, object $model, string $action, ?string $description = null): AuditLog
    {
        return $this->log($module, $action, $model, null, ['status' => $model->status ?? null], $description);
    }
}
