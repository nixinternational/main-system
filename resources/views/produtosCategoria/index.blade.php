@extends('layouts.app')
@section('title', 'Produtos da Categoria: ' . $categoria->nome)

@section('actions')

@endsection


@section('content')
    <!-- Main content -->
    <form class="" id="formSearch" action="{{ route('produtosCategoria.index', $categoria->id) }}" method="GET">

        <div class="d-flex">

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
                </div>
                <input value="{{ $_GET['search'] ?? '' }}" style="max-width: 150px" type="text" id="search"
                    name="search" class="form-control mr-2" placeholder="" aria-label="" aria-describedby="basic-addon1">
                <a href="{{ route('produtosCategoria.index', $categoria->id) }}" class="btn btn-primary">Limpar busca</a>
            </div>
            {{-- {{$produtosCategoria->appends(['paginacao' => $_GET['paginacao'] ?? 10])}} --}}
        </div>
        @if ($produtosCategoria->isNotEmpty())
            <table id="produtosCategoriaTable" class="table shadow rounded table-striped table-hover">
                <thead class="bg-primary ">
                    <tr>
                        <th>Id</th>
                        <th>Produto</th>
                        <th>Marca</th>
                        <th>Fornecedor</th>
                        <th>Criado por</th>

                        <th class="d-flex justify-content-center">Informações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($produtosCategoria as $produto)
                        <tr>
                            <td>{{ $produto->id }}</td>
                            <td>{{ $produto->nome }}</td>
                            <td>{{ $produto->marca->nome }}</td>

                            <td>{{ $produto->fornecedor->nome }}</td>

                            <td>{{ $produto->responsavel->name }}</td>

                            <td class="d-flex  justify-content-center">
                                <button data-id="{{ $produto->id }}" type="button" class="btn btn-primary infoProduto">
                                    <i class="fas fa-info"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <x-not-found />
        @endif
    </form>


    @include('produtosCategoria.modalInfo')
@endsection
@push('scripts')
    <script>
        $('.infoProduto').on('click', function() {
            console.log(this)
            fetch(`/produto/${this.dataset.id}`).then(async (response) => {
                let result = await response.json();
                console.log(result)
                document.getElementById('nomeProduto').textContent = result.data.nome

                document.getElementById('codigoProduto').value = result.data.id

                document.getElementById('unidadeMedida').value = result.data.unidade_medida

                document.getElementById('porcao').value = result.data.informacao_nutricional.porcao
                document.getElementById('proteina').value = result.data.informacao_nutricional.proteina
                document.getElementById('carboidrato').value = result.data.informacao_nutricional
                    .carboidrato
                document.getElementById('gordura_total').value = result.data.informacao_nutricional
                    .gordura_total


                document.getElementById('fornecedorNome').value = result.data.fornecedor.nome
                document.getElementById('marca').value = result.data.marca.nome

                let date = new Date(Date.parse(result.data.created_at));
                let dia = date.getDate().toString().padStart(2, '0');
                let mes = (date.getMonth() + 1).toString().padStart(2, '0');
                let ano = date.getFullYear();
                document.getElementById('dataCadastro').value = `${dia}/${mes}/${ano}`

                document.getElementById('responsavel').value = result.data.responsavel.name
                document.getElementById('descricaoProduto').value = result.data.descricao

                $('#produtoModal').modal('show')
            })

        })

        document.getElementById('produtoModal').addEventListener('hide.bs.modal', function() {
            document.getElementById('formProduto').reset()
        })
        document.getElementById('search').addEventListener('change', function() {
            document.getElementById('formSearch').submit()
        })
        document.getElementById('paginacao').addEventListener('change', function() {
            document.getElementById('formSearch').submit()
        })
    </script>
@endpush
