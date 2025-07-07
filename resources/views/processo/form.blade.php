@extends('layouts.app')
@section('title', isset($processo) ? '' : 'Cadastrar Processo')

@section('content')
    <style>
        /* Aumentar a altura do modal */
        .modal-dialog.modal-xl {
            max-width: 90%;
            height: 80vh;
            margin: 30px auto;
        }

        .modal-content {
            height: 100%;
        }

        .modal-body {
            height: 100%;
            overflow-y: auto;
        }

        #pdf-iframe {
            width: 100%;
            height: 80vh;
            border: none;
        }

        #imagePreview {
            width: 100%;
            max-height: 80vh;
            object-fit: contain;
            display: none;
        }

        #doc-text {
            display: none;
        }

        th {
            min-width: 200px;
        }

        tr {
            min-height: 300px;

        }

        .table-products td {
            /* text-align: center; */
            vertical-align: middle;
        }

        /* Garante que a coluna fixa tenha o mesmo estilo que as outras */
        table thead th:first-child,
        table tbody td:first-child {
            position: sticky;
            left: 0;
            z-index: 10;
            background-color: #f8f9fa;
        }

        table thead th:first-child {
            z-index: 20;
            background-color: #212529;
        }

        /* Estilo para o botão de remover */
        .btn-remove {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        /* Ajuste para o sticky funcionar corretamente */
        table tbody tr {
            position: relative;
        }

        .dados-container {
            display: flex;
            gap: 40px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin-bottom: 30px;

        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .dados-basicos th,
        .dados-basicos td {
            border: 1px solid #000;
            /* padding: 5px 8px; */
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
            /* margin-bottom: 5px; */
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
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-two-tabContent">
                        <div class="tab-pane fade active show" id="custom-tabs-two-home" role="tabpanel"
                            aria-labelledby="custom-tabs-two-home-tab">
                            <small class="text-danger d-none" id="avisoProcessoAlterado">Processo alterado, pressione o
                                botão salvar para persistir as alterações</small>
                            <form enctype="multipart/form-data"
                                action="{{ isset($processo) ? route('processo.update', $processo->id) : route('processo.store') }}"
                                method="POST">
                                @csrf
                                @if (isset($processo))
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-4">
                                        <label for="exampleInputEmail1" class="form-label">Cliente</label>
                                        <select {{ isset($processo) ? 'readonly' : '' }} class="custom-select select2"
                                            name="cliente_id">
                                            <option selected disabled>Selecione uma opção</option>
                                            @foreach ($clientes as $cliente)
                                                <option
                                                    {{ isset($processo) && $processo->cliente_id == $cliente->id ? 'selected' : '' }}
                                                    value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="processo_codigo_interno" class="form-label">PROCESSO</label>
                                        <input value="{{ isset($processo) ? $processo->codigo_interno : '' }}"
                                            class="form-control" name="codigo_interno" id="processo_codigo_interno">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-4">
                                        <label for="frete_internacional" class="form-label">FRETE INTERNACIONAL
                                            (USD)</label>
                                        <input value="{{ isset($processo) ? $processo->frete_internacional : '' }}"
                                            class="form-control moneyReal" name="frete_internacional"
                                            id="frete_internacional">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="seguro_internacional" class="form-label">SEGURO INTERNACIONAL
                                            (USD)</label>
                                        <input value="{{ isset($processo) ? $processo->seguro_internacional : '' }}"
                                            class="form-control moneyReal" name="seguro_internacional"
                                            id="seguro_internacional">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="acrescimo_frete" class="form-label">ACRESCIMO DO FRETE (USD)</label>
                                        <input value="{{ isset($processo) ? $processo->acrescimo_frete : '' }}"
                                            class="form-control moneyReal" name="acrescimo_frete" id="acrescimo_frete">
                                    </div>
                                </div>

                                <div class="row mt-1">


                                    <div class="col-md-4">
                                        <label for="thc_capatazia" class="form-label">THC/CAPATAZIA (USD)</label>
                                        <input value="{{ isset($processo) ? $processo->thc_capatazia : '' }}"
                                            class="form-control moneyReal" name="thc_capatazia" id="thc_capatazia">
                                    </div>

                                    <div class="col-md-4">
                                        <label for="peso_bruto" class="form-label">PESO BRUTO</label>
                                        <input type="number" value="{{ isset($processo) ? $processo->peso_bruto : '' }}"
                                            class="form-control moneyReal" name="peso_bruto" id="peso_bruto">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="multa" class="form-label">MULTA</label>
                                        <input type="number" value="{{ isset($processo) ? $processo->multa : '' }}"
                                            class="form-control moneyReal" name="multa" id="multa">
                                    </div>
                                </div>

                                <div class="col-4">
                                    <button type="submit" class="btn btn-primary mt-3">Salvar</button>
                                </div>
                                <div class="dados-container">

                                    <table class="table mt-4">
                                        <thead>
                                            <tr>
                                                <th colspan="2" class="text-white">DADOS BÁSICOS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>DI</td>
                                                <td class="">24/2387113-8</td>
                                            </tr>
                                            <tr>
                                                <td>PROCESSO</td>
                                                <td class="">
                                                    {{ isset($processo) ? $processo->codigo_interno : '' }}</td>
                                            </tr>
                                            <tr>
                                                <td>VALOR FOB</td>
                                                <td class="">USD <span id="fobTotalProcesso"></span> / R$ <span
                                                        id="fobTotalProcessoReal"></span></td>
                                            </tr>
                                            <tr>
                                                <td>VALOR CIF</td>
                                                <td class="">USD 83.185,00 / R$ 480.817,62</td>
                                            </tr>
                                            <tr>
                                                <td>TAXA DO DOLAR</td>
                                                <td class="">{{ Formatter::moneyUSD($dolar, true) }}</td>
                                            </tr>
                                            <tr>
                                                <td>IUAN RENMIMBI</td>
                                                <td class="">-</td>
                                            </tr>
                                            <tr>
                                                <td>PESO LIQUIDO</td>
                                                <td>14.046,9700</td>
                                            </tr>
                                            <tr>
                                                <td>FRETE INTERNACIONAL</td>
                                                <td>USD
                                                    {{ isset($processo) ? Formatter::moneyUSD($processo->frete_internacional) : '' }}
                                                    / R$
                                                    {{ isset($processo) ? Formatter::money($processo->frete_internacional * $dolar) : '' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>SEGURO INTERNACIONAL</td>
                                                <td>USD
                                                    {{ isset($processo) ? Formatter::moneyUSD($processo->seguro_internacional) : '' }}
                                                    / R$
                                                    {{ isset($processo) ? Formatter::money($processo->seguro_internacional * $dolar) : '' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>ACRESCIMO DO FRETE</td>
                                                <td>USD
                                                    {{ isset($processo) ? Formatter::moneyUSD($processo->acrescimo_frete) : '' }}
                                                    / R$
                                                    {{ isset($processo) ? Formatter::money($processo->acrescimo_frete * $dolar) : '' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>THC/CAPATAZIA</td>
                                                <td>USD
                                                    {{ isset($processo) ? Formatter::moneyUSD($processo->thc_capatazia) : '' }}
                                                    / R$
                                                    {{ isset($processo) ? Formatter::money($processo->thc_capatazia * $dolar) : '' }}
                                                </td>
                                            </tr>


                                        </tbody>
                                    </table>
                                    <table class="table mt-4">
                                        <thead>
                                            <tr>
                                                <th colspan="2" class="text-white">INFORMAÇÕES COMPLEMENTARES</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>DESCRIÇÃO DA MERCADORIA</td>
                                                <td>Conforme DI 24/2387113-8, desembaraçada em 31/10/2024</td>
                                            </tr>
                                            <tr>
                                                <td>II</td>
                                                <td>R$ 59.247,82</td>
                                            </tr>
                                            <tr>
                                                <td>IPI</td>
                                                <td>R$ 5.977,14</td>
                                            </tr>
                                            <tr>
                                                <td>PIS</td>
                                                <td>R$ 10.961,58</td>
                                            </tr>
                                            <tr>
                                                <td>COFINS</td>
                                                <td>R$ 54.764,78</td>
                                            </tr>
                                            <tr>
                                                <td>Desp. Aduanei</td>
                                                <td>R$ 25.668,48</td>
                                            </tr>
                                            <tr>
                                                <td>QUANTIDADE</td>
                                                <td>411</td>
                                            </tr>
                                            <tr>
                                                <td>ESPECIE</td>
                                                <td>VOLUMES</td>
                                            </tr>
                                            <tr>
                                                <td>PESO BRUTO</td>
                                                <td>14.621,4500</td>
                                            </tr>
                                            <tr>
                                                <td>PESO LIQUIDO</td>
                                                <td>14.046,9700</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @if (isset($processo) && isset($productsClient))
                                    <button type="button" class="btn btn-primary mb-2 addProduct">Adicionar
                                        Produto</button>
                                    <div style="overflow-x: auto; width: 100%;">
                                        <table class="table table-bordered table-striped table-products"
                                            style="min-width: 3000px;">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th
                                                        style="position: sticky; left: 0; z-index: 10; background-color: #212529; min-width: 50px;">
                                                        Ações</th>
                                                    <th style="min-width: 300px !important;">PRODUTO</th>
                                                    <th style="min-width: 500px !important;">DESCRIÇÃO</th>
                                                    <th>ADIÇÃO</th>
                                                    <th>ITEM</th>
                                                    <th>CODIGO</th>
                                                    <th>NCM</th>
                                                    <th>QUANTD</th>
                                                    <th>PESO LIQ. UNIT</th>
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
                                            <tbody id="productsBody">
                                                @foreach ($processoProdutos as $index => $processoProduto)
                                                    <tr>
                                                        <td
                                                            style="position: sticky; left: 0; z-index: 5; background-color: white;">
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm btn-remove"
                                                                data-id="{{ $processoProduto->id }}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </td>

                                                        <input type="hidden"
                                                            name="produtos[{{ $index }}][processo_produto_id]"
                                                            id="processo_produto_id-{{ $index }}"
                                                            value="{{ $processoProduto->id }}">

                                                        <td>
                                                            <select data-row="{{ $index }}"
                                                                class="custom-select selectProduct select2"
                                                                name="produtos[{{ $index }}][produto_id]"
                                                                id="produto_id-{{ $index }}">
                                                                <option selected disabled>Selecione uma opção</option>
                                                                @foreach ($productsClient as $produto)
                                                                    <option value="{{ $produto->id }}"
                                                                        {{ $processoProduto->produto_id == $produto->id ? 'selected' : '' }}>
                                                                        {{ $produto->modelo }} - {{ $produto->codigo }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>

                                                        <td>
                                                            <p id="descricao-{{ $index }}">
                                                                {{ $processoProduto->produto->descricao }}
                                                            </p>
                                                            {{-- <input type="text" class="form-control" readonly
                                                            name="produtos[{{ $index }}][descricao]"
                                                            id="descricao-{{ $index }}"
                                                            value="{{ $processoProduto->produto->descricao }}"> --}}
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control"
                                                                name="produtos[{{ $index }}][adicao]"
                                                                id="adicao-{{ $index }}"
                                                                value="{{ $processoProduto->adicao ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control" readonly
                                                                id="item-{{ $index }}"
                                                                value="{{ $loop->iteration }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" class="form-control" readonly
                                                                name="produtos[{{ $index }}][codigo]"
                                                                id="codigo-{{ $index }}"
                                                                value="{{ $processoProduto->produto->codigo }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" class="form-control" readonly
                                                                name="produtos[{{ $index }}][ncm]"
                                                                id="ncm-{{ $index }}"
                                                                value="{{ $processoProduto->produto->ncm }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="number"
                                                                step="1" class="form-control"
                                                                name="produtos[{{ $index }}][quantidade]"
                                                                id="quantidade-{{ $index }}"
                                                                value="{{ $processoProduto->quantidade ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control moneyReal" readonly
                                                                name="produtos[{{ $index }}][peso_liquido_unitario]"
                                                                id="peso_liquido_unitario-{{ $index }}"
                                                                value="{{ $processoProduto->peso_liquido_unitario ?? null }}">
                                                        </td>

                                                        <td>
                                                            {{-- @dd() --}}
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control pesoLiqTotal moneyReal"
                                                                name="produtos[{{ $index }}][peso_liquido_total]"
                                                                id="peso_liquido_total-{{ $index }}"
                                                                value="{{ $processoProduto->peso_liquido_total ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][fator_peso]"
                                                                id="fator_peso-{{ $index }}"
                                                                value="{{ $processoProduto->fator_peso ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control fobUnitario moneyUSD"
                                                                name="produtos[{{ $index }}][fob_unit_usd]"
                                                                id="fob_unit_usd-{{ $index }}"
                                                                value="{{ $processoProduto->fob_unit_usd ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control moneyUSD" readonly
                                                                name="produtos[{{ $index }}][fob_total_usd]"
                                                                id="fob_total_usd-{{ $index }}"
                                                                value="{{ $processoProduto->fob_total_usd ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control moneyReal" readonly
                                                                name="produtos[{{ $index }}][fob_total_brl]"
                                                                id="fob_total_brl-{{ $index }}"
                                                                value="{{ $processoProduto->fob_total_brl ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control moneyUSD" readonly
                                                                name="produtos[{{ $index }}][frete_usd]"
                                                                id="frete_usd-{{ $index }}"
                                                                value="{{ $processoProduto->frete_usd ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control moneyReal" readonly
                                                                name="produtos[{{ $index }}][frete_brl]"
                                                                id="frete_brl-{{ $index }}"
                                                                value="{{ $processoProduto->frete_brl ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control moneyUSD" readonly
                                                                name="produtos[{{ $index }}][seguro_usd]"
                                                                id="seguro_usd-{{ $index }}"
                                                                value="{{ $processoProduto->seguro_usd ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control moneyReal" readonly
                                                                name="produtos[{{ $index }}][seguro_brl]"
                                                                id="seguro_brl-{{ $index }}"
                                                                value="{{ $processoProduto->seguro_brl ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control moneyUSD" readonly
                                                                name="produtos[{{ $index }}][acresc_frete_usd]"
                                                                id="acresc_frete_usd-{{ $index }}"
                                                                value="{{ $processoProduto->acresc_frete_usd ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control moneyReal" readonly
                                                                name="produtos[{{ $index }}][acresc_frete_brl]"
                                                                id="acresc_frete_brl-{{ $index }}"
                                                                value="{{ $processoProduto->acresc_frete_brl ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control moneyUSD" readonly
                                                                name="produtos[{{ $index }}][thc_usd]"
                                                                id="thc_usd-{{ $index }}"
                                                                value="{{ $processoProduto->thc_usd ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control moneyReal" readonly
                                                                name="produtos[{{ $index }}][thc_brl]"
                                                                id="thc_brl-{{ $index }}"
                                                                value="{{ $processoProduto->thc_brl ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control moneyUSD" readonly
                                                                name="produtos[{{ $index }}][valor_aduaneiro_usd]"
                                                                id="valor_aduaneiro_usd-{{ $index }}"
                                                                value="{{ $processoProduto->valor_aduaneiro_usd ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control moneyReal" readonly
                                                                name="produtos[{{ $index }}][valor_aduaneiro_brl]"
                                                                id="valor_aduaneiro_brl-{{ $index }}"
                                                                value="{{ $processoProduto->valor_aduaneiro_brl ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control"
                                                                name="produtos[{{ $index }}][ii_percent]"
                                                                id="ii_percent-{{ $index }}"
                                                                value="{{ $processoProduto->ii_percent ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control"
                                                                name="produtos[{{ $index }}][ipi_percent]"
                                                                id="ipi_percent-{{ $index }}"
                                                                value="{{ $processoProduto->ipi_percent ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control"
                                                                name="produtos[{{ $index }}][pis_percent]"
                                                                id="pis_percent-{{ $index }}"
                                                                value="{{ $processoProduto->pis_percent ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control"
                                                                name="produtos[{{ $index }}][cofins_percent]"
                                                                id="cofins_percent-{{ $index }}"
                                                                value="{{ $processoProduto->cofins_percent ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control"
                                                                name="produtos[{{ $index }}][icms_percent]"
                                                                id="icms_percent-{{ $index }}"
                                                                value="{{ $processoProduto->icms_percent ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][icms_reduzido_percent]"
                                                                id="icms_reduzido_percent-{{ $index }}"
                                                                value="{{ $processoProduto->icms_reduzido_percent ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][reducao]"
                                                                id="reducao-{{ $index }}"
                                                                value="{{ $processoProduto->reducao ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control moneyReal" readonly
                                                                name="produtos[{{ $index }}][valor_ii]"
                                                                id="valor_ii-{{ $index }}"
                                                                value="{{ $processoProduto->valor_ii ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control moneyReal" readonly
                                                                name="produtos[{{ $index }}][base_ipi]"
                                                                id="base_ipi-{{ $index }}"
                                                                value="{{ $processoProduto->base_ipi ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control moneyReal" readonly
                                                                name="produtos[{{ $index }}][valor_ipi]"
                                                                id="valor_ipi-{{ $index }}"
                                                                value="{{ $processoProduto->valor_ipi ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control moneyReal" readonly
                                                                name="produtos[{{ $index }}][base_pis_cofins]"
                                                                id="base_pis_cofins-{{ $index }}"
                                                                value="{{ $processoProduto->base_pis_cofins ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control moneyReal" readonly
                                                                name="produtos[{{ $index }}][valor_pis]"
                                                                id="valor_pis-{{ $index }}"
                                                                value="{{ $processoProduto->valor_pis ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control moneyReal" readonly
                                                                name="produtos[{{ $index }}][valor_cofins]"
                                                                id="valor_cofins-{{ $index }}"
                                                                value="{{ $processoProduto->valor_cofins ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][despesa_aduaneira]"
                                                                id="despesa_aduaneira-{{ $index }}"
                                                                value="{{ $processoProduto->despesa_aduaneira ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][base_icms_sem_reducao]"
                                                                id="base_icms_sem_reducao-{{ $index }}"
                                                                value="{{ $processoProduto->base_icms_sem_reducao ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][valor_icms_sem_reducao]"
                                                                id="valor_icms_sem_reducao-{{ $index }}"
                                                                value="{{ $processoProduto->valor_icms_sem_reducao ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][base_icms_reduzido]"
                                                                id="base_icms_reduzido-{{ $index }}"
                                                                value="{{ $processoProduto->base_icms_reduzido ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][valor_icms_reduzido]"
                                                                id="valor_icms_reduzido-{{ $index }}"
                                                                value="{{ $processoProduto->valor_icms_reduzido ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input data-row="{{ $index }}" type="text"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][valor_unit_nf]"
                                                                id="valor_unit_nf-{{ $index }}"
                                                                value="{{ $processoProduto->valor_unit_nf ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][valor_total_nf]"
                                                                id="valor_total_nf-{{ $index }}"
                                                                value="{{ $processoProduto->valor_total_nf ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][valor_total_nf_sem_icms_st]"
                                                                id="valor_total_nf_sem_icms_st-{{ $index }}"
                                                                value="{{ $processoProduto->valor_total_nf_sem_icms_st ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][base_icms_st]"
                                                                id="base_icms_st-{{ $index }}"
                                                                value="{{ $processoProduto->base_icms_st ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][mva]"
                                                                id="mva-{{ $index }}"
                                                                value="{{ $processoProduto->mva ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][icms_st]"
                                                                id="icms_st-{{ $index }}"
                                                                value="{{ $processoProduto->icms_st ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][valor_icms_st]"
                                                                id="valor_icms_st-{{ $index }}"
                                                                value="{{ $processoProduto->valor_icms_st ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][valor_total_nf_com_icms_st]"
                                                                id="valor_total_nf_com_icms_st-{{ $index }}"
                                                                value="{{ $processoProduto->valor_total_nf_com_icms_st ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][fator_valor_fob]"
                                                                id="fator_valor_fob-{{ $index }}"
                                                                value="{{ $processoProduto->fator_valor_fob ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][fator_tx_siscomex]"
                                                                id="fator_tx_siscomex-{{ $index }}"
                                                                value="{{ $processoProduto->fator_tx_siscomex ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][multa]"
                                                                id="multa-{{ $index }}"
                                                                value="{{ $processoProduto->multa ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][tx_def_li]"
                                                                id="tx_def_li-{{ $index }}"
                                                                value="{{ $processoProduto->tx_def_li ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][taxa_siscomex]"
                                                                id="taxa_siscomex-{{ $index }}"
                                                                value="{{ $processoProduto->taxa_siscomex ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][outras_taxas_agente]"
                                                                id="outras_taxas_agente-{{ $index }}"
                                                                value="{{ $processoProduto->outras_taxas_agente ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][liberacao_bl]"
                                                                id="liberacao_bl-{{ $index }}"
                                                                value="{{ $processoProduto->liberacao_bl ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][desconsolidacao]"
                                                                id="desconsolidacao-{{ $index }}"
                                                                value="{{ $processoProduto->desconsolidacao ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][isps_code]"
                                                                id="isps_code-{{ $index }}"
                                                                value="{{ $processoProduto->isps_code ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][handling]"
                                                                id="handling-{{ $index }}"
                                                                value="{{ $processoProduto->handling ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][capatazia]"
                                                                id="capatazia-{{ $index }}"
                                                                value="{{ $processoProduto->capatazia ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][afrmm]"
                                                                id="afrmm-{{ $index }}"
                                                                value="{{ $processoProduto->afrmm ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][armazenagem_sts]"
                                                                id="armazenagem_sts-{{ $index }}"
                                                                value="{{ $processoProduto->armazenagem_sts ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][frete_dta_sts_ana]"
                                                                id="frete_dta_sts_ana-{{ $index }}"
                                                                value="{{ $processoProduto->frete_dta_sts_ana ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][sda]"
                                                                id="sda-{{ $index }}"
                                                                value="{{ $processoProduto->sda ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][rep_sts]"
                                                                id="rep_sts-{{ $index }}"
                                                                value="{{ $processoProduto->rep_sts ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][armaz_ana]"
                                                                id="armaz_ana-{{ $index }}"
                                                                value="{{ $processoProduto->armaz_ana ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][lavagem_container]"
                                                                id="lavagem_container-{{ $index }}"
                                                                value="{{ $processoProduto->lavagem_container ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][rep_anapolis]"
                                                                id="rep_anapolis-{{ $index }}"
                                                                value="{{ $processoProduto->rep_anapolis ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][li_dta_honor_nix]"
                                                                id="li_dta_honor_nix-{{ $index }}"
                                                                value="{{ $processoProduto->li_dta_honor_nix ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][honorarios_nix]"
                                                                id="honorarios_nix-{{ $index }}"
                                                                value="{{ $processoProduto->honorarios_nix ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][desp_desenbaraco]"
                                                                id="desp_desenbaraco-{{ $index }}"
                                                                value="{{ $processoProduto->desp_desenbaraco ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][diferenca_cambial_frete]"
                                                                id="diferenca_cambial_frete-{{ $index }}"
                                                                value="{{ $processoProduto->diferenca_cambial_frete ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][diferenca_cambial_fob]"
                                                                id="diferenca_cambial_fob-{{ $index }}"
                                                                value="{{ $processoProduto->diferenca_cambial_fob ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][custo_unitario_final]"
                                                                id="custo_unitario_final-{{ $index }}"
                                                                value="{{ $processoProduto->custo_unitario_final ?? '' }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" data-row="{{ $index }}"
                                                                class="form-control" readonly
                                                                name="produtos[{{ $index }}][custo_total_final]"
                                                                id="custo_total_final-{{ $index }}"
                                                                value="{{ $processoProduto->custo_total_final ?? '' }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot id="resultado-totalizadores">
                                            </tfoot>
                                        </table>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (isset($productsClient))
        <input type="hidden" name="productsClient" id="productsClient" value="{{ $productsClient }}">
        <input type="hidden" name="dolarHoje" id="dolarHoje" value="{{ $dolar }}">
        <input type="hidden" id="processoAlterado" name="processoAlterado" value="0">
    @endif
    <script>
        $(document).ready(function() {

            // $('.moneyReal').mask('#.##0,00', {
            //     reverse: true,
            //     placeholder: "",
            //     maxlength: false
            // });

            // // Máscara para Dólar (USD) - permite valores como 1,401.23 ou 23.5
            // $('.moneyUSD').mask('#,##0.00', {
            //     reverse: true,
            //     placeholder: "",
            //     maxlength: false
            // });
            $('.select2').select2();
            let valorReal = MoneyUtils.parseMoney("1.401,23"); // Retorna 1401.23 (float)
            let valorDolar = MoneyUtils.parseMoney("1,401.23"); // Retorna 1401.23 (float)

            // Formatação
            console.log(MoneyUtils.formatMoney(1401.23)); // "1.401,23"
            console.log(MoneyUtils.formatMoney(23.5)); // "23,50"
            console.log(MoneyUtils.formatUSD(1401.23));

        });
        $('#frete_internacional, #seguro_internacional, #acrescimo_frete').trigger('change');

        const MoneyUtils = {
            parseMoney: function(value) {
                if (!value || value === "") return 0;

                // Remove todos os pontos (separadores de milhar)
                let cleanValue = value.toString().replace(/\./g, '');

                // Para Real (R$): substitui vírgula por ponto para parseFloat
                cleanValue = cleanValue.replace(',', '.');

                return parseFloat(cleanValue) || 0;
            },

            formatMoney: function(value, decimals = 2) {
                if (value === null || value === undefined) return "0,00";

                // Converte para número se for string
                let num = typeof value === 'string' ? parseFloat(value.replace(',', '.')) : value;

                // Formata com 2 decimais por padrão, mas mantém os decimais existentes
                let fixedDecimals = num.toFixed(decimals);

                // Separa parte inteira e decimal
                let parts = fixedDecimals.split('.');
                let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                let decimalPart = parts[1] || '00';

                return `${integerPart},${decimalPart}`;
            },

            formatUSD: function(value, decimals = 2) {
                if (value === null || value === undefined) return "0.00";

                let num = typeof value === 'string' ? parseFloat(value.replace(',', '.')) : value;
                let fixedDecimals = num.toFixed(decimals);

                let parts = fixedDecimals.split('.');
                let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                let decimalPart = parts[1] || '00';

                return `${integerPart}.${decimalPart}`;
            }
        };

        $(document).on('change', 'input, select, textarea', function() {
            $('#avisoProcessoAlterado').removeClass('d-none');
            let rowId = $(this).data('row');
            if (rowId != null) {
                let pesoTotal = MoneyUtils.parseMoney($(`#peso_liquido_total-${rowId}`).val());
                let fobUnitario = MoneyUtils.parseMoney($(`#fob_unit_usd-${rowId}`).val());
                let quantidade = parseInt($(`#quantidade-${rowId}`).val());

                let pesoLiqUnit = pesoTotal / (quantidade || 1);
                let fobTotal = fobUnitario * quantidade;
                let totalPesoLiq = 0;

                $('.pesoLiqTotal').each(function() {
                    let valor = MoneyUtils.parseMoney($(this).val());
                    console.log($(this).val())
                    console.log(valor)
                    totalPesoLiq += valor;
                });

                let fatorPesoRow = 0;
                $('.pesoLiqTotal').each(function() {
                    const rowIdLine = $(this).data('row');
                    const pesoTotalLinha = parseFloat($(this).val());
                    const fatorPeso = pesoTotalLinha / (totalPesoLiq || 1);

                    if (rowId == rowIdLine) {
                        fatorPesoRow = fatorPeso;
                    }

                    $(`#fator_peso-${rowIdLine}`).val(fatorPeso.toFixed(6));
                });

                let fobTotalGeral = 0;
                $('.fobUnitario').each(function() {
                    const rowId = $(this).data('row');
                    const unitario = MoneyUtils.parseMoney($(this).val());
                    const qtd = MoneyUtils.parseMoney($(`#quantidade-${rowId}`).val());
                    fobTotalGeral += unitario * qtd;
                });

                let dolar = parseFloat($('#dolarHoje').val());

                // Formatando os totais para exibição
                $('#fobTotalProcesso').text(MoneyUtils.formatMoney(fobTotalGeral));
                $('#fobTotalProcessoReal').text(MoneyUtils.formatMoney(fobTotalGeral * dolar));

                const freteUsdInt = MoneyUtils.parseMoney($('#frete_internacional').val()) * fatorPesoRow;
                const thc_capataziaBase = MoneyUtils.parseMoney($('#thc_capatazia').val());
                const thcRow = thc_capataziaBase * fatorPesoRow;

                const seguroIntUsdTotal = MoneyUtils.parseMoney($('#seguro_internacional').val());
                const seguroIntUsdRow = (seguroIntUsdTotal / fobTotalGeral) * fobTotal;

                const acrescimoFreteBase = MoneyUtils.parseMoney($('#acrescimo_frete').val());
                const acrescimoFreteUsdRow = (acrescimoFreteBase / (fobTotalGeral * dolar)) * fobTotal * dolar;

                const vlrAduaneiroUsd = fobTotal + freteUsdInt + acrescimoFreteUsdRow + seguroIntUsdRow + (thcRow /
                    dolar);

                const vlrAduaneiroBrl = vlrAduaneiroUsd * dolar;

                const ii = parseFloat($(`#ii_percent-${rowId}`).val()) / 100;
                const ipi = parseFloat($(`#ipi_percent-${rowId}`).val()) / 100;
                const pis = parseFloat($(`#pis_percent-${rowId}`).val()) / 100;
                const cofins = parseFloat($(`#cofins_percent-${rowId}`).val()) / 100;
                const icms = parseFloat($(`#icms_percent-${rowId}`).val()) / 100;

                const vlrII = vlrAduaneiroBrl * ii;
                const bcIpi = vlrAduaneiroBrl + vlrII;
                const vlrIpi = bcIpi * ipi;
                const bcPisCofins = vlrAduaneiroBrl;
                const vlrPis = bcPisCofins * pis;
                const vlrCofins = bcPisCofins * cofins;

                console.log([
                    $(`#multa-${rowId}`).val() ?? '0',
                    $(`#tx_def_li-${rowId}`).val() ?? '0'
                ])
                const multaAZ =  $(`#multa-${rowId}`).val()  != '' ? parseFloat($(`#multa-${rowId}`).val()) : 0;
                const txDefLiBA = $(`#tx_def_li-${rowId}`).val() != '' ? parseFloat($(`#tx_def_li-${rowId}`).val()) : 0;
                const taxaSisComex = calcularTaxaSiscomex($('#productsBody tr').length)
                const fatorTaxaSiscomex_AY = taxaSisComex / (fobTotal * dolar);
                const taxaSisComexUnitaria_BB = fatorTaxaSiscomex_AY * (fobUnitario * dolar)
                const fatorVlrFob_AX = fobTotal / fobTotalGeral
                const capatazia_BH = thc_capataziaBase * fatorVlrFob_AX
                const valorAFRMM_BI_17 = 3659.39
                const afrmm_BI = valorAFRMM_BI_17 * fatorVlrFob_AX
                const armazenagem_BJ = 2505.79 * fatorVlrFob_AX
                const frete_dta_BK = 17100.00 * fatorVlrFob_AX
                const honorarios_nix = 706 * fatorVlrFob_AX;
                console.log([
                    multaAZ, txDefLiBA, taxaSisComex, fatorTaxaSiscomex_AY,
                    taxaSisComexUnitaria_BB
                ])
                let despesa_aduaneira = multaAZ + txDefLiBA + taxaSisComex + fatorTaxaSiscomex_AY +
                    taxaSisComexUnitaria_BB;
                despesa_aduaneira += fatorVlrFob_AX + capatazia_BH + valorAFRMM_BI_17 + afrmm_BI + armazenagem_BJ;
                despesa_aduaneira += frete_dta_BK + honorarios_nix
                const base_icms_st = parseFloat($(`#base_icms_st-${rowId}`).val())
                const mva = parseFloat($(`#mva-${rowId}`).val())
                const icms_st = parseFloat($(`#icms_st-${rowId}`).val())
                const valor_icms_st = parseFloat($(`#valor_icms_st-${rowId}`).val())
                const bcIcmsSReducao = (vlrAduaneiroBrl + vlrII + vlrIpi + vlrPis + vlrCofins + despesa_aduaneira) /
                    (1 - icms)
                const vlrIcmsSReducao = bcIcmsSReducao * icms
                const icms_reduzido_percent = parseFloat($(`#icms_reduzido_percent-${rowId}`).val()) / 100
                const reducao = parseFloat($(`#reducao-${rowId}`).val())

                const denominadorBcIcmsReduzido = !reducao ? 1 : reducao;

                const bcImcsReduzido = (vlrAduaneiroBrl + vlrII + vlrIpi + vlrPis + vlrCofins + despesa_aduaneira) /
                    denominadorBcIcmsReduzido
                const vlrIcmsReduzido = bcIcmsSReducao * icms

                const vlrTotalProdutoNf = vlrAduaneiroBrl + vlrII
                const vlrUnitProdutNf = vlrTotalProdutoNf / quantidade
                const vlrTotalNfSemIcms = vlrTotalProdutoNf + vlrIpi + vlrPis + vlrCofins + despesa_aduaneira +
                    vlrIcmsReduzido

                const vlrTotalNfComIcms = vlrTotalNfSemIcms + valor_icms_st



                $(`#peso_liquido_unitario-${rowId}`).val(MoneyUtils.formatMoney(pesoLiqUnit));
                $(`#fob_total_usd-${rowId}`).val(MoneyUtils.formatMoney(fobTotal));
                $(`#fob_total_brl-${rowId}`).val(MoneyUtils.formatMoney(fobTotal * dolar));
                $(`#frete_usd-${rowId}`).val(MoneyUtils.formatMoney(freteUsdInt));
                $(`#frete_brl-${rowId}`).val(MoneyUtils.formatMoney(freteUsdInt * dolar));
                $(`#seguro_usd-${rowId}`).val(MoneyUtils.formatMoney(seguroIntUsdRow));
                $(`#seguro_brl-${rowId}`).val(MoneyUtils.formatMoney(seguroIntUsdRow * dolar));
                $(`#acresc_frete_usd-${rowId}`).val(MoneyUtils.formatMoney(acrescimoFreteUsdRow));
                $(`#acresc_frete_brl-${rowId}`).val(MoneyUtils.formatMoney(acrescimoFreteUsdRow * dolar));
                $(`#thc_usd-${rowId}`).val(MoneyUtils.formatMoney(thcRow / dolar));
                $(`#thc_brl-${rowId}`).val(MoneyUtils.formatMoney(thcRow));
                $(`#valor_aduaneiro_usd-${rowId}`).val(MoneyUtils.formatMoney(vlrAduaneiroUsd));
                $(`#valor_aduaneiro_brl-${rowId}`).val(MoneyUtils.formatMoney(vlrAduaneiroBrl));
                $(`#valor_ii-${rowId}`).val(MoneyUtils.formatMoney(vlrII));
                $(`#base_ipi-${rowId}`).val(MoneyUtils.formatMoney(bcIpi));
                $(`#valor_ipi-${rowId}`).val(MoneyUtils.formatMoney(vlrIpi));
                $(`#base_pis_cofins-${rowId}`).val(MoneyUtils.formatMoney(bcPisCofins));
                $(`#valor_pis-${rowId}`).val(MoneyUtils.formatMoney(vlrPis));
                $(`#valor_cofins-${rowId}`).val(MoneyUtils.formatMoney(vlrCofins));
                $(`#despesa_aduaneira-${rowId}`).val(MoneyUtils.formatMoney(despesa_aduaneira));
                $(`#base_icms_sem_reducao-${rowId}`).val(MoneyUtils.formatMoney(bcIcmsSReducao));
                $(`#valor_icms_sem_reducao-${rowId}`).val(MoneyUtils.formatMoney(vlrIcmsSReducao));
                $(`#base_icms_reduzido-${rowId}`).val(MoneyUtils.formatMoney(bcImcsReduzido));
                $(`#valor_icms_reduzido-${rowId}`).val(MoneyUtils.formatMoney(vlrIcmsReduzido));
                $(`#valor_unit_nf-${rowId}`).val(MoneyUtils.formatMoney(vlrUnitProdutNf));
                $(`#valor_total_nf-${rowId}`).val(MoneyUtils.formatMoney(vlrTotalProdutoNf));
                $(`#valor_total_nf_sem_icms_st-${rowId}`).val(MoneyUtils.formatMoney(vlrTotalNfSemIcms));
                $(`#valor_total_nf_com_icms_st-${rowId}`).val(MoneyUtils.formatMoney(vlrTotalNfComIcms));
                $(`#fator_valor_fob-${rowId}`).val(MoneyUtils.formatMoney(fatorVlrFob_AX));
                $(`#fator_tx_siscomex-${rowId}`).val(MoneyUtils.formatMoney(fatorTaxaSiscomex_AY));



            }
        });

        function calcularTaxaSiscomex(quantidade) {
            const valorRegistroDI = 115.67;

            const faixas = [{
                    max: 2,
                    adicional: 38.56
                },
                {
                    max: 3,
                    adicional: 30.85
                },
                {
                    max: 5,
                    adicional: 30.85
                },
                {
                    max: 7,
                    adicional: 23.14
                },
                {
                    max: 9,
                    adicional: 23.14
                },
                {
                    max: 10,
                    adicional: 23.14
                },
                {
                    max: 20,
                    adicional: 15.42
                },
                {
                    max: 50,
                    adicional: 7.71
                },
                {
                    max: Infinity,
                    adicional: 3.86
                },
            ];

            const faixa = faixas.find(f => quantidade <= f.max);
            const total = valorRegistroDI + faixa.adicional;

            return parseFloat(total.toFixed(2));
        }
        // Atualização para os campos de frete, seguro e acréscimo
        $(document).on('change', '#frete_internacional, #seguro_internacional, #acrescimo_frete', function() {
            let dolar = MoneyUtils.parseMoney($('#dolarHoje').val());

            const updateValorReal = (inputId, spanId) => {
                let valor = MoneyUtils.parseMoney($(`#${inputId}`).val());
                let convertido = valor * dolar;
                $(`#${spanId}`).text(MoneyUtils.formatMoney(convertido));
            };

            updateValorReal('frete_internacional', 'frete_internacional_real');
            updateValorReal('seguro_internacional', 'seguro_internacional_real');
            updateValorReal('acrescimo_frete', 'acrescimo_frete_real');
        });
        $(document).on('change', '.selectProduct', function() {
            let products = JSON.parse($('#productsClient').val());
            let productObject = products.find(el => el.id == this.value);
            let rowId = $(this).data('row');

            if (productObject) {
                console.log(productObject)
                $(`#codigo-${rowId}`).val(productObject.codigo);
                $(`#ncm-${rowId}`).val(productObject.ncm);
                $(`#descricao-${rowId}`).text(productObject.descricao);
            }
        });
        $(document).on('click', '.addProduct', function() {
            let products = JSON.parse($('#productsClient').val());
            let lengthOptions = $('#productsBody tr').length;
            let newIndex = lengthOptions; // Índice baseado em 0 para o novo item

            // Cria o select de produtos
            let select = `<select data-row="${newIndex}" class="custom-select selectProduct select2" name="produtos[${newIndex}][produto_id]" id="produto_id-${newIndex}">
            <option selected disabled>Selecione uma opção</option>`;

            for (let produto of products) {
                select += `<option value="${produto.id}">${produto.modelo} - ${produto.codigo}</option>`;
            }
            select += '</select>';

            // Cria a nova linha com o mesmo padrão do Blade
            let tr = `<tr>
        <!-- Coluna fixa de ações -->
        <td style="position: sticky; left: 0; z-index: 5; background-color: white;">
            <button type="button" class="btn btn-danger btn-sm btn-remove" data-id="">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
        
        <input type="hidden" name="produtos[${newIndex}][processo_produto_id]" id="processo_produto_id-${newIndex}" value="">
        
        <!-- Colunas de dados -->
        <td>${select}</td>
        <td><p id="descricao-${newIndex}"></p></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" name="produtos[${newIndex}][adicao]" id="adicao-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly id="item-${newIndex}" value="${newIndex + 1}"></td>
        <td><input type="text" class="form-control" readonly name="produtos[${newIndex}][codigo]" id="codigo-${newIndex}" value=""></td>
        <td><input type="text" class="form-control" readonly name="produtos[${newIndex}][ncm]" id="ncm-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="number" step="1" class="form-control" name="produtos[${newIndex}][quantidade]" id="quantidade-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="number" class="form-control" readonly name="produtos[${newIndex}][peso_liquido_unitario]" id="peso_liquido_unitario-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control pesoLiqTotal" name="produtos[${newIndex}][peso_liquido_total]" id="peso_liquido_total-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][fator_peso]" id="fator_peso-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control fobUnitario" name="produtos[${newIndex}][fob_unit_usd]" id="fob_unit_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][fob_total_usd]" id="fob_total_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][fob_total_brl]" id="fob_total_brl-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][frete_usd]" id="frete_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][frete_brl]" id="frete_brl-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][seguro_usd]" id="seguro_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][seguro_brl]" id="seguro_brl-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][acresc_frete_usd]" id="acresc_frete_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][acresc_frete_brl]" id="acresc_frete_brl-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][thc_usd]" id="thc_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][thc_brl]" id="thc_brl-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][valor_aduaneiro_usd]" id="valor_aduaneiro_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][valor_aduaneiro_brl]" id="valor_aduaneiro_brl-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" name="produtos[${newIndex}][ii_percent]" id="ii_percent-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" name="produtos[${newIndex}][ipi_percent]" id="ipi_percent-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" name="produtos[${newIndex}][pis_percent]" id="pis_percent-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" name="produtos[${newIndex}][cofins_percent]" id="cofins_percent-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" name="produtos[${newIndex}][icms_percent]" id="icms_percent-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][icms_reduzido_percent]" id="icms_reduzido_percent-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][reducao]" id="reducao-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][valor_ii]" id="valor_ii-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][base_ipi]" id="base_ipi-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][valor_ipi]" id="valor_ipi-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][base_pis_cofins]" id="base_pis_cofins-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][valor_pis]" id="valor_pis-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][valor_cofins]" id="valor_cofins-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][despesa_aduaneira]" id="despesa_aduaneira-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][base_icms_sem_reducao]" id="base_icms_sem_reducao-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][valor_icms_sem_reducao]" id="valor_icms_sem_reducao-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][base_icms_reduzido]" id="base_icms_reduzido-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][valor_icms_reduzido]" id="valor_icms_reduzido-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" readonly name="produtos[${newIndex}][valor_unit_nf]" id="valor_unit_nf-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][valor_total_nf]" id="valor_total_nf-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][valor_total_nf_sem_icms_st]" id="valor_total_nf_sem_icms_st-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][base_icms_st]" id="base_icms_st-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][mva]" id="mva-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][icms_st]" id="icms_st-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][valor_icms_st]" id="valor_icms_st-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][valor_total_nf_com_icms_st]" id="valor_total_nf_com_icms_st-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][fator_valor_fob]" id="fator_valor_fob-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][fator_tx_siscomex]" id="fator_tx_siscomex-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" name="produtos[${newIndex}][multa]" id="multa-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" name="produtos[${newIndex}][tx_def_li]" id="tx_def_li-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][taxa_siscomex]" id="taxa_siscomex-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][outras_taxas_agente]" id="outras_taxas_agente-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][liberacao_bl]" id="liberacao_bl-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][desconsolidacao]" id="desconsolidacao-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][isps_code]" id="isps_code-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][handling]" id="handling-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][capatazia]" id="capatazia-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][afrmm]" id="afrmm-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][armazenagem_sts]" id="armazenagem_sts-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][frete_dta_sts_ana]" id="frete_dta_sts_ana-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][sda]" id="sda-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][rep_sts]" id="rep_sts-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][armaz_ana]" id="armaz_ana-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][lavagem_container]" id="lavagem_container-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][rep_anapolis]" id="rep_anapolis-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][li_dta_honor_nix]" id="li_dta_honor_nix-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][honorarios_nix]" id="honorarios_nix-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][desp_desenbaraco]" id="desp_desenbaraco-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][diferenca_cambial_frete]" id="diferenca_cambial_frete-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][diferenca_cambial_fob]" id="diferenca_cambial_fob-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][custo_unitario_final]" id="custo_unitario_final-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class="form-control" readonly name="produtos[${newIndex}][custo_total_final]" id="custo_total_final-${newIndex}" value=""></td>
    </tr>`;

            // Adiciona a nova linha à tabela
            $('#productsBody').append(tr);

            // Inicializa o select2 para o novo select
            $('.select2').select2();

            // Dispara o evento de alteração para calcular os valores iniciais
            $('input[data-row="' + newIndex + '"], select[data-row="' + newIndex + '"]').trigger('change');
        });
    </script>
@endsection
