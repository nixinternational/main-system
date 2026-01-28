<?php

namespace App\Services\Auditoria;

use App\Models\AuditLogCatalogo;

class CatalogoAuditService extends BaseAuditService
{
    protected string $modelClass = AuditLogCatalogo::class;
}
