@extends('layouts.app')
@section('title', isset($cliente) ? '' : 'Cadastrar Processo')


@section('content')
    <style>
        /* Aumentar a altura do modal */
        .modal-dialog.modal-xl {
            max-width: 90%;
            height: 80vh;
            /* Aumenta a altura do modal */
            margin: 30px auto;
            /* Ajusta a margem para o modal */
        }

        .modal-content {
            height: 100%;
            /* Garante que o conteúdo ocupe toda a altura do modal */
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

        th {
            min-width: 200px;
        }
    </style>
    <style>
        .dados-container {
            display: flex;
            gap: 40px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin-bottom: 30px;
            margin-top: 30px
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .dados-basicos th,
        .dados-basicos td {
            border: 1px solid #000;
            padding: 5px 8px;
        }

        .dados-basicos th {
            background-color: #d9d9d9;
            text-align: left;
        }

        .highlight {
            background-color: #fef7a1;
        }

        .green-highlight {
            background-color: #d4f4d4;
        }

        .info-complementares {
            max-width: 400px;
        }

        .info-complementares h4 {
            background-color: #d9d9d9;
            padding: 5px;
            border: 1px solid #000;
            margin-bottom: 10px;
        }

        .info-complementares .linha {
            margin-bottom: 5px;
        }

        .info-complementares .bold {
            font-weight: bold;
        }
    </style>
    <div class="row">
        <div class="col-12 shadow-lg px-0">
            <div class="card w-100 card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                        <li class="pt-2 px-3">
                            <h3 class="card-title text-dark font-weight-bold" style="">
                                {{ $processo->codigo_interno ?? 'Novo Processo' }}</h3>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-tabs-two-home-tab" data-toggle="pill"
                                href="#custom-tabs-two-home" role="tab" aria-controls="custom-tabs-two-home"
                                aria-selected="false">Informações Cadastrais</a>
                        </li>
                        {{-- @if (isset($cliente))
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
                        @endif --}}
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-two-tabContent">
                        <div class="tab-pane fade active show" id="custom-tabs-two-home" role="tabpanel"
                            aria-labelledby="custom-tabs-two-home-tab">
                            <form enctype="multipart/form-data"
                                action="{{ isset($cliente) ? route('processo.update', $cliente->id) : route('processo.store') }}"
                                method="POST">
                                @csrf
                                @if (isset($cliente))
                                    @method('PUT')
                                @endif

                                <div class="row">
                                    <div class="col-4">
                                        <label for="exampleInputEmail1" class="form-label">Cliente</label>

                                        <select class="custom-select select2" name="cliente_id">
                                            <option selected disabled>Selecione uma opção</option>
                                            @foreach ($clientes as $cliente)
                                                <option
                                                    {{ isset($processo) && $processo->cliente_id == $cliente->id ? 'selected' : '' }}
                                                    value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-4">
                                                                            <button type="submit" class="btn btn-primary mt-3">Salvar</button>

                                    </div>
                                </div>

                            </form>
                            <div class="dados-container">
                                <!-- DADOS BÁSICOS -->
                                <table class="dados-basicos">
                                    <thead>
                                        <tr>
                                            <th colspan="3">DADOS BÁSICOS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>DI</td>
                                            <td colspan="2" class="highlight">24/2387113-8</td>
                                        </tr>
                                        <tr>
                                            <td>PROCESSO</td>
                                            <td class="highlight">I9IIMP-008/24</td>
                                            <td class="highlight">REAL</td>
                                        </tr>
                                        <tr>
                                            <td>VALOR FOB</td>
                                            <td class="highlight">USD 75.435,00</td>
                                            <td class="highlight">R$ 436.021,84</td>
                                        </tr>
                                        <tr>
                                            <td>FRETE INTERNACIONAL</td>
                                            <td class="highlight">USD 7.750,00</td>
                                            <td class="highlight">R$ 44.795,78</td>
                                        </tr>
                                        <tr>
                                            <td>SEGURO INTERNACIONAL</td>
                                            <td class="highlight">USD -</td>
                                            <td class="highlight">R$ -</td>
                                        </tr>
                                        <tr>
                                            <td>ACRESCIMO DO FRETE</td>
                                            <td class="highlight">USD -</td>
                                            <td class="highlight">R$ -</td>
                                        </tr>
                                        <tr>
                                            <td>VALOR CIF</td>
                                            <td class="highlight">USD 83.185,00</td>
                                            <td class="highlight">R$ 480.817,62</td>
                                        </tr>
                                        <tr>
                                            <td>TAXA DO DOLAR</td>
                                            <td class="highlight">5,7801</td>
                                            <td class="highlight">DOLAR</td>
                                        </tr>
                                        <tr>
                                            <td>IUAN RENMIMBI</td>
                                            <td class="highlight">-</td>
                                            <td></td>
                                        </tr>
                                        <tr class="green-highlight">
                                            <td>THC/CAPATAZIA</td>
                                            <td>R$ 1.250,00</td>
                                            <td>USD 216,26</td>
                                        </tr>
                                        <tr>
                                            <td>PESO BRUTO</td>
                                            <td colspan="2">14.621,4500</td>
                                        </tr>
                                        <tr>
                                            <td>PESO LIQUIDO</td>
                                            <td colspan="2">14.046,9700</td>
                                        </tr>
                                        <tr>
                                            <td>MULTA</td>
                                            <td colspan="2" class="highlight">R$ -</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- INFORMAÇÕES COMPLEMENTARES -->
                                <div class="info-complementares">
                                    <h4>INFORMAÇÕES COMPLEMENTARES</h4>

                                    <div class="linha">
                                        <span class="bold">DESCRIÇÃO DA MERCADORIA:</span><br>
                                        Conforme DI 24/2387113-8, desembaraçada em 31/10/2024
                                    </div>

                                    <div class="linha"><span class="bold">II:</span> R$ 59.247,82</div>
                                    <div class="linha"><span class="bold">IPI:</span> R$ 5.977,14</div>
                                    <div class="linha"><span class="bold">PIS:</span> R$ 10.961,58</div>
                                    <div class="linha"><span class="bold">COFINS:</span> R$ 54.764,78</div>
                                    <div class="linha"><span class="bold">Desp. Aduanei:</span> R$ 25.668,48</div>

                                    <br>

                                    <div class="linha"><span class="bold">QUANTIDADE:</span> 411</div>
                                    <div class="linha"><span class="bold">ESPECIE:</span> VOLUMES</div>
                                    <div class="linha"><span class="bold">PESO BRUTO:</span> 14.621,4500</div>
                                    <div class="linha"><span class="bold">PESO LIQUIDO:</span> 14.046,9700</div>
                                </div>
                            </div>
                            <div style="overflow-x: auto; width: 100%;">
                                <table class="table table-bordered table-striped" style="min-width: 3000px;">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>PRODUTO</th>
                                            <th>ADIÇÃO</th>
                                            <th>ITEM</th>
                                            <th>CODIGO</th>
                                            <th>NCM</th>
                                            <th>QUANTD</th>
                                            <th>PESO LID. UNIT</th>
                                            <th>PESO LIQ TOTAL</th>
                                            <th>FATOR PESO</th>
                                            <th>FOB UNIT USD</th>
                                            <th>FOB TOTAL USD</th>
                                            <th>VLR TOTALFOB R$</th>
                                            <th>FRETE INT.USD</th>
                                            <th>FRETE INT.R$</th>
                                            <th>SEGURO INT.USD</th>
                                            <th>SEGURO INT.R$</th>
                                            <th>ACRESC. FRETE USD</th>
                                            <th>ACRESC. FRETE R$</th>
                                            <th>THC USD</th>
                                            <th>THC R$</th>
                                            <th>VLR ADUANEIRO USD</th>
                                            <th>VLR ADUANEIRO R$</th>
                                            <th>II</th>
                                            <th>IPI</th>
                                            <th>PIS</th>
                                            <th>COFINS</th>
                                            <th>ICMS</th>
                                            <th>ICMS REDUZIDO</th>
                                            <th>REDUÇÃO</th>
                                            <th>VLR II</th>
                                            <th>BC IPI</th>
                                            <th>VLR IPI</th>
                                            <th>BC PIS/COFINS</th>
                                            <th>VLR PIS</th>
                                            <th>VLR COFINS</th>
                                            <th>DESP. ADUANEIRA</th>
                                            <th>BC ICMS S/REDUÇÃO</th>
                                            <th>VLR ICMS S/RED.</th>
                                            <th>BC ICMS REDUZIDO</th>
                                            <th>VLR ICMS REDUZ.</th>
                                            <th>VLR UNIT PROD. NF</th>
                                            <th>VLR TOTAL PROD. NF</th>
                                            <th>VLR TOTAL NF S/ICMS ST</th>
                                            <th>BC ICMS-ST</th>
                                            <th>MVA</th>
                                            <th>ICMS-ST</th>
                                            <th>VLR ICMS-ST</th>
                                            <th>VLR TOTAL NF C/ICMS-ST</th>
                                            <th>FATOR VLR FOB</th>
                                            <th>FATOR TX SISCOMEX</th>
                                            <th>MULTA</th>
                                            <th>TX DEF. LI</th>
                                            <th>TAXA SISCOMEX</th>
                                            <th>OUTRAS TX AGENTE</th>
                                            <th>LIBERAÇÃO BL</th>
                                            <th>DESCONS.</th>
                                            <th>ISPS CODE</th>
                                            <th>HANDLING</th>
                                            <th>CAPATAZIA</th>
                                            <th>AFRMM</th>
                                            <th>ARMAZENAGEM STS</th>
                                            <th>FRETE DTA STS/ANA</th>
                                            <th>S.D.A</th>
                                            <th>REP.STS</th>
                                            <th>ARMAZ. ANA</th>
                                            <th>LAVAGEM CONT</th>
                                            <th>REP. ANAPOLIS</th>
                                            <th>LI+DTA+HONOR.NIX</th>
                                            <th>HONORÁRIOS NIX</th>
                                            <th>DESP. DESEMBARAÇO</th>
                                            <th>DIF. CAMBIAL FRETE</th>
                                            <th>DIF.CAMBIAL FOB</th>
                                            <th>CUSTO UNIT FINAL</th>
                                            <th>CUSTO TOTAL FINAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Dados -->
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        {{-- @if (isset($cliente))

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
                                <form enctype="multipart/form-data"
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
                                            <input value="{{ isset($cliente) ? $cliente->data_procuracao : '' }}"
                                                type="date" class="form-control" id="data_procuracao"
                                                name="data_procuracao">

                                        </div>

                                        <div class="col-lg-4 col-md-6 col-sm-6">
                                            <label for="validationTooltip03">Data de Vencimento da Procuração</label>
                                            <input
                                                value="{{ isset($cliente) ? $cliente->data_vencimento_procuracao : '' }}"
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
                                                <select class="form-control" id="afrmm_bb" name="afrmm_bb">
                                                    <option value="" disabled selected>Selecione uma opção</option>
                                                    <option {{ $cliente->afrmm_bb == 1 ? 'selected' : '' }}
                                                        value="true">
                                                        Sim</option>
                                                    <option {{ $cliente->afrmm_bb == 0 ? 'selected' : '' }}
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
                                                    <select class="form-control" id="itau_di" name="itau_di">
                                                        <option value="" disabled selected>Selecione uma opção
                                                        </option>
                                                        <option {{ $cliente->itau_di == 1 ? 'selected' : '' }}
                                                            value="true">Sim</option>
                                                        <option {{ $cliente->itau_di == 0 ? 'selected' : '' }}
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
                                                        <tr>
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
                                            method="POST">
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
                                                                <td >
                                                                    <select class="form-control" id="tipo_documento"
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
                                                                                'proposta_nix' => 'Proposta nix',
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
                                                                    <label data-id="{{ $loop->index }}" for="inputFile"
                                                                        class="btn btn-secondary w-100 anexarArquivo">
                                                                        {{ $documento->url ? '📎 Atualizar documento' : 'Anexar documento' }}
                                                                        <p id="legenda-file-{{$loop->index}}" style="font-size: 11px">{{explode('/',$documento->path_file)[1]}}</p>
                                                                    </label>
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
                        @endif --}}
                    </div>
                </div>
            </div>
        </div>

    </div>


    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            customClass: {
                popup: 'colored-toast',
            },
            animation: true,
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
        });

        function downloadDocument(url) {
            const link = document.createElement('a');
            link.href = url;
            link.download = url.substring(url.lastIndexOf('/') + 1); // Obtém o nome do arquivo a partir da URL

            link.click();

            Toast.fire({
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
        $(document).ready(function($) {
            $('input[name=cnpj]').mask('99.999.999/9999-99')
            $('input[name=cpf_responsavel_legal]').mask('999.999.999-99')
            var activeTab = localStorage.getItem('activeTab');
            if (activeTab) {
                $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
            }

        })
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
                     
                    <tr>
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
                    nome: 'Proposta nix'
                }
            ];

            let select = `<select class="form-control" name="tipoDocumentos[]" id="documento-${id}">`;
            select += `<option hidden value="">Selecione uma opção</option>`;

            for (let tipo of tipoDocumentos) {
                select += `<option value="${tipo.id}">${tipo.nome}</option>`;
            }

            select += "</select>";

            let tr = `<tr data-id="${id}">
    <td>
        ${select}
    </td>
    <td colspan="2" >
<input type="file"
    accept="image/*,application/pdf,.docx"
    name="documentos[]"
    class="d-none"
    data-id="${id}"
    id="file-${id}">
        <label data-id="${id}" for="file-${id}"
            class="btn btn-secondary anexarArquivo w-100">
            📎 Anexar documento
            <p id="legenda-file-${id}" style="font-size: 11px"></p>

        </label>
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

        $('.previewModalButton').on('click', function() {

            const fileUrl = this.dataset.url; // Substitua pelo caminho do arquivo
            const fileType = fileUrl.split('.').pop().toLowerCase();

            if (fileType === 'pdf') {
                $('#pdf-iframe').attr('src', fileUrl).show(); // Exibe o iframe
                $('#imagePreview').hide(); // Esconde a imagem
                $('#doc-text').hide(); // Esconde o texto de descrição
            } else if (fileType === 'docx') {
                $('#doc-text').text('Descrição: Documento indisponível').show();
                $('#imagePreview').hide(); // Esconde a imagem
                $('#pdf-iframe').hide(); // Esconde o iframe
            } else if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'].includes(fileType)) {
                $('#imagePreview').attr('src', fileUrl).show(); // Exibe a imagem
                $('#pdf-iframe').hide(); // Esconde o iframe
                $('#doc-text').hide(); // Esconde o texto de descrição
            } else {
                $('#doc-text').text('Tipo de arquivo não suportado').show();
                $('#imagePreview').hide(); // Esconde a imagem
                $('#pdf-iframe').hide(); // Esconde o iframe
            }
            $('#tipoDocumentoName').text(this.dataset.nome)
            $('#tipoDocumentoDescription').text(this.dataset.descricao)
        })
    </script>


@endsection
