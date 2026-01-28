<?php

namespace App\Services\Auditoria;

use App\Models\AuditLog;

class ProcessoAuditService extends BaseAuditService
{
    protected string $modelClass = AuditLog::class;
}
