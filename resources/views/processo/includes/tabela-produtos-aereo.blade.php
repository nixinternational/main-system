<div class="tab-pane fade " id="custom-tabs-three-home" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab">
    @if (isset($productsClient))
        <form id="form-produtos" action="{{ route('processo.update', $processo->id) }}" enctype="multipart/form-data"
            action="POST">
            @csrf
            @method('PUT')
            <div class="d-flex flex-wrap mb-3" style="gap: 10px;">
                <button type="button" class="btn btn-primary addProduct">
                    <i class="fas fa-plus me-2"></i>Adicionar Produto
                </button>
                <button id="btnDeleteSelectedProdutos" class="btn btn-danger" type="button">
                    <i class="fas fa-trash me-2"></i>Excluir Selecionados
                </button>
                <button type="button" class="btn btn-info" id="recalcularTabela">
                    <i class="fas fa-calculator me-2"></i>Recalcular Toda a Tabela
                </button>
                <button type="button" class="btn btn-secondary btn-reordenar">
                    <i class="fas fa-sort me-2"></i>Reordenar por Adição/Item
                </button>
            </div>
            <div class="table-products-wrapper">
                <!-- Barra de scroll horizontal extra acima do cabeçalho -->
                <div class="table-products-scrollbar" id="tableProductsScrollbar">
                    <div class="table-products-scrollbar-content"></div>
                </div>
                <!-- Container da tabela com scroll -->
                <div class="table-products-container" id="tableProductsContainer" style="overflow-x: auto; overflow-y: auto; max-height: 80vh; width: 100%;">
                    <table class="table table-bordered table-striped table-products" style="min-width: 3000px;">
                    <thead class=" text-center">
                        <tr>
                            <th style="background-color: #fff"></th>
                            <th style="background-color: #fff" colspan="23"></th>
                            <th colspan="7">ALÍQUOTAS</th>

                            <th colspan="7" style="background-color: #fff">VLR II</th>

                            <th colspan="2">BASE E VALOR SEM REDUÇÃO</th>
                            <th colspan="2" style="background-color: #fff"></th>
                            <th colspan="8">CALCULADOS SEM A BASE REDUZIDA-COLUNAS AL E AM
                            </th>
                            <th style="background-color:#fff"></th>
                            <th>PREENCHER</th>
                            <th colspan="33" style="background-color:#fff">VLR TOTAL PROD.
                                NF
                            </th>
                        </tr>
                        <tr class="middleRow">
                            @php

                                $moedaProcesso = $processo->moeda_processo ?? 'USD';
                                $moedaFrete = $processo->frete_internacional_moeda ?? 'USD';
                                $moedaSeguro = $processo->seguro_internacional_moeda ?? 'USD';
                                $moedaAcrescimo = $processo->acrescimo_frete_moeda ?? 'USD';
                                $colspanBeforeMiddleRow = 12; // Colunas fixas iniciais (Ações até FATOR PESO, incluindo ORIGEM e PESO LIQ. LBS)

                                // Colunas FOB
                                if ($moedaProcesso == 'USD') {
                                    $colspanBeforeMiddleRow += 3; // FOB UNIT USD + TOTALFOB USD + TOTALFOB R$
                                } else {
                                    $colspanBeforeMiddleRow += 4; // FOB UNIT MOEDA + TOTALFOB MOEDA + TOTALFOB USD + TOTALFOB R$
                                }

                                // Colunas FRETE
                                if ($moedaFrete != 'USD') {
                                    $colspanBeforeMiddleRow += 3; // FRETE MOEDA + FRETE USD + FRETE R$
                                } else {
                                    $colspanBeforeMiddleRow += 2; // FRETE USD + FRETE R$
                                }

                                // Colunas SEGURO
                                if ($moedaSeguro != 'USD') {
                                    $colspanBeforeMiddleRow += 3; // SEGURO MOEDA + SEGURO USD + SEGURO R$
                                } else {
                                    $colspanBeforeMiddleRow += 2; // SEGURO USD + SEGURO R$
                                }

                                // Colunas ACRÉSCIMO
                                if ($moedaAcrescimo != 'USD') {
                                    $colspanBeforeMiddleRow += 3; // ACRESC MOEDA + ACRESC USD + ACRESC R$
                                } else {
                                    $colspanBeforeMiddleRow += 2; // ACRESC USD + ACRESC R$
                                }

                                // Colunas fixas após (VLR CFR até VLR TOTAL NF C/ICMS-ST)
                                $colspanBeforeMiddleRow += 30; // VLR CFR UNIT + VLR CFR TOTAL até VLR TOTAL NF C/ICMS-ST (removidos delivery_fee e collect_fee que agora estão na middleRow)

                                // Colunas dos fatores (FATOR VLR FOB até TAXA SISCOMEX)
                                $colspanBeforeMiddleRow += 5; // FATOR VLR FOB até TAXA SISCOMEX
                            @endphp

                            <th style="background-color: #fff"> </th>
                            @php
                                // Calcular quantas colunas existem antes do PESO LIQ TOTAL
                                // Ações (1) + PRODUTO (1) + DESCRIÇÃO (1) + ADIÇÃO (1) + ITEM (1) + ORIGEM (1) + CODIGO (1) + NCM (1) + QUANTD (1) + PESO LIQ. LBS (1) + PESO LIQ. UNIT (1) = 11 colunas
                                $colspanAntesPesoLiq = 9;
                            @endphp
                            <th colspan="{{ $colspanAntesPesoLiq }}"></th>
                            <th class="middleRowInputTh">
                                <input type="text" class="form-control cabecalhoInputs moneyReal"
                                    name="peso_liquido_total_cabecalho" id="peso_liquido_total_cabecalho"
                                    value="{{ number_format($processo->peso_liquido ?? 0, 5, ',', '.') }}">
                            </th>
                            <th colspan="{{ $colspanBeforeMiddleRow - $colspanAntesPesoLiq - 1 }}"></th>

                            @php
                                // Ordem: OUTRAS TX AGENTE, DELIVERY FEE, DELIVERY FEE R$, COLLECT FEE, COLLECT FEE R$, DESCONS., HANDLING, DAI, HONORÁRIOS NIX, DAPE, CORREIOS, LI+DTA+HONOR.NIX
                                $campos = [
                                    'outras_taxas_agente',
                                    'delivery_fee',
                                    'collect_fee',
                                    'desconsolidacao',
                                    'handling',
                                    'dai',
                                    'honorarios_nix', // Acima do DAPE conforme solicitado
                                    'dape',
                                    'correios',
                                    'li_dta_honor_nix',
                                ];
                                $camposCambiais = ['diferenca_cambial_frete', 'diferenca_cambial_fob'];
                            @endphp

                            {{-- OUTRAS TX AGENTE --}}
                            <th class="middleRowInputTh">
                                <input type="text" class="form-control cabecalhoInputs moneyReal"
                                    name="outras_taxas_agente" id="outras_taxas_agente"
                                    value="{{ number_format($processo->outras_taxas_agente ?? 0, 5, ',', '.') }}">
                            </th>
                            
                            {{-- DELIVERY FEE --}}
                            <th class="middleRowInputTh">
                                <input type="text" class="form-control cabecalhoInputs moneyReal"
                                    name="delivery_fee" id="delivery_fee"
                                    value="{{ number_format($processo->delivery_fee ?? 0, 5, ',', '.') }}">
                            </th>
                            
                            {{-- DELIVERY FEE R$ --}}
                            <th class="middleRowInputTh">
                                <input type="text" class="form-control cabecalhoInputs moneyReal"
                                    name="delivery_fee_brl" id="delivery_fee_brl"
                                    value="{{ number_format($processo->delivery_fee_brl ?? 0, 5, ',', '.') }}">
                            </th>
                            
                            {{-- COLLECT FEE --}}
                            <th class="middleRowInputTh">
                                <input type="text" class="form-control cabecalhoInputs moneyReal"
                                    name="collect_fee" id="collect_fee"
                                    value="{{ number_format($processo->collect_fee ?? 0, 5, ',', '.') }}">
                            </th>
                            
                            {{-- COLLECT FEE R$ --}}
                            <th class="middleRowInputTh">
                                <input type="text" class="form-control cabecalhoInputs moneyReal"
                                    name="collect_fee_brl" id="collect_fee_brl"
                                    value="{{ number_format($processo->collect_fee_brl ?? 0, 5, ',', '.') }}">
                            </th>
                            
                            {{-- DESCONSOLIDAÇÃO --}}
                            <th class="middleRowInputTh">
                                <input type="text" class="form-control cabecalhoInputs moneyReal"
                                    name="desconsolidacao" id="desconsolidacao"
                                    value="{{ number_format($processo->desconsolidacao ?? 0, 5, ',', '.') }}">
                            </th>
                            
                            {{-- HANDLING --}}
                            <th class="middleRowInputTh">
                                <input type="text" class="form-control cabecalhoInputs moneyReal"
                                    name="handling" id="handling"
                                    value="{{ number_format($processo->handling ?? 0, 5, ',', '.') }}">
                            </th>
                            
                            {{-- DAI --}}
                            <th class="middleRowInputTh">
                                <input type="text" class="form-control cabecalhoInputs moneyReal"
                                    name="dai" id="dai"
                                    value="{{ number_format($processo->dai ?? 0, 5, ',', '.') }}">
                            </th>
                            
                            {{-- HONORÁRIOS NIX --}}
                            <th class="middleRowInputTh">
                                <input type="text" class="form-control cabecalhoInputs moneyReal"
                                    name="honorarios_nix" id="honorarios_nix"
                                    value="{{ number_format($processo->honorarios_nix ?? 0, 5, ',', '.') }}">
                            </th>
                            
                            {{-- DAPE --}}
                            <th class="middleRowInputTh">
                                <input type="text" class="form-control cabecalhoInputs moneyReal"
                                    name="dape" id="dape"
                                    value="{{ number_format($processo->dape ?? 0, 5, ',', '.') }}">
                            </th>
                            
                            {{-- CORREIOS --}}
                            <th class="middleRowInputTh">
                                <input type="text" class="form-control cabecalhoInputs moneyReal"
                                    name="correios" id="correios"
                                    value="{{ number_format($processo->correios ?? 0, 5, ',', '.') }}">
                            </th>
                            
                            {{-- LI+DTA+HONOR.NIX --}}
                            <th class="middleRowInputTh">
                                <input type="text" class="form-control cabecalhoInputs moneyReal"
                                    name="li_dta_honor_nix" id="li_dta_honor_nix"
                                    value="{{ number_format($processo->li_dta_honor_nix ?? 0, 5, ',', '.') }}">
                            </th>

                            @php
                                // Colunas restantes após os campos da middleRow
                                // DESP. DESEMBARAÇO (1 coluna) - depois vêm os campos cambiais como inputs separados, depois CUSTO UNIT FINAL e CUSTO TOTAL FINAL
                                $colspanAfterMiddleRow = 1; // Apenas DESP. DESEMBARAÇO (os campos cambiais são inputs separados, e depois vêm CUSTO UNIT FINAL e CUSTO TOTAL FINAL)
                            @endphp

                            <th colspan="{{ $colspanAfterMiddleRow }}"></th>

                            @foreach ($camposCambiais as $campoCambial)
                                <th class="middleRowInputTh">
                                    <input type="text" class="form-control difCambial moneyReal"
                                        name="{{ $campoCambial }}" id="{{ $campoCambial }}"
                                        value="{{ number_format($processo->$campoCambial ?? 0, 5, ',', '.') }}">
                                </th>
                            @endforeach
                            
                            {{-- CUSTO UNIT FINAL e CUSTO TOTAL FINAL (2 colunas vazias após os campos cambiais) --}}
                            <th colspan="2"></th>
                        </tr>
                        <tr>
                            <th class="d-flex align-items-center justify-content-center"
                                style="background-color: #212529 !important; ">Ações <input type="checkbox"
                                    style="margin-left: 10px" id="select-all-produtos" title="Selecionar todos"></th>
                            <th style="min-width: 300px !important;">PRODUTO</th>
                            <th style="min-width: 500px !important;">DESCRIÇÃO</th>
                            <th>ADIÇÃO</th>
                            <th>ITEM</th>
                            <th>ORIGEM</th>
                            <th>CODIGO</th>
                            <th>NCM</th>
                            <th>QUANTD</th>
                            <th>PESO LIQ. LBS</th>
                            <th>PESO LIQ. UNIT</th>
                            <th>PESO LIQ TOTAL KG</th>
                            <th>FATOR PESO</th>
                            <!-- COLUNAS FOB CONDICIONAIS -->
                            <!-- COLUNAS FOB CONDICIONAIS -->
                            <!-- COLUNAS FOB - SEMPRE EXISTEM -->
                            @php
                                $moedaProcesso = $processo->moeda_processo ?? 'USD';
                            @endphp

                            @if ($moedaProcesso == 'USD')
                                <th>FOB UNIT USD</th>
                            @else
                                <th>FOB UNIT {{ $moedaProcesso }}</th>
                            @endif

                            @if ($moedaProcesso != 'USD')
                                <th>VLR TOTALFOB {{ $moedaProcesso }}</th>
                            @endif
                            <th>VLR TOTALFOB USD</th>
                            <th>VLR TOTALFOB R$</th>
                            <!-- FRETE - Colunas condicionais -->
                            @php
                                $moedaFrete = $processo->frete_internacional_moeda ?? 'USD';
                                $moedaSeguro = $processo->seguro_internacional_moeda ?? 'USD';
                                $moedaAcrescimo = $processo->acrescimo_frete_moeda ?? 'USD';
                            @endphp

                            @if ($moedaFrete != 'USD')
                                <th>FRETE INT.{{ $moedaFrete }}</th>
                            @endif
                            <th>FRETE INT.USD</th>
                            <th>FRETE INT.R$</th>

                            <!-- SEGURO - Colunas condicionais -->
                            @if ($moedaSeguro != 'USD')
                                <th>SEGURO INT.{{ $moedaSeguro }}</th>
                            @endif
                            <th>SEGURO INT.USD</th>
                            <th>SEGURO INT.R$</th>

                            <!-- ACRÉSCIMO - Colunas condicionais -->
                            @if ($moedaAcrescimo != 'USD')
                                <th>ACRESC. FRETE {{ $moedaAcrescimo }}</th>
                            @endif
                            <th>ACRESC. FRETE USD</th>
                            <th>ACRESC. FRETE R$</th>

                            <th>VLR CFR UNIT</th>
                            <th>VLR CFR TOTAL</th>
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
                            <th style="min-width: 300px !important;">VLR TOTAL NF S/ICMS ST
                            </th>
                            <th>BC ICMS-ST</th>
                            <th>MVA</th>
                            <th>ICMS-ST</th>
                            <th>VLR ICMS-ST</th>
                            <th style="min-width: 300px !important;">VLR TOTAL NF C/ICMS-ST
                            </th>
                            <th>FATOR VLR FOB</th>
                            <th>FATOR TX SISCOMEX</th>
                            <th>MULTA</th>
                            <th>TX DEF. LI</th>
                            <th>TAXA SISCOMEX</th>
                            <th>OUTRAS TX AGENTE</th>
                            <th>DELIVERY FEE</th>
                            <th>DELIVERY FEE R$</th>
                            <th>COLLECT FEE</th>
                            <th>COLLECT FEE R$</th>
                            <th>DESCONS.</th>
                            <th>HANDLING</th>
                            <th>DAI</th>
                            <th>HONORÁRIOS NIX</th>
                            <th>DAPE</th>
                            <th>CORREIOS</th>
                            <th>LI+DTA+HONOR.NIX</th>
                            <th style="min-width: 300px !important;">DESP. DESEMBARAÇO</th>
                            <th>DIF. CAMBIAL FRETE</th>
                            <th>DIF.CAMBIAL FOB</th>
                            <th>CUSTO UNIT FINAL</th>
                            <th>CUSTO TOTAL FINAL</th>
                        </tr>
                    </thead>
                    <tbody id="productsBody">
                        @if (isset($processoProdutos) && count($processoProdutos) > 0)
                            @foreach ($processoProdutos as $index => $processoProduto)
                            <tr class="linhas-input" id="row-{{ $index }}">
                                <td class="d-flex align-items-center justify-content-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" onclick="showDeleteConfirmation({{ $processoProduto->id }})"
                                            class="btn btn-danger btn-sm btn-remove" data-id="{{ $processoProduto->id }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-info btn-sm btn-debug-linha" data-row="{{ $index }}" title="Cálculo da linha">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                    </div>
                                    <input type="checkbox" style="margin-left: 10px" class="select-produto"
                                        value="{{ $processoProduto->id }}">
                                </td>

                                <input type="hidden" name="produtos[{{ $index }}][processo_produto_id]"
                                    id="processo_produto_id-{{ $index }}" value="{{ $processoProduto->id }}">

                                <td>
                                    <select data-row="{{ $index }}"
                                        class="custom-select selectProduct w-100 select2"
                                        name="produtos[{{ $index }}][produto_id]"
                                        id="produto_id-{{ $index }}">
                                        <option selected disabled>Selecione uma opção</option>
                                        @foreach ($productsClient as $produto)
                                            <option value="{{ $produto->id }}"
                                                {{ $processoProduto->produto_id == $produto->id ? 'selected' : '' }}>
                                                {{ $produto->modelo }} -
                                                {{ $produto->codigo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text" class=" form-control"
                                        name="produtos[{{ $index }}][descricao]"
                                        id="descricao-{{ $index }}" value="{{ $processoProduto->descricao }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text" class=" form-control"
                                        name="produtos[{{ $index }}][adicao]" id="adicao-{{ $index }}"
                                        value="{{ $processoProduto->adicao ?? '' }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="number" class=" form-control "
                                        name="produtos[{{ $index }}][item]" id="item-{{ $index }}"
                                        value="{{ $processoProduto->item }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text" class=" form-control"
                                        name="produtos[{{ $index }}][origem]" id="origem-{{ $index }}"
                                        value="{{ $processoProduto->origem ?? '' }}">
                                </td>

                                <td>
                                    <input type="text" class=" form-control" readonly
                                        name="produtos[{{ $index }}][codigo]" id="codigo-{{ $index }}"
                                        value="{{ $processoProduto->produto->codigo }}">
                                </td>

                                <td>
                                    <input type="text" class=" form-control" readonly
                                        name="produtos[{{ $index }}][ncm]" id="ncm-{{ $index }}"
                                        value="{{ $processoProduto->produto->ncm }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class=" form-control moneyReal"
                                        name="produtos[{{ $index }}][quantidade]"
                                        value="{{ $processoProduto->quantidade ? number_format($processoProduto->quantidade, 5, ',', '.') : '' }}"
                                        id="quantidade-{{ $index }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class="form-control pesoLiqLbs moneyReal"
                                        name="produtos[{{ $index }}][peso_liq_lbs]"
                                        id="peso_liq_lbs-{{ $index }}"
                                        value="{{ $processoProduto->peso_liq_lbs ? number_format($processoProduto->peso_liq_lbs, 6, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class="form-control moneyReal" readonly
                                        name="produtos[{{ $index }}][peso_liquido_unitario]"
                                        id="peso_liquido_unitario-{{ $index }}"
                                        value="{{ $processoProduto->peso_liquido_unitario ? number_format($processoProduto->peso_liquido_unitario, 6, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class="form-control pesoLiqTotalKg moneyReal" readonly
                                        name="produtos[{{ $index }}][peso_liq_total_kg]"
                                        id="peso_liq_total_kg-{{ $index }}"
                                        value="{{ $processoProduto->peso_liq_total_kg ? number_format($processoProduto->peso_liq_total_kg, 6, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class=" form-control moneyReal" readonly
                                        name="produtos[{{ $index }}][fator_peso]"
                                        id="fator_peso-{{ $index }}"
                                        value="{{ $processoProduto->fator_peso ? number_format($processoProduto->fator_peso, 8, ',', '.') : '' }}">
                                </td>

                                <!-- COLUNAS FOB - SEMPRE EXISTEM -->
                                @if ($moedaProcesso == 'USD')
                                    <td>
                                        <input data-row="{{ $index }}" type="text"
                                            class="form-control fobUnitario moneyReal7"
                                            name="produtos[{{ $index }}][fob_unit_usd]"
                                            id="fob_unit_usd-{{ $index }}"
                                            value="{{ $processoProduto->fob_unit_usd ? number_format($processoProduto->fob_unit_usd, 7, ',', '.') : '' }}">
                                    </td>
                                @else
                                    <td>
                                        <input data-row="{{ $index }}" type="text"
                                            class="form-control fobUnitarioMoedaEstrangeira moneyReal7"
                                            name="produtos[{{ $index }}][fob_unit_moeda_estrangeira]"
                                            id="fob_unit_moeda_estrangeira-{{ $index }}"
                                            value="{{ $processoProduto->fob_unit_moeda_estrangeira ? number_format($processoProduto->fob_unit_moeda_estrangeira, 7, ',', '.') : '' }}">
                                    </td>
                                @endif

                                @if ($moedaProcesso != 'USD')
                                    <td>
                                        <input data-row="{{ $index }}" type="text"
                                            class="form-control moneyReal7" readonly
                                            name="produtos[{{ $index }}][fob_total_moeda_estrangeira]"
                                            id="fob_total_moeda_estrangeira-{{ $index }}"
                                            value="{{ $processoProduto->fob_total_moeda_estrangeira ? number_format($processoProduto->fob_total_moeda_estrangeira, 7, ',', '.') : '' }}">
                                    </td>
                                @endif

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class="form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][fob_total_usd]"
                                        id="fob_total_usd-{{ $index }}"
                                        value="{{ $processoProduto->fob_total_usd ? number_format($processoProduto->fob_total_usd, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class="form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][fob_total_brl]"
                                        id="fob_total_brl-{{ $index }}"
                                        value="{{ $processoProduto->fob_total_brl ? number_format($processoProduto->fob_total_brl, 7, ',', '.') : '' }}">
                                </td>

                                <!-- FRETE - Colunas condicionais -->
                                @if ($moedaFrete != 'USD')
                                    <td>
                                        <input data-row="{{ $index }}" type="text"
                                            class="form-control moneyReal7" readonly
                                            name="produtos[{{ $index }}][frete_moeda_estrangeira]"
                                            id="frete_moeda_estrangeira-{{ $index }}"
                                            value="{{ $processoProduto->frete_moeda_estrangeira ? number_format($processoProduto->frete_moeda_estrangeira, 7, ',', '.') : '' }}">
                                    </td>
                                @endif
                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class="form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][frete_usd]"
                                        id="frete_usd-{{ $index }}"
                                        value="{{ $processoProduto->frete_usd ? number_format($processoProduto->frete_usd, 7, ',', '.') : '' }}">
                                </td>
                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class="form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][frete_brl]"
                                        id="frete_brl-{{ $index }}"
                                        value="{{ $processoProduto->frete_brl ? number_format($processoProduto->frete_brl, 7, ',', '.') : '' }}">
                                </td>

                                <!-- SEGURO - Colunas condicionais -->
                                @if ($moedaSeguro != 'USD')
                                    <td>
                                        <input data-row="{{ $index }}" type="text"
                                            class="form-control moneyReal7" readonly
                                            name="produtos[{{ $index }}][seguro_moeda_estrangeira]"
                                            id="seguro_moeda_estrangeira-{{ $index }}"
                                            value="{{ $processoProduto->seguro_moeda_estrangeira ? number_format($processoProduto->seguro_moeda_estrangeira, 7, ',', '.') : '' }}">
                                    </td>
                                @endif
                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class="form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][seguro_usd]"
                                        id="seguro_usd-{{ $index }}"
                                        value="{{ $processoProduto->seguro_usd ? number_format($processoProduto->seguro_usd, 7, ',', '.') : '' }}">
                                </td>
                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class="form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][seguro_brl]"
                                        id="seguro_brl-{{ $index }}"
                                        value="{{ $processoProduto->seguro_brl ? number_format($processoProduto->seguro_brl, 7, ',', '.') : '' }}">
                                </td>

                                <!-- ACRÉSCIMO - Colunas condicionais -->
                                @if ($moedaAcrescimo != 'USD')
                                    <td>
                                        <input data-row="{{ $index }}" type="text"
                                            class="form-control moneyReal7" readonly
                                            name="produtos[{{ $index }}][acrescimo_moeda_estrangeira]"
                                            id="acrescimo_moeda_estrangeira-{{ $index }}"
                                            value="{{ $processoProduto->acrescimo_moeda_estrangeira ? number_format($processoProduto->acrescimo_moeda_estrangeira, 7, ',', '.') : '' }}">
                                    </td>
                                @endif
                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class="form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][acresc_frete_usd]"
                                        id="acresc_frete_usd-{{ $index }}"
                                        value="{{ $processoProduto->acresc_frete_usd ? number_format($processoProduto->acresc_frete_usd, 7, ',', '.') : '' }}">
                                </td>
                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class="form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][acresc_frete_brl]"
                                        id="acresc_frete_brl-{{ $index }}"
                                        value="{{ $processoProduto->acresc_frete_brl ? number_format($processoProduto->acresc_frete_brl, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class="form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][vlr_cfr_unit]"
                                        id="vlr_cfr_unit-{{ $index }}"
                                        value="{{ $processoProduto->vlr_cfr_unit ? number_format($processoProduto->vlr_cfr_unit, 7, ',', '.') : '' }}">
                                </td>
                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class="form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][vlr_cfr_total]"
                                        id="vlr_cfr_total-{{ $index }}"
                                        value="{{ $processoProduto->vlr_cfr_total ? number_format($processoProduto->vlr_cfr_total, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class="form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][valor_aduaneiro_usd]"
                                        id="valor_aduaneiro_usd-{{ $index }}"
                                        value="{{ $processoProduto->valor_aduaneiro_usd ? number_format($processoProduto->valor_aduaneiro_usd, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class="form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][valor_aduaneiro_brl]"
                                        id="valor_aduaneiro_brl-{{ $index }}"
                                        value="{{ $processoProduto->valor_aduaneiro_brl ? number_format($processoProduto->valor_aduaneiro_brl, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class=" form-control percentage2"
                                        name="produtos[{{ $index }}][ii_percent]"
                                        id="ii_percent-{{ $index }}"
                                        value="{{ $processoProduto->ii_percent ? number_format($processoProduto->ii_percent, 2, ',', '.') : '' }} %">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class=" form-control percentage2"
                                        name="produtos[{{ $index }}][ipi_percent]"
                                        id="ipi_percent-{{ $index }}"
                                        value="{{ $processoProduto->ipi_percent ? number_format($processoProduto->ipi_percent, 2, ',', '.') : '' }} %">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class=" form-control percentage2"
                                        name="produtos[{{ $index }}][pis_percent]"
                                        id="pis_percent-{{ $index }}"
                                        value="{{ $processoProduto->pis_percent ? number_format($processoProduto->pis_percent, 2, ',', '.') : '' }} %">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class=" form-control percentage2"
                                        name="produtos[{{ $index }}][cofins_percent]"
                                        id="cofins_percent-{{ $index }}"
                                        value="{{ $processoProduto->cofins_percent ? number_format($processoProduto->cofins_percent, 2, ',', '.') : '' }} %">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class=" form-control percentage2"
                                        name="produtos[{{ $index }}][icms_percent]"
                                        id="icms_percent-{{ $index }}"
                                        value="{{ $processoProduto->icms_percent ? number_format($processoProduto->icms_percent, 2, ',', '.') : '' }} %">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class=" form-control percentage2 icms_reduzido_percent"
                                        name="produtos[{{ $index }}][icms_reduzido_percent]"
                                        id="icms_reduzido_percent-{{ $index }}"
                                        value="{{ $processoProduto->icms_reduzido_percent ? number_format($processoProduto->icms_reduzido_percent, 2, ',', '.') : '' }} %">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][reducao]"
                                        id="reducao-{{ $index }}"
                                        value="{{ $processoProduto->reducao ? number_format($processoProduto->reducao, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class="form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][valor_ii]"
                                        id="valor_ii-{{ $index }}"
                                        value="{{ $processoProduto->valor_ii ? number_format($processoProduto->valor_ii, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class="form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][base_ipi]"
                                        id="base_ipi-{{ $index }}"
                                        value="{{ $processoProduto->base_ipi ? number_format($processoProduto->base_ipi, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class="form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][valor_ipi]"
                                        id="valor_ipi-{{ $index }}"
                                        value="{{ $processoProduto->valor_ipi ? number_format($processoProduto->valor_ipi, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class="form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][base_pis_cofins]"
                                        id="base_pis_cofins-{{ $index }}"
                                        value="{{ $processoProduto->base_pis_cofins ? number_format($processoProduto->base_pis_cofins, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class="form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][valor_pis]"
                                        id="valor_pis-{{ $index }}"
                                        value="{{ $processoProduto->valor_pis ? number_format($processoProduto->valor_pis, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class="form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][valor_cofins]"
                                        id="valor_cofins-{{ $index }}"
                                        value="{{ $processoProduto->valor_cofins ? number_format($processoProduto->valor_cofins, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][despesa_aduaneira]"
                                        id="despesa_aduaneira-{{ $index }}"
                                        value="{{ $processoProduto->despesa_aduaneira ? number_format($processoProduto->despesa_aduaneira, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][base_icms_sem_reducao]"
                                        id="base_icms_sem_reducao-{{ $index }}"
                                        value="{{ $processoProduto->base_icms_sem_reducao ? number_format($processoProduto->base_icms_sem_reducao, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][valor_icms_sem_reducao]"
                                        id="valor_icms_sem_reducao-{{ $index }}"
                                        value="{{ $processoProduto->valor_icms_sem_reducao ? number_format($processoProduto->valor_icms_sem_reducao, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][base_icms_reduzido]"
                                        id="base_icms_reduzido-{{ $index }}"
                                        value="{{ $processoProduto->base_icms_reduzido ? number_format($processoProduto->base_icms_reduzido, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][valor_icms_reduzido]"
                                        id="valor_icms_reduzido-{{ $index }}"
                                        value="{{ $processoProduto->valor_icms_reduzido ? number_format($processoProduto->valor_icms_reduzido, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input data-row="{{ $index }}" type="text"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][valor_unit_nf]"
                                        id="valor_unit_nf-{{ $index }}"
                                        value="{{ $processoProduto->valor_unit_nf ? number_format($processoProduto->valor_unit_nf, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][valor_total_nf]"
                                        id="valor_total_nf-{{ $index }}"
                                        value="{{ $processoProduto->valor_total_nf ? number_format($processoProduto->valor_total_nf, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][valor_total_nf_sem_icms_st]"
                                        id="valor_total_nf_sem_icms_st-{{ $index }}"
                                        value="{{ $processoProduto->valor_total_nf_sem_icms_st ? number_format($processoProduto->valor_total_nf_sem_icms_st, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][base_icms_st]"
                                        id="base_icms_st-{{ $index }}"
                                        value="{{ $processoProduto->base_icms_st ? number_format($processoProduto->base_icms_st, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control percentage" name="produtos[{{ $index }}][mva]"
                                        id="mva-{{ $index }}"
                                        value="{{ $processoProduto->mva ? number_format($processoProduto->mva, 2, ',', '.') : '' }} %">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control percentage"
                                        name="produtos[{{ $index }}][icms_st]"
                                        id="icms_st-{{ $index }}"
                                        value="{{ $processoProduto->icms_st ? number_format($processoProduto->icms_st, 2, ',', '.') : '' }} %">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][valor_icms_st]"
                                        id="valor_icms_st-{{ $index }}"
                                        value="{{ $processoProduto->valor_icms_st ? number_format($processoProduto->valor_icms_st, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][valor_total_nf_com_icms_st]"
                                        id="valor_total_nf_com_icms_st-{{ $index }}"
                                        value="{{ $processoProduto->valor_total_nf_com_icms_st ? number_format($processoProduto->valor_total_nf_com_icms_st, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal" readonly
                                        name="produtos[{{ $index }}][fator_valor_fob]"
                                        id="fator_valor_fob-{{ $index }}"
                                        value="{{ $processoProduto->fator_valor_fob ? number_format($processoProduto->fator_valor_fob, 8, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal" readonly
                                        name="produtos[{{ $index }}][fator_tx_siscomex]"
                                        id="fator_tx_siscomex-{{ $index }}"
                                        value="{{ $processoProduto->fator_tx_siscomex ? number_format($processoProduto->fator_tx_siscomex, 8, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7"
                                        name="produtos[{{ $index }}][multa]" id="multa-{{ $index }}"
                                        value="{{ $processoProduto->multa ? number_format($processoProduto->multa, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control percentage2"
                                        name="produtos[{{ $index }}][tx_def_li]"
                                        id="tx_def_li-{{ $index }}"
                                        value="{{ $processoProduto->tx_def_li ? number_format($processoProduto->tx_def_li, 2, ',', '.') : '' }} %">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][taxa_siscomex]"
                                        id="taxa_siscomex-{{ $index }}"
                                        value="{{ $processoProduto->taxa_siscomex ? number_format($processoProduto->taxa_siscomex, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][outras_taxas_agente]"
                                        id="outras_taxas_agente-{{ $index }}"
                                        value="{{ $processoProduto->outras_taxas_agente ? number_format($processoProduto->outras_taxas_agente, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][delivery_fee]"
                                        id="delivery_fee-{{ $index }}"
                                        value="{{ $processoProduto->delivery_fee ? number_format($processoProduto->delivery_fee, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][delivery_fee_brl]"
                                        id="delivery_fee_brl-{{ $index }}"
                                        value="{{ $processoProduto->delivery_fee_brl ? number_format($processoProduto->delivery_fee_brl, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][collect_fee]"
                                        id="collect_fee-{{ $index }}"
                                        value="{{ $processoProduto->collect_fee ? number_format($processoProduto->collect_fee, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][collect_fee_brl]"
                                        id="collect_fee_brl-{{ $index }}"
                                        value="{{ $processoProduto->collect_fee_brl ? number_format($processoProduto->collect_fee_brl, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][desconsolidacao]"
                                        id="desconsolidacao-{{ $index }}"
                                        value="{{ $processoProduto->desconsolidacao ? number_format($processoProduto->desconsolidacao, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][handling]"
                                        id="handling-{{ $index }}"
                                        value="{{ $processoProduto->handling ? number_format($processoProduto->handling, 7, ',', '.') : '' }}">
                                </td>

                                <!-- Campos específicos do transporte aéreo -->
                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][dai]"
                                        id="dai-{{ $index }}"
                                        value="{{ $processoProduto->dai ? number_format($processoProduto->dai, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][honorarios_nix]"
                                        id="honorarios_nix-{{ $index }}"
                                        value="{{ $processoProduto->honorarios_nix ? number_format($processoProduto->honorarios_nix, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][dape]"
                                        id="dape-{{ $index }}"
                                        value="{{ $processoProduto->dape ? number_format($processoProduto->dape, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][correios]"
                                        id="correios-{{ $index }}"
                                        value="{{ $processoProduto->correios ? number_format($processoProduto->correios, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][li_dta_honor_nix]"
                                        id="li_dta_honor_nix-{{ $index }}"
                                        value="{{ $processoProduto->li_dta_honor_nix ? number_format($processoProduto->li_dta_honor_nix, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][desp_desenbaraco]"
                                        id="desp_desenbaraco-{{ $index }}"
                                        value="{{ $processoProduto->desp_desenbaraco ? number_format($processoProduto->desp_desenbaraco, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][diferenca_cambial_frete]"
                                        id="diferenca_cambial_frete-{{ $index }}"
                                        value="{{ $processoProduto->diferenca_cambial_frete ? number_format($processoProduto->diferenca_cambial_frete, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][diferenca_cambial_fob]"
                                        id="diferenca_cambial_fob-{{ $index }}"
                                        value="{{ $processoProduto->diferenca_cambial_fob ? number_format($processoProduto->diferenca_cambial_fob, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][custo_unitario_final]"
                                        id="custo_unitario_final-{{ $index }}"
                                        value="{{ $processoProduto->custo_unitario_final ? number_format($processoProduto->custo_unitario_final, 7, ',', '.') : '' }}">
                                </td>

                                <td>
                                    <input type="text" data-row="{{ $index }}"
                                        class=" form-control moneyReal7" readonly
                                        name="produtos[{{ $index }}][custo_total_final]"
                                        id="custo_total_final-{{ $index }}"
                                        value="{{ $processoProduto->custo_total_final ? number_format($processoProduto->custo_total_final, 7, ',', '.') : '' }}">
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="100" class="text-center">
                                    <p class="text-muted mt-3">Nenhum produto cadastrado. Clique em "Adicionar Produto" para começar.</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot id="resultado-totalizadores">

                    </tfoot>
                    </table>
                </div>
            </div>
        </form>

        <div class="modal fade" id="debugLinhaModal" tabindex="-1" aria-labelledby="debugLinhaModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable debug-modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="debugLinhaModalLabel">Cálculo da linha</h5>
                        <button type="button" class="close btn-close-debug" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="debugLinhaConteudo" class="table-responsive"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-close-debug">Fechar</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Sistema de salvamento em fases para produtos
            class SalvamentoProdutosFases {
                constructor(processoId) {
                    this.processoId = processoId;
                    this.blocos = [];
                    this.blocoAtual = 0;
                    this.totalSalvos = 0;
                }

                async salvarProdutosEmFases() {
                    try {
                        const produtos = this.coletarDadosProdutos();

                        if (produtos.length === 0) {
                            this.mostrarMensagem('Nenhum produto para salvar.', 'warning');
                            return false;
                        }

                        // Dividir em blocos de 5 produtos
                        this.blocos = this.dividirEmBlocos(produtos, 5);
                        this.blocoAtual = 0;
                        this.totalSalvos = 0;

                        // Mostrar confirmação com SweetAlert2
                        const result = await Swal.fire({
                            title: 'Salvar em Fases?',
                            html: `
                    <div class="text-left">
                        <p><strong>Total de produtos:</strong> ${produtos.length}</p>
                        <p><strong>Número de blocos:</strong> ${this.blocos.length}</p>
                        <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> A tela ficará bloqueada durante o processo</p>
                    </div>
                `,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Sim, iniciar salvamento!',
                            cancelButtonText: 'Cancelar',
                            customClass: {
                                confirmButton: 'btn btn-success',
                                cancelButton: 'btn btn-secondary'
                            }
                        });

                        if (!result.isConfirmed) {
                            return false;
                        }

                        // Iniciar processo de salvamento
                        return await this.iniciarProcessoSalvamento();

                    } catch (error) {
                        console.error('Erro no salvamento em fases:', error);
                        this.mostrarErroSweetAlert('Erro ao iniciar o salvamento: ' + error.message);
                        return false;
                    }
                }

                async iniciarProcessoSalvamento() {
                    console.log('Iniciando processo de salvamento. Total de blocos:', this.blocos.length);

                    // Mostrar modal de progresso com SweetAlert2
                    this.swalProgress = Swal.fire({
                        title: 'Salvando Produtos...',
                        html: this.getHtmlProgresso(0, this.blocos.length, 0),
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        showConfirmButton: false,
                        showCancelButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    console.log('Modal de progresso aberto');

                    try {
                        // Processar bloco por bloco
                        for (let i = 0; i < this.blocos.length; i++) {
                            console.log(`Iniciando processamento do bloco ${i + 1}`);
                            const sucesso = await this.salvarBloco(i);
                            if (!sucesso) {
                                console.error(`Falha no bloco ${i + 1}`);
                                // Fechar o modal de progresso primeiro
                                await Swal.close();
                                await Swal.fire({
                                    title: 'Erro',
                                    text: `Erro ao salvar bloco ${i + 1}`,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                                return false;
                            }
                        }

                        console.log('Todos os blocos processados com sucesso');

                        // Fechar o modal de progresso e mostrar sucesso
                        await Swal.close();
                        await this.mostrarSucessoSweetAlert();
                        return true;

                    } catch (error) {
                        console.error('Erro no processo de salvamento:', error);
                        await Swal.close();
                        await this.mostrarErroSweetAlert('Erro durante o salvamento: ' + error.message);
                        return false;
                    }
                }

                getHtmlProgresso(blocoAtual, totalBlocos, registrosSalvos) {
                    const percentual = Math.round((blocoAtual / totalBlocos) * 100);

                    return `
            <div class="container-fluid">
                <div class="progress mb-3" style="height: 25px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                         style="width: ${percentual}%">
                        <span class="progress-text">${percentual}%</span>
                    </div>
                </div>
                <div class="text-center">
                    <p class="mb-1"><strong>Processando bloco ${blocoAtual} de ${totalBlocos}</strong></p>
                    <p class="text-muted mb-0">
                        <i class="fas fa-check-circle text-success mr-1"></i>
                        ${registrosSalvos} produtos salvos
                    </p>
                </div>
            </div>
        `;
                }

                async atualizarProgressoSweetAlert(blocoAtual, totalBlocos, registrosSalvos) {
                    console.log(`Atualizando progresso: ${blocoAtual}/${totalBlocos}, registros: ${registrosSalvos}`);

                    // Atualizar o modal existente
                    Swal.update({
                        html: this.getHtmlProgresso(blocoAtual, totalBlocos, registrosSalvos)
                    });
                }

                coletarDadosProdutos() {
                    const produtos = [];
                    let rowIndex = 0;

                    $('#productsBody tr').each(function(index) {
                        const produto = {};
                        let hasData = false;

                        // Coletar todos os campos da linha
                        $(this).find('input, select, textarea').each(function() {
                            const name = $(this).attr('name');
                            if (name && name.includes('produtos[') && name.includes(']')) {
                                // Extrair o conteúdo entre produtos[ e ][
                                const start = name.indexOf('produtos[') + 9;
                                const end = name.indexOf(']', start);
                                const fieldName = name.substring(end + 2, name.length -
                                    1); // Remove os últimos ]

                                if (start !== -1 && end !== -1) {
                                    let value = $(this).val();

                                    // Tratar campos vazios
                                    if (value === '' || value === null || value === undefined) {
                                        value = '';
                                    }

                                    // Tratar campos percentuais
                                    if (typeof value === 'string' && value.includes('%')) {
                                        value = value.replace('%', '').trim();
                                    }

                                    produto[fieldName] = value;
                                    hasData = true;
                                }
                            }
                        });

                        // Só adiciona se tiver dados e produto_id
                        if (hasData && produto.produto_id && produto.produto_id !== '') {
                            // Garantir que temos um processo_produto_id (pode ser vazio para novos)
                            if (!produto.processo_produto_id) {
                                produto.processo_produto_id = '';
                            }

                            produtos.push(produto);
                            console.log(`Linha ${rowIndex} coletada:`, produto);
                            rowIndex++;
                        } else {
                            console.log(`Linha ${index} ignorada - sem produto_id ou dados:`, produto);
                        }
                    });

                    console.log('Total de produtos coletados:', produtos.length);
                    return produtos;
                }

                dividirEmBlocos(array, tamanhoBloco) {
                    const blocos = [];
                    for (let i = 0; i < array.length; i += tamanhoBloco) {
                        blocos.push(array.slice(i, i + tamanhoBloco));
                    }
                    return blocos;
                }

                async salvarBloco(indiceBloco) {
                    const blocoProdutos = this.blocos[indiceBloco];
                    console.log(`Iniciando salvamento do bloco ${indiceBloco + 1} com ${blocoProdutos.length} produtos`);

                    try {
                        // Usar FormData que é mais adequado para envio de arquivos e dados complexos
                        const formData = new FormData();
                        formData.append('_token', '{{ csrf_token() }}');
                        formData.append('_method', 'PUT');
                        formData.append('bloco_indice', indiceBloco);
                        formData.append('total_blocos', this.blocos.length);
                        formData.append('salvar_apenas_produtos', 'true');

                        let campos = [
                            'peso_liquido_total_cabecalho',
                            'outras_taxas_agente',
                            'delivery_fee',
                            'delivery_fee_brl',
                            'collect_fee',
                            'collect_fee_brl',
                            'desconsolidacao',
                            'handling',
                            'dai',
                            'honorarios_nix',
                            'dape',
                            'correios',
                            'li_dta_honor_nix',
                            'diferenca_cambial_frete',
                            'diferenca_cambial_fob'
                        ];

                        for (let campo of campos) {
                            formData.append(campo, MoneyUtils.parseMoney($(`#${campo}`).val()) || 0)
                        }

                        // Adicionar produtos do bloco
                        blocoProdutos.forEach((produto, index) => {
                            Object.keys(produto).forEach(campo => {
                                // Verificar se o valor não é undefined ou null
                                if (produto[campo] !== undefined && produto[campo] !== null) {
                                    formData.append(`produtos[${index}][${campo}]`, produto[campo]);
                                }
                            });
                        });

                        console.log('Enviando requisição para o servidor...');

                        const response = await fetch(`/processo/${this.processoId}`, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        console.log('Resposta recebida, status:', response.status);

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const data = await response.json();
                        console.log('Dados da resposta:', data);

                        if (data.success) {
                            this.totalSalvos += blocoProdutos.length;
                            console.log(`Bloco ${indiceBloco + 1} salvo com sucesso. Total salvo: ${this.totalSalvos}`);
                            await this.atualizarProgressoSweetAlert(
                                indiceBloco + 1,
                                this.blocos.length,
                                this.totalSalvos
                            );
                            return true;
                        } else {
                            console.error('Erro no servidor:', data);
                            throw new Error(data.error || 'Erro desconhecido no servidor');
                        }

                    } catch (error) {
                        console.error('Erro ao salvar bloco:', error);
                        return false;
                    }
                }

                async mostrarSucessoSweetAlert() {
                    await Swal.fire({
                        title: '✅ Sucesso!',
                        html: `
                <div class="text-center">
                    <p class="lead">Todos os produtos foram salvos!</p>
                    <p><strong>Total processado:</strong> ${this.totalSalvos} produtos</p>
                    <p class="text-info small">
                        <i class="fas fa-sync-alt mr-1"></i>
                        Recarregando página...
                    </p>
                </div>
            `,
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false
                    });

                    // Recarregar a página
                    location.reload();
                }

                mostrarErroSweetAlert(mensagem) {
                    Swal.fire({
                        title: '❌ Erro',
                        text: mensagem,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-danger'
                        }
                    });
                }

                mostrarMensagem(mensagem, tipo = 'info') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        showCloseButton: true,
                        timer: 3000,
                        timerProgressBar: true,
                    });

                    Toast.fire({
                        icon: tipo,
                        title: mensagem
                    });
                }
            }

            // Inicialização e botão de salvamento
            function inicializarSalvamentoFases() {
                // Adicionar botão de salvamento em fases se não existir
                if (!$('#btnSalvarFases').length) {
                    const botaoHTML = `
            <button type="button" class="btn btn-success" id="btnSalvarFases">
                <i class="fas fa-layer-group mr-1"></i>
                Salvar Produtos em Fases
            </button>
        `;

                    $('.addProduct').after(botaoHTML);
                }

                // Evento do botão
                $(document).off('click', '#btnSalvarFases').on('click', '#btnSalvarFases', function() {
                    const processoId = {{ $processo->id }};
                    const salvamento = new SalvamentoProdutosFases(processoId);
                    salvamento.salvarProdutosEmFases();
                });
            }

            // Inicializar quando o documento carregar
            $(document).ready(function() {
                inicializarSalvamentoFases();
            });

            // Também inicializar quando houver mudanças dinâmicas na página
            $(document).on('ajaxComplete', function() {
                inicializarSalvamentoFases();
            });
        </script>
        <script>
            (function() {
                // URL gerada pelo blade; certifique-se de ter a rota nomeada 'processo.produtos.batchDelete'
                const deleteUrl = "{{ route('processo.produtos.batchDelete') }}";
                const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

                function selectAllToggle(checked) {
                    document.querySelectorAll('.select-produto').forEach(cb => cb.checked = checked);
                }

                // Event: selecionar todos
                const selectAll = document.getElementById('select-all-produtos');
                if (selectAll) {
                    selectAll.addEventListener('change', function(e) {
                        selectAllToggle(e.target.checked);
                    });
                }

                // Coleta ids numéricos de checkboxes marcados (só produtos já salvos no DB)
                function collectSelectedSavedIds() {
                    return Array.from(document.querySelectorAll('.select-produto:checked'))
                        .map(i => i.value)
                        .filter(v => /^[0-9]+$/.test(v))
                        .map(Number);
                }

                async function batchDeleteSelected() {
                    const ids = collectSelectedSavedIds();
                    if (!ids.length) {
                        await Swal.fire({
                            icon: 'warning',
                            title: 'Nada selecionado',
                            text: 'Nenhum produto salvo selecionado. Para remover linhas não salvas, apague-as manualmente antes de salvar.',
                        });
                        return;
                    }

                    const result = await Swal.fire({
                        title: `Excluir ${ids.length} produto(s)?`,
                        text: "Esta ação não pode ser desfeita.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sim, excluir',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    });

                    if (!result.isConfirmed) {
                        return;
                    }

                    try {
                        const res = await fetch(deleteUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                ids: ids
                            })
                        });

                        const data = await res.json();
                        if (!res.ok || !data.success) {
                            console.error(data);
                            await Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: data.message || 'Erro ao excluir produtos. Veja o console.',
                            });
                            return;
                        }

                        // Remove do DOM as linhas excluídas
                        (data.deleted_ids || ids).forEach(id => {
                            const row = document.querySelector(`tr[data-id="${id}"]`);
                            if (row) row.remove();
                        });

                        await Swal.fire({
                            icon: 'success',
                            title: 'Excluídos',
                            text: `Excluídos: ${data.deleted_count ?? (data.deleted_ids || ids).length} item(s).`
                        });

                        location.reload();
                    } catch (err) {
                        console.error(err);
                        await Swal.fire({
                            icon: 'error',
                            title: 'Erro de rede',
                            text: 'Erro de rede ao excluir produtos.',
                        });
                    }
                }

                const btnDelete = document.getElementById('btnDeleteSelectedProdutos');
                if (btnDelete) {
                    btnDelete.addEventListener('click', batchDeleteSelected);
                }

                // === Suporte para inserção dinâmica de novas linhas ===
                // Se sua UI cria linhas via JS, essa função garante que toda linha nova tenha o checkbox
                function ensureCheckboxesForRows() {
                    document.querySelectorAll('#produtos-body tr').forEach(tr => {
                        if (!tr.querySelector('.select-produto')) {
                            // tenta extrair data-id se existir
                            const id = tr.getAttribute('data-id') || '';
                            const tdFirst = tr.querySelector('td');
                            if (tdFirst) {
                                const checkbox = document.createElement('input');
                                checkbox.type = 'checkbox';
                                checkbox.className = 'select-produto';
                                checkbox.value = id; // se não tiver id, será string vazia
                                // insere no início da primeira célula ou cria uma célula nova
                                const firstCell = tr.querySelector('td');
                                if (firstCell) {
                                    firstCell.parentNode.insertBefore(document.createElement('td'), firstCell);
                                    // move conteúdo para a segunda célula
                                    const newFirst = tr.querySelector('td');
                                    newFirst.appendChild(checkbox);
                                }
                            }
                        }
                    });
                }

                // Observador de mutação para capturar linhas adicionadas dinamicamente
                const tbody = document.getElementById('produtos-body');
                if (tbody) {
                    const mo = new MutationObserver((mutations) => {
                        ensureCheckboxesForRows();
                    });
                    mo.observe(tbody, {
                        childList: true,
                        subtree: true
                    });
                }

                // Chamada inicial para garantir checkboxes nas linhas existentes
                document.addEventListener('DOMContentLoaded', ensureCheckboxesForRows);
                // Também chamar logo agora caso esse script seja injetado após DOMContentLoaded
                ensureCheckboxesForRows();

            })();
        </script>
        <script>
            // Sincronização da barra de scroll horizontal extra com o container da tabela
            (function() {
                let isScrolling = false;

                function initScrollbarSync() {
                    const scrollbar = document.getElementById('tableProductsScrollbar');
                    const container = document.getElementById('tableProductsContainer');

                    if (!scrollbar || !container) {
                        // Tenta novamente após um pequeno delay se os elementos ainda não existirem
                        setTimeout(initScrollbarSync, 100);
                        return;
                    }

                    // Sincroniza o scroll da barra extra com o container
                    scrollbar.addEventListener('scroll', function() {
                        if (!isScrolling) {
                            isScrolling = true;
                            container.scrollLeft = scrollbar.scrollLeft;
                            setTimeout(() => {
                                isScrolling = false;
                            }, 10);
                        }
                    });

                    // Sincroniza o scroll do container com a barra extra
                    container.addEventListener('scroll', function() {
                        if (!isScrolling) {
                            isScrolling = true;
                            scrollbar.scrollLeft = container.scrollLeft;
                            setTimeout(() => {
                                isScrolling = false;
                            }, 10);
                        }
                    });

                    // Atualiza a largura da barra de scroll quando a tabela muda de tamanho
                    function updateScrollbarWidth() {
                        const table = container.querySelector('.table-products');
                        if (table && scrollbar) {
                            const scrollbarContent = scrollbar.querySelector('.table-products-scrollbar-content');
                            if (scrollbarContent) {
                                // Usa scrollWidth para pegar a largura total incluindo conteúdo oculto
                                const tableWidth = Math.max(table.scrollWidth, table.offsetWidth);
                                scrollbarContent.style.minWidth = tableWidth + 'px';
                            }
                        }
                    }

                    // Observa mudanças na tabela
                    const observer = new MutationObserver(function() {
                        setTimeout(updateScrollbarWidth, 50);
                    });
                    
                    const table = container.querySelector('.table-products');
                    if (table) {
                        observer.observe(table, {
                            childList: true,
                            subtree: true,
                            attributes: true,
                            attributeFilter: ['style', 'class']
                        });
                        
                        // Observa também o container para mudanças de tamanho
                        observer.observe(container, {
                            attributes: true,
                            attributeFilter: ['style']
                        });
                        
                        // Inicializa a largura
                        setTimeout(updateScrollbarWidth, 100);
                    }

                    // Atualiza quando a janela é redimensionada
                    let resizeTimeout;
                    window.addEventListener('resize', function() {
                        clearTimeout(resizeTimeout);
                        resizeTimeout = setTimeout(updateScrollbarWidth, 100);
                    });

                    // Atualiza quando a tabela é modificada dinamicamente
                    $(document).on('DOMNodeInserted DOMNodeRemoved', '.table-products', function() {
                        setTimeout(updateScrollbarWidth, 100);
                    });
                }

                // Inicializa quando o DOM estiver pronto
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initScrollbarSync);
                } else {
                    // Se já estiver carregado, tenta inicializar imediatamente
                    setTimeout(initScrollbarSync, 100);
                }
            })();
        </script>

    @endif

    <style>
        /* Estilos para a área de produtos - melhorias sutis */
        #custom-tabs-three-home {
            padding: 20px;
        }

        /* Botões melhorados */
        #custom-tabs-three-home .btn {
            transition: all 0.2s ease;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 8px 16px;
        }

        #custom-tabs-three-home .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
        }

        #custom-tabs-three-home .btn-primary {
            background: var(--theme-gradient-primary);
            border: none;
        }

        #custom-tabs-three-home .btn-primary:hover {
            background: var(--theme-gradient-primary-hover);
        }

        /* Espaçamento de ícones nos botões */
        #custom-tabs-three-home .btn i {
            margin-right: 6px;
        }

        /* Barra de scroll melhorada */
        .table-products-scrollbar {
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-bottom: none;
            border-radius: 6px 6px 0 0;
        }

        .table-products-scrollbar::-webkit-scrollbar-thumb {
            background: var(--theme-scrollbar-thumb);
            border-radius: 4px;
        }

        .table-products-scrollbar::-webkit-scrollbar-thumb:hover {
            background: var(--theme-scrollbar-thumb-hover);
        }

        /* Container da tabela */
        .table-products-container {
            border: 1px solid #e0e0e0;
            border-top: none;
            border-radius: 0 0 6px 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* Tabela melhorada */
        .table-products {
            margin-bottom: 0;
        }

        .table-products thead th {
            font-weight: 600;
            font-size: 13px;
            padding: 10px 8px;
            border: 1px solid #dee2e6;
        }

        .table-products tbody td {
            padding: 8px;
            border: 1px solid #e9ecef;
            font-size: 13px;
        }

        .table-products tbody tr:hover {
            background-color: rgba(183, 170, 9, 0.03);
        }

        /* Inputs na tabela */
        .table-products .form-control {
            border: 1px solid #ced4da;
            transition: all 0.2s ease;
            font-size: 16px;
        }

        .table-products .form-control:focus {
            border-color: #b7aa09;
            box-shadow: 0 0 0 0.15rem rgba(183, 170, 9, 0.15);
        }

        /* Botão de remover */
        .table-products .btn-remove {
            transition: all 0.2s ease;
        }

        .table-products .btn-remove:hover {
            transform: scale(1.1);
        }

        /* Checkbox */
        .table-products .select-produto {
            cursor: pointer;
            width: 18px;
            height: 18px;
        }

        /* Select2 melhorado */
        .table-products .select2-container--default .select2-selection--single {
            border: 1px solid #ced4da;
            border-radius: 4px;
            height: 32px;
        }

        .table-products .select2-container--default .select2-selection--single:focus {
            border-color: #b7aa09;
        }

        /* MiddleRow inputs */
        .table-products .middleRowInputTh {
            background-color: #B6A909 !important;
        }

        .table-products .middleRowInputTh input {
            background-color: #B6A909 !important;
            border: 1px solid #9A8E08;
        }

        /* Separador de adição */
        .separador-adicao td {
            background-color: #b7aa09 !important;
            border: none !important;
            height: 5px;
            padding: 0 !important;
        }

        /* Responsividade dos botões */
        @media (max-width: 768px) {
            #custom-tabs-three-home .d-flex.flex-wrap {
                flex-direction: column;
            }

            #custom-tabs-three-home .d-flex.flex-wrap .btn {
                width: 100%;
                margin-bottom: 8px;
            }
        }

        #debugLinhaModal .modal-dialog {
            max-width: none;
            width: auto;
        }

        #debugLinhaModal .modal-body {
            overflow-x: auto;
        }

        .debug-section {
            background: #fff;
            border: 1px solid #e4e7f2;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .debug-section-title {
            background: var(--theme-gradient-primary, linear-gradient(90deg, #253b80 0%, #485fc7 100%));
            color: #fff;
            padding: 14px 18px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.08em;
        }

        .debug-grid {
            display: grid;
            grid-template-columns: minmax(180px, 1fr) minmax(180px, 1fr) minmax(220px, 1.4fr) minmax(220px, 1.4fr);
            gap: 0;
        }

        .debug-grid > div {
            padding: 14px 18px;
            border-top: 1px solid #f0f2f8;
            background: #fff;
        }

        .debug-grid-header {
            background: #f5f6fb;
            font-weight: 600;
            font-size: 12px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #5f6385;
        }

        .debug-grid-header > div {
            border-top: none;
        }

        .debug-grid-row:nth-child(even) {
            background: #fafbfc;
        }

        .debug-cell-label {
            font-weight: 500;
            color: #2d3748;
        }

        .debug-cell-value {
            font-weight: 600;
            color: #1a202c;
            font-family: 'Courier New', monospace;
        }

        .debug-cell-text {
            color: #4a5568;
            font-size: 13px;
            line-height: 1.6;
        }
    </style>
</div>
