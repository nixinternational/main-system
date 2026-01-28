<?php

namespace App\Services\Auditoria;

use App\Models\AuditLogCliente;

class ClienteAuditService extends BaseAuditService
{
    protected string $modelClass = AuditLogCliente::class;
}
