@extends('layouts.app')
@section('title', isset($catalogo) ? 'Catálogo' : 'Cadastrar catálogo')


@section('content')

    <div class="row">
        <div class="col-12 shadow-lg px-0">
            <div class="card w-100 card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                        <li class="pt-2 px-3">
                            <h3 class="card-title text-dark font-weight-bold" style="">
                                {{ $catalogo?->nome ?? 'Novo Catálogo' }}</h3>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-tabs-two-home-tab" data-toggle="pill"
                                href="#custom-tabs-two-home" role="tab" aria-controls="custom-tabs-two-home"
                                aria-selected="false">Geral</a>
                        </li>

                    </ul>
                </div>
                <div class="card-body">

                    <form enctype="multipart/form-data" method="POST" action="{{ route('catalogo.store') }}">
                        @csrf
                        @if (isset($catalogo))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-4">
                                <label for="exampleInputEmail1" class="form-label">Nome</label>
                                <input value="{{ isset($catalogo) ? $catalogo->nome : '' }}" class="form-control"
                                    name="nome" id="nome">
                                @error('nome')
                                    <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                                @enderror
                            </div>

                            <div class="col-4">
                                <label for="exampleInputEmail1" class="form-label">Cliente</label>

                                <select class="custom-select select2" name="cliente_id">
                                    <option selected disabled>Selecione uma opção</option>
                                    @foreach ($clientes as $cliente)
                                        <option
                                            {{ isset($catalogo) && $catalogo->cliente_id == $cliente->id ? 'selected' : '' }}
                                            value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Salvar</button>
                    </form>
                    @if (isset($catalogo))

                        <div style="height: 0.1px; width: 100%; border: 0.1px solid #cecece;" class="my-3">
                        </div>
                        <div class="row w-100">

                            <div class="col-12 d-flex mb-2" style="justify-content: space-between; align-items: center;">
                                <h4>Produtos</h4>
                                <button type="button" data-toggle="modal" data-target="#exampleModal"
                                    class="btn btn-secondary mt-3">Adicionar Produto</button>
                            </div>

                            @if (!$produtos->isEmpty())
                                <table id="produtosTable" class="table shadow rounded table-striped table-hover">
                                    <thead class="bg-primary ">
                                        <tr>
                                            <th>Id</th>
                                            <th>Nome</th>
                                            <th>Descrição</th>
                                            <th>Data de Criação</th>
                                            <th class="d-flex justify-content-center">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($produtos as $produto)
                                            <tr>

                                                <td>{{ $produto->id }}</td>
                                                <td>{{ $produto->nome }}</td>
                                                <td>{{ \Illuminate\Support\Str::limit($produto->descricao, 100) }}
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($produto->created_at)->format('d/m/Y H:i') }}
                                                </td>

                                                <td class="d-flex  justify-content-around">
                                                    <button type="button" data-toggle="modal" data-target="#descricaoModal" data-nome="{{$produto->nome}}"
                                                        data-descricao="{{$produto->descricao}}"
                                                    class="btn btn-info descricaoModalButton">
                                                        <i class="fas fa-info"></i>
                                                    </button>
                                                    <button type="button"
                                                        data-nome="{{$produto->nome}}"
                                                        data-descricao="{{$produto->descricao}}"
                                                        data-id="{{$produto->id}}"
                                                        data-toggle="modal"
                                                        data-target="#editProductModal"
                                                        class="btn btn-warning mr-1 editModal"><i
                                                            class="fas fa-edit"></i></button>
                                                    <form method="POST"
                                                        action="{{ route('produto.destroy', $produto->id) }}"
                                                        enctype="multipart/form-data">
                                                        @method('DELETE')

                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-danger"><i
                                                                class="fa fa-trash"></i></button>
                                                    </form>

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
                <!-- /.card -->
            </div>
        </div>

    </div>

    <div class="modal fade" id="descricaoModal" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-xl">
           
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Descrição de <span id="nameProductDescription"></span></h5>
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
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Cadastro de Produto</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('produto.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value="{{ $catalogo->id }}" name="catalogo_id">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-6">

                                    <label for="nome_produto" class="font-weight-bold">Nome</label>
                                    <input type="text" class="form-control" id="nome_produto" name="nome_produto"
                                        value="">
                                </div>
                                <div class="col-6">
                                    <label for="descricao" class="font-weight-bold">Descrição</label>
                                    <textarea rows="3" type="text" class="form-control" id="descricao" name="descricao">{{ old('descricao', '') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    @endif
    @if (isset($catalogo))
        <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-6">

                                    <label for="nome_produto" class="font-weight-bold">Nome</label>
                                    <input type="text" class="form-control" id="nome_edit_produto" name="nome_edit_produto"
                                        value="">
                                </div>
                                <div class="col-6">
                                    <label for="descricao" class="font-weight-bold">Descrição</label>
                                    <textarea rows="3" type="text" class="form-control" id="descricao_edit" name="descricao_edit">{{ old('descricao_edit', '') }}
                            </textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    @endif
    <script>
        $(document).ready(function($) {

            $('.descricaoModalButton').on('click',function(){


                $('#nameProductDescription').text(this.dataset.nome)
                $('#descriptionProduct').text(this.dataset.descricao)
            })
            $('.editModal').on('click',function(){
                $('#formEdit').attr('action',`/produto/${this.dataset.id}`)
                $('#nome_edit_produto').val(this.dataset.nome)
                $('#descricao_edit').val(this.dataset.descricao)
            })

            // Tabs com localStorage
            // var activeTab = localStorage.getItem('activeTab');
            // if (activeTab) {
            //     $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
            // }

        })
    </script>


@endsection
