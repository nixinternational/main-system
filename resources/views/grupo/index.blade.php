@extends('layouts.app')
@section('title', 'Grupos de permissão')

@section('actions')
    <a href="{{ route('grupo.create') }}" class="btn btn-primary">
        Cadastrar grupo
    </a>
@endsection


@section('content')
    <div class="card shadow-sm">
        <div class="card-header" style="background: var(--theme-gradient-primary);">
            <h3 class="card-title mb-0 text-white">
                <i class="fas fa-users-cog me-2"></i>Listagem de Grupos de Permissão
            </h3>
        </div>
        <div class="card-body">
            <form class="mb-4" id="formSearch" action="{{ route('grupo.index') }}" method="GET">
                <div class="row mb-3">
                    <div class="col-md-8 col-sm-12 mb-2 mb-md-0">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                            <input value="{{ $_GET['search'] ?? '' }}" type="text" id="search" name="search" 
                                class="form-control" placeholder="Buscar por nome..." 
                                aria-label="Buscar" aria-describedby="basic-addon1">
                            <button type="button" onclick="window.location.href='{{ route('grupo.index') }}'" 
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
                        {{ $grupos->appends(['paginacao' => $_GET['paginacao'] ?? 10])->links() }}
                    </div>
                </div>
            </form>

            @if (!$grupos->isEmpty())
                <div class="table-responsive">
                    <table id="grupoTable" class="table table-striped table-hover mb-0">
                        <thead style="background: var(--theme-gradient-primary);">
                            <tr>
                                <th>{!! sortable('id', 'ID') !!}</th>
                                <th>{!! sortable('nome', 'Nome') !!}</th>
                                <th class="text-center text-white">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($grupos as $grupo)
                                <tr @if ($grupo->deleted_at != null) class="table-danger" @endif>
                                    <td>{{ $grupo->id }}</td>
                                    <td>{{ $grupo->nome }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center" style="gap: 8px;">
                                            @if ($grupo->deleted_at == null)
                                                <a href="{{ route('grupo.edit', $grupo->id) }}" 
                                                    class="btn btn-sm btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            <form method="POST"
                                                action="{{ route($grupo->deleted_at == null ? 'grupo.destroy' : 'grupo.ativar', $grupo->id) }}"
                                                enctype="multipart/form-data" class="d-inline">
                                                @if ($grupo->deleted_at == null)
                                                    @method('DELETE')
                                                @else
                                                    @method('PUT')
                                                @endif
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-sm {{ $grupo->deleted_at == null ? 'btn-danger' : 'btn-success' }}"
                                                    title="{{ $grupo->deleted_at == null ? 'Desativar' : 'Ativar' }}">
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
    <div class="modal fade" id="fotoModal" tabindex="-1" aria-labelledby="fotoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fotoModalLabel">
                        Foto da grupo: <b><span id="nomeFotogrupo"></span></b>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="">
                        <div class="d-flex justify-content-center">
                            <img src="" id="fotogrupo" alt="">
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>




@endsection

@push('scripts')
    <script>
        $('.infoFoto').on('click', function() {
            $('#fotogrupo').attr('src', this.dataset.url)
            $('#nomeFotogrupo').text(this.dataset.nome)
            $('#fotoModal').modal('show')
        })
        document.getElementById('search').addEventListener('change', function() {
            document.getElementById('formSearch').submit()
        })
        document.getElementById('paginacao').addEventListener('change', function() {
            document.getElementById('formSearch').submit()
        })
    </script>
@endpush
