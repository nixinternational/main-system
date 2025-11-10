@extends('layouts.app')
@section('title', 'Tipos de Documento')

@section('actions')
    <a href="{{ route('tipo-documento.create') }}" class="btn btn-primary">
        Cadastrar tipo de documento
    </a>
@endsection


@section('content')
    <div class="card shadow-sm">
        <div class="card-header" style="background: linear-gradient(135deg, #b7aa09 0%, #9a8e08 100%);">
            <h3 class="card-title mb-0 text-white">
                <i class="fas fa-file-alt me-2"></i>Listagem de Tipos de Documento
            </h3>
        </div>
        <div class="card-body">
            <form class="mb-4" id="formSearch" action="{{ route('tipo-documento.index') }}" method="GET">
                <div class="row mb-3">
                    <div class="col-md-8 col-sm-12 mb-2 mb-md-0">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                            <input value="{{ $_GET['search'] ?? '' }}" type="text" id="search" name="search" 
                                class="form-control" placeholder="Buscar por nome, descrição..." 
                                aria-label="Buscar" aria-describedby="basic-addon1">
                            <button type="button" onclick="window.location.href='{{ route('tipo-documento.index') }}'" 
                                class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Limpar
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="d-flex justify-content-end align-items-center" style="gap: 8px;">
                            <label for="paginacao" class="mb-0 small">Itens por página:</label>
                            <select id="paginacao" name="paginacao" class="form-control form-control-sm" style="width: auto; min-width: 80px">
                                <option value="10" {{ isset($_GET['paginacao']) && $_GET['paginacao'] == '10' ? 'selected' : '' }}>10</option>
                                <option value="20" {{ isset($_GET['paginacao']) && $_GET['paginacao'] == '20' ? 'selected' : '' }}>20</option>
                                <option value="30" {{ isset($_GET['paginacao']) && $_GET['paginacao'] == '30' ? 'selected' : '' }}>30</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        {{ $tipoDocumentos->appends(['paginacao' => $_GET['paginacao'] ?? 10])->links() }}
                    </div>
                </div>
            </form>

            @if (!$tipoDocumentos->isEmpty())
                <div class="table-responsive">
                    <table id="tipoDocumentoTable" class="table table-striped table-hover mb-0">
                        <thead style="background: linear-gradient(135deg, #b7aa09 0%, #9a8e08 100%);">
                            <tr>
                                <th>{!! sortable('id', 'ID') !!}</th>
                                <th>{!! sortable('nome', 'Nome') !!}</th>
                                <th class="text-white">Descrição</th>
                                <th class="text-center text-white">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tipoDocumentos as $tipoDocumento)
                                <tr @if ($tipoDocumento->deleted_at != null) class="table-danger" @endif>
                                    <td>{{ $tipoDocumento->id }}</td>
                                    <td>{{ $tipoDocumento->nome }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($tipoDocumento->descricao ?? '-', 100) }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center" style="gap: 8px;">
                                            @if ($tipoDocumento->deleted_at == null)
                                                <a href="{{ route('tipo-documento.edit', $tipoDocumento->id) }}" 
                                                    class="btn btn-sm btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            <form method="POST"
                                                action="{{ route($tipoDocumento->deleted_at == null ? 'tipo-documento.destroy' : 'tipo-documento.ativar', $tipoDocumento->id) }}"
                                                enctype="multipart/form-data" class="d-inline">
                                                @if ($tipoDocumento->deleted_at == null)
                                                    @method('DELETE')
                                                @else
                                                    @method('PUT')
                                                @endif
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-sm {{ $tipoDocumento->deleted_at == null ? 'btn-danger' : 'btn-success' }}"
                                                    title="{{ $tipoDocumento->deleted_at == null ? 'Desativar' : 'Ativar' }}">
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
