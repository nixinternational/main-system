@extends('layouts.app')
@section('title', isset($cliente) ? "Editar cliente $cliente->nome" : 'Cadastrar cliente')


@section('content')
    <form enctype="multipart/form-data"
        action="{{ isset($cliente) ? route('cliente.update', $cliente->id) : route('cliente.store') }}" method="POST">
        @csrf
        @if (isset($cliente))
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-6">
                <label for="exampleInputEmail1" class="form-label">Nome</label>
                <input value="{{ isset($cliente) ? $cliente->name : '' }}" class="form-control" name="name" id="nome">
                @error('nome')
                    <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                @enderror
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label for="exampleInputEmail1" class="form-label">CNPJ</label>
                    <input value="{{ isset($cliente) ? $cliente->cnpj : old('cnpj') ?? '' }}" class="form-control"
                        name="cnpj" id="cnpj">
                    @error('cnpj')
                        <span class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                    @enderror
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="exampleInputEmail1" class="form-label">CEP</label>
                    <input value="{{ isset($cliente) ? $cliente->cep : '' }}" class="form-control" name="cep"
                        id="cep">

                </div>
            </div>
            <div class="col-5">
                <div class="form-group">
                    <label for="exampleInputEmail1" class="form-label">logradouro</label>
                    <input value="{{ isset($cliente) ? $cliente->logradouro : '' }}" class="form-control" name="logradouro"
                        id="logradouro">
                    @error('logradouro')
                        <span class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                    @enderror
                </div>
            </div>
            <div class="col-1">
                <div class="form-group">
                    <label for="exampleInputEmail1" class="form-label">Numero</label>
                    <input value="{{ isset($cliente) ? $cliente->numero : '' }}" class="form-control" name="numero"
                        id="numero">
                    @error('numero')
                        <span class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                    @enderror
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="exampleInputEmail1" class="form-label">Bairro</label>
                    <input value="{{ isset($cliente) ? $cliente->bairro : '' }}" class="form-control" name="bairro"
                        id="bairro">
                    @error('bairro')
                        <span class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                    @enderror
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="exampleInputEmail1" class="form-label">Cidade</label>
                    <input value="{{ isset($cliente) ? $cliente->cidade : '' }}" class="form-control" name="cidade"
                        id="cidade">
                    @error('cidade')
                        <span class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                    @enderror
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label for="exampleInputEmail1" class="form-label">Complemento</label>
                    <input value="{{ isset($cliente) ? $cliente->complemento : '' }}" class="form-control"
                        name="complemento" id="complemento">
                    @error('complemento')
                        <span class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                    @enderror
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label for="exampleInputEmail1" class="form-label">Categoria</label>
                    <div class="input-group  ">
                        <select name="tipo_cliente"
                            value="{{ isset($produto) ? $produto->categoria_id : old('tipo_cliente') ?? '' }}"
                            class="custom-select" id="inputGroupSelect01">
                            <option value="" hidden>Selecione uma opção</option>
                            <option {{ isset($cliente) && $cliente->tipo_cliente == 'a' ? 'selected' : '' }}
                                value="a">Nível A</option>
                            <option {{ isset($cliente) && $cliente->tipo_cliente == 'b' ? 'selected' : '' }}
                                value="b">Nível B</option>
                            <option {{ isset($cliente) && $cliente->tipo_cliente == 'c' ? 'selected' : '' }}
                                value="c">Nível C</option>
                            <option {{ isset($cliente) && $cliente->tipo_cliente == 'd' ? 'selected' : '' }}
                                value="d">Nível D</option>
                            <option {{ isset($cliente) && $cliente->tipo_cliente == 'e' ? 'selected' : '' }}
                                value="e">Nível E</option>
                            <option {{ isset($cliente) && $cliente->tipo_cliente == 'f' ? 'selected' : '' }}
                                value="f">Nível F</option>
                            <option {{ isset($cliente) && $cliente->tipo_cliente == 'g' ? 'selected' : '' }}
                                value="g">Nível G</option>
                            <option {{ isset($cliente) && $cliente->tipo_cliente == 'h' ? 'selected' : '' }}
                                value="h">Nível H</option>

                        </select>
                    </div>
                    @error('tipo_cliente')
                        <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                    @enderror
                </div>
            </div>

        </div>




        <button type="submit" class="btn btn-primary mt-3">Salvar</button>

    </form>

    <script>
        $(document).ready(function($) {
            $('input[name=cnpj]').mask('99.999.999/9999-99')
        })
        $('#cep').on('change', async function() {
            let cep = this.value.replace('-', '')
            if (cep.length == 8) {

                await fetch(`https://viacep.com.br/ws/${this.value}/json/`, ).then(async (response) => {
                    const resultado = await response.json();
                    console.log(resultado);
                    $('#logradouro').val(resultado.logradouro)
                    $('#bairro').val(resultado.bairro)
                    $('#cidade').val(resultado.localidade)
                });
            }
        })
    </script>


@endsection
