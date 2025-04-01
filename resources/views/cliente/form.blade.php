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
                                aria-selected="false">Informações Cadastrais</a>
                        </li>
                        @if (isset($cliente))
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-two-settings-info" data-toggle="pill"
                                href="#custom-tabs-two-info" role="tab" aria-controls="custom-tabs-two-info"
                                aria-selected="false">Cadastro Siscomex</a>
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
                                        <input value="{{ isset($cliente) ? $cliente->data_procuracao : '' }}"
                                            type="date" class="form-control" id="data_procuracao"
                                            name="data_procuracao">

                                    </div>
                                    <div class="col-2">
                                        <label for="validationTooltip03">Data de vencimento procuração</label>
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
                                                value="{{ isset($cliente) ? $cliente->cpf_responsavel_legal : old('cpf_responsavel_legal') ?? '' }}"
                                                class="form-control" name="cpf_responsavel_legal"
                                                id="cpf_responsavel_legal">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" class="form-label">Email</label>
                                            <input
                                                value="{{ isset($cliente) ? $cliente->email_responsavel_legal : old('email_responsavel_legal') ?? '' }}"
                                                class="form-control" name="email_responsavel_legal"
                                                id="email_responsavel_legal">

                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1" class="form-label">Telefone</label>
                                            <input
                                                value="{{ isset($cliente) ? $cliente->telefone_responsavel_legal : old('telefone_responsavel_legal') ?? '' }}"
                                                class="form-control" name="telefone_responsavel_legal"
                                                id="telefone_responsavel_legal">
                                            @error('telefone_responsavel_legal')
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
                                                        <th scope="col">Departamento</th>
                                                        <th scope="col">Telefone</th>
                                                        <th scope="col">Email</th>

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
                                                                        value="{{ $responsavel->departamento }}"
                                                                        id="departamento-{{ $loop->index }}"
                                                                        name="departamentos[]">
                                                                </td>
                                                                


                                                                <td>
                                                                    <input type="text" class="telefone form-control "
                                                                        data-id="{{ $loop->index }}"
                                                                        value="{{ $responsavel->telefone }}"
                                                                        id="email-{{ $loop->index }}"
                                                                        name="telefones[]">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="telefone form-control "
                                                                        data-id="{{ $loop->index }}"
                                                                        value="{{ $responsavel->email }}"
                                                                        id="email-{{ $loop->index }}"
                                                                        name="emails[]">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="4">Não há responsáveis cadastrados</td>
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
                                                        <th scope="col">Modalidade</th>
                                                        <th scope="col">URF de Despacho</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    @if (count($cliente->aduanas) > 0)
                                                        @foreach ($cliente->aduanas as $aduana)
                                                            <tr data-id="{{ $loop->index }}">
                                                                <td>
                                                                    <select class="form-control" id="modalidade" name="modalidades[]">
                                                                        <option value=""
                                                                            {{ old('modalidade', $aduana->modalidade) == '' ? 'selected' : '' }}>
                                                                            Selecione...</option>
                                                                        <option value="aereo"
                                                                            {{ old('modalidade', $aduana->modalidade) == 'aereo' ? 'selected' : '' }}>
                                                                            Aéreo</option>
                                                                        <option value="maritima"
                                                                            {{ old('modalidade', $aduana->modalidade) == 'maritima' ? 'selected' : '' }}>
                                                                            Marítima</option>
                                                                        <option value="rodoviaria"
                                                                            {{ old('modalidade', $aduana->modalidade) == 'rodoviaria' ? 'selected' : '' }}>
                                                                            Rodoviária</option>
                                                                        <option value="multimodal"
                                                                            {{ old('modalidade', $aduana->modalidade) == 'multimodal' ? 'selected' : '' }}>
                                                                            Multimodal</option>
                                                                        <option value="courier"
                                                                            {{ old('modalidade', $aduana->modalidade) == 'courier' ? 'selected' : '' }}>
                                                                            Courier</option>
                                                                    </select>
                                                                </td>
                                                                <td><input type="text" class="email form-control "
                                                                        data-id="{{ $loop->index }}"
                                                                        value="{{ $aduana->urf_despacho }}"
                                                                        id="aduana-{{ $loop->index }}" name="urf_despacho[]">
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
                                                <label for="credenciamento_radar">Credenciamento radar inicial</label>
                                                <input type="date" class="form-control" id="credenciamento_radar" name="credenciamento_radar_inicial"
                                                    value="{{ old('credenciamento_radar_inicial', $cliente->credenciamento_radar_inicial) }}">
                                            </div>
                                        </div>
                                        
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="marinha_mercante">Marinha Mercante inicial</label>
                                                <input type="date" class="form-control" id="marinha_mercante" name="marinha_mercante_inicial"
                                                    value="{{ old('marinha_mercante_inicial', $cliente->marinha_mercante_inicial) }}">
                                            </div>
                                        </div>
                                        
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="afrmm_bb">AFRMM Vinculação BB inicial</label>
                                                <input type="date" class="form-control" id="afrmm_bb" name="afrmm_bb_inicial"
                                                    value="{{ old('afrmm_bb_inicial', $cliente->afrmm_bb_inicial) }}">
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
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="credenciamento_radar">Credenciamento radar final</label>
                                                <input type="date" class="form-control" id="credenciamento_radar_final" name="credenciamento_radar_final"
                                                    value="{{ old('credenciamento_radar_final', $cliente->credenciamento_radar_final) }}">
                                            </div>
                                        </div>
                                        
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="marinha_mercante">Marinha Mercante final</label>
                                                <input type="date" class="form-control" id="marinha_mercante_final" name="marinha_mercante_final"
                                                    value="{{ old('marinha_mercante_final', $cliente->marinha_mercante_final) }}">
                                            </div>
                                        </div>
                                        
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="afrmm_bb">AFRMM Vinculação BB final</label>
                                                <input type="date" class="form-control" id="afrmm_bb_final" name="afrmm_bb_final"
                                                    value="{{ old('afrmm_bb_final', $cliente->afrmm_bb_final) }}">
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
                                    
                                    <div class="form-group">
                                        <label class="font-weight-bold" for="debito_impostos_nix">Débito impostos</label>
                                        <select class="form-control" id="debito_impostos_nix" name="debito_impostos_nix">
                                            <option value="nix"
                                                {{ old('debito_impostos_nix', $cliente->debito_impostos) == 'nix' ? 'selected' : '' }}>
                                                Débito na conta NIX</option>
                                            <option value="cliente"
                                                {{ old('debito_impostos_nix', $cliente->debito_impostos) == 'cliente'  ? 'selected' : '' }}>
                                                Débito na conta do cliente</option>
                                     
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-2 d-flex justify-content-center align-center align-items-start">
                                            <button id="addBank" type="button"
                                                class="btn btn-success rounded shadow">Adicionar Nova conta
                                            </button>
                                        </div>
                                        <div class="col-10">
                                            <table class="table table-bordered" id="bancoCliente">
                                                <thead class="thead-primary">
                                                    <tr class="bg-primary">
                                                        <th scope="col">Banco</th>
                                                        <th scope="col">Agência</th>
                                                        <th scope="col">Conta corrente</th>
                                                        <th scope="col">N° Banco</th>
                                                        <th scope="col">Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($bancosCliente as  $banco)
                                                    <tr>
                                                        <td>
                                                            
                                                            
                                                            
                                                            <input value="{{$banco->nome}}" type="text" {{$banco->banco_nix ? 'disabled':''}} class=" form-control" 
                                                            data-id="{{ $loop->index }}" id="banco-{{ $loop->index }}"  
                                                            name="{{$banco->banco_nix ? '':'bancos[]'}}">
                                                        </td>
                                                        <td>
                                                            <input value="{{$banco->agencia}}" {{$banco->banco_nix ? 'disabled':''}}  type="text" class=" form-control" 
                                                            data-id="{{ $loop->index }}" id="banco-{{ $loop->index }}" 
                                                            name="{{$banco->banco_nix ? '':'agencias[]'}}">
                                                        </td>
                                                        <td>
                                                            <input value="{{$banco->conta_corrente}}" {{$banco->banco_nix ? 'disabled':''}}  type="text" class=" form-control" 
                                                            data-id="{{ $loop->index }}" id="banco-{{ $loop->index }}" 
                                                            name="{{$banco->banco_nix ? '':'conta_correntes[]'}}">
                                                        </td>
                                                        <td>
                                                            <input value="{{$banco->numero_banco}}" {{$banco->banco_nix ? 'disabled':''}}  type="text" class=" form-control" 
                                                            data-id="{{ $loop->index }}" id="banco-{{ $loop->index }}" 
                                                            name="{{$banco->banco_nix ? '':'numero_bancos[]'}}">
                                                        </td>
                                                        <td>
                                                            {{-- <form action="{{route('banco.cliente.destroy',$banco->id)}}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                                            </form> --}}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
        
                                            </table>
                                        </div>
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
    <input type="hidden" id="bancoOptions" value="{{json_encode($bancosNix)}}"  alt="">
    <script>
        $(document).ready(function($) {
            $('input[name=cnpj]').mask('99.999.999/9999-99')
            $('input[name=cpf_responsavel_legal]').mask('999.999.999-99')
        })
        $('#debito_impostos_nix').on('change', async function() {
            let value = this.value         
            $('#bancoCliente tbody tr').remove()
               if(value == 'nix'){
                $('#colBancoNix').removeClass('d-none')
                $('#colNomeBancoNix').addClass('d-none')
            }else{                
                $('#colNomeBancoNix').removeClass('d-none')
                $('#colBancoNix').addClass('d-none')
            }
            
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


        $('#addBank').on('click', async function() {
            let id = $('#bancoCliente tbody tr').length;
            let tr = '';
            if($('#debito_impostos_nix').val() == 'nix'){
                const bancosNix = JSON.parse($('#bancoOptions').val())
                let select = `<select name="bancos[]" data-id="${id}" class="bancoNix form-control" id="banco-${id}">`
                    select += `<option value="">Selecione uma opção</option>`
                for(let banco of bancosNix){
                    select += `<option value="${banco.id}">${banco.nome}</option>`
                }       
                select += "</select>"

                tr = `
                    <tr data-id="${id}">
                        <td>
                            ${select}  
                        </td>
                        <td id="agencia-${id}"></td>
                        <td id="conta-corrente-${id}"></td>
                        <td id="numero-banco-${id}"></td>
                    </tr>
                `

            }else{
                tr = `
                    <tr data-id="${id}">
                        <td>
                            <input type="text" class=" form-control" 
                            data-id="${id}" id="banco-${id}" 
                            name="bancos[]">
                        </td>
                        <td>
                            <input type="text" class=" form-control" 
                            data-id="${id}" id="banco-${id}" 
                            name="agencias[]">
                        </td>
                        <td>
                            <input type="text" class=" form-control" 
                            data-id="${id}" id="banco-${id}" 
                            name="conta_correntes[]">
                        </td>
                        <td>
                            <input type="text" class=" form-control" 
                            data-id="${id}" id="banco-${id}" 
                            name="numero_bancos[]">
                        </td>
                    <tr>
                `
            }
            $('#bancoCliente tbody').append(tr)
        })


        $(document).on('change','.bancoNix',function(){
            let idRow = this.dataset.id;
            let idBanco = this.value;
            const bancosNix = JSON.parse($('#bancoOptions').val())
            const banco = bancosNix.find((el) => el.id == idBanco)
            console.log(banco,idRow)
            $(`#agencia-${idRow}`).text(banco.agencia)
            $(`#conta-corrente-${idRow}`).text(banco.conta_corrente)
            $(`#numero-banco-${idRow}`).text(banco.numero_banco)
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
