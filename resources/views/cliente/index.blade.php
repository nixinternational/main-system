@extends('layouts.app')
@section('title', 'Cadastro de Clientes')

@section('actions')
    <a href="{{ route('cliente.create') }}" class="btn btn-primary">
        Cadastrar cliente
    </a>
@endsection


@section('content')
    <div class="card shadow-sm">
        <div class="card-header" style="background: linear-gradient(135deg, #b7aa09 0%, #9a8e08 100%);">
            <h3 class="card-title mb-0 text-white">
                <i class="fas fa-list me-2"></i>Listagem de Clientes
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
                                class="form-control" placeholder="Buscar por nome, CNPJ, cidade..." 
                                aria-label="Buscar" aria-describedby="basic-addon1">
                            <button type="button" onclick="window.location.href='{{ route('cliente.index') }}'" 
                                class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Limpar
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="d-flex justify-content-end">
                            {{ $clientes->appends(['paginacao' => $_GET['paginacao'] ?? 10])->links() }}
                        </div>
                    </div>
                </div>
            </form>

            @if (!$clientes->isEmpty())
                <div class="table-responsive">
                    <table id="clienteTable" class="table table-striped table-hover mb-0">
                        <thead style="background: linear-gradient(135deg, #b7aa09 0%, #9a8e08 100%);">
                            <tr>
                                <th>{!! sortable('name', 'Nome') !!}</th>
                                <th>{!! sortable('cnpj', 'CNPJ') !!}</th>
                                <th class="text-white">Resp. Legal</th>
                                <th class="text-white">Data Venc. Procuração</th>
                                <th>{!! sortable('cidade', 'Cidade') !!}</th>
                                <th class="text-white">Modalidade Radar</th>
                                <th class="text-center text-white">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clientes as $cliente)
                                <tr @if ($cliente->deleted_at != null) class="table-danger" @endif>
                                    <td>{{ $cliente->nome }}</td>
                                    <td>{{ $cliente->cnpj }}</td>
                                    <td>{{ $cliente->nome_responsavel_legal ?? '-' }}</td>
                                    <td>{{ $cliente->data_vencimento_procuracao ? \Carbon\Carbon::parse($cliente->data_vencimento_procuracao)->format('d/m/Y') : 'Sem procuração' }}</td>
                                    <td>{{ $cliente->cidade ?? '-' }}</td>
                                    <td>{{ $cliente->modalidade_radar ? ucfirst($cliente->modalidade_radar) : 'Não possui' }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center" style="gap: 8px;">
                                            @if ($cliente->deleted_at == null)
                                                <a href="{{ route('cliente.edit', $cliente->id) }}" 
                                                    class="btn btn-sm btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            <form method="POST"
                                                action="{{ route($cliente->deleted_at == null ? 'cliente.destroy' : 'cliente.ativar', $cliente->id) }}"
                                                enctype="multipart/form-data" class="d-inline">
                                                @if ($cliente->deleted_at == null)
                                                    @method('DELETE')
                                                @else
                                                    @method('PUT')
                                                @endif
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-sm {{ $cliente->deleted_at == null ? 'btn-danger' : 'btn-success' }}"
                                                    title="{{ $cliente->deleted_at == null ? 'Desativar' : 'Ativar' }}">
                                                    <i class="fa fa-power-off"></i>
                                                </button>
                                            </form>
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
