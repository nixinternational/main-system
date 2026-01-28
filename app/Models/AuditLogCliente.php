<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLogCliente extends Model
{
    use HasFactory;

    protected $table = 'audit_logs_clientes';

    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'process_type',
        'process_id',
        'client_id',
        'changed_fields',
        'old_values',
        'new_values',
        'changed_fields_count',
        'context',
        'request_id',
        'ip',
        'user_agent',
        'url',
    ];

    protected $casts = [
        'changed_fields' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
