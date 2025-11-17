@extends('layouts.app')
@section('title', isset($cliente) ? '' : 'Cadastrar cliente')


@section('content')
    <style>
        /* Prevenir movimento indesejado no container do formulário */
        .card.card-tabs,
        .card.card-primary.card-tabs {
            transition: none !important;
        }
        
        .card.card-tabs:hover,
        .card.card-primary.card-tabs:hover {
            transform: none !important;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1) !important;
        }
        
        /* Prevenir transições em inputs dentro do card */
        .card.card-tabs .form-control,
        .card.card-primary.card-tabs .form-control {
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease !important;
        }
        
        .form-section {
            margin-bottom: 1.75rem;
        }

        .section-title h4 {
            margin-bottom: 0.75rem;
            font-weight: 600;
            color: #1A1A1A;
        }

        .section-divider {
            width: 100%;
            height: 1px;
            background: #cecece;
            margin: 1.5rem 0;
        }

        /* Aumentar a altura do modal */
        .modal-dialog.modal-xl {
            max-width: 90%;

            /* Aumenta a altura do modal */
            margin: 30px auto;
            /* Ajusta a margem para o modal */
        }

        .modal-content {
            height: 100%;
            /* Garante que o conteúdo ocupe toda a altura do modal */
        }
        
        /* Garantir que o Select2 funcione corretamente no modal */
        .select2-container--open {
            z-index: 9999 !important;
        }
        .select2-dropdown {
            z-index: 9999 !important;
        }
        
        /* Corrigir posicionamento do dropdown */
        .select2-container--default .select2-selection--single {
            height: 38px !important;
            padding: 6px 12px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
        
        /* Garantir que o campo de busca funcione */
        .select2-search--dropdown .select2-search__field {
            padding: 8px !important;
            border: 1px solid #ccc !important;
            border-radius: 4px !important;
            width: 100% !important;
            box-sizing: border-box !important;
            pointer-events: auto !important;
            cursor: text !important;
            opacity: 1 !important;
        }
        .select2-search--dropdown .select2-search__field:focus {
            outline: none !important;
            border-color: #007bff !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #aaa !important;
            padding: 5px !important;
        }

        .modal-body {
            height: 100%;
            /* Faz com que o corpo do modal ocupe toda a altura disponível */
            overflow-y: auto;
            /* Permite rolagem se o conteúdo for maior */
        }

        /* Ajusta o iframe */
        #pdf-iframe {
            width: 100%;
            height: 80vh;
            /* Aumenta a altura do iframe */
            border: none;
            /* Remove a borda do iframe */
        }

        /* Ajusta a imagem */
        #imagePreview {
            width: 100%;
            max-height: 80vh;
            /* Limita a altura da imagem */
            object-fit: contain;
            /* Faz com que a imagem se ajuste ao contêiner sem distorção */
            display: none;
            /* Inicialmente escondido */
        }

        /* Ajusta a visibilidade do texto de descrição */
        #doc-text {
            display: none;
            /* Inicialmente escondido */
        }
    </style>
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
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-three-settings-tab" data-toggle="pill"
                                    href="#custom-tabs-three-settings" role="tab"
                                    aria-controls="custom-tabs-three-settings" aria-selected="false">Documentos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-four-settings-tab" data-toggle="pill"
                                    href="#custom-tabs-four-settings" role="tab"
                                    aria-controls="custom-tabs-four-settings" aria-selected="false">Fornecedores</a>
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
                                @if(auth()->user()?->hasPermission('clientes_dados_cadastrais_visualizar') || auth()->user()?->isSuperUser())
                                <div class="form-section">
                                    <div class="section-title">
                                        <h4>Empresa</h4>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="exampleInputEmail1" class="form-label">Nome</label>
                                            <input value="{{ old('name', $cliente->nome ?? '') }}" class="form-control"
                                                name="name" id="nome">
                                            @error('name')
                                                <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                                            @enderror
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1" class="form-label">CNPJ</label>
                                                <input value="{{ old('cnpj', $cliente->cnpj ?? '') }}"
                                                    class="form-control" name="cnpj" id="cnpj">
                                                @error('cnpj')
                                                    <span
                                                        class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if(auth()->user()?->hasPermission('clientes_dados_cadastrais_visualizar') || auth()->user()?->isSuperUser())
                                <div class="section-divider"></div>
                                @endif
                                @if(auth()->user()?->hasPermission('clientes_dados_cadastrais_visualizar') || auth()->user()?->isSuperUser())
                                <div class="form-section">
                                    <div class="section-title">
                                        <h4>Endereço</h4>
                                    </div>
                                    <div class="row">
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1" class="form-label">CEP</label>
                                                <input value="{{ old('cep', $cliente->cep ?? '') }}" class="form-control"
                                                    name="cep" id="cep">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1" class="form-label">Logradouro</label>
                                                <input value="{{ old('logradouro', $cliente->logradouro ?? '') }}"
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
                                                <input value="{{ old('numero', $cliente->numero ?? '') }}"
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
                                                <input value="{{ old('bairro', $cliente->bairro ?? '') }}"
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
                                                <input value="{{ old('cidade', $cliente->cidade ?? '') }}"
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
                                                <input value="{{ old('estado', $cliente->estado ?? '') }}"
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
                                                <input value="{{ old('complemento', $cliente->complemento ?? '') }}"
                                                    class="form-control" name="complemento" id="complemento">
                                                @error('complemento')
                                                    <span
                                                        class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if(auth()->user()?->hasPermission('clientes_responsaveis_visualizar') || auth()->user()?->isSuperUser())
                                <div class="section-divider"></div>
                                <div class="form-section">
                                    <div class="section-title">
                                        <h4>Responsável Legal</h4>
                                    </div>
                                    <div class="row">
                                        <div class="col-3">
                                            <label for="exampleInputEmail1" class="form-label">Nome</label>
                                            <input value="{{ old('nome_responsavel_legal', $cliente->nome_responsavel_legal ?? '') }}"
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
                                                    value="{{ old('cpf_responsavel_legal', $cliente->cpf_responsavel_legal ?? '') }}"
                                                    class="form-control" name="cpf_responsavel_legal"
                                                    id="cpf_responsavel_legal">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1" class="form-label">Email</label>
                                                <input
                                                    value="{{ old('email_responsavel_legal', $cliente->email_responsavel_legal ?? '') }}"
                                                    class="form-control" name="email_responsavel_legal"
                                                    id="email_responsavel_legal">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1" class="form-label">Telefone Fixo</label>
                                                <input
                                                    value="{{ old('telefone_fixo_responsavel_legal', $cliente->telefone_fixo_responsavel_legal ?? '') }}"
                                                    class="form-control" name="telefone_fixo_responsavel_legal"
                                                    id="telefone_fixo_responsavel_legal">
                                                @error('telefone_fixo_responsavel_legal')
                                                    <span
                                                        class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1" class="form-label">Telefone Celular</label>
                                                <input
                                                    value="{{ old('telefone_celular_responsavel_legal', $cliente->telefone_celular_responsavel_legal ?? '') }}"
                                                    class="form-control" name="telefone_celular_responsavel_legal"
                                                    id="telefone_celular_responsavel_legal">
                                                @error('telefone_celular_responsavel_legal')
                                                    <span
                                                        class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
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
                                                                        id="email-{{ $loop->index }}" name="emails[]">
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
                                                                    <select class="form-control" id="modalidade"
                                                                        name="modalidades[]">
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
                                                                        id="aduana-{{ $loop->index }}"
                                                                        name="urf_despacho[]">
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
                                <form id="formEspecificidades" enctype="multipart/form-data"
                                    action="{{ route('cliente.update.especificidades', $cliente->id) }}" method="POST">
                                    @csrf
                                    <div class="row gap-2">
                                        <div class="col-lg-4 col-md-6 col-sm-6">
                                            <div class="form-group">
                                                <label for="credenciamento_radar">Credenciamento SISCOMEX</label>
                                                <input type="date" class="form-control" id="credenciamento_radar"
                                                    name="credenciamento_radar_inicial"
                                                    value="{{ old('credenciamento_radar_inicial', $cliente->credenciamento_radar_inicial) }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6">
                                            <label for="validationTooltip03">Data da Procuração</label>
                                            <input value="{{ old('data_procuracao', $cliente->data_procuracao ?? '') }}"
                                                type="date" class="form-control" id="data_procuracao"
                                                name="data_procuracao">

                                        </div>

                                        <div class="col-lg-4 col-md-6 col-sm-6">
                                            <label for="validationTooltip03">Data de Vencimento da Procuração</label>
                                            <input
                                                value="{{ old('data_vencimento_procuracao', $cliente->data_vencimento_procuracao ?? '') }}"
                                                type="date" class="form-control" id="data_vencimento_procuracao"
                                                name="data_vencimento_procuracao">

                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6">
                                            <div class="form-group">
                                                <label for="marinha_mercante">Cadastro AFRMM - Sistema</label>
                                                <input type="date" class="form-control" id="marinha_mercante"
                                                    name="marinha_mercante_inicial"
                                                    value="{{ old('marinha_mercante_inicial', $cliente->marinha_mercante_inicial) }}">
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group">
                                                <label for="afrmm_bb">Cadastro AFRMM Banco do Brasil</label>
                                                @php
                                                    $afrmmValue = old('afrmm_bb', isset($cliente) ? ($cliente->afrmm_bb ? 'true' : ($cliente->afrmm_bb === 0 ? 'false' : null)) : null);
                                                @endphp
                                                <select class="form-control" id="afrmm_bb" name="afrmm_bb">
                                                    <option value="" disabled
                                                        {{ $afrmmValue === null ? 'selected' : '' }}>
                                                        Selecione uma opção</option>
                                                    <option {{ $afrmmValue === 'true' ? 'selected' : '' }}
                                                        value="true">
                                                        Sim</option>
                                                    <option {{ $afrmmValue === 'false' ? 'selected' : '' }}
                                                        value="false">
                                                        Não</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group px-0">
                                                <div class="custom-control px-0">
                                                    <label class="">Cadastro Itaú
                                                    </label>
                                                    @php
                                                        $itauValue = old('itau_di', isset($cliente) ? ($cliente->itau_di ? 'true' : ($cliente->itau_di === 0 ? 'false' : null)) : null);
                                                    @endphp
                                                    <select class="form-control" id="itau_di" name="itau_di">
                                                        <option value="" disabled
                                                            {{ $itauValue === null ? 'selected' : '' }}>
                                                            Selecione uma opção
                                                        </option>
                                                        <option {{ $itauValue === 'true' ? 'selected' : '' }}
                                                            value="true">Sim</option>
                                                        <option {{ $itauValue === 'false' ? 'selected' : '' }}
                                                            value="false">Não</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">


                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold" for="modalidade_radar">Modalidade
                                                    Radar</label>
                                                <select class="form-control" id="modalidade_radar"
                                                    name="modalidade_radar">
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
                                                {{ old('debito_impostos_nix', $cliente->debito_impostos) == 'cliente' ? 'selected' : '' }}>
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
                                                        <th scope="col">N° Banco</th>
                                                        <th scope="col">Banco</th>
                                                        <th scope="col">Agência</th>
                                                        <th scope="col">Conta corrente</th>
                                                        <th scope="col">Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($bancosCliente as $banco)
                                                        <tr data-banco-id="{{ $banco->id }}" data-banco-nix="{{ $banco->banco_nix ? '1' : '0' }}">
                                                            <td>
                                                                <input value="{{ $banco->numero_banco }}"
                                                                    {{ $banco->banco_nix ? 'disabled' : '' }}
                                                                    type="text" class=" form-control"
                                                                    data-id="{{ $loop->index }}"
                                                                    id="banco-{{ $loop->index }}"
                                                                    name="{{ $banco->banco_nix ? '' : 'numero_bancos[]' }}">
                                                            </td>
                                                            <td>

                                                                <input value="{{ $banco->nome }}" type="text"
                                                                    {{ $banco->banco_nix ? 'disabled' : '' }}
                                                                    class=" form-control" data-id="{{ $loop->index }}"
                                                                    id="banco-{{ $loop->index }}"
                                                                    name="{{ $banco->banco_nix ? '' : 'bancos[]' }}">
                                                            </td>
                                                            <td>
                                                                <input value="{{ $banco->agencia }}"
                                                                    {{ $banco->banco_nix ? 'disabled' : '' }}
                                                                    type="text" class=" form-control"
                                                                    data-id="{{ $loop->index }}"
                                                                    id="banco-{{ $loop->index }}"
                                                                    name="{{ $banco->banco_nix ? '' : 'agencias[]' }}">
                                                            </td>
                                                            <td>
                                                                <input value="{{ $banco->conta_corrente }}"
                                                                    {{ $banco->banco_nix ? 'disabled' : '' }}
                                                                    type="text" class=" form-control"
                                                                    data-id="{{ $loop->index }}"
                                                                    id="banco-{{ $loop->index }}"
                                                                    name="{{ $banco->banco_nix ? '' : 'conta_correntes[]' }}">
                                                            </td>

                                                            <td>
                                                                @if (!$banco->banco_nix)
                                                                    <input type="hidden" name="bancos_ids[]" value="{{ $banco->id }}">
                                                                    <input type="hidden" name="bancos_remover[]" value="0" class="banco-remover-flag">
                                                                    <button type="button" class="btn btn-sm btn-danger btn-remover-banco" title="Remover banco" data-banco-id="{{ $banco->id }}">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                @else
                                                                    <span class="text-muted">Banco NIX</span>
                                                                @endif
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
                            <div class="tab-pane fade" id="custom-tabs-three-settings" role="tabpanel"
                                aria-labelledby="custom-tabs-three-settings-info">
                                <div class="row">
                                    <div class="col-2 d-flex justify-content-center align-center align-items-start">
                                        <button id="addDocumento" type="button"
                                            class="btn btn-success rounded shadow">Adicionar Novo Documento
                                        </button>
                                    </div>
                                    <div class="col-10">
                                        <form enctype="multipart/form-data"
                                            action="{{ route('cliente.update.documents', $cliente->id) }}"
                                            id="form-documents" method="POST">
                                            @csrf
                                            <table class="table table-bordered" id="clientesDocumento">
                                                <thead class="thead-primary">
                                                    <tr class="bg-primary">
                                                        <th scope="col">Tipo do documento</th>
                                                        <th scope="col">Arquivo</th>
                                                        <th scope="col">Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    @if (count($cliente->documentos) > 0)
                                                        @foreach ($cliente->documentos as $documento)
                                                            <tr data-id="{{ $loop->index }}">
                                                                <input type="hidden" name="idDocumentos[]"
                                                                    value="{{ $documento->id }}">
                                                                <td>
                                                                    <select class="form-control"
                                                                        id="tipo_documento-{{ $loop->index }}"
                                                                        name="tipoDocumentos[]">
                                                                        <option value="" hidden disabled>Selecione...
                                                                        </option>
                                                                        @php
                                                                            $opcoes = [
                                                                                'contrato_social' => 'Contrato Social',
                                                                                'cnpj' => 'CNPJ',
                                                                                'procuracao' => 'Procuração',
                                                                                'termo_credenciamento_radar' =>
                                                                                    'Termo Credenciamento RADAR',
                                                                                'tare_beneficio_fiscal' =>
                                                                                    'TARE - Benefício Fiscal',
                                                                                'proposta_comercial_recintos_aduaneiros' =>
                                                                                    'Proposta Comercial Recintos Aduaneiros',
                                                                                'proposta_nix' => 'Proposta NIX',
                                                                            ];
                                                                        @endphp
                                                                        @foreach ($opcoes as $valor => $label)
                                                                            <option value="{{ $valor }}"
                                                                                {{ $valor == $documento->tipo_documento ? 'selected' : '' }}>
                                                                                {{ $label }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td>

                                                                    <input type="file"
                                                                        accept="image/*,application/pdf,.docx"
                                                                        name="documentos[]" class="d-none"
                                                                        data-id="{{ $loop->index }}"
                                                                        id="file-{{ $loop->index }}">
                                                                    <p id="legenda-file-{{ $loop->index }}"
                                                                        style="font-size: 14px">
                                                                        {{ explode('/', $documento->path_file)[1] }}</p>

                                                                </td>

                                                                <td>
                                                                    <button type="button" data-toggle="modal"
                                                                        data-target="#previewModal"
                                                                        data-nome="{{ $documento->tipo_documento }}"
                                                                        data-url="{{ $documento->url }}"
                                                                        class="btn btn-sm btn-info previewModalButton">
                                                                        <i class="fas fa-info"></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-sm btn-danger"
                                                                        onclick="showDeleteConfirmation({{ $documento->id }})">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-sm btn-primary"
                                                                        onclick="downloadDocument('{{ $documento->url }}')">
                                                                        <i class="fas fa-download"></i>
                                                                    </button>
                                                                    <button type="button" data-id="{{ $loop->index }}"
                                                                        for="inputFile"
                                                                        class="btn btn-sm btn-secondary anexarArquivo">
                                                                        <i class="fas fa-paperclip"></i>

                                                                    </button>
                                                                </td>

                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td>Não há documentos cadastrados</td>
                                                        </tr>
                                                    @endif
                                                </tbody>

                                            </table>
                                            <button type="submit" class="btn btn-primary mt-3">Salvar</button>

                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="custom-tabs-four-settings" role="tabpanel"
                                aria-labelledby="custom-tabs-four-settings-info">
                                <div class="w-100 d-flex justify-content-end mb-3">
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                        data-target="#fornecedorModal">Adicionar
                                        fornecedor</button>
                                </div>
                                @if (isset($cliente) && !$cliente->fornecedores->isEmpty())
                                    <div class="table-responsive">
                                        <table id="fornecedorTable" class="table table-striped table-hover mb-0">
                                            <thead style="background: var(--theme-gradient-primary);">
                                                <tr>
                                                    <th class="text-white">Nome</th>
                                                    <th class="text-white">CNPJ</th>
                                                    <th class="text-white">País</th>
                                                    <th class="text-center text-white">Ações</th>
                                                </tr>
                                            </thead>
                                        <tbody>
                                            @foreach ($cliente->fornecedores as $fornecedor)
                                                <tr
                                                    @if ($fornecedor->deleted_at != null) style="background-color:#ff8e8e" @endif>
                                                    <td>{{ $fornecedor->nome }}</td>
                                                    <td>{{ substr($fornecedor->cnpj, 0, 2) . '.' . substr($fornecedor->cnpj, 2, 3) . '.' . substr($fornecedor->cnpj, 5, 3) . '/' . substr($fornecedor->cnpj, 8, 4) . '-' . substr($fornecedor->cnpj, 12, 2) }}
                                                    </td>
                                                    <td>{{ $fornecedor->pais_origem }}</td>

                                                    <td>
                                                        <div class="d-flex justify-content-center" style="gap: 8px;">
                                                            <button data-toggle="modal" data-target="#fornecedorModal"
                                                                data-route="{{ route('fornecedor.update', $fornecedor->id) }}"
                                                                type="button" data-nome="{{ $fornecedor->nome }}"
                                                                data-cnpj="{{ $fornecedor->cnpj }}"
                                                                data-pais_origem="{{ $fornecedor->pais_origem }}"
                                                                data-logradouro="{{ $fornecedor->logradouro }}"
                                                                data-numero="{{ $fornecedor->numero }}"
                                                                data-complemento="{{ $fornecedor->complemento }}"
                                                                data-cidade="{{ $fornecedor->cidade }}"
                                                                data-estado="{{ $fornecedor->estado }}"
                                                                data-nome_contato="{{ $fornecedor->nome_contato }}"
                                                                data-email_contato="{{ $fornecedor->email_contato }}"
                                                                data-telefone_contato="{{ $fornecedor->telefone_contato }}"
                                                                class="btn btn-warning btn-sm editModal" title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </button>

                                                            <form method="POST"
                                                                action="{{ route($cliente->deleted_at == null ? 'fornecedor.destroy' : 'fornecedor.ativar', $cliente->id) }}"
                                                                class="delete-form-fornecedor" enctype="multipart/form-data">
                                                                @method('DELETE')
                                                                @csrf
                                                                <button type="button" data-id="{{ $fornecedor->id }}"
                                                                    class="btn btn-danger btn-sm removeFornecedor" title="Excluir">
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
                        @endif
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>

    </div>

    <form id="delete-form" method="POST" action="{{ route('documento.cliente.destroy', 'document_id') }}"
        style="display:none;">
        @method('DELETE')
        @csrf
    </form>
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-xl">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Documento de <span id="tipoDocumentoName"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body w-100">
                    <p class="text-break" id="tipoDocumentoDescription"></p>
                    <img class="w-100" src="" id="imagePreview" alt="">
                    <iframe class="w-100 h-100" src="" id="pdf-iframe" frameborder="0"></iframe>
                    <p class="w-100" id="doc-text"> Pré visualização não disponível</p>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="fornecedorModal" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--theme-gradient-primary);">
                    <h5 class="modal-title text-white" id="exampleModalLabel">
                        <i class="fas fa-truck mr-2"></i><span>Adicionar</span> fornecedor
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="fornecedorForm" action="{{ route('fornecedor.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">

                        @csrf
                        @if (isset($cliente))
                            <input type="hidden" name="cliente_id" value="{{ $cliente->id }}">
                        @endif
                        <div class="row">
                            <div class="col-3 form-group">
                                <label class="form-label">Nome</label>
                                <input class="form-control" name="nome" id="nome">
                                @error('nome')
                                    <span class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                                @enderror
                            </div>


                            <div class="col-2 form-group">
                                <label class="form-label">CNPJ</label>
                                <input class="form-control" name="cnpj" id="cnpj">
                                @error('cnpj')
                                    <span class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                                @enderror
                            </div>

                            <div class="col-3 form-group">
                                <label class="form-label">País de Origem</label>
                                <select name="pais_origem" class="form-control select2 w-100" id="paises" style="width: 100%">
                                    <option value="" selected>Selecione um país</option>
                                    @php
                                        $paises = [
                                            'Afeganistão',
                                            'África do Sul',
                                            'Albânia',
                                            'Alemanha',
                                            'Andorra',
                                            'Angola',
                                            'Antiga e Barbuda',
                                            'Arábia Saudita',
                                            'Argélia',
                                            'Argentina',
                                            'Arménia',
                                            'Austrália',
                                            'Áustria',
                                            'Azerbaijão',
                                            'Bahamas',
                                            'Bangladexe',
                                            'Barbados',
                                            'Barém',
                                            'Bélgica',
                                            'Belize',
                                            'Benim',
                                            'Bielorrússia',
                                            'Bolívia',
                                            'Bósnia e Herzegovina',
                                            'Botsuana',
                                            'Brasil',
                                            'Brunei',
                                            'Bulgária',
                                            'Burquina Faso',
                                            'Burúndi',
                                            'Butão',
                                            'Cabo Verde',
                                            'Camarões',
                                            'Camboja',
                                            'Canadá',
                                            'Catar',
                                            'Cazaquistão',
                                            'Chade',
                                            'Chile',
                                            'China',
                                            'Chipre',
                                            'Colômbia',
                                            'Comores',
                                            'Congo-Brazzaville',
                                            'Coreia do Norte',
                                            'Coreia do Sul',
                                            'Cosovo',
                                            'Costa do Marfim',
                                            'Costa Rica',
                                            'Croácia',
                                            'Cuaite',
                                            'Cuba',
                                            'Dinamarca',
                                            'Dominica',
                                            'Egito',
                                            'Emirados Árabes Unidos',
                                            'Equador',
                                            'Eritreia',
                                            'Eslováquia',
                                            'Eslovénia',
                                            'Espanha',
                                            'Estado da Palestina',
                                            'Estados Unidos',
                                            'Estónia',
                                            'Etiópia',
                                            'Fiji',
                                            'Filipinas',
                                            'Finlândia',
                                            'França',
                                            'Gabão',
                                            'Gâmbia',
                                            'Gana',
                                            'Geórgia',
                                            'Granada',
                                            'Grécia',
                                            'Guatemala',
                                            'Guiana',
                                            'Guiné',
                                            'Guiné Equatorial',
                                            'Guiné-Bissau',
                                            'Haiti',
                                            'Honduras',
                                            'Hong Kong',
                                            'Hungria',
                                            'Iémen',
                                            'Ilhas Marechal',
                                            'Índia',
                                            'Indonésia',
                                            'Irão',
                                            'Iraque',
                                            'Irlanda',
                                            'Islândia',
                                            'Israel',
                                            'Itália',
                                            'Jamaica',
                                            'Japão',
                                            'Jibuti',
                                            'Jordânia',
                                            'Laus',
                                            'Lesoto',
                                            'Letónia',
                                            'Líbano',
                                            'Libéria',
                                            'Líbia',
                                            'Listenstaine',
                                            'Lituânia',
                                            'Luxemburgo',
                                            'Macedónia do Norte',
                                            'Madagáscar',
                                            'Malásia',
                                            'Maláui',
                                            'Maldivas',
                                            'Mali',
                                            'Malta',
                                            'Marrocos',
                                            'Maurícia',
                                            'Mauritânia',
                                            'México',
                                            'Mianmar',
                                            'Micronésia',
                                            'Moçambique',
                                            'Moldávia',
                                            'Mónaco',
                                            'Mongólia',
                                            'Montenegro',
                                            'Namíbia',
                                            'Nauru',
                                            'Nepal',
                                            'Nicarágua',
                                            'Níger',
                                            'Nigéria',
                                            'Noruega',
                                            'Nova Zelândia',
                                            'Omã',
                                            'Países Baixos',
                                            'Palau',
                                            'Panamá',
                                            'Papua Nova Guiné',
                                            'Paquistão',
                                            'Paraguai',
                                            'Peru',
                                            'Polónia',
                                            'Portugal',
                                            'Quénia',
                                            'Quirguistão',
                                            'Quiribáti',
                                            'Reino Unido',
                                            'República Centro-Africana',
                                            'República Checa',
                                            'República Democrática do Congo',
                                            'República Dominicana',
                                            'Roménia',
                                            'Ruanda',
                                            'Rússia',
                                            'Salomão',
                                            'Salvador',
                                            'Samoa',
                                            'Santa Lúcia',
                                            'São Cristóvão e Neves',
                                            'São Marinho',
                                            'São Tomé e Príncipe',
                                            'São Vicente e Granadinas',
                                            'Seicheles',
                                            'Senegal',
                                            'Serra Leoa',
                                            'Sérvia',
                                            'Singapura',
                                            'Síria',
                                            'Somália',
                                            'Sri Lanca',
                                            'Suazilândia',
                                            'Sudão',
                                            'Sudão do Sul',
                                            'Suécia',
                                            'Suíça',
                                            'Suriname',
                                            'Tailândia',
                                            'Taiuão',
                                            'Tajiquistão',
                                            'Tanzânia',
                                            'Timor-Leste',
                                            'Togo',
                                            'Tonga',
                                            'Trindade e Tobago',
                                            'Tunísia',
                                            'Turcomenistão',
                                            'Turquia',
                                            'Tuvalu',
                                            'Ucrânia',
                                            'Uganda',
                                            'Uruguai',
                                            'Usbequistão',
                                            'Vanuatu',
                                            'Vaticano',
                                            'Venezuela',
                                            'Vietname',
                                            'Zâmbia',
                                            'Zimbábué',
                                        ];

                                    @endphp
                                    @foreach ($paises as $pais)
                                        <option value="{{ $pais }}">{{ $pais }}</option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="col-3 form-group">
                                <label class="form-label">Logradouro</label>
                                <input class="form-control" name="logradouro">
                            </div>
                            <div class="col-1 form-group">
                                <label class="form-label">Número</label>
                                <input class="form-control" name="numero">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4 form-group">
                                <label class="form-label">Complemento</label>
                                <input class="form-control" name="complemento">
                            </div>
                            <div class="col-3 form-group">
                                <label class="form-label">Cidade</label>
                                <input class="form-control" name="cidade">
                            </div>

                            <div class=" col-2 form-group">
                                <label class="form-label">Estado</label>
                                <input class="form-control" name="estado">
                            </div>

                        </div>
                        <div style="height: 0.1px; width: 100%; border: 0.1px solid #cecece;" class="mb-3">
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h4>Contato</h4>
                            </div>
                            <div class="col-4">
                                <label for="exampleInputEmail1" class="form-label">Nome</label>
                                <input class="form-control" name="nome_contato" id="nome_contato">
                                @error('nome_contato')
                                    <span class="mt-1  text-red p-1 rounded"><small>{{ $message }}</small></span>
                                @enderror
                            </div>

                            <div class="col-2">
                                <div class="form-group">
                                    <label for="exampleInputEmail1" class="form-label">Email</label>
                                    <input class="form-control" name="email_contato" id="email_contato">

                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label for="exampleInputEmail1" class="form-label">Telefone</label>
                                    <input class="form-control" name="telefone_contato" id="telefone_contato">
                                    @error('telefone_contato')
                                        <span class="mt-1 text-red p-1 rounded"><small>{{ $message }}</small></span>
                                    @enderror
                                </div>
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
    <input type="hidden" id="bancoOptions" value="{{ json_encode($bancosNix) }}" alt="">
    <input type="hidden" id="tipoDocumentoOptions" value="{{ json_encode($tipoDocumentos) }}" alt="">
    <script>
        const toastLocal = Swal.mixin({
            toast: true,
            position: 'top-end',
            customClass: {
                popup: 'colored-toast',
            },
            animation: true,
            showConfirmButton: false,
            showCloseButton: true,
            timer: 3500,
            timerProgressBar: true,
            allowOutsideClick: true,
        });

        function downloadDocument(url) {
            const link = document.createElement('a');
            link.href = url;
            link.download = url.substring(url.lastIndexOf('/') + 1); // Obtém o nome do arquivo a partir da URL

            link.click();

            toastLocal.fire({
                icon: 'success',
                title: 'Download iniciado...'
            });
        }

        function showDeleteConfirmation(documentId) {
            const deleteUrl = '/destroy-document/' + documentId; // Ajuste a URL conforme necessário

            // SweetAlert de confirmação
            Swal.fire({
                title: 'Você tem certeza?',
                text: 'Esta ação não poderá ser desfeita!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Modificando o formulário dinamicamente
                    $('#delete-form').attr('action', deleteUrl); // Atualiza a URL do formulário
                    $('#delete-form').submit(); // Submete o formulário
                } else {
                    // Caso o usuário cancele, podemos mostrar um toast de cancelamento
                    Toast.fire({
                        icon: 'info',
                        title: 'Ação cancelada'
                    });
                }
            });
        }
    </script>
    
    <script>
        // Inicializar Select2 quando a página estiver pronta
        $(document).ready(function() {
            // Máscaras de input
            $('input[name=cnpj]').mask('99.999.999/9999-99')
            $('input[name=cpf_responsavel_legal]').mask('999.999.999-99')
            
            // Restaurar tab ativa
            var activeTab = localStorage.getItem('activeTab');
            if (activeTab) {
                $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
            }
            
            // Inicializar Select2 do campo de países assim que a página estiver pronta
            var $selectPaises = $('#paises');
            if ($selectPaises.length > 0) {
                $selectPaises.select2({
                    placeholder: 'Selecione um país',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#fornecedorModal'),
                    language: {
                        noResults: function() {
                            return "Nenhum resultado encontrado";
                        },
                        searching: function() {
                            return "Buscando...";
                        }
                    },
                    minimumResultsForSearch: 1,
                    minimumInputLength: 0
                });
            }
            
            // Limpar formulário quando o modal for fechado (evento simples)
            $('#fornecedorModal').on('hidden.bs.modal', function() {
                $('#fornecedorForm')[0].reset();
                $('#paises').val('').trigger('change');
                $('#fornecedorModal .modal-title span').text('Adicionar');
                $('#fornecedorModal input[name="_method"]').remove();
                $('#fornecedorModal #fornecedorForm').attr('action', '{{ route('fornecedor.store') }}');
            });
        });
        $(document).on('click', '.removeFornecedor', function() {
            Swal.fire({
                title: 'Você tem certeza que deseja excluir este registro?',
                text: 'Esta ação não poderá ser desfeita! Todos os produtos desse fornecedor serão excluídos e consequentemente os produtos que estiverem em processos.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const id = this.dataset.id;
                    const deleteUrl = `/fornecedor/${id}`;
                    const form = $(this).closest('.delete-form-fornecedor');
                    form.attr('action', deleteUrl);
                    form.submit();
                } else {
                    Toast.fire({
                        icon: 'info',
                        title: 'Ação cancelada'
                    });
                }
            });
        });

        // Evento quando o botão de editar fornecedor é clicado
        $(document).on('click', '.editModal', function(event) {
            var button = $(event.currentTarget)
            var route = button.data('route')
            var modal = $('#fornecedorModal')
            
            // Configurar formulário
            modal.find('#fornecedorForm').attr('action', route)
            modal.find('.modal-title span').text('Editar')
            if (modal.find('input[name="_method"]').length === 0) {
                modal.find('#fornecedorForm').prepend('<input type="hidden" name="_method" value="put">');
            } else {
                modal.find('input[name="_method"]').val('put');
            }
            
            // Preencher campos
            var cnpj = button.data('cnpj');
            if (cnpj) {
                cnpj = String(cnpj);
                cnpj = cnpj.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, "$1.$2.$3/$4-$5");
                modal.find('input[name="cnpj"]').val(cnpj);
            }
            modal.find('input[name="nome"]').val(button.data('nome'));
            modal.find('input[name="logradouro"]').val(button.data('logradouro'));
            modal.find('input[name="numero"]').val(button.data('numero'));
            modal.find('input[name="complemento"]').val(button.data('complemento'));
            modal.find('input[name="cidade"]').val(button.data('cidade'));
            modal.find('input[name="estado"]').val(button.data('estado'));
            modal.find('input[name="nome_contato"]').val(button.data('nome_contato'));
            modal.find('input[name="email_contato"]').val(button.data('email_contato'));
            modal.find('input[name="telefone_contato"]').val(button.data('telefone_contato'));
            
            // Atualizar Select2 com o país de origem
            var paisOrigem = button.data('pais_origem');
            if (paisOrigem) {
                $('#paises').val(paisOrigem).trigger('change');
            }
        });

        $('.nav-link').on('click', function(e) {
            var currentTab = $(e.target).attr('href');
            localStorage.setItem('activeTab', currentTab);
        });
        $(document).on('click', '.anexarArquivo', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            $(`#file-${id}`).click();

        });
        $(document).on('change', 'input[type="file"]', function() {
            const id = this.dataset.id;
            const file = this.files[0];

            if (file) {
                $(`#legenda-file-${id}`).text(file.name);
                Toast.fire({
                    icon: 'success',
                    title: 'Documento anexado'
                });
                const tipo_documento = $(`#tipo_documento-${id}`).val()
                if (tipo_documento) {
                    $('#form-documents').submit()
                }
            } else {
                $(`#legenda-file-${id}`).text('Nenhum arquivo selecionado');
            }
        });
        $('#debito_impostos_nix').on('change', async function() {
            // let value = this.value
            // $('#bancoCliente tbody tr').remove()
            // if (value == 'nix') {
            //     $('#colBancoNix').removeClass('d-none')
            //     $('#colNomeBancoNix').addClass('d-none')
            // } else {
            //     $('#colNomeBancoNix').removeClass('d-none')
            //     $('#colBancoNix').addClass('d-none')
            // }

        })
        $('#cep').on('change', async function() {
            let cep = this.value.replace(/\D/g, '');
            if (cep.length == 8) {

                await fetch(`https://viacep.com.br/ws/${cep}/json/`, ).then(async (response) => {
                    const resultado = await response.json();

                    $('#logradouro').val(resultado.logradouro)
                    $('#bairro').val(resultado.bairro)
                    $('#cidade').val(resultado.localidade)
                    $('#estado').val(resultado.estado)
                });
            }
        })

        // Handler para remover banco - marca para remoção e submete o formulário
        $(document).on('click', '.btn-remover-banco', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const row = $(this).closest('tr');
            
            // Marcar o banco para remoção
            row.find('.banco-remover-flag').val('1');
            
            // Desabilitar inputs da linha para que não sejam processados
            row.find('input[type="text"]').prop('disabled', true);
            
            // Esconder a linha visualmente
            row.fadeOut(200);
            
            // Submeter o formulário automaticamente
            const form = $('#formEspecificidades');
            if (form.length) {
                form.submit();
            }
        });
        
        $('#addBank').on('click', async function() {
            let id = $('#bancoCliente tbody tr').length;
            let tr = '';
            tr = `
                    <tr data-id="${id}">
                        <td>
                            <input type="text" class=" form-control" 
                            data-id="${id}" id="banco-${id}" 
                            name="numero_bancos[]">
                        </td>
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
                            <input type="hidden" name="bancos_ids[]" value="">
                            <input type="hidden" name="bancos_remover[]" value="0" class="banco-remover-flag">
                            <button type="button" class="btn btn-sm btn-danger btn-remover-banco" title="Remover banco">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `
            $('#bancoCliente tbody').append(tr)
        })

        $(document).on('change', '.bancoNix', function() {
            let idRow = this.dataset.id;
            let idBanco = this.value;
            const bancosNix = JSON.parse($('#bancoOptions').val())
            const banco = bancosNix.find((el) => el.id == idBanco)
            console.log(banco, idRow)
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
        $('#addDocumento').on('click', async function() {
            let id = $('#clientesDocumento tbody tr').length;
            const tipoDocumentos = [{
                    id: 'contrato_social',
                    nome: 'Contrato Social'
                },
                {
                    id: 'cnpj',
                    nome: 'CNPJ'
                },
                {
                    id: 'procuracao',
                    nome: 'Procuração'
                },
                {
                    id: 'termo_credenciamento_radar',
                    nome: 'Termo Credenciamento RADAR'
                },
                {
                    id: 'tare_beneficio_fiscal',
                    nome: 'TARE - Benefício Fiscal'
                },
                {
                    id: 'proposta_comercial_recintos_aduaneiros',
                    nome: 'Proposta Comercial Recintos Aduaneiros'
                },
                {
                    id: 'proposta_nix',
                    nome: 'Proposta NIX'
                }
            ];

            let select = `<select class="form-control" name="tipoDocumentos[]" id="tipo_documento-${id}">`;
            select += `<option hidden value="">Selecione uma opção</option>`;

            for (let tipo of tipoDocumentos) {
                select += `<option value="${tipo.id}">${tipo.nome}</option>`;
            }

            select += "</select>";

            let tr = `<tr data-id="${id}">
    <td>
        ${select}
    </td>
    <td colspan="1" >
<input type="file"
    accept="image/*,application/pdf,.docx"
    name="documentos[]"
    class="d-none"
    data-id="${id}"
    id="file-${id}">

          <p id="legenda-file-${id}" style="font-size: 14px"></p>

      
    </td>
    <td>
            <button type="button" data-id="${id}" for="inputFile" class="btn btn-sm btn-secondary anexarArquivo">
                                                                        <i class="fas fa-paperclip"></i>

            </button>
    </td>
</tr>`;
            $('#clientesDocumento tbody').append(tr)


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

        // Evento quando o modal de preview é exibido
        $(document).on('show.bs.modal', '#previewModal', function(event) {
            // Buscar dados do botão que acionou o modal
            const button = $(event.relatedTarget);
            if (button.length && button.hasClass('previewModalButton')) {
                const fileUrl = button.data('url');
                const fileType = fileUrl ? fileUrl.split('.').pop().toLowerCase() : '';
                const nome = button.data('nome') || '';
                const descricao = button.data('descricao') || '';

                // Definir o nome do documento
                $('#tipoDocumentoName').text(nome);
                $('#tipoDocumentoDescription').text(descricao);

                // Limpar conteúdo anterior
                $('#pdf-iframe').attr('src', '').hide();
                $('#imagePreview').attr('src', '').hide();
                $('#doc-text').hide();

                // Configurar conteúdo baseado no tipo de arquivo
                if (fileUrl) {
                    if (fileType === 'pdf') {
                        $('#pdf-iframe').attr('src', fileUrl).show();
                        $('#imagePreview').hide();
                        $('#doc-text').hide();
                    } else if (fileType === 'docx') {
                        $('#doc-text').text('Descrição: Documento indisponível').show();
                        $('#imagePreview').hide();
                        $('#pdf-iframe').hide();
                    } else if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'].includes(fileType)) {
                        $('#imagePreview').attr('src', fileUrl).show();
                        $('#pdf-iframe').hide();
                        $('#doc-text').hide();
                    } else {
                        $('#doc-text').text('Tipo de arquivo não suportado').show();
                        $('#imagePreview').hide();
                        $('#pdf-iframe').hide();
                    }
                } else {
                    $('#doc-text').text('URL do documento não disponível').show();
                    $('#imagePreview').hide();
                    $('#pdf-iframe').hide();
                }
            }
        });
    </script>


@endsection
