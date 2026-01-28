@extends('layouts.app')
@section('title', 'Auditoria')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header" style="background: var(--theme-gradient-primary);">
            <h3 class="card-title mb-0 text-white">
                <i class="fas fa-shield-alt me-2"></i>Auditoria
            </h3>
        </div>
        <div class="card-body">
            @php
                $tabProcessosUrl = route('auditoria.index', array_merge(request()->query(), ['tab' => 'processos']));
                $tabClientesUrl = route('auditoria.index', array_merge(request()->query(), ['tab' => 'clientes']));
                $tabCatalogosUrl = route('auditoria.index', array_merge(request()->query(), ['tab' => 'catalogos']));
            @endphp
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'processos' ? 'active' : '' }}" href="{{ $tabProcessosUrl }}">
                        Processos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'clientes' ? 'active' : '' }}" href="{{ $tabClientesUrl }}">
                        Clientes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'catalogos' ? 'active' : '' }}" href="{{ $tabCatalogosUrl }}">
                        Catálogo
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade {{ $tab === 'processos' ? 'show active' : '' }}" id="tab-processos">
                    <form class="mb-4" action="{{ route('auditoria.index') }}" method="GET">
                        <input type="hidden" name="tab" value="processos">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Data inicial</label>
                                <input type="date" class="form-control" name="date_start" value="{{ $dateStart }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Data final</label>
                                <input type="date" class="form-control" name="date_end" value="{{ $dateEnd }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Usuário</label>
                                <select name="user_id" class="form-select select2" data-placeholder="Todos">
                                    <option value="">Todos</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ação</label>
                                <select name="action" class="form-select select2" data-placeholder="Todas">
                                    <option value="">Todas</option>
                                    @foreach ($actions as $action)
                                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                            {{ strtoupper($action) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Busca geral</label>
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="ID, request, URL...">
                            </div>
                            <div class="col-12 d-flex gap-2">
                                <button type="submit" class="btn btn-primary audit-filter-btn">
                                    <i class="fas fa-filter me-1"></i>Filtrar
                                </button>
                                @if ($hasFilters)
                                    <a href="{{ route('auditoria.index', ['tab' => 'processos']) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Limpar
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    @if ($summary->count() > 0)
                        <div class="mb-4">
                            <h5 class="mb-3">Resumo diário por usuário</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Dia</th>
                                            <th>Usuário</th>
                                            <th class="text-center">Ações</th>
                                            <th class="text-center">Processos criados</th>
                                            <th class="text-center">Produtos criados</th>
                                            <th class="text-center">Campos alterados</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($summary as $row)
                                            @php
                                                $user = $usersMap[$row->user_id] ?? null;
                                            @endphp
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($row->dia)->format('d/m/Y') }}</td>
                                                <td>{{ $user?->name ?? 'Sistema' }}</td>
                                                <td class="text-center">{{ $row->total_acoes }}</td>
                                                <td class="text-center">{{ $row->processos_criados }}</td>
                                                <td class="text-center">{{ $row->produtos_criados }}</td>
                                                <td class="text-center">{{ $row->campos_alterados }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Data</th>
                                    <th>Usuário</th>
                                    <th>Ação</th>
                                    <th>Tipo</th>
                                    <th>Processo</th>
                                    <th>Item</th>
                                    <th class="text-center">Campos</th>
                                    <th>Contexto</th>
                                    <th class="text-center">Detalhes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($displayProcessLogs as $entry)
                                    @if (($entry['type'] ?? 'single') === 'group')
                                        @php
                                            $actionBadge = match ($entry['action']) {
                                                'create' => 'badge bg-success',
                                                'update' => 'badge bg-warning text-dark',
                                                'delete' => 'badge bg-danger',
                                                default => 'badge bg-secondary',
                                            };
                                            $actionLabel = match ($entry['action']) {
                                                'create' => 'Criação',
                                                'update' => 'Atualização',
                                                'delete' => 'Exclusão',
                                                default => ucfirst($entry['action'] ?? '-'),
                                            };
                                            $createdAt = ($entry['created_at'] ?? null)?->format('d/m/Y H:i:s') ?? '-';
                                            $itemsCount = $entry['items_count'] ?? count($entry['items'] ?? []);
                                            $fieldsCount = $entry['changed_fields_count'] ?? 0;
                                            $context = $entry['context'] ?? 'processo.produto.lote';
                                            $processoLabel = $processoTipos[$entry['process_type'] ?? ''] ?? ucfirst($entry['process_type'] ?? '-') ?? '-';
                                        @endphp
                                        <tr class="table-light">
                                            <td>{{ $createdAt }}</td>
                                            <td>{{ $entry['user_name'] ?? 'Sistema' }}</td>
                                            <td><span class="{{ $actionBadge }}">{{ $actionLabel }}</span></td>
                                            <td>{{ $processoLabel }}</td>
                                            <td>{{ $entry['process_id'] ?? '-' }}</td>
                                            <td>Produtos ({{ $itemsCount }} itens)</td>
                                            <td class="text-center">{{ $fieldsCount }}</td>
                                            <td>{{ $context }}</td>
                                            <td class="text-center">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary btn-view-audit"
                                                    data-action="{{ $entry['action'] }}"
                                                    data-user="{{ $entry['user_name'] ?? 'Sistema' }}"
                                                    data-process="{{ $entry['process_id'] ?? '-' }}"
                                                    data-type="Produtos (lote)"
                                                    data-context="{{ $context }}"
                                                    data-request-id="{{ $entry['request_id'] ?? '' }}"
                                                    data-url=""
                                                    data-created-at="{{ $createdAt }}"
                                                    data-fields='{}'
                                                    data-old='{}'
                                                    data-new='{}'
                                                    data-group-items='@json($entry['items'] ?? [])'
                                                    title="Ver detalhes do lote">
                                                    <i class="fas fa-layer-group mr-2"></i>Lote
                                                </button>
                                            </td>
                                        </tr>
                                    @else
                                        @php
                                            $log = $entry['log'];
                                            $actionBadge = match ($log->action) {
                                                'create' => 'badge bg-success',
                                                'update' => 'badge bg-warning text-dark',
                                                'delete' => 'badge bg-danger',
                                                default => 'badge bg-secondary',
                                            };
                                            $actionLabel = match ($log->action) {
                                                'create' => 'Criação',
                                                'update' => 'Atualização',
                                                'delete' => 'Exclusão',
                                                default => ucfirst($log->action ?? '-'),
                                            };
                                            $auditableBase = class_basename($log->auditable_type ?? '');
                                            $auditableMap = [
                                                'Processo' => 'Processo Marítimo',
                                                'ProcessoAereo' => 'Processo Aéreo',
                                                'ProcessoRodoviario' => 'Processo Rodoviário',
                                                'ProcessoProduto' => 'Produto',
                                                'ProcessoAereoProduto' => 'Produto Aéreo',
                                                'ProcessoRodoviarioProduto' => 'Produto Rodoviário',
                                                'ProcessoProdutoMulta' => 'Produto Multa',
                                            ];
                                            $auditableLabel = $auditableMap[$auditableBase] ?? $auditableBase;
                                            $fieldsCount = $log->changed_fields_count ?? 0;
                                            if (!$fieldsCount) {
                                                $changedFields = $log->changed_fields ?? [];
                                                if (is_array($changedFields) && !empty($changedFields)) {
                                                    $fieldsCount = count($changedFields);
                                                } else {
                                                    $oldValues = $log->old_values ?? [];
                                                    $newValues = $log->new_values ?? [];
                                                    $mergedKeys = array_unique(array_merge(array_keys((array) $oldValues), array_keys((array) $newValues)));
                                                    $fieldsCount = count($mergedKeys);
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                            <td>{{ $log->user?->name ?? 'Sistema' }}</td>
                                            <td><span class="{{ $actionBadge }}">{{ $actionLabel }}</span></td>
                                            <td>{{ $processoTipos[$log->process_type] ?? ucfirst($log->process_type ?? '-') }}</td>
                                            <td>{{ $log->process_id ?? '-' }}</td>
                                            <td>{{ $auditableLabel }} #{{ $log->auditable_id ?? '-' }}</td>
                                            <td class="text-center">{{ $fieldsCount }}</td>
                                            <td>{{ $log->context ?? '-' }}</td>
                                            <td class="text-center">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary btn-view-audit"
                                                    data-action="{{ $log->action }}"
                                                    data-user="{{ $log->user?->name ?? 'Sistema' }}"
                                                    data-process="{{ $log->process_id ?? '-' }}"
                                                    data-type="{{ $auditableLabel }}"
                                                    data-context="{{ $log->context ?? '-' }}"
                                                    data-request-id="{{ $log->request_id ?? '' }}"
                                                    data-url="{{ $log->url ?? '' }}"
                                                    data-created-at="{{ $log->created_at->format('d/m/Y H:i:s') }}"
                                                    data-fields='@json($log->changed_fields ?? [])'
                                                    data-old='@json($log->old_values ?? [])'
                                                    data-new='@json($log->new_values ?? [])'
                                                    data-group-items='[]'
                                                    title="Ver detalhes">
                                                    <i class="fas fa-eye mr-2"></i>Ver
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Nenhum registro encontrado</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $processLogs->links() }}
                    </div>
                </div>

                <div class="tab-pane fade {{ $tab === 'clientes' ? 'show active' : '' }}" id="tab-clientes">
                    <form class="mb-4" action="{{ route('auditoria.index') }}" method="GET">
                        <input type="hidden" name="tab" value="clientes">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Data inicial</label>
                                <input type="date" class="form-control" name="date_start" value="{{ $dateStart }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Data final</label>
                                <input type="date" class="form-control" name="date_end" value="{{ $dateEnd }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Usuário</label>
                                <select name="user_id" class="form-select select2" data-placeholder="Todos">
                                    <option value="">Todos</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ação</label>
                                <select name="action" class="form-select select2" data-placeholder="Todas">
                                    <option value="">Todas</option>
                                    @foreach ($actions as $action)
                                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                            {{ strtoupper($action) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Busca geral</label>
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="ID, request, URL...">
                            </div>
                            <div class="col-12 d-flex gap-2">
                                <button type="submit" class="btn btn-primary audit-filter-btn">
                                    <i class="fas fa-filter me-1"></i>Filtrar
                                </button>
                                @if ($hasFilters)
                                    <a href="{{ route('auditoria.index', ['tab' => 'clientes']) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Limpar
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    @php
                        $auditableMap = [
                            'Cliente' => 'Cliente',
                            'ClienteDocumento' => 'Documento do Cliente',
                            'BancoCliente' => 'Banco do Cliente',
                            'ClienteEmail' => 'Email do Cliente',
                            'ClienteResponsavelProcesso' => 'Responsável',
                            'ClienteAduana' => 'Aduana',
                        ];
                    @endphp
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Data</th>
                                    <th>Usuário</th>
                                    <th>Ação</th>
                                    <th>Item</th>
                                    <th class="text-center">Campos</th>
                                    <th>Contexto</th>
                                    <th class="text-center">Detalhes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($clienteLogs as $log)
                                    @php
                                        $actionBadge = match ($log->action) {
                                            'create' => 'badge bg-success',
                                            'update' => 'badge bg-warning text-dark',
                                            'delete' => 'badge bg-danger',
                                            default => 'badge bg-secondary',
                                        };
                                        $actionLabel = match ($log->action) {
                                            'create' => 'Criação',
                                            'update' => 'Atualização',
                                            'delete' => 'Exclusão',
                                            default => ucfirst($log->action ?? '-'),
                                        };
                                        $auditableBase = class_basename($log->auditable_type ?? '');
                                        $auditableLabel = $auditableMap[$auditableBase] ?? $auditableBase;
                                        $fieldsCount = $log->changed_fields_count ?? 0;
                                        if (!$fieldsCount) {
                                            $changedFields = $log->changed_fields ?? [];
                                            if (is_array($changedFields) && !empty($changedFields)) {
                                                $fieldsCount = count($changedFields);
                                            } else {
                                                $oldValues = $log->old_values ?? [];
                                                $newValues = $log->new_values ?? [];
                                                $mergedKeys = array_unique(array_merge(array_keys((array) $oldValues), array_keys((array) $newValues)));
                                                $fieldsCount = count($mergedKeys);
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td>{{ $log->user?->name ?? 'Sistema' }}</td>
                                        <td><span class="{{ $actionBadge }}">{{ $actionLabel }}</span></td>
                                        <td>{{ $auditableLabel }} #{{ $log->auditable_id ?? '-' }}</td>
                                        <td class="text-center">{{ $fieldsCount }}</td>
                                        <td>{{ $log->context ?? '-' }}</td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary btn-view-audit"
                                                data-action="{{ $log->action }}"
                                                data-user="{{ $log->user?->name ?? 'Sistema' }}"
                                                data-process="{{ $log->process_id ?? '-' }}"
                                                data-type="{{ $auditableLabel }}"
                                                data-context="{{ $log->context ?? '-' }}"
                                                data-request-id="{{ $log->request_id ?? '' }}"
                                                data-url="{{ $log->url ?? '' }}"
                                                data-created-at="{{ $log->created_at->format('d/m/Y H:i:s') }}"
                                                data-fields='@json($log->changed_fields ?? [])'
                                                data-old='@json($log->old_values ?? [])'
                                                data-new='@json($log->new_values ?? [])'
                                                data-group-items='[]'
                                                title="Ver detalhes">
                                                <i class="fas fa-eye mr-2"></i>Ver
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Nenhum registro encontrado</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $clienteLogs->links() }}
                    </div>
                </div>

                <div class="tab-pane fade {{ $tab === 'catalogos' ? 'show active' : '' }}" id="tab-catalogos">
                    <form class="mb-4" action="{{ route('auditoria.index') }}" method="GET">
                        <input type="hidden" name="tab" value="catalogos">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Data inicial</label>
                                <input type="date" class="form-control" name="date_start" value="{{ $dateStart }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Data final</label>
                                <input type="date" class="form-control" name="date_end" value="{{ $dateEnd }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Usuário</label>
                                <select name="user_id" class="form-select select2" data-placeholder="Todos">
                                    <option value="">Todos</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ação</label>
                                <select name="action" class="form-select select2" data-placeholder="Todas">
                                    <option value="">Todas</option>
                                    @foreach ($actions as $action)
                                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                            {{ strtoupper($action) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Busca geral</label>
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="ID, request, URL...">
                            </div>
                            <div class="col-12 d-flex gap-2">
                                <button type="submit" class="btn btn-primary audit-filter-btn">
                                    <i class="fas fa-filter me-1"></i>Filtrar
                                </button>
                                @if ($hasFilters)
                                    <a href="{{ route('auditoria.index', ['tab' => 'catalogos']) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Limpar
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    @php
                        $auditableMap = [
                            'Catalogo' => 'Catálogo',
                            'Produto' => 'Produto do Catálogo',
                        ];
                    @endphp
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Data</th>
                                    <th>Usuário</th>
                                    <th>Ação</th>
                                    <th>Item</th>
                                    <th class="text-center">Campos</th>
                                    <th>Contexto</th>
                                    <th class="text-center">Detalhes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($catalogoLogs as $log)
                                    @php
                                        $actionBadge = match ($log->action) {
                                            'create' => 'badge bg-success',
                                            'update' => 'badge bg-warning text-dark',
                                            'delete' => 'badge bg-danger',
                                            default => 'badge bg-secondary',
                                        };
                                        $actionLabel = match ($log->action) {
                                            'create' => 'Criação',
                                            'update' => 'Atualização',
                                            'delete' => 'Exclusão',
                                            default => ucfirst($log->action ?? '-'),
                                        };
                                        $auditableBase = class_basename($log->auditable_type ?? '');
                                        $auditableLabel = $auditableMap[$auditableBase] ?? $auditableBase;
                                        $fieldsCount = $log->changed_fields_count ?? 0;
                                        if (!$fieldsCount) {
                                            $changedFields = $log->changed_fields ?? [];
                                            if (is_array($changedFields) && !empty($changedFields)) {
                                                $fieldsCount = count($changedFields);
                                            } else {
                                                $oldValues = $log->old_values ?? [];
                                                $newValues = $log->new_values ?? [];
                                                $mergedKeys = array_unique(array_merge(array_keys((array) $oldValues), array_keys((array) $newValues)));
                                                $fieldsCount = count($mergedKeys);
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td>{{ $log->user?->name ?? 'Sistema' }}</td>
                                        <td><span class="{{ $actionBadge }}">{{ $actionLabel }}</span></td>
                                        <td>{{ $auditableLabel }} #{{ $log->auditable_id ?? '-' }}</td>
                                        <td class="text-center">{{ $fieldsCount }}</td>
                                        <td>{{ $log->context ?? '-' }}</td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary btn-view-audit"
                                                data-action="{{ $log->action }}"
                                                data-user="{{ $log->user?->name ?? 'Sistema' }}"
                                                data-process="{{ $log->process_id ?? '-' }}"
                                                data-type="{{ $auditableLabel }}"
                                                data-context="{{ $log->context ?? '-' }}"
                                                data-request-id="{{ $log->request_id ?? '' }}"
                                                data-url="{{ $log->url ?? '' }}"
                                                data-created-at="{{ $log->created_at->format('d/m/Y H:i:s') }}"
                                                data-fields='@json($log->changed_fields ?? [])'
                                                data-old='@json($log->old_values ?? [])'
                                                data-new='@json($log->new_values ?? [])'
                                                data-group-items='[]'
                                                title="Ver detalhes">
                                                <i class="fas fa-eye mr-2"></i>Ver
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Nenhum registro encontrado</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $catalogoLogs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="auditDetailModal" tabindex="-1" aria-labelledby="auditDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="auditDetailModalLabel">
                        <i class="fas fa-search me-2"></i>Detalhes da Auditoria
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <strong>Ação:</strong> <span id="auditModalAction"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Data:</strong> <span id="auditModalDate"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Usuário:</strong> <span id="auditModalUser"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Processo:</strong> <span id="auditModalProcess"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Tipo:</strong> <span id="auditModalType"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Contexto:</strong> <span id="auditModalContext"></span>
                        </div>
                        <div class="col-12">
                            <strong>Request ID:</strong> <span id="auditModalRequestId"></span>
                        </div>
                        <div class="col-12">
                            <strong>URL:</strong> <span id="auditModalUrl"></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <h6 class="mb-3">
                            <i class="fas fa-edit me-2"></i>Campos Alterados
                        </h6>
                        <div id="auditModalFieldsContainer">
                            <p class="text-muted mb-0">Nenhum campo alterado</p>
                        </div>
                    </div>
                    <div class="mb-3" id="auditModalGroupWrapper" style="display: none;">
                        <h6 class="mb-3">
                            <i class="fas fa-layer-group me-2"></i>Itens do lote
                        </h6>
                        <div id="auditModalGroupContainer"></div>
                    </div>
                    <div class="mb-3" id="auditModalCotacaoWrapper" style="display: none;">
                        <h6 class="mb-3">
                            <i class="fas fa-coins me-2"></i>Cotação do processo
                        </h6>
                        <div id="auditModalCotacaoContainer"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function decodeHtmlEntities(value) {
            if (!value || typeof value !== 'string') return value;
            const textarea = document.createElement('textarea');
            textarea.innerHTML = value;
            return textarea.value;
        }

        function safeJsonParse(value) {
            if (!value) return {};
            if (typeof value === 'object') return value;
            try {
                return JSON.parse(value);
            } catch (err) {
                try {
                    return JSON.parse(decodeHtmlEntities(value));
                } catch (err2) {
                    return {};
                }
            }
        }

        const actionLabelMap = {
            create: 'Criação',
            update: 'Atualização',
            delete: 'Exclusão'
        };

        const auditableLabelMap = {
            ProcessoProduto: 'Produto',
            ProcessoAereoProduto: 'Produto Aéreo',
            ProcessoRodoviarioProduto: 'Produto Rodoviário',
            ProcessoProdutoMulta: 'Produto Multa'
        };

        const fieldLabelMap = {
            acrescimo_frete_moeda: 'Acréscimo frete Moeda',
            frete_internacional: 'Frete internacional',
            frete_internacional_moeda: 'Frete internacional Moeda',
            frete_internacional_brl: 'Frete internacional (BRL)',
            frete_internacional_usd: 'Frete internacional (USD)',
            fob_unit_usd: 'FOB unitário (USD)',
            fob_total_usd: 'FOB total (USD)',
            fob_unit: 'FOB unitário',
            fob_total: 'FOB total',
            valor_fob: 'Valor FOB',
            valor_fob_unitario: 'Valor FOB unitário',
            valor_fob_total: 'Valor FOB total',
            valor_total: 'Valor total',
            valor_unitario: 'Valor unitário',
            quantidade: 'Quantidade',
            peso_bruto: 'Peso bruto',
            peso_liquido: 'Peso líquido',
            peso_liq_total_kg: 'Peso líquido total (kg)',
            peso_liquido_unitario: 'Peso líquido unitário',
            ncm: 'NCM',
            descricao: 'Descrição',
            moeda: 'Moeda',
            moeda_processo: 'Moeda do processo',
            cotacao_moeda_processo: 'Cotação do processo',
            taxa_dolar: 'Cotação dólar',
            cotacao_dolar: 'Cotação dólar',
            cotacao_euro: 'Cotação euro',
            cotacao_libra: 'Cotação libra',
            data_cotacao: 'Data da cotação',
            canal: 'Canal',
            status: 'Status',
            processo_id: 'Processo',
            cliente_id: 'Cliente',
            fornecedor_id: 'Fornecedor',
            service_charges: 'Service charges',
            service_charges_moeda: 'Service charges Moeda',
            service_charges_usd: 'Service charges (USD)',
            service_charges_brl: 'Service charges (BRL)',
            honorarios_nix: 'Honorários Nix',
            li_dta_honor_nix: 'LI/DTA Honor. Nix',
            correios: 'Correios',
            armazenagem: 'Armazenagem',
            desp_fronteira: 'Desp. fronteira',
            das_fronteira: 'DAS fronteira',
            rep_fronteira: 'Rep. fronteira',
            frete_foz_gyn: 'Frete Foz/Gyn',
            armaz_anapolis: 'Armaz. Anápolis',
            mov_anapolis: 'Mov. Anápolis',
            rep_anapolis: 'Rep. Anápolis',
            nome: 'Nome',
            cnpj: 'CNPJ',
            cpf: 'CPF',
            logradouro: 'Logradouro',
            numero: 'Número',
            cep: 'CEP',
            complemento: 'Complemento',
            bairro: 'Bairro',
            cidade: 'Cidade',
            estado: 'Estado',
            nome_responsavel_legal: 'Responsável legal',
            cpf_responsavel_legal: 'CPF responsável legal',
            email_responsavel_legal: 'Email responsável legal',
            telefone_responsavel_legal: 'Telefone responsável legal',
            telefone_fixo_responsavel_legal: 'Telefone fixo responsável legal',
            telefone_celular_responsavel_legal: 'Telefone celular responsável legal',
            modelo: 'Modelo',
            codigo: 'Código',
            unidade: 'Unidade',
            fornecedor_id: 'Fornecedor',
            catalogo_id: 'Catálogo',
            cpf_cnpj: 'CPF/CNPJ',
            status: 'Status',
            emails: 'Emails',
            aduanas: 'Aduanas',
            responsaveis: 'Responsáveis',
            documentos: 'Documentos',
            bancos: 'Bancos',
            modalidade_radar: 'Modalidade radar',
            beneficio_fiscal: 'Benefício fiscal',
            observacoes: 'Observações',
            debito_impostos: 'Débito de impostos',
            data_vencimento_procuracao: 'Vencimento procuração',
            data_procuracao: 'Data procuração'
        };

        function formatActionLabel(action) {
            if (!action) return '-';
            return actionLabelMap[action] ?? (action.charAt(0).toUpperCase() + action.slice(1));
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatFieldValue(value) {
            if (value === null || value === undefined) {
                return '<span class="text-muted fst-italic">(vazio)</span>';
            }
            
            if (typeof value === 'boolean') {
                return value ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-secondary">Não</span>';
            }
            
            if (typeof value === 'number') {
                if (value % 1 !== 0 || value > 1000) {
                    return new Intl.NumberFormat('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 6
                    }).format(value);
                }
                return value.toLocaleString('pt-BR');
            }
            
            if (typeof value === 'string') {
                const dateRegex = /^\d{4}-\d{2}-\d{2}/;
                if (dateRegex.test(value)) {
                    try {
                        const date = new Date(value);
                        return date.toLocaleDateString('pt-BR', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    } catch (e) {
                        return value;
                    }
                }
                return value;
            }
            
            if (Array.isArray(value)) {
                if (value.length === 0) {
                    return '<span class="text-muted">[]</span>';
                }
                return '<pre class="mb-0" style="font-size: 0.8rem;">' + JSON.stringify(value, null, 2) + '</pre>';
            }
            
            if (typeof value === 'object') {
                return '<pre class="mb-0" style="font-size: 0.8rem;">' + JSON.stringify(value, null, 2) + '</pre>';
            }
            
            return String(value);
        }

        function normalizeCotacaoValue(value) {
            if (!value) return {};
            if (typeof value === 'string') {
                return safeJsonParse(value);
            }
            if (Array.isArray(value)) {
                const mapped = {};
                value.forEach(item => {
                    if (item && item.moeda) {
                        mapped[item.moeda] = item;
                    }
                });
                return mapped;
            }
            if (typeof value === 'object') {
                return value;
            }
            return {};
        }

        function formatCotacaoNumber(value) {
            if (value === null || value === undefined || value === '') {
                return '<span class="text-muted fst-italic">(vazio)</span>';
            }
            const numeric = typeof value === 'number' ? value : (isNaN(value) ? null : Number(value));
            if (numeric !== null && !isNaN(numeric)) {
                return new Intl.NumberFormat('pt-BR', {
                    minimumFractionDigits: 4,
                    maximumFractionDigits: 6
                }).format(numeric);
            }
            return formatFieldValue(value);
        }

        function renderCotacaoComparison(oldValue, newValue) {
            const oldObj = normalizeCotacaoValue(oldValue);
            const newObj = normalizeCotacaoValue(newValue);
            const moedas = Array.from(new Set([
                ...Object.keys(oldObj || {}),
                ...Object.keys(newObj || {})
            ])).sort();

            if (!moedas.length) {
                return '<p class="text-muted mb-0">Nenhuma cotação registrada</p>';
            }

            let html = '<div class="table-responsive">';
            html += '<table class="table table-sm table-bordered mb-0 cotacao-compare">';
            html += '<thead>';
            html += '<tr>';
            html += '<th rowspan="2" style="min-width: 70px;">Moeda</th>';
            html += '<th rowspan="2" style="min-width: 220px;">Nome</th>';
            html += '<th colspan="2">Data</th>';
            html += '<th colspan="2">Compra</th>';
            html += '<th colspan="2">Venda</th>';
            html += '<th colspan="2">Erro</th>';
            html += '</tr>';
            html += '<tr>';
            html += '<th>Antes</th><th>Depois</th>';
            html += '<th>Antes</th><th>Depois</th>';
            html += '<th>Antes</th><th>Depois</th>';
            html += '<th>Antes</th><th>Depois</th>';
            html += '</tr>';
            html += '</thead><tbody>';

            moedas.forEach(code => {
                const oldC = oldObj?.[code] || {};
                const newC = newObj?.[code] || {};
                const nome = newC.nome || oldC.nome || code;
                const moeda = newC.moeda || oldC.moeda || code;

                html += '<tr>';
                html += '<td><strong>' + moeda + '</strong></td>';
                html += '<td>' + formatFieldValue(nome) + '</td>';
                html += '<td>' + formatFieldValue(oldC.data) + '</td>';
                html += '<td>' + formatFieldValue(newC.data) + '</td>';
                html += '<td>' + formatCotacaoNumber(oldC.compra) + '</td>';
                html += '<td>' + formatCotacaoNumber(newC.compra) + '</td>';
                html += '<td>' + formatCotacaoNumber(oldC.venda) + '</td>';
                html += '<td>' + formatCotacaoNumber(newC.venda) + '</td>';
                html += '<td>' + formatFieldValue(oldC.erro) + '</td>';
                html += '<td>' + formatFieldValue(newC.erro) + '</td>';
                html += '</tr>';
            });

            html += '</tbody></table></div>';
            return html;
        }

        function formatFieldName(fieldName) {
            if (!fieldName) return '-';
            if (fieldLabelMap[fieldName]) {
                return fieldLabelMap[fieldName];
            }

            const cleaned = fieldName.replace(/\./g, ' ').replace(/_/g, ' ').trim();
            const tokens = cleaned.split(/\s+/);
            const upperTokens = ['usd', 'brl', 'kg', 'ncm', 'fob', 'dta', 'li', 'das', 'iof', 'dai', 'dape', 'cif', 'bl'];
            const lowerTokens = ['de', 'da', 'do', 'dos', 'das', 'e', 'em', 'para'];

            return tokens
                .map((token, index) => {
                    const lower = token.toLowerCase();
                    if (upperTokens.includes(lower)) {
                        return lower.toUpperCase();
                    }
                    if (lowerTokens.includes(lower) && index > 0) {
                        return lower;
                    }
                    return lower.charAt(0).toUpperCase() + lower.slice(1);
                })
                .join(' ');
        }

        function resolveFieldKeys(fields, oldValues, newValues) {
            let keys = [];
            if (Array.isArray(fields)) {
                keys = fields;
            } else if (fields && typeof fields === 'object') {
                keys = Object.keys(fields);
            }

            if (!keys || keys.length === 0) {
                const oldKeys = oldValues && typeof oldValues === 'object' ? Object.keys(oldValues) : [];
                const newKeys = newValues && typeof newValues === 'object' ? Object.keys(newValues) : [];
                keys = Array.from(new Set([...oldKeys, ...newKeys]));
            }

            return keys;
        }

        function resolveOldNewValues(fields, oldValues, newValues, field) {
            if (fields && typeof fields === 'object' && !Array.isArray(fields)) {
                const fieldData = fields[field];
                if (fieldData && typeof fieldData === 'object' && ('old' in fieldData || 'new' in fieldData)) {
                    return {
                        oldValue: fieldData.old !== undefined ? fieldData.old : null,
                        newValue: fieldData.new !== undefined ? fieldData.new : null
                    };
                }
            }

            return {
                oldValue: oldValues && oldValues[field] !== undefined ? oldValues[field] : null,
                newValue: newValues && newValues[field] !== undefined ? newValues[field] : null
            };
        }

        function buildChangesTable(fields, oldValues, newValues, options = {}) {
            const excludeKeys = options.excludeKeys || [];
            const fieldKeys = resolveFieldKeys(fields, oldValues, newValues)
                .filter(key => !excludeKeys.includes(key));

            if (!fieldKeys.length) {
                return null;
            }

            let html = '<div class="table-responsive"><table class="table table-sm table-bordered mb-0">';
            html += '<thead class="table-light"><tr><th style="width: 30%;">Campo</th><th style="width: 35%;">Valor Anterior</th><th style="width: 35%;">Valor Novo</th></tr></thead>';
            html += '<tbody>';

            const descricaoKeys = ['descricao', 'descricao_produto', 'descricao_item', 'descricao_mercadoria'];
            fieldKeys.forEach(field => {
                const { oldValue, newValue } = resolveOldNewValues(fields, oldValues, newValues, field);
                const isDescricao = descricaoKeys.includes(field);
                const valueClass = isDescricao ? ' class="field-value field-description"' : ' class="field-value"';
                html += '<tr>';
                html += '<td><strong>' + formatFieldName(field) + '</strong></td>';
                html += '<td' + valueClass + '>' + formatFieldValue(oldValue) + '</td>';
                html += '<td' + valueClass + '>' + formatFieldValue(newValue) + '</td>';
                html += '</tr>';
            });

            html += '</tbody></table></div>';
            return html;
        }

        function resolveAuditableLabel(item) {
            if (item && item.auditable_label) {
                return item.auditable_label;
            }
            const type = item?.auditable_type || '';
            const base = type.split('\\').pop();
            return auditableLabelMap[base] ?? base ?? 'Produto';
        }

        function renderGroupItems(items) {
            const container = document.getElementById('auditModalGroupContainer');

            if (!items || !items.length) {
                container.innerHTML = '<p class="text-muted mb-0">Nenhum item no lote</p>';
                return;
            }

            let html = '';
            items.forEach(item => {
                const label = resolveAuditableLabel(item);
                const itemId = item?.auditable_id ?? '-';
                const fieldsCount = item?.changed_fields_count
                    ?? (item?.changed_fields && typeof item.changed_fields === 'object'
                        ? Object.keys(item.changed_fields).length
                        : 0);
                const detailsTable = buildChangesTable(item?.changed_fields ?? {}, item?.old_values ?? {}, item?.new_values ?? {}, {
                    excludeKeys: ['cotacao_moeda_processo']
                });

                html += '<div class="group-item">';
                html += '<div class="d-flex flex-wrap align-items-center justify-content-between gap-2">';
                html += '<div><strong>' + escapeHtml(label) + ' #' + escapeHtml(itemId) + '</strong></div>';
                html += '<div class="text-muted small">' + escapeHtml(item?.created_at ?? '') + '</div>';
                html += '</div>';
                html += '<div class="small text-muted mb-2">Campos alterados: <strong>' + escapeHtml(fieldsCount) + '</strong></div>';
                html += '<details>';
                html += '<summary>Ver detalhes</summary>';
                html += detailsTable ?? '<p class="text-muted mb-0">Nenhum campo alterado</p>';
                html += '</details>';
                html += '</div>';
            });

            container.innerHTML = html;
        }

        function renderChangedFields(fields, oldValues, newValues) {
            const container = document.getElementById('auditModalFieldsContainer');
            const cotacaoWrapper = document.getElementById('auditModalCotacaoWrapper');
            const cotacaoContainer = document.getElementById('auditModalCotacaoContainer');
            const mainTable = buildChangesTable(fields, oldValues, newValues, {
                excludeKeys: ['cotacao_moeda_processo']
            });
            container.innerHTML = mainTable ?? '<p class="text-muted mb-0">Nenhum campo alterado</p>';

            // Render cotação em tabela separada
            const cotacaoValues = resolveOldNewValues(fields, oldValues, newValues, 'cotacao_moeda_processo');
            const oldCot = normalizeCotacaoValue(cotacaoValues.oldValue);
            const newCot = normalizeCotacaoValue(cotacaoValues.newValue);
            const cotacaoKeys = [
                ...Object.keys(oldCot || {}),
                ...Object.keys(newCot || {})
            ];

            if (cotacaoKeys.length > 0) {
                cotacaoWrapper.style.display = '';
                cotacaoContainer.innerHTML = renderCotacaoComparison(cotacaoValues.oldValue, cotacaoValues.newValue);
            } else {
                cotacaoWrapper.style.display = 'none';
                cotacaoContainer.innerHTML = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (window.$ && $.fn.select2) {
                $('.select2').select2({
                    width: '100%',
                    allowClear: true
                });
            }

            document.querySelectorAll('.btn-view-audit').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const action = btn.getAttribute('data-action');
                    const user = btn.getAttribute('data-user');
                    const processId = btn.getAttribute('data-process');
                    const type = btn.getAttribute('data-type');
                    const context = btn.getAttribute('data-context');
                    const requestId = btn.getAttribute('data-request-id');
                    const url = btn.getAttribute('data-url');
                    const createdAt = btn.getAttribute('data-created-at');
                    const groupItems = safeJsonParse(btn.getAttribute('data-group-items'));
                    const fields = safeJsonParse(btn.getAttribute('data-fields'));
                    const oldValues = safeJsonParse(btn.getAttribute('data-old'));
                    const newValues = safeJsonParse(btn.getAttribute('data-new'));
                    document.getElementById('auditModalAction').textContent = formatActionLabel(action);
                    document.getElementById('auditModalDate').textContent = createdAt;
                    document.getElementById('auditModalUser').textContent = user;
                    document.getElementById('auditModalProcess').textContent = processId;
                    document.getElementById('auditModalType').textContent = type;
                    document.getElementById('auditModalContext').textContent = context;
                    document.getElementById('auditModalRequestId').textContent = requestId || '-';
                    document.getElementById('auditModalUrl').textContent = url || '-';
                    const isGroup = Array.isArray(groupItems) && groupItems.length > 0;
                    const groupWrapper = document.getElementById('auditModalGroupWrapper');
                    const groupContainer = document.getElementById('auditModalGroupContainer');

                    if (isGroup) {
                        groupWrapper.style.display = '';
                        renderGroupItems(groupItems);
                        document.getElementById('auditModalFieldsContainer').innerHTML =
                            '<p class="text-muted mb-0">Lote com ' + groupItems.length + ' itens.</p>';
                        document.getElementById('auditModalCotacaoWrapper').style.display = 'none';
                    } else {
                        groupWrapper.style.display = 'none';
                        groupContainer.innerHTML = '';
                        renderChangedFields(fields, oldValues, newValues);
                    }

                    const modal = new bootstrap.Modal(document.getElementById('auditDetailModal'));
                    modal.show();
                });
            });
        });
    </script>

    <style>
        #auditDetailModal .modal-dialog {
            max-width: 1200px;
            width: 95vw;
        }

        #auditDetailModal .modal-body {
            padding: 1.5rem;
        }

        #auditModalFieldsContainer table {
            font-size: 0.9rem;
            table-layout: fixed;
            width: 100%;
        }

        #auditModalFieldsContainer table td {
            vertical-align: middle;
            word-break: break-word;
            white-space: normal;
        }

        #auditModalFieldsContainer table td:first-child {
            background-color: #f8f9fa;
            font-weight: 500;
        }

        #auditModalFieldsContainer .table-responsive {
            max-height: 520px;
            overflow-y: auto;
        }

        #auditModalGroupContainer table {
            table-layout: fixed;
            width: 100%;
        }

        #auditModalGroupContainer table td {
            vertical-align: middle;
            word-break: break-word;
            white-space: normal;
        }

        #auditModalFieldsContainer .field-value,
        #auditModalGroupContainer .field-value {
            white-space: normal;
            word-break: break-word;
        }

        #auditModalFieldsContainer .field-description,
        #auditModalGroupContainer .field-description {
            max-width: 380px;
        }

        #auditModalGroupContainer .group-item {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
            background: #fff;
        }

        #auditModalGroupContainer details summary {
            cursor: pointer;
            font-weight: 600;
        }

        #auditModalGroupContainer details[open] summary {
            margin-bottom: 8px;
        }

        #auditModalCotacaoContainer .cotacao-compare th,
        #auditModalCotacaoContainer .cotacao-compare td {
            font-size: 0.82rem;
            white-space: nowrap;
        }

        #auditModalCotacaoContainer .cotacao-compare td:nth-child(2) {
            white-space: normal;
            min-width: 220px;
        }

        .select2-container .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }

        .btn-view-audit {
            display: flex;
            
            padding: 0.35rem 0.75rem !important;
        }

        .audit-filter-btn {
            margin-top: 1.35rem;
        }
    </style>
@endsection
