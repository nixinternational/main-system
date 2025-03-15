@extends('layouts.app')
@section('title', isset($produto) ? "Editar produto: $produto->nome" : 'Cadastrar lote')


@section('content')
    <form enctype="multipart/form-data"
        action="{{ isset($produto) ? route('lote.update', $produto->id) : route('lote.store') }}" method="POST">
        @csrf
        @if (isset($produto))
            @method('PUT')
        @endif
        <div class="row">
            <div class="col-3 ">
                <label for="exampleInputEmail1" class="form-label">Categoria</label>
                <div class="input-group  ">
                    <select id="categoria" name="categoria"
                        value="{{ isset($produto) ? $produto->categoria_id : old('categoria') ?? '' }}"
                        class="custom-select" id="inputGroupSelect01">
                        <option value="-1" selected>Selecione uma opção
                        <option>
                            @foreach ($categorias as $categoria)
                        <option
                            value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                        @endforeach
                    </select>
                </div>
                @error('categoria')
                    <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                @enderror
            </div>
            <div class="col-3 ">
                <label for="exampleInputEmail1" class="form-label">Produto</label>
                <div class="input-group  ">
                    <select id="produto" name="produto"
                        
                        class="custom-select" id="inputGroupSelect01">
                        <option value="-1" selected>Selecione uma categoria

                    </select>
                </div>
                @error('produto')
                    <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                @enderror
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="dataValidade">Data de Fabricação</label>
                    <input type="date" value="{{old('dataFabricacao')  ?? ''}}" class="form-control" id="dataFabricacao" name="dataFabricacao">
                    @error('dataFabricacao')
                        <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                    @enderror
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="dataValidade">Data de Validade</label>
                    <input value="{{old('dataValidade')  ?? ''}}" type="date" class="form-control" id="dataValidade" name="dataValidade">
                    @error('dataValidade')
                        <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                    @enderror
                </div>
            </div>
        
            <div class="col-3">
                <label for="exampleInputEmail1" class="form-label">Preço de custo</label>
                <input type="number" name="preco_custo"
                    value="{{ old('preco_custo') ?? '' }}" class="form-control"
                    id="preco_custo">
                @error('preco_custo')
                    <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                @enderror
            </div>
            <div class="col-3">
                <label for="exampleInputEmail1" class="form-label">Preço de venda estimado</label>
                <input type="number" name="preco_venda"
                    value="{{ old('preco_venda') ?? '' }}" class="form-control"
                    id="preco_venda">
                @error('preco_venda')
                    <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                @enderror
            </div>
            <div class="col-3">
                <label for="exampleInputEmail1" class="form-label">Quantidade de Produtos</label>
                <input type="number" name="quantidade"
                    value="{{old('quantidade') ?? '' }}"
                    class="form-control" id="codigoProduto">
                @error('quantidade')
                    <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                @enderror
            </div>
            <div class="col-3">
                <label for="exampleInputEmail1" class="form-label">Responsável</label>
                <input name="responsavel" class="form-control" id="responsavel" disabled value="{{ Auth::user()->name }}">

            </div>


        </div>

        <button type="submit" class="btn btn-primary mt-2">Salvar</button>


    </form>


    <script>
        document.getElementById('categoria').addEventListener('change', function() {
            console.log(this)
            if (this.value != -1) {
                fetch(`/categoria/${this.value}`).then(async (response) => {
                    let result = await response.json();

                    $('#produto').empty();
                    for (let item of result.data) {
                        $('#produto').append(`<option value="${item.id}">${item.nome}</option>`)
                    }
                    console.log(result)

                })
            }

        })
    </script>
@endsection
