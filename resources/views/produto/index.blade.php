@extends('layouts.app')
@section('title', 'Produtos')

@section('actions')
    <a href="{{ route('produto.create') }}" class="btn btn-primary">
        Cadastrar produto
    </a>
@endsection


@section('content')
    <div class="card shadow-sm">
        <div class="card-header" style="background: var(--theme-gradient-primary);">
            <h3 class="card-title mb-0 text-white">
                <i class="fas fa-box me-2"></i>Listagem de Produtos
            </h3>
        </div>
        <div class="card-body">
            <form class="mb-4" id="formSearch" action="{{ route('produto.index') }}" method="GET">
                <div class="row mb-3">
                    <div class="col-md-8 col-sm-12 mb-2 mb-md-0">
                        <div class="d-flex gap-2 align-items-stretch">
                            <div class="input-group flex-grow-1">
                                <input value="{{ $_GET['search'] ?? '' }}" type="text" id="search" name="search" 
                                    class="form-control" placeholder="Buscar por nome, código..." 
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
                        {{ $produtos->appends(['paginacao' => $_GET['paginacao'] ?? 10])->links() }}
                    </div>
                </div>
            </form>

            @if (!$produtos->isEmpty())
                <div class="table-responsive">
                    <table id="produtosTable" class="table table-striped table-hover mb-0">
                        <thead style="background: var(--theme-gradient-primary);">
                            <tr>
                                <th>{!! sortable('id', 'ID') !!}</th>
                                <th>{!! sortable('nome', 'Nome') !!}</th>
                                <th>{!! sortable('unidade', 'Unidade') !!}</th>
                                <th class="text-white">Preço A (R$)</th>
                                <th class="text-white">Preço B (R$)</th>
                                <th class="text-white">Preço C (R$)</th>
                                <th class="text-center text-white">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produtos as $produto)
                                <tr @if ($produto->deleted_at != null) class="table-danger" @endif>
                                    <td>{{ $produto->id }}</td>
                                    <td>{{ $produto->nome }}</td>
                                    <td>{{ $produto->unidade ?? '-' }}</td>
                                    <td>R$ {{ number_format($produto->precos['a'] ?? 0, 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($produto->precos['b'] ?? 0, 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($produto->precos['c'] ?? 0, 2, ',', '.') }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center" style="gap: 8px;">
                                            @if ($produto->deleted_at == null)
                                                <a href="{{ route('produto.edit', $produto->id) }}" 
                                                    class="btn btn-sm btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            <form method="POST"
                                                action="{{ route($produto->deleted_at == null ? 'produto.destroy' : 'produto.ativar', $produto->id) }}"
                                                enctype="multipart/form-data" class="d-inline">
                                                @if ($produto->deleted_at == null)
                                                    @method('DELETE')
                                                @else
                                                    @method('PUT')
                                                @endif
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-sm {{ $produto->deleted_at == null ? 'btn-danger' : 'btn-success' }}"
                                                    title="{{ $produto->deleted_at == null ? 'Desativar' : 'Ativar' }}">
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
    @include('produto.modalInfo')

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
