<div class="tab-pane fade " id="custom-tabs-three-home" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab">
    @if (isset($productsClient))
        <button type="button" class="btn btn-primary mb-2 addProduct ">Adicionar
            Produto</button>
        <button type="button" class="btn btn-info mb-2" id="recalcularTabela">
            <i class="fas fa-calculator"></i> Recalcular Toda a Tabela
        </button>
        <button type="button" class="btn btn-secondary mb-2 btn-reordenar">
            <i class="fas fa-sort"></i> Reordenar por Adição/Item
        </button>
        <div style="overflow-x: auto; width: 100%;">
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
                        <th style="background-color: #fff"> </th>
                        <th colspan="54"></th>

                        @php
                            $campos = [
                                'outras_taxas_agente',
                                'liberacao_bl',
                                'desconsolidacao',
                                'isps_code',
                                'handling',
                                'capatazia',
                                'afrmm',
                                'armazenagem_sts',
                                'frete_dta_sts_ana',
                                'sda',
                                'rep_sts',
                                'armaz_ana',
                                'lavagem_container',
                                'rep_anapolis',
                                'li_dta_honor_nix',
                                'honorarios_nix',
                            ];
                        @endphp

                        @foreach ($campos as $campo)
                            <th class="middleRowInputTh">
                                @if ($campo == 'capatazia')
                                    <input type="text" class=" form-control moneyReal" name="{{ $campo }}"
                                        id="{{ $campo }}" readonly
                                        value="{{ number_format($processo->thc_capatazia ?? 0, 5, ',', '.') }}">
                                @else
                                    <input type="text" class=" form-control moneyReal" name="{{ $campo }}"
                                        id="{{ $campo }}"
                                        value="{{ number_format($processo->$campo ?? 0, 5, ',', '.') }}">
                                @endif
                            </th>
                        @endforeach
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>

                    </tr>
                    <tr>
                        <th style="background-color: #212529 !important">Ações</th>
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
                        <th style="min-width: 300px !important;">DESP. DESEMBARAÇO</th>
                        <th>DIF. CAMBIAL FRETE</th>
                        <th>DIF.CAMBIAL FOB</th>
                        <th>CUSTO UNIT FINAL</th>
                        <th>CUSTO TOTAL FINAL</th>
                    </tr>
                </thead>
                <tbody id="productsBody">
                    @foreach ($processoProdutos as $index => $processoProduto)
                        <tr id="row-{{ $index }}">
                            <td>
                                <button type="button" onclick="showDeleteConfirmation({{ $processoProduto->id }})"
                                    class="btn btn-danger btn-sm btn-remove" data-id="{{ $processoProduto->id }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
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
                                    name="produtos[{{ $index }}][descricao]" id="descricao-{{ $index }}"
                                    value="{{ $processoProduto->descricao }}">
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
                                <input data-row="{{ $index }}" type="text" class=" form-control moneyReal"
                                    name="produtos[{{ $index }}][quantidade]"
                                    value="{{ $processoProduto->quantidade ? number_format($processoProduto->quantidade, 5, ',', '.') : '' }}"
                                    id="quantidade-{{ $index }}">
                            </td>

                            <td>
                                <input data-row="{{ $index }}" type="text" class="form-control moneyReal"
                                    readonly name="produtos[{{ $index }}][peso_liquido_unitario]"
                                    id="peso_liquido_unitario-{{ $index }}"
                                    value="{{ $processoProduto->peso_liquido_unitario ? number_format($processoProduto->peso_liquido_unitario, 5, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input data-row="{{ $index }}" type="text"
                                    class="form-control pesoLiqTotal moneyReal"
                                    name="produtos[{{ $index }}][peso_liquido_total]"
                                    id="peso_liquido_total-{{ $index }}"
                                    value="{{ $processoProduto->peso_liquido_total ? number_format($processoProduto->peso_liquido_total, 5, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input data-row="{{ $index }}" type="text" class=" form-control moneyReal"
                                    readonly name="produtos[{{ $index }}][fator_peso]"
                                    id="fator_peso-{{ $index }}"
                                    value="{{ $processoProduto->fator_peso ? number_format($processoProduto->fator_peso, 5, ',', '.') : '' }}">
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
                                <input data-row="{{ $index }}" type="text" class="form-control moneyReal7"
                                    readonly name="produtos[{{ $index }}][fob_total_usd]"
                                    id="fob_total_usd-{{ $index }}"
                                    value="{{ $processoProduto->fob_total_usd ? number_format($processoProduto->fob_total_usd, 7, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input data-row="{{ $index }}" type="text" class="form-control moneyReal7"
                                    readonly name="produtos[{{ $index }}][fob_total_brl]"
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
                                <input data-row="{{ $index }}" type="text" class="form-control moneyReal7"
                                    readonly name="produtos[{{ $index }}][frete_usd]"
                                    id="frete_usd-{{ $index }}"
                                    value="{{ $processoProduto->frete_usd ? number_format($processoProduto->frete_usd, 7, ',', '.') : '' }}">
                            </td>
                            <td>
                                <input data-row="{{ $index }}" type="text" class="form-control moneyReal7"
                                    readonly name="produtos[{{ $index }}][frete_brl]"
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
                                <input data-row="{{ $index }}" type="text" class="form-control moneyReal7"
                                    readonly name="produtos[{{ $index }}][seguro_usd]"
                                    id="seguro_usd-{{ $index }}"
                                    value="{{ $processoProduto->seguro_usd ? number_format($processoProduto->seguro_usd, 7, ',', '.') : '' }}">
                            </td>
                            <td>
                                <input data-row="{{ $index }}" type="text" class="form-control moneyReal7"
                                    readonly name="produtos[{{ $index }}][seguro_brl]"
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

                            <!-- Resto das colunas permanecem iguais -->
                            <td>
                                <input data-row="{{ $index }}" type="text"
                                    class="form-control moneyReal7" readonly
                                    name="produtos[{{ $index }}][thc_usd]" id="thc_usd-{{ $index }}"
                                    value="{{ $processoProduto->thc_usd ? number_format($processoProduto->thc_usd, 7, ',', '.') : '' }}">
                            </td>


                            <td>
                                <input data-row="{{ $index }}" type="text"
                                    class="form-control moneyReal7" readonly
                                    name="produtos[{{ $index }}][thc_brl]" id="thc_brl-{{ $index }}"
                                    value="{{ $processoProduto->thc_brl ? number_format($processoProduto->thc_brl, 7, ',', '.') : '' }}">
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
                                    value="{{ $processoProduto->ii_percent ? number_format($processoProduto->ii_percent, 7, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input data-row="{{ $index }}" type="text"
                                    class=" form-control percentage2"
                                    name="produtos[{{ $index }}][ipi_percent]"
                                    id="ipi_percent-{{ $index }}"
                                    value="{{ $processoProduto->ipi_percent ? number_format($processoProduto->ipi_percent, 7, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input data-row="{{ $index }}" type="text"
                                    class=" form-control percentage2"
                                    name="produtos[{{ $index }}][pis_percent]"
                                    id="pis_percent-{{ $index }}"
                                    value="{{ $processoProduto->pis_percent ? number_format($processoProduto->pis_percent, 7, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input data-row="{{ $index }}" type="text"
                                    class=" form-control percentage2"
                                    name="produtos[{{ $index }}][cofins_percent]"
                                    id="cofins_percent-{{ $index }}"
                                    value="{{ $processoProduto->cofins_percent ? number_format($processoProduto->cofins_percent, 7, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input data-row="{{ $index }}" type="text"
                                    class=" form-control percentage2"
                                    name="produtos[{{ $index }}][icms_percent]"
                                    id="icms_percent-{{ $index }}"
                                    value="{{ $processoProduto->icms_percent ? number_format($processoProduto->icms_percent, 7, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input data-row="{{ $index }}" type="text"
                                    class=" form-control percentage2" readonly
                                    name="produtos[{{ $index }}][icms_reduzido_percent]"
                                    id="icms_reduzido_percent-{{ $index }}"
                                    value="{{ $processoProduto->icms_reduzido_percent ? number_format($processoProduto->icms_reduzido_percent, 7, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input data-row="{{ $index }}" type="text"
                                    class=" form-control moneyReal7" readonly
                                    name="produtos[{{ $index }}][reducao]" id="reducao-{{ $index }}"
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
                                    value="{{ $processoProduto->mva ? number_format($processoProduto->mva, 7, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input type="text" data-row="{{ $index }}"
                                    class=" form-control percentage" name="produtos[{{ $index }}][icms_st]"
                                    id="icms_st-{{ $index }}"
                                    value="{{ $processoProduto->icms_st ? number_format($processoProduto->icms_st, 7, ',', '.') : '' }}">
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
                                    value="{{ $processoProduto->fator_valor_fob ? number_format($processoProduto->fator_valor_fob, 5, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input type="text" data-row="{{ $index }}"
                                    class=" form-control moneyReal" readonly
                                    name="produtos[{{ $index }}][fator_tx_siscomex]"
                                    id="fator_tx_siscomex-{{ $index }}"
                                    value="{{ $processoProduto->fator_tx_siscomex ? number_format($processoProduto->fator_tx_siscomex, 5, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input type="text" data-row="{{ $index }}"
                                    class=" form-control moneyReal7" readonly
                                    name="produtos[{{ $index }}][multa]" id="multa-{{ $index }}"
                                    value="{{ $processoProduto->multa ? number_format($processoProduto->multa, 7, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input type="text" data-row="{{ $index }}"
                                    class=" form-control moneyReal7" readonly
                                    name="produtos[{{ $index }}][tx_def_li]"
                                    id="tx_def_li-{{ $index }}"
                                    value="{{ $processoProduto->tx_def_li ? number_format($processoProduto->tx_def_li, 7, ',', '.') : '' }}">
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
                                    name="produtos[{{ $index }}][liberacao_bl]"
                                    id="liberacao_bl-{{ $index }}"
                                    value="{{ $processoProduto->liberacao_bl ? number_format($processoProduto->liberacao_bl, 7, ',', '.') : '' }}">
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
                                    name="produtos[{{ $index }}][isps_code]"
                                    id="isps_code-{{ $index }}"
                                    value="{{ $processoProduto->isps_code ? number_format($processoProduto->isps_code, 7, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input type="text" data-row="{{ $index }}"
                                    class=" form-control moneyReal7" readonly
                                    name="produtos[{{ $index }}][handling]"
                                    id="handling-{{ $index }}"
                                    value="{{ $processoProduto->handling ? number_format($processoProduto->handling, 7, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input type="text" data-row="{{ $index }}"
                                    class=" form-control moneyReal7" readonly
                                    name="produtos[{{ $index }}][capatazia]"
                                    id="capatazia-{{ $index }}"
                                    value="{{ $processoProduto->capatazia ? number_format($processoProduto->capatazia, 7, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input type="text" data-row="{{ $index }}"
                                    class=" form-control moneyReal7" readonly
                                    name="produtos[{{ $index }}][afrmm]" id="afrmm-{{ $index }}"
                                    value="{{ $processoProduto->afrmm ? number_format($processoProduto->afrmm, 7, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input type="text" data-row="{{ $index }}"
                                    class=" form-control moneyReal7" readonly
                                    name="produtos[{{ $index }}][armazenagem_sts]"
                                    id="armazenagem_sts-{{ $index }}"
                                    value="{{ $processoProduto->armazenagem_sts ? number_format($processoProduto->armazenagem_sts, 7, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input type="text" data-row="{{ $index }}"
                                    class=" form-control moneyReal7" readonly
                                    name="produtos[{{ $index }}][frete_dta_sts_ana]"
                                    id="frete_dta_sts_ana-{{ $index }}"
                                    value="{{ $processoProduto->frete_dta_sts_ana ? number_format($processoProduto->frete_dta_sts_ana, 7, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input type="text" data-row="{{ $index }}"
                                    class=" form-control moneyReal7" readonly
                                    name="produtos[{{ $index }}][sda]" id="sda-{{ $index }}"
                                    value="{{ $processoProduto->sda ? number_format($processoProduto->sda, 7, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input type="text" data-row="{{ $index }}"
                                    class=" form-control moneyReal7" readonly
                                    name="produtos[{{ $index }}][rep_sts]" id="rep_sts-{{ $index }}"
                                    value="{{ $processoProduto->rep_sts ? number_format($processoProduto->rep_sts, 7, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input type="text" data-row="{{ $index }}"
                                    class=" form-control moneyReal7" readonly
                                    name="produtos[{{ $index }}][armaz_ana]"
                                    id="armaz_ana-{{ $index }}"
                                    value="{{ $processoProduto->armaz_ana ? number_format($processoProduto->armaz_ana, 7, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input type="text" data-row="{{ $index }}"
                                    class=" form-control moneyReal7" readonly
                                    name="produtos[{{ $index }}][lavagem_container]"
                                    id="lavagem_container-{{ $index }}"
                                    value="{{ $processoProduto->lavagem_container ? number_format($processoProduto->lavagem_container, 7, ',', '.') : '' }}">
                            </td>

                            <td>
                                <input type="text" data-row="{{ $index }}"
                                    class=" form-control moneyReal7" readonly
                                    name="produtos[{{ $index }}][rep_anapolis]"
                                    id="rep_anapolis-{{ $index }}"
                                    value="{{ $processoProduto->rep_anapolis ? number_format($processoProduto->rep_anapolis, 7, ',', '.') : '' }}">
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
                                    name="produtos[{{ $index }}][honorarios_nix]"
                                    id="honorarios_nix-{{ $index }}"
                                    value="{{ $processoProduto->honorarios_nix ? number_format($processoProduto->honorarios_nix, 7, ',', '.') : '' }}">
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
                </tbody>
                <tfoot id="resultado-totalizadores">
                </tfoot>
            </table>
        </div>
    @endif
</div>
