@extends('layouts.app')
@section('title', 'Logs do Sistema')

@section('actions')
    <div class="d-flex gap-2">
        <a href="{{ route('logs.download') }}" class="btn btn-info">
            <i class="fas fa-download"></i> Download
        </a>
        <button type="button" class="btn btn-danger" id="btnLimparLogs">
            <i class="fas fa-trash"></i> Limpar Logs
        </button>
    </div>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-header" style="background: var(--theme-gradient-primary);">
            <h3 class="card-title mb-0 text-white">
                <i class="fas fa-file-alt me-2"></i>Logs do Servidor
            </h3>
        </div>
        <div class="card-body">
            <form class="mb-4" id="formSearch" action="{{ route('logs.index') }}" method="GET">
                <div class="row mb-3">
                    <div class="col-md-8 col-sm-12 mb-2 mb-md-0">
                        <div class="d-flex gap-2 align-items-stretch">
                            <div class="input-group flex-grow-1">
                                <input value="{{ $search }}" type="text" id="search" name="search" 
                                    class="form-control" placeholder="Buscar nos logs..." 
                                    aria-label="Buscar">
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                                <span>Buscar</span>
                            </button>
                            @if($search)
                                <a href="{{ route('logs.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i>
                                    <span>Limpar</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="d-flex justify-content-end align-items-center">
                            <span class="text-muted">
                                @if($search)
                                    {{ number_format($filteredTotal ?? $totalLines, 0, ',', '.') }} de {{ number_format($totalLines, 0, ',', '.') }} linhas
                                @else
                                    Total: {{ number_format($totalLines, 0, ',', '.') }} linhas
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 80px;">#</th>
                            <th style="width: 100px;">Tipo</th>
                            <th>Log (Preview)</th>
                            <th style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($logs) > 0)
                            @foreach($logs as $index => $log)
                                @php
                                    $lineNumber = ($currentPage - 1) * $perPage + $index + 1;
                                    // Detectar tipo de log para colorir
                                    $logType = 'info';
                                    $logClass = '';
                                    $logIcon = '';
                                    $logBadge = '';
                                    
                                    if (stripos($log, 'error') !== false || stripos($log, 'exception') !== false) {
                                        $logType = 'error';
                                        $logClass = 'table-danger';
                                        $logIcon = '<i class="fas fa-exclamation-circle text-danger"></i>';
                                        $logBadge = '<span class="badge bg-danger">ERROR</span>';
                                    } elseif (stripos($log, 'warning') !== false) {
                                        $logType = 'warning';
                                        $logClass = 'table-warning';
                                        $logIcon = '<i class="fas fa-exclamation-triangle text-warning"></i>';
                                        $logBadge = '<span class="badge bg-warning text-dark">WARNING</span>';
                                    } elseif (stripos($log, 'info') !== false) {
                                        $logType = 'info';
                                        $logClass = 'table-info';
                                        $logIcon = '<i class="fas fa-info-circle text-info"></i>';
                                        $logBadge = '<span class="badge bg-info">INFO</span>';
                                    } elseif (stripos($log, 'debug') !== false) {
                                        $logType = 'debug';
                                        $logClass = 'table-secondary';
                                        $logIcon = '<i class="fas fa-bug text-secondary"></i>';
                                        $logBadge = '<span class="badge bg-secondary">DEBUG</span>';
                                    } else {
                                        $logBadge = '<span class="badge bg-secondary">LOG</span>';
                                    }
                                    
                                    // Truncar log para preview (primeiras 150 caracteres)
                                    $logPreview = strlen($log) > 150 ? substr($log, 0, 150) . '...' : $log;
                                @endphp
                                <tr class="{{ $logClass }}">
                                    <td class="text-center align-middle">
                                        <small class="text-muted">{{ $lineNumber }}</small>
                                    </td>
                                    <td class="text-center align-middle">
                                        {!! $logBadge !!}
                                    </td>
                                    <td>
                                        <div class="log-preview">
                                            <span class="mr-2">{!! $logIcon !!}</span>
                                            <code class="log-text">{{ $logPreview }}</code>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-view-log" 
                                            data-log="{{ htmlspecialchars($log, ENT_QUOTES, 'UTF-8') }}" 
                                            data-line="{{ $lineNumber }}"
                                            data-type="{{ $logType }}"
                                            title="Ver detalhes">
                                            <i class="fas fa-eye me-1"></i> Ver
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Nenhum log encontrado</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if(count($logs) > 0)
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">
                            @php
                                $filteredTotalDisplay = $filteredTotal ?? $totalLines;
                                $start = (($currentPage - 1) * $perPage) + 1;
                                $end = min($currentPage * $perPage, $filteredTotalDisplay);
                            @endphp
                            Mostrando {{ number_format($start, 0, ',', '.') }} até {{ number_format($end, 0, ',', '.') }} de {{ number_format($filteredTotalDisplay, 0, ',', '.') }} linhas
                        </small>
                    </div>
                    <div>
                        @if($currentPage > 1)
                            <a href="{{ route('logs.index', ['page' => $currentPage - 1, 'search' => $search]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-chevron-left"></i> Anterior
                            </a>
                        @endif
                        <span class="mx-2">Página {{ $currentPage }}</span>
                        @if(count($logs) == $perPage)
                            <a href="{{ route('logs.index', ['page' => $currentPage + 1, 'search' => $search]) }}" class="btn btn-sm btn-outline-primary">
                                Próxima <i class="fas fa-chevron-right"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para visualização detalhada do log -->
    <div class="modal fade" id="logDetailModal" tabindex="-1" aria-labelledby="logDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logDetailModalLabel">
                        <i class="fas fa-file-alt me-2"></i>Detalhes do Log - Linha <span id="modalLineNumber"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo:</label>
                        <div id="modalLogType"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Conteúdo Completo:</label>
                        <div class="log-detail-container">
                            <pre id="modalLogContent" class="log-detail-content"></pre>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Copiar Log:</label>
                        <div class="input-group">
                            <textarea id="modalLogCopy" class="form-control font-monospace" rows="3" readonly></textarea>
                            <button class="btn btn-outline-secondary" type="button" id="btnCopyLog" title="Copiar para área de transferência">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Handler para visualizar log detalhado
            $(document).on('click', '.btn-view-log', function() {
                const logContent = $(this).data('log');
                const lineNumber = $(this).data('line');
                const logType = $(this).data('type');
                
                // Atualizar modal
                $('#modalLineNumber').text(lineNumber);
                $('#modalLogContent').text(logContent);
                $('#modalLogCopy').val(logContent);
                
                // Atualizar badge de tipo
                let badgeHtml = '';
                switch(logType) {
                    case 'error':
                        badgeHtml = '<span class="badge bg-danger">ERROR</span>';
                        $('#modalLogContent').removeClass().addClass('log-detail-content log-error');
                        break;
                    case 'warning':
                        badgeHtml = '<span class="badge bg-warning text-dark">WARNING</span>';
                        $('#modalLogContent').removeClass().addClass('log-detail-content log-warning');
                        break;
                    case 'info':
                        badgeHtml = '<span class="badge bg-info">INFO</span>';
                        $('#modalLogContent').removeClass().addClass('log-detail-content log-info');
                        break;
                    case 'debug':
                        badgeHtml = '<span class="badge bg-secondary">DEBUG</span>';
                        $('#modalLogContent').removeClass().addClass('log-detail-content log-debug');
                        break;
                    default:
                        badgeHtml = '<span class="badge bg-secondary">LOG</span>';
                        $('#modalLogContent').removeClass().addClass('log-detail-content');
                }
                $('#modalLogType').html(badgeHtml);
                
                // Mostrar modal
                const modal = new bootstrap.Modal(document.getElementById('logDetailModal'));
                modal.show();
            });
            
            // Handler para copiar log
            $('#btnCopyLog').on('click', function() {
                const logText = $('#modalLogCopy').val();
                navigator.clipboard.writeText(logText).then(function() {
                    $(this).html('<i class="fas fa-check"></i> Copiado!');
                    const btn = $(this);
                    setTimeout(function() {
                        btn.html('<i class="fas fa-copy"></i> Copiar');
                    }, 2000);
                }.bind(this)).catch(function(err) {
                    // Fallback para navegadores mais antigos
                    $('#modalLogCopy').select();
                    document.execCommand('copy');
                    $(this).html('<i class="fas fa-check"></i> Copiado!');
                    const btn = $(this);
                    setTimeout(function() {
                        btn.html('<i class="fas fa-copy"></i> Copiar');
                    }, 2000);
                }.bind(this));
            });
            
            // Handler para limpar logs
            $('#btnLimparLogs').on('click', function() {
                Swal.fire({
                    title: 'Tem certeza?',
                    text: 'Esta ação irá limpar todos os logs do sistema. Esta ação não pode ser desfeita!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sim, limpar logs!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("logs.clear") }}',
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Sucesso!',
                                        text: response.message,
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Erro!',
                                        text: response.message,
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Erro!',
                                    text: 'Erro ao limpar logs. Tente novamente.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>

    <style>
        .log-preview {
            display: flex;
            align-items: flex-start;
            max-height: 60px;
            overflow: hidden;
        }
        
        .log-text {
            font-size: 0.85rem;
            background-color: #f8f9fa;
            padding: 4px 8px;
            border-radius: 4px;
            margin: 0;
            white-space: pre-wrap;
            word-break: break-word;
            display: block;
            flex: 1;
            max-height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .table-danger .log-text {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .table-warning .log-text {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .table-info .log-text {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .table-secondary .log-text {
            background-color: #e2e3e5;
            color: #383d41;
        }
        
        .log-detail-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background-color: #f8f9fa;
            padding: 12px;
        }
        
        .log-detail-content {
            font-size: 0.875rem;
            font-family: 'Courier New', monospace;
            white-space: pre-wrap;
            word-break: break-word;
            margin: 0;
            padding: 0;
            background: transparent;
            border: none;
            color: #212529;
        }
        
        .log-detail-content.log-error {
            color: #721c24;
        }
        
        .log-detail-content.log-warning {
            color: #856404;
        }
        
        .log-detail-content.log-info {
            color: #0c5460;
        }
        
        .log-detail-content.log-debug {
            color: #383d41;
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
        
        .btn-view-log {
            padding: 0.4rem 0.9rem !important;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        
        .btn-view-log:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
        }
        
        .btn-view-log i {
            font-size: 0.9rem;
        }
        
        table tbody tr {
            height: auto;
        }
        
        table tbody td {
            vertical-align: middle;
        }
        
        .modal-body {
            max-height: calc(100vh - 200px);
        }
        
        @media (max-width: 768px) {
            .log-preview {
                max-height: 80px;
            }
            
            .log-text {
                max-height: 80px;
                font-size: 0.75rem;
            }
            
            .btn-view-log {
                padding: 0.35rem 0.7rem !important;
                font-size: 0.8rem;
            }
        }
    </style>
@endsection
