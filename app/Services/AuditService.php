<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    public function log(
        string $action,
        string $model,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        try {
            AuditTrail::create([
                'user_id'    => Auth::user()?->user_id,
                'action'     => $action,
                'model'      => $model,
                'model_id'   => $modelId,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'url'        => request()->fullUrl(),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Audit log failed: ' . $e->getMessage());
        }
    }
}
