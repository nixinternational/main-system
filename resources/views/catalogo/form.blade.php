@extends('layouts.app')
@section('title', isset($catalogo) ? 'Catálogo' : 'Cadastrar catálogo')


@section('content')

    <div class="card shadow-sm">
        <div class="card-header" style="background: var(--theme-gradient-primary);">
            <h3 class="card-title mb-0 text-white">
                <i class="fas fa-book me-2"></i>{{ isset($catalogo) ? 'Editar Catálogo' : 'Novo Catálogo' }}
            </h3>
        </div>
        <div class="card-body">

                    <form enctype="multipart/form-data" method="POST" action="{{ route('catalogo.store') }}">
                        @csrf
                        @if (isset($catalogo))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-4">
                                <label for="exampleInputEmail1" class="form-label">Cliente</label>

                                <select {{ isset($catalogo) ? 'disabled' : '' }} class="custom-select select2"
                                    name="cliente_id">
                                    <option selected disabled>Selecione uma opção</option>
                                    @foreach ($clientes as $cliente)
                                        <option
                                            {{ isset($catalogo) && $catalogo->cliente_id == $cliente->id ? 'selected' : '' }}
                                            value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if (!isset($catalogo))
                            <button type="submit" class="btn btn-primary mt-3">Salvar</button>
                        @endif
                    </form>
                    @if (isset($catalogo))

                        <div style="height: 0.1px; width: 100%; border: 0.1px solid #cecece;" class="my-3">
                        </div>
                        <form class="mr-2 d-flex justify-content-between" id="formSearch"
                            action="{{ route('catalogo.edit', $id) }}" method="GET">
                            <div class="d-flex ">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i
                                                class="fas fa-search"></i></span>
                                    </div>
                                    <input value="{{ $_GET['search'] ?? '' }}" type="text" id="search" name="search"
                                        class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
                                    <a href="{{ route('catalogo.edit', $id) }}" class="btn btn-primary">Limpar busca</a>

                                </div>
                            </div>
                        </form>
                        <div class="row w-100">

                            <div class="col-12 d-flex mb-2" style="justify-content: space-between; align-items: center;">
                                <h4>Produtos</h4>
                                <button type="button" data-toggle="modal" data-target="#exampleModal"
                                    class="btn btn-secondary mt-3">Adicionar Produto</button>
                            </div>

                            @if (!$produtos->isEmpty())
                                {{ $produtos->appends([]) }}
                                <table id="produtosTable" class="table shadow rounded table-striped table-hover">
                                    <thead style="background: var(--theme-gradient-primary);">
                                        <tr>
                                            <th>{!! sortable('modelo', 'Modelo', 'catalogo.edit') !!}</th>
                                            <th>{!! sortable('codigo', 'Codigo', 'catalogo.edit') !!}</th>
                                            <th>{!! sortable('ncm', 'NCM', 'catalogo.edit') !!}</th>
                                            <th class="text-white">Fornecedor</th>
                                            <th>{!! sortable('descricao', 'Descrição', 'catalogo.edit') !!}</th>
                                            <th>{!! sortable('created_at', 'Data de Criação', 'catalogo.edit') !!}</th>
                                            <th class="text-center text-white">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($produtos as $produto)
                                            <tr>

                                                <td>{{ $produto->modelo }}</td>
                                                <td>{{ $produto->codigo }}</td>
                                                <td>{{ $produto->ncm }}</td>
                                                <td>{{$produto->fornecedor->nome ?? '-'}}</td>
                                                <td>{{ \Illuminate\Support\Str::limit($produto->descricao, 100) }}
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($produto->created_at)->format('d/m/Y H:i') }}
                                                </td>

                                                <td>
                                                    <div class="d-flex justify-content-center" style="gap: 8px;">
                                                        <button type="button" data-modelo="{{ $produto->modelo }}"
                                                            data-ncm="{{ $produto->ncm }}"
                                                            data-codigo="{{ $produto->codigo }}"
                                                            data-descricao="{{ $produto->descricao }}"
                                                            data-fornecedor="{{ $produto->fornecedor->id ?? '' }}"
                                                            data-id="{{ $produto->id }}" data-toggle="modal"
                                                            data-target="#editProductModal"
                                                            class="btn btn-sm btn-warning editModal" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form method="POST"
                                                            action="{{ route('produto.destroy', $produto->id) }}"
                                                            enctype="multipart/form-data" class="d-inline">
                                                            @method('DELETE')
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            @else
                                <x-not-found />
                            @endif

                        </div>
                    @endif
        </div>
    </div>

    <div class="modal fade" id="descricaoModal" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-xl">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Descrição de <span id="nameProductDescription"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body w-100">
                    <p class="text-break" id="descriptionProduct"></p>
                </div>
            </div>
        </div>
    </div>
    @if (isset($catalogo))
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Cadastro de Produto</h5>
                        <button type="button" class="close btn-close-modal" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="formAddProduct" action="{{ route('produto.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="page" value="{{ $_GET['page'] ?? '1' }}">

                        <input type="hidden" value="{{ $catalogo->id }}" name="catalogo_id">
                        <input type="hidden" id="add_more" name="add_more" value="0"> {{-- default = não adicionar mais --}}

                        <div class="modal-body">
                            @if ($errors->has('produto'))
                                <div class="alert alert-danger text-sm mb-3">
                                    {{ $errors->first('produto') }}
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="modelo" class="font-weight-bold">Modelo</label>
                                    <input type="text" class="form-control @error('modelo') is-invalid @enderror" id="modelo" name="modelo"
                                        value="{{ old('modelo') }}">
                                    @error('modelo')
                                        <span class="invalid-feedback d-block"><small>{{ $message }}</small></span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="codigo" class="font-weight-bold">Código</label>
                                    <input type="text" class="form-control @error('codigo') is-invalid @enderror" id="codigo" name="codigo"
                                        value="{{ old('codigo') }}">
                                    @error('codigo')
                                        <span class="invalid-feedback d-block"><small>{{ $message }}</small></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="ncm" class="font-weight-bold">NCM</label>
                                    <input type="text" class="form-control @error('ncm') is-invalid @enderror" id="ncm" name="ncm"
                                        value="{{ old('ncm') }}">
                                    @error('ncm')
                                        <span class="invalid-feedback d-block"><small>{{ $message }}</small></span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="fornecedor_id" class="font-weight-bold">Fornecedor</label>

                                    <select name="fornecedor_id" class="form-control select2 w-100 @error('fornecedor_id') is-invalid @enderror" id="paises">
                                        <option value="" {{ old('fornecedor_id') ? '' : 'selected' }}>Selecione um país</option>

                                        @foreach ($catalogo->cliente->fornecedores as $fornecedor)
                                            <option value="{{ $fornecedor->id }}" {{ old('fornecedor_id') == $fornecedor->id ? 'selected' : '' }}>{{ $fornecedor->nome }}</option>
                                        @endforeach
                                    </select>
                                    @error('fornecedor_id')
                                        <span class="invalid-feedback d-block"><small>{{ $message }}</small></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">

                                    <label for="descricao" class="font-weight-bold">Descrição</label>
                                    <textarea rows="3" class="form-control @error('descricao') is-invalid @enderror" id="descricao" name="descricao">{{ old('descricao') }}</textarea>
                                    @error('descricao')
                                        <span class="invalid-feedback d-block"><small>{{ $message }}</small></span>
                                    @enderror

                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-close-modal" data-dismiss="modal">Fechar</button>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                            <button type="submit" class="btn btn-success"
                                onclick="document.getElementById('add_more').value=1">
                                Adicionar mais produtos
                            </button>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    @endif
    @if (isset($catalogo))
        <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edição de Produto</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="formEdit" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="page" value="{{ $_GET['page'] ?? '1' }}">
                        <input type="hidden" id="add_more_edit" name="add_more_edit" value="0">
                        {{-- default = não adicionar mais --}}

                        <div class="modal-body">
                            @if ($errors->has('produto_edit'))
                                <div class="alert alert-danger text-sm mb-3">
                                    {{ $errors->first('produto_edit') }}
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="modelo" class="font-weight-bold">Modelo</label>
                                    <input type="text" id="modelo_edit" name="modelo_edit" class="form-control @error('modelo_edit') is-invalid @enderror"
                                        value="{{ old('modelo_edit') }}">
                                    @error('modelo_edit')
                                        <span class="invalid-feedback d-block"><small>{{ $message }}</small></span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="codigo" class="font-weight-bold">Código</label>
                                    <input type="text" class="form-control @error('codigo_edit') is-invalid @enderror" id="codigo_edit" name="codigo_edit"
                                        value="{{ old('codigo_edit') }}">
                                    @error('codigo_edit')
                                        <span class="invalid-feedback d-block"><small>{{ $message }}</small></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="ncm" class="font-weight-bold">NCM</label>
                                    <input type="text" class="form-control @error('ncm_edit') is-invalid @enderror" id="ncm_edit" name="ncm_edit"
                                        value="{{ old('ncm_edit') }}">
                                    @error('ncm_edit')
                                        <span class="invalid-feedback d-block"><small>{{ $message }}</small></span>
                                    @enderror
                                </div>
                                 <div class="col-6">
                                    <label for="fornecedor_id" class="font-weight-bold">Fornecedor</label>

                                    <select name="fornecedor_id_edit" class="form-control select2 w-100 @error('fornecedor_id_edit') is-invalid @enderror" id="fornecedor_id_edit">
                                        <option value="" {{ old('fornecedor_id_edit') ? '' : 'selected' }}>Selecione um país</option>

                                        @foreach ($catalogo->cliente->fornecedores as $fornecedor)
                                            <option value="{{ $fornecedor->id }}" {{ old('fornecedor_id_edit') == $fornecedor->id ? 'selected' : '' }}>{{ $fornecedor->nome }}</option>
                                        @endforeach
                                    </select>
                                    @error('fornecedor_id_edit')
                                        <span class="invalid-feedback d-block"><small>{{ $message }}</small></span>
                                    @enderror
                                </div>
                               
                            </div>
                            <div class="row">
                                 <div class="col-md-12">
                                    <label for="descricao" class="font-weight-bold">Descrição</label>
                                    <textarea rows="3" class="form-control @error('descricao_edit') is-invalid @enderror" id="descricao_edit" name="descricao_edit">{{ old('descricao_edit') }}</textarea>
                                    @error('descricao_edit')
                                        <span class="invalid-feedback d-block"><small>{{ $message }}</small></span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                            <button type="submit" class="btn btn-success"
                                onclick="document.getElementById('add_more_edit').value=1">
                                Adicionar mais produtos
                            </button>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    @endif
    <script>
        $(document).ready(function($) {

            $('.descricaoModalButton').on('click', function() {
                $('#nameProductDescription').text(this.dataset.nome)
                $('#descriptionProduct').text(this.dataset.descricao)
            })

            $('.editModal').on('click', function() {
                const produtoId = this.dataset.id;
                $('#formEdit').attr('action', `/produto/${produtoId}`)
                $('#modelo_edit').val(this.dataset.modelo || '')
                $('#ncm_edit').val(this.dataset.ncm || '')
                $('#descricao_edit').val(this.dataset.descricao || '')
                $('#codigo_edit').val(this.dataset.codigo || '')
                $('#fornecedor_id_edit').val(this.dataset.fornecedor || '').trigger('change')
            })
            
            // Garantir que os campos sejam preenchidos quando o modal abrir
            $('#editProductModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                if (button.hasClass('editModal')) {
                    const produtoId = button.data('id');
                    $('#formEdit').attr('action', `/produto/${produtoId}`)
                    $('#modelo_edit').val(button.data('modelo') || '')
                    $('#ncm_edit').val(button.data('ncm') || '')
                    $('#descricao_edit').val(button.data('descricao') || '')
                    $('#codigo_edit').val(button.data('codigo') || '')
                    $('#fornecedor_id_edit').val(button.data('fornecedor') || '').trigger('change')
                }
            })


            $('.select2').select2({
                placeholder: 'Selecione um fornecedor',
                allowClear: true,
                width: '100%'
            });
            
            // Garantir que o botão de fechar sempre funcione, mesmo com validação HTML5
            $(document).on('click', '.btn-close-modal', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Marcar que estamos fechando o modal para prevenir submit
                $('#formAddProduct').data('closing', true);
                
                // Resetar o valor de add_more
                $('#add_more').val('0');
                
                // Desabilitar validação HTML5 temporariamente
                const form = document.getElementById('formAddProduct');
                if (form) {
                    form.setAttribute('novalidate', 'novalidate');
                }
                
                // Limpar campos do formulário
                $('#exampleModal input[type="text"]').val('');
                $('#exampleModal textarea').val('');
                $('#exampleModal select').val('').trigger('change');
                
                // Remover classes de validação
                $('#exampleModal').find('.is-invalid').removeClass('is-invalid');
                $('#exampleModal').find('.invalid-feedback').remove();
                
                // Fechar o modal imediatamente
                $('#exampleModal').modal('hide');
                
                // Reabilitar validação após um pequeno delay
                setTimeout(function() {
                    if (form) {
                        form.removeAttribute('novalidate');
                    }
                }, 100);
            });
            
            // Prevenir submit do formulário quando o botão de fechar for clicado
            $('#formAddProduct').on('submit', function(e) {
                // Se o botão de fechar foi clicado recentemente, não submeter
                if ($(this).data('closing')) {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Limpar campos quando o modal for fechado
            $('#exampleModal').on('hidden.bs.modal', function() {
                // Resetar o valor de add_more
                $('#add_more').val('0');
                
                // Limpar todos os campos
                $(this).find('input[type="text"]').val('');
                $(this).find('textarea').val('');
                $(this).find('select').val('').trigger('change');
                
                // Remover classes de validação
                $(this).find('.is-invalid').removeClass('is-invalid');
                $(this).find('.invalid-feedback').remove();
                
                // Resetar flag de fechamento
                $('#formAddProduct').data('closing', false);
            });
        })

        setTimeout(() => {
            document.getElementById('search').addEventListener('change', function() {
                document.getElementById('formSearch').submit()
            })
        }, 500);
    </script>
    @if (session('open_modal') === 'exampleModal')
        <script>
            $(document).ready(function() {
                $('#add_more').val('0');
                $('#exampleModal').modal('show');
            });
        </script>
    @endif
    @if (session('open_modal') === 'editProductModal')
        <script>
            $(document).ready(function() {
                const produtoId = "{{ session('edit_product_id') }}";
                if (produtoId) {
                    $('#formEdit').attr('action', `/produto/${produtoId}`);
                }
                $('#editProductModal').modal('show');
            });
        </script>
    @endif
@endsection
