@extends('layouts.app')
@section('title', 'Processos')

@section('actions')
    <a href="{{ route('currency.update') }}" class="btn btn-primary">
        Atualizar moedas
    </a>
@endsection


@section('content')
    <div class="card shadow-sm">
        <div class="card-header" style="background: var(--theme-gradient-primary);">
            <h3 class="card-title mb-0 text-white">
                <i class="fas fa-file-alt me-2"></i>Listagem de Processos
            </h3>
        </div>
        <div class="card-body">
            <form class="mb-4" id="formSearch" action="{{ route('processo.index') }}" method="GET">
                <div class="row mb-3">
                    <div class="col-md-8 col-sm-12 mb-2 mb-md-0">
                        <div class="d-flex gap-2 align-items-stretch">
                            <div class="input-group flex-grow-1">
                                <input value="{{ $_GET['search'] ?? '' }}" type="text" id="search" name="search" 
                                    class="form-control" placeholder="Buscar por nome do cliente..." 
                                    aria-label="Buscar">
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                                <span>Buscar</span>
                            </button>
                            <button type="button" id="btnLimpar" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                                <span>Limpar</span>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="d-flex justify-content-end">
                            {{ $processos->appends(['paginacao' => $_GET['paginacao'] ?? 10])->links() }}
                        </div>
                    </div>
                </div>
            </form>

            @if (!$processos->isEmpty())
                <div class="table-responsive">
                    <table id="clienteTable" class="table table-striped table-hover mb-0">
                        <thead style="background: var(--theme-gradient-primary);">
                            <tr>
                                <th>{!! sortable('nome', 'Cliente') !!}</th>
                                <th class="text-center text-white">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($processos as $processo)
                                <tr>
                                    <td>{{ $processo->nome }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center" style="gap: 8px;">
                                            <a href="{{ route('processo-cliente', $processo->id) }}" 
                                                class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <x-not-found />
            @endif
        </div>
    </div>



@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formSearch = document.getElementById('formSearch');
            const searchInput = document.getElementById('search');
            const btnLimpar = document.getElementById('btnLimpar');
            const paginacao = document.getElementById('paginacao');
            
            // Botão limpar
            if (btnLimpar) {
                btnLimpar.addEventListener('click', function() {
                    searchInput.value = '';
                    const url = new URL(window.location.href);
                    url.searchParams.delete('search');
                    url.searchParams.delete('page');
                    window.location.href = url.toString();
                });
            }
            
            // Paginação
            if (paginacao) {
                paginacao.addEventListener('change', function() {
                    formSearch.submit();
                });
            }
        });
    </script>
@endpush
