<?php

namespace App\Services;

use App\Models\Approval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    public const HIGH_VALUE_PO_THRESHOLD = 10000000;

    public function __construct(private AuditService $auditService) {}

    public function request(string $module, Model $model, float $amount, ?string $notes = null): Approval
    {
        return DB::transaction(function () use ($module, $model, $amount, $notes) {
            $approval = Approval::create([
                'approval_number' => $this->generateApprovalNumber(),
                'module' => $module,
                'approvable_type' => get_class($model),
                'approvable_id' => $model->getKey(),
                'amount' => $amount,
                'status' => 'Pending',
                'requested_by' => Auth::id(),
                'notes' => $notes,
            ]);

            $this->auditService->logCreation('approval', $approval, "Approval {$approval->approval_number} requested for {$module}");

            return $approval;
        });
    }

    public function approve(Approval $approval, ?string $notes = null): Approval
    {
        return DB::transaction(function () use ($approval, $notes) {
            $approval = Approval::whereKey($approval->id)->lockForUpdate()->firstOrFail();

            if ($approval->status !== 'Pending') {
                throw new \RuntimeException('Only pending approvals can be approved.');
            }

            if ($approval->requested_by && $approval->requested_by === Auth::id()) {
                throw new \RuntimeException('Users cannot approve their own transactions.');
            }

            $approval->update([
                'status' => 'Approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'notes' => $notes ?: $approval->notes,
            ]);

            $this->auditService->logStatusChange('approval', $approval, 'approve', "Approval {$approval->approval_number} approved");

            return $approval;
        });
    }

    private function generateApprovalNumber(): string
    {
        $prefix = 'APR-' . date('Ym') . '-';
        $lastNumber = Approval::where('approval_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->max(DB::raw("CAST(SUBSTRING(approval_number, " . (strlen($prefix) + 1) . ") AS UNSIGNED)"));

        return $prefix . str_pad(($lastNumber ?? 0) + 1, 4, '0', STR_PAD_LEFT);
    }
}
