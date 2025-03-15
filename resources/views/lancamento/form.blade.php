@extends('layouts.app')
@section('title', isset($produto) ?: 'Cadastrar saída de produtos')


@section('content')
    <form enctype="multipart/form-data" action="{{ route('lancamento.store') }}" method="POST">
        @csrf
        @if (isset($produto))
            @method('PUT')
        @endif
        <div class="row">
            <div class="col-7">
                <label for="exampleInputEmail1" class="form-label">Código do Lote</label>
                <input type="number" value="" name="lote_id" class="form-control" id="codigoProduto">
                @error('lote_id')
                    <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                @enderror
                <label for="exampleInputEmail1" class="form-label">Quantidade de Produtos</label>
                <input type="number" name="quantidade" value="" class="form-control" id="quantidade">
                @error('quantidade')
                    <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                @enderror
                <div class="mt-1">
                    <label for="exampleInputEmail1" class="form-label">Responsável</label>
                    <input name="responsavel" class="form-control" id="responsavel" disabled
                        value="{{ Auth::user()->name }}">

                </div>
                <button id="submitButton" type="submit" class="btn btn-primary mt-3">Salvar</button>

            </div>

            <div class="card  card-primary  ml-3 shadow-lg" id="infoLote" style="height: 20%;min-width:40%">
                <div class="card-header ">
                    <h3 class="card-title">Informações do Lote</h3>
                    <div class="card-tools">

                    </div>

                </div>

                <div class="card-body">
                    <div> Código do lote: <strong><span id="codigoLote"> </span></strong></div>
                    <div> Nome do produto: <strong><span id="nomeProduto"> </span></strong></div>
                    <div> Quantidade em estoque: <strong><span id="quantidadeEstoque"> </span></strong></div>
                    <div> Vencido: <strong><span id="loteVencido"> </span></strong></div>
                </div>
            </div>
        </div>

    </form>
    <input type="hidden" id="quantidadeAtual">

    <script>
        let products = [];
        document.getElementById('codigoProduto').addEventListener('change', function() {
            fetch(`/lote/${this.value}`).then(async (response) => {
                let result = await response.json();
                if (response.status == 400) {
                    Toast.fire({
                        icon: 'error',
                        title: result.message
                    });
                } else {
                    if (result.data.quantidadeAtual == 0) {
                        Toast.fire({
                            icon: 'error',
                            title: 'Lote sem produtos! Escolha outro lote.'
                        });

                        document.getElementById('submitButton').setAttribute('disabled', '')
                    } else {
                        document.getElementById('submitButton').removeAttribute('disabled')
                    }
                    document.getElementById('codigoLote').textContent = result.data.id
                    document.getElementById('nomeProduto').textContent = result.data.produto.nome
                    document.getElementById('quantidadeEstoque').textContent = result.data
                        .quantidadeAtual
                    document.getElementById('quantidadeAtual').value = result.data.quantidadeAtual
                    document.getElementById('loteVencido').textContent = result.data.vencido ? 'Sim' :
                        'Não'


                }
            })

        })

        document.getElementById('quantidade').addEventListener('change', function() {
            let valorMaximo = parseInt(document.getElementById('quantidadeAtual').value);
            let valorRetirada = parseInt(this.value)
            if (valorRetirada > valorMaximo) {
                Toast.fire({
                    icon: 'error',
                    title: 'A quantidade a ser retirada não pode ser maior que a quantidade em estoque atual!'
                });
                document.getElementById('submitButton').setAttribute('disabled', '')
            } else {
                document.getElementById('submitButton').removeAttribute('disabled')

            }
        })
    </script>
@endsection
