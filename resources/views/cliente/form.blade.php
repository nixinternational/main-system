@extends('layouts.app')
@section('title', isset($cliente) ? '' : 'Cadastrar cliente')


@section('content')

    <div class="row">
        <div class="col-12 shadow-lg px-0">
            <div class="card w-100 card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                        <li class="pt-2 px-3">
                            <h3 class="card-title text-dark font-weight-bold" style="">
                                {{ $cliente?->nome ?? 'Novo cliente' }}</h3>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-tabs-two-home-tab" data-toggle="pill"
                                href="#custom-tabs-two-home" role="tab" aria-controls="custom-tabs-two-home"
                                aria-selected="false">Informações Gerais</a>
                        </li>
                        @if (isset($cliente))
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-two-profile-tab" data-toggle="pill"
                                    href="#custom-tabs-two-profile" role="tab" aria-controls="custom-tabs-two-profile"
                                    aria-selected="false">Emails</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " id="custom-tabs-two-messages-tab" data-toggle="pill"
                                    href="#custom-tabs-two-messages" role="tab" aria-controls="custom-tabs-two-messages"
                                    aria-selected="true">Responsáveis</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-two-settings-tab" data-toggle="pill"
                                    href="#custom-tabs-two-settings" role="tab" aria-controls="custom-tabs-two-settings"
                                    aria-selected="false">Aduanas</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-two-settings-info" data-toggle="pill"
                                    href="#custom-tabs-two-info" role="tab" aria-controls="custom-tabs-two-info"
                                    aria-selected="false">Informações Específicas</a>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-two-tabContent">
                        <div class="tab-pane fade active show" id="custom-tabs-two-home" role="tabpanel"
                            aria-labelledby="custom-tabs-two-home-tab">
                            <form enctype="multipart/form-data"
                                action="{{ isset($cliente) ? route('cliente.update', $cliente->id) : route('cliente.store') }}"
                                method="POST">
                                @csrf
                                @if (isset($cliente))
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-12">
                                        <h4>Empresa</h4>
                                    </div>
                                    <div class="col-4">
                                        <label for="exampleInputEmail1" class="form-label">Nome</label>
                                        <input value="{{ isset($cliente) ? $cliente->nome : '' }}" class="form-control"
                                            name="name" id="nome">
                                        @error('nome')
                                            <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                                        @enderror
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" class="form-label">CNPJ</label>
                                            <input value="{{ isset($cliente) ? $cliente->cnpj : old('cnpj') ?? '' }}"
                                                class="form-control" name="cnpj" id="cnpj">
                                            @error('cnpj')
                                                <span
                                                    class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <label for="validationTooltip03">Data da procuração</label>
                                        <input value="{{ isset($cliente) ? $cliente->data_vencimento_procuracao : '' }}"
                                            type="date" class="form-control" id="data_vencimento_procuracao"
                                            name="data_vencimento_procuracao">

                                    </div>
                                </div>
                                <div style="height: 0.1px; width: 100%; border: 0.1px solid #cecece;" class="mb-3"></div>
                                <div class="row">
                                    <div class="col-12">
                                        <h4>Endereço</h4>
                                    </div>

                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" class="form-label">CEP</label>
                                            <input value="{{ isset($cliente) ? $cliente->cep : '' }}" class="form-control"
                                                name="cep" id="cep">

                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" class="form-label">Logradouro</label>
                                            <input value="{{ isset($cliente) ? $cliente->logradouro : '' }}"
                                                class="form-control" name="logradouro" id="logradouro">
                                            @error('logradouro')
                                                <span
                                                    class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" class="form-label">Numero</label>
                                            <input value="{{ isset($cliente) ? $cliente->numero : '' }}"
                                                class="form-control" name="numero" id="numero">
                                            @error('numero')
                                                <span
                                                    class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" class="form-label">Bairro</label>
                                            <input value="{{ isset($cliente) ? $cliente->bairro : '' }}"
                                                class="form-control" name="bairro" id="bairro">
                                            @error('bairro')
                                                <span
                                                    class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" class="form-label">Cidade</label>
                                            <input value="{{ isset($cliente) ? $cliente->cidade : '' }}"
                                                class="form-control" name="cidade" id="cidade">
                                            @error('cidade')
                                                <span
                                                    class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" class="form-label">Estado</label>
                                            <input value="{{ isset($cliente) ? $cliente->estado : '' }}"
                                                class="form-control" name="estado" id="estado">
                                            @error('estado')
                                                <span
                                                    class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" class="form-label">Complemento</label>
                                            <input value="{{ isset($cliente) ? $cliente->complemento : '' }}"
                                                class="form-control" name="complemento" id="complemento">
                                            @error('complemento')
                                                <span
                                                    class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div style="height: 0.1px; width: 100%; border: 0.1px solid #cecece;" class="mb-3">
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <h4>Responsável Legal</h4>
                                    </div>
                                    <div class="col-4">
                                        <label for="exampleInputEmail1" class="form-label">Nome</label>
                                        <input value="{{ isset($cliente) ? $cliente->nome_responsavel_legal : '' }}"
                                            class="form-control" name="nome_responsavel_legal"
                                            id="nome_responsavel_legal">
                                        @error('nome_responsavel_legal')
                                            <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                                        @enderror
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" class="form-label">CPF</label>
                                            <input
                                                value="{{ isset($cliente) ? $cliente->cpf_responsavel_legal : old('cnpj') ?? '' }}"
                                                class="form-control" name="cpf_responsavel_legal"
                                                id="cpf_responsavel_legal">
                                            @error('cnpj')
                                                <span
                                                    class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">Salvar</button>
                            </form>
                        </div>
                        @if (isset($cliente))
                            <div class="tab-pane fade" id="custom-tabs-two-profile" role="tabpanel"
                                aria-labelledby="custom-tabs-two-profile-tab">
                                <div class="row">
                                    <div class="col-2 d-flex justify-content-center align-center align-items-start">
                                        <button id="addClientEmail" type="button"
                                            class="btn btn-success rounded shadow">Adicionar Novo Email
                                        </button>
                                    </div>
                                    <div class="col-10">
                                        <form enctype="multipart/form-data"
                                            action="{{ route('cliente.update.email', $cliente->id) }}" method="POST">
                                            @csrf
                                            <table class="table table-bordered" id="clientesEmail">
                                                <thead class="thead-primary">
                                                    <tr class="bg-primary">
                                                        <th scope="col">E-MAIL</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    @if (count($cliente->emails) > 0)
                                                        @foreach ($cliente->emails as $email)
                                                            <tr data-id="{{ $loop->index }}">
                                                                <td><input type="email" class="email form-control "
                                                                        data-id="{{ $loop->index }}"
                                                                        value="{{ $email->email }}"
                                                                        id="email-{{ $loop->index }}" name="emails[]">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td>Não há emails cadastrados</td>
                                                        </tr>
                                                    @endif
                                                </tbody>

                                            </table>
                                            <button type="submit" class="btn btn-primary mt-3">Salvar</button>

                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade " id="custom-tabs-two-messages" role="tabpanel"
                                aria-labelledby="custom-tabs-two-messages-tab">
                                <div class="row">
                                    <div class="col-2 d-flex justify-content-center align-center align-items-start">
                                        <button id="addClientResponsavel" type="button"
                                            class="btn btn-success rounded shadow">Adicionar Novo Responsável
                                        </button>
                                    </div>
                                    <div class="col-10">
                                        <form enctype="multipart/form-data"
                                            action="{{ route('cliente.update.responsavel', $cliente->id) }}"
                                            method="POST">
                                            @csrf
                                            <table class="table table-bordered" id="clientesResponsavelProcesso">
                                                <thead class="thead-primary">
                                                    <tr class="bg-primary">
                                                        <th scope="col">Nome</th>
                                                        <th scope="col">Telefone</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    @if (count($cliente->responsaveisProcesso) > 0)
                                                        @foreach ($cliente->responsaveisProcesso as $responsavel)
                                                            <tr data-id="{{ $loop->index }}">
                                                                <td>
                                                                    <input type="text" class="email form-control "
                                                                        data-id="{{ $loop->index }}"
                                                                        value="{{ $responsavel->nome }}"
                                                                        id="email-{{ $loop->index }}" name="nomes[]">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="telefone form-control "
                                                                        data-id="{{ $loop->index }}"
                                                                        value="{{ $responsavel->telefone }}"
                                                                        id="email-{{ $loop->index }}"
                                                                        name="telefones[]">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="2">Não há responsáveis cadastrados</td>
                                                        </tr>
                                                    @endif
                                                </tbody>

                                            </table>
                                            <button type="submit" class="btn btn-primary mt-3">Salvar</button>

                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="custom-tabs-two-settings" role="tabpanel"
                                aria-labelledby="custom-tabs-two-settings-tab">
                                <div class="row">
                                    <div class="col-2 d-flex justify-content-center align-center align-items-start">
                                        <button id="addClientAduana" type="button"
                                            class="btn btn-success rounded shadow">Adicionar Nova Aduana
                                        </button>
                                    </div>
                                    <div class="col-10">
                                        <form enctype="multipart/form-data"
                                            action="{{ route('cliente.update.aduanas', $cliente->id) }}" method="POST">
                                            @csrf
                                            <table class="table table-bordered" id="clientesAduana">
                                                <thead class="thead-primary">
                                                    <tr class="bg-primary">
                                                        <th scope="col">Nome</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    @if (count($cliente->aduanas) > 0)
                                                        @foreach ($cliente->aduanas as $aduana)
                                                            <tr data-id="{{ $loop->index }}">
                                                                <td><input type="text" class="email form-control "
                                                                        data-id="{{ $loop->index }}"
                                                                        value="{{ $aduana->nome }}"
                                                                        id="aduana-{{ $loop->index }}" name="aduanas[]">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td>Não há aduanas cadastradas</td>
                                                        </tr>
                                                    @endif
                                                </tbody>

                                            </table>
                                            <button type="submit" class="btn btn-primary mt-3">Salvar</button>

                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="custom-tabs-two-info" role="tabpanel"
                                aria-labelledby="custom-tabs-two-settings-info">
                                <form enctype="multipart/form-data" action="{{route('cliente.update.especificidades',$cliente->id)}}"  method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="despachante_siscomex" name="despachante_siscomex" value="1"
                                                        {{ old('despachante_siscomex', $cliente->despachante_siscomex) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="despachante_siscomex">Despachante
                                                        Siscomex</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3">
    
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="marinha_mercante"
                                                        name="marinha_mercante" value="1"
                                                        {{ old('marinha_mercante', $cliente->marinha_mercante) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="marinha_mercante">Marinha
                                                        Mercante</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="afrmm"
                                                        name="afrmm" value="1"
                                                        {{ old('afrmm', $cliente->afrmm) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="afrmm">AFRMM</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="itau_di"
                                                        name="itau_di" value="1"
                                                        {{ old('itau_di', $cliente->itau_di) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="itau_di">Itau DI</label>
                                                </div>
                                            </div>
                                        </div>
    
    
    
    
    
    
                                    </div>
    
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold" for="modalidade_radar">Modalidade
                                                    Radar</label>
                                                <select class="form-control" id="modalidade_radar" name="modalidade_radar">
                                                    <option value=""
                                                        {{ old('modalidade_radar', $cliente->modalidade_radar) == '' ? 'selected' : '' }}>
                                                        Selecione...</option>
                                                    <option value="expresso"
                                                        {{ old('modalidade_radar', $cliente->modalidade_radar) == 'expresso' ? 'selected' : '' }}>
                                                        Expresso</option>
                                                    <option value="limitado"
                                                        {{ old('modalidade_radar', $cliente->modalidade_radar) == 'limitado' ? 'selected' : '' }}>
                                                        Limitado</option>
                                                    <option value="ilimitado"
                                                        {{ old('modalidade_radar', $cliente->modalidade_radar) == 'ilimitado' ? 'selected' : '' }}>
                                                        Ilimitado</option>
                                                </select>
                                            </div>
    
    
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="beneficio_fiscal" class="font-weight-bold">Benefício
                                                    Fiscal</label>
                                                <input type="text" class="form-control" id="beneficio_fiscal"
                                                    name="beneficio_fiscal"
                                                    value="{{ old('beneficio_fiscal', $cliente->beneficio_fiscal ?? '') }}">
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="form-group">
                                        <label for="beneficio_fiscal" class="font-weight-bold">Observações</label>
                                        <textarea rows="3" type="text" class="form-control" id="observacoes" name="observacoes">{{ old('observacoes', $cliente->observacoes ?? '') }}
                                        </textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-3">Salvar</button>

                                </form>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
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


        $('#addClientEmail').on('click', async function() {
            let id = $('#clientesEmail tbody tr').length;
            let tr = `<tr data-id="${id}">
                            <td><input  type="email" class="email form-control " data-id="${id}" id="email-${id}" name="emails[]"></td>
                    </tr>
                    `
            $('#clientesEmail tbody').append(tr)


        })

        $('#addClientAduana').on('click', async function() {
            let id = $('#clientesAduana tbody tr').length;
            let tr = `<tr data-id="${id}">
                            <td><input  type="text" class="email form-control " data-id="${id}" id="aduana-${id}" name="aduanas[]"></td>
                    </tr>
                    `
            $('#clientesAduana tbody').append(tr)


        })


        $('#addClientResponsavel').on('click', async function() {
            let id = $('#clientesResponsavelProcesso tbody tr').length;
            let tr = `<tr data-id="${id}">
                            <td><input  type="text" class="nome form-control " data-id="${id}" id="nome-${id}" name="nomes[]"></td>
                            <td><input  type="text" class="nome form-control " data-id="${id}" id="telefone-${id}" name="telefones[]"></td>
                    </tr>
                    `
            $('#clientesResponsavelProcesso tbody').append(tr)


        })
    </script>


@endsection
