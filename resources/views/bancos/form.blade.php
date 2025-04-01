@extends('layouts.app')
@section('title', isset($cliente) ? '' : 'Cadastrar Banco Nix')


@section('content')


    <div class="card w-100 card-primary card-tabs">

        <div class="card-body">
            <form action="{{ isset($banco) ? route('banco-nix.update',$banco->id):route('banco-nix.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($banco))
                    @method('PUT')
                @endif
                <div class="row">
                    <div class="col-3">
                        <label for="exampleInputEmail1" class="form-label">Nome</label>
                        <input name="nome" value="{{ isset($banco) ? $banco->nome : old('nome') ?? '' }}"
                            class="form-control" id="nome">
                        @error('nome')
                            <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                        @enderror
                    </div>
                    <div class="col-3">
                        <label for="exampleInputEmail1" class="form-label">Agência</label>
                        <input name="agencia" value="{{ isset($banco) ? $banco->agencia : old('agencia') ?? '' }}"
                            class="form-control" id="agencia">
                        @error('agencia')
                            <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                        @enderror
                    </div>
                    <div class="col-3">
                        <label for="exampleInputEmail1" class="form-label">Conta Corrente</label>
                        <input name="conta_corrente"
                            value="{{ isset($banco) ? $banco->conta_corrente : old('conta_corrente') ?? '' }}"
                            class="form-control" id="conta_corrente">
                        @error('conta_corrente')
                            <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                        @enderror
                    </div>
                    <div class="col-3">
                        <label for="exampleInputEmail1" class="form-label">Número do banco</label>
                        <input name="numero_banco" value="{{ isset($banco) ? $banco->numero_banco : old('numero_banco') ?? '' }}"
                            class="form-control" id="numero_banco">
                        @error('numero_banco')
                            <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Salvar</button>

            </form>
        </div>
        <!-- /.card -->

    </div>

    <script>
        $(document).ready(function($) {
            $('input[name=cnpj]').mask('99.999.999/9999-99')
            $('input[name=cpf_responsavel_legal]').mask('999.999.999-99')
        })
        $('#cep').on('change', async function() {
            let cep = this.value.replace('-', '')
            if (cep.length == 8) {

                await fetch(`https://viacep.com.br/ws/${this.value}/json/`, ).then(async (response) => {
                    const resultado = await response.json();

                    $('#logradouro').val(resultado.logradouro)
                    $('#bairro').val(resultado.bairro)
                    $('#cidade').val(resultado.localidade)
                    $('#estado').val(resultado.estado)
                });
            }
        })




        $('#addClientAduana').on('click', async function() {
            let id = $('#clientesAduana tbody tr').length;
            let tr = `<tr data-id="${id}">
                             <td>
                                                                    <select class="form-control" id="modalidade-${id}" name="modalidades[]">
                                                                        <option value="">Selecione...</option>
                                                                        <option value="aereo">Aéreo</option>
                                                                        <option value="maritima">Marítima</option>
                                                                        <option value="rodoviaria">Rodoviária</option>
                                                                        <option value="multimodal">Multimodal</option>
                                                                        <option value="courier">Courier</option>
                                                                    </select>
                                                                </td>
                                                                <td><input type="text" class="email form-control "
                                                                        data-id="${id}"
                                                                        id="aduana-${id}" name="urf_despacho[]">
                                                                </td>
                    </tr>
                    `
            $('#clientesAduana tbody').append(tr)


        })


        $('#addClientResponsavel').on('click', async function() {
            let id = $('#clientesResponsavelProcesso tbody tr').length;
            let tr = `<tr data-id="${id}">
                            <td><input  type="text" class="nome form-control " data-id="${id}" id="nome-${id}" name="nomes[]"></td>
                            <td><input  type="text" class="nome form-control " data-id="${id}" id="departamento-${id}" name="departamentos[]"></td>
                            <td><input  type="text" class="nome form-control " data-id="${id}" id="telefone-${id}" name="telefones[]"></td>
                            <td><input  type="email" class="nome form-control " data-id="${id}" id="email-${id}" name="emails[]"></td>
                    </tr>
                    `
            $('#clientesResponsavelProcesso tbody').append(tr)


        })
    </script>


@endsection
