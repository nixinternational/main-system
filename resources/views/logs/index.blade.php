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
                <i class="fas fa-file-alt me-2"></i>Logs do Sistema
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
                            <span class="text-muted mr-3">
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
                <table class="table table-sm table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 80px;">#</th>
                            <th>Log</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($logs) > 0)
                            @foreach($logs as $index => $log)
                                @php
                                    $lineNumber = ($currentPage - 1) * $perPage + $index + 1;
                                    // Detectar tipo de log para colorir
                                    $logClass = '';
                                    $logIcon = '';
                                    if (stripos($log, 'error') !== false || stripos($log, 'exception') !== false) {
                                        $logClass = 'table-danger';
                                        $logIcon = '<i class="fas fa-exclamation-circle text-danger"></i>';
                                    } elseif (stripos($log, 'warning') !== false) {
                                        $logClass = 'table-warning';
                                        $logIcon = '<i class="fas fa-exclamation-triangle text-warning"></i>';
                                    } elseif (stripos($log, 'info') !== false) {
                                        $logClass = 'table-info';
                                        $logIcon = '<i class="fas fa-info-circle text-info"></i>';
                                    } elseif (stripos($log, 'debug') !== false) {
                                        $logClass = 'table-secondary';
                                        $logIcon = '<i class="fas fa-bug text-secondary"></i>';
                                    }
                                @endphp
                                <tr class="{{ $logClass }}">
                                    <td class="text-center">
                                        <small class="text-muted">{{ $lineNumber }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-start">
                                            <span class="mr-2">{!! $logIcon !!}</span>
                                            <code class="text-dark" style="white-space: pre-wrap; word-break: break-all;">{{ $log }}</code>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="2" class="text-center py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">Nenhum log encontrado</p>
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

    <script>
        $(document).ready(function() {
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
        code {
            font-size: 0.85rem;
            background-color: #f8f9fa;
            padding: 2px 4px;
            border-radius: 3px;
        }
        .table-danger code {
            background-color: #f8d7da;
        }
        .table-warning code {
            background-color: #fff3cd;
        }
        .table-info code {
            background-color: #d1ecf1;
        }
        .table-secondary code {
            background-color: #e2e3e5;
        }
    </style>
@endsection

