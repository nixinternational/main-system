<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\AuditLogCatalogo;
use App\Models\AuditLogCliente;
use App\Models\Processo;
use App\Models\ProcessoAereo;
use App\Models\ProcessoAereoProduto;
use App\Models\ProcessoProduto;
use App\Models\ProcessoProdutoMulta;
use App\Models\ProcessoRodoviario;
use App\Models\ProcessoRodoviarioProduto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'processos');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');
        $hasDateFilter = !empty($dateStart) || !empty($dateEnd);

        if (!$hasDateFilter) {
            $dateStart = now()->toDateString();
            $dateEnd = now()->toDateString();
        }

        $applyFilters = function ($query) use ($request, $dateStart, $dateEnd) {
            if ($dateStart) {
                $query->whereDate('created_at', '>=', $dateStart);
            }

            if ($dateEnd) {
                $query->whereDate('created_at', '<=', $dateEnd);
            }

            if ($request->filled('user_id')) {
                $query->where('user_id', $request->input('user_id'));
            }

            if ($request->filled('action')) {
                $query->where('action', $request->input('action'));
            }

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('context', 'like', '%' . $search . '%')
                        ->orWhere('request_id', 'like', '%' . $search . '%')
                        ->orWhere('url', 'like', '%' . $search . '%');

                    if (is_numeric($search)) {
                        $q->orWhere('process_id', $search)
                            ->orWhere('auditable_id', $search);
                    }
                });
            }
        };

        $processTypes = [
            Processo::class,
            ProcessoAereo::class,
            ProcessoRodoviario::class,
        ];

        $produtoTypes = [
            ProcessoProduto::class,
            ProcessoAereoProduto::class,
            ProcessoRodoviarioProduto::class,
            ProcessoProdutoMulta::class,
        ];

        $processTypesSql = "'" . implode("','", array_map('addslashes', $processTypes)) . "'";
        $produtoTypesSql = "'" . implode("','", array_map('addslashes', $produtoTypes)) . "'";

        $processQuery = AuditLog::query()->with('user');
        $applyFilters($processQuery);

        $summary = (clone $processQuery)
            ->selectRaw('user_id, DATE(created_at) as dia')
            ->selectRaw('COUNT(*) as total_acoes')
            ->selectRaw('SUM(changed_fields_count) as campos_alterados')
            ->selectRaw("SUM(CASE WHEN action = 'create' AND auditable_type IN ($processTypesSql) THEN 1 ELSE 0 END) as processos_criados")
            ->selectRaw("SUM(CASE WHEN action = 'create' AND auditable_type IN ($produtoTypesSql) THEN 1 ELSE 0 END) as produtos_criados")
            ->groupBy('user_id', DB::raw('DATE(created_at)'))
            ->orderBy('dia', 'desc')
            ->get();

        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $usersMap = $users->keyBy('id');

        $processLogs = $processQuery->orderBy('created_at', 'desc')
            ->paginate(50, ['*'], 'page_processos');
        $processLogs->appends(array_merge($request->query(), ['tab' => 'processos']));

        $auditableMap = [
            'Processo' => 'Processo Marítimo',
            'ProcessoAereo' => 'Processo Aéreo',
            'ProcessoRodoviario' => 'Processo Rodoviário',
            'ProcessoProduto' => 'Produto',
            'ProcessoAereoProduto' => 'Produto Aéreo',
            'ProcessoRodoviarioProduto' => 'Produto Rodoviário',
            'ProcessoProdutoMulta' => 'Produto Multa',
        ];

        $displayProcessLogs = [];
        $groupIndex = [];
        foreach ($processLogs as $log) {
            $isProduto = in_array($log->auditable_type, $produtoTypes, true);
            $canGroup = $isProduto && $log->action === 'update' && !empty($log->request_id);

            if ($canGroup) {
                $groupKey = implode('|', [
                    $log->request_id,
                    $log->process_id,
                    $log->process_type,
                    $log->action,
                    $log->user_id ?? 'system'
                ]);

                if (!isset($groupIndex[$groupKey])) {
                    $displayProcessLogs[] = [
                        'type' => 'group',
                        'request_id' => $log->request_id,
                        'action' => $log->action,
                        'user_name' => $log->user?->name ?? 'Sistema',
                        'process_type' => $log->process_type,
                        'process_id' => $log->process_id,
                        'context' => $log->context,
                        'created_at' => $log->created_at,
                        'items' => [],
                    ];
                    $groupIndex[$groupKey] = count($displayProcessLogs) - 1;
                }

                $auditableBase = class_basename($log->auditable_type ?? '');
                $auditableLabel = $auditableMap[$auditableBase] ?? $auditableBase;

                $displayProcessLogs[$groupIndex[$groupKey]]['items'][] = [
                    'auditable_type' => $log->auditable_type,
                    'auditable_label' => $auditableLabel,
                    'auditable_id' => $log->auditable_id,
                    'changed_fields' => $log->changed_fields ?? [],
                    'old_values' => $log->old_values ?? [],
                    'new_values' => $log->new_values ?? [],
                    'context' => $log->context,
                    'created_at' => $log->created_at?->format('d/m/Y H:i:s'),
                    'changed_fields_count' => $log->changed_fields_count
                        ?? (is_array($log->changed_fields) ? count($log->changed_fields) : 0),
                ];
                continue;
            }

            $displayProcessLogs[] = [
                'type' => 'single',
                'log' => $log,
            ];
        }

        foreach ($displayProcessLogs as &$entry) {
            if (($entry['type'] ?? null) === 'group') {
                $entry['items_count'] = count($entry['items']);
                $entry['changed_fields_count'] = array_sum(array_map(
                    fn ($item) => (int) ($item['changed_fields_count'] ?? 0),
                    $entry['items']
                ));
            }
        }
        unset($entry);

        $clienteQuery = AuditLogCliente::query()->with('user');
        $applyFilters($clienteQuery);
        $clienteLogs = $clienteQuery->orderBy('created_at', 'desc')
            ->paginate(50, ['*'], 'page_clientes');
        $clienteLogs->appends(array_merge($request->query(), ['tab' => 'clientes']));

        $catalogoQuery = AuditLogCatalogo::query()->with('user');
        $applyFilters($catalogoQuery);
        $catalogoLogs = $catalogoQuery->orderBy('created_at', 'desc')
            ->paginate(50, ['*'], 'page_catalogos');
        $catalogoLogs->appends(array_merge($request->query(), ['tab' => 'catalogos']));

        $actions = ['create', 'update', 'delete'];
        $processoTipos = [
            'maritimo' => 'Marítimo',
            'aereo' => 'Aéreo',
            'rodoviario' => 'Rodoviário',
        ];

        $hasFilters = $request->filled('search')
            || $request->filled('user_id')
            || $request->filled('action')
            || $hasDateFilter;

        return view('auditoria.index', compact(
            'tab',
            'processLogs',
            'displayProcessLogs',
            'clienteLogs',
            'catalogoLogs',
            'summary',
            'users',
            'usersMap',
            'actions',
            'processoTipos',
            'dateStart',
            'dateEnd',
            'hasFilters'
        ));
    }
}
