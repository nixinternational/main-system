@extends('layouts.app')
@section('title', 'Processos')

@section('actions')
    <a href="{{ route('currency.update') }}" class="btn btn-primary">
        Atualizar moedas
    </a>
@endsection


@section('content')
    <div class="card shadow-sm">
        <div class="card-header" style="background: linear-gradient(135deg, #b7aa09 0%, #9a8e08 100%);">
            <h3 class="card-title mb-0 text-white">
                <i class="fas fa-file-alt me-2"></i>Listagem de Processos
            </h3>
        </div>
        <div class="card-body">
            <form class="mb-4" id="formSearch" action="{{ route('cliente.index') }}" method="GET">
                <div class="row mb-3">
                    <div class="col-md-8 col-sm-12 mb-2 mb-md-0">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                            <input value="{{ $_GET['search'] ?? '' }}" type="text" id="search" name="search" 
                                class="form-control" placeholder="Buscar por nome do cliente..." 
                                aria-label="Buscar" aria-describedby="basic-addon1">
                            <button type="button" onclick="window.location.href='{{ route('cliente.index') }}'" 
                                class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Limpar
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
                        <thead style="background: linear-gradient(135deg, #b7aa09 0%, #9a8e08 100%);">
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
        document.getElementById('search').addEventListener('change', function() {
            document.getElementById('formSearch').submit()
        })
        document.getElementById('paginacao').addEventListener('change', function() {
            document.getElementById('formSearch').submit()
        })
    </script>
@endpush
