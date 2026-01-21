<div class="tab-pane fade" id="custom-tabs-three-multa" role="tabpanel"
    aria-labelledby="custom-tabs-three-multa-tab">
    @if (isset($productsClient))
        <form id="form-produtos-multa" action="{{ route('processo.update', $processo->id) }}" enctype="multipart/form-data"
            action="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="tipo_processo" value="maritimo">
            <input type="hidden" name="salvar_apenas_produtos_multa" value="1">
            <div class="d-flex flex-wrap mb-3" style="gap: 10px;">
                <button type="button" class="btn btn-primary addProductMulta">
                    <i class="fas fa-plus me-2"></i>Adicionar Produto (Multa)
                </button>
                <button id="btnDeleteSelectedProdutosMulta" class="btn btn-danger" type="button">
                    <i class="fas fa-trash me-2"></i>Excluir Selecionados
                </button>
                <button type="button" class="btn btn-secondary btn-reordenar-multa">
                    <i class="fas fa-sort me-2"></i>Reordenar por Adição/Item
                </button>
                <button type="button" class="btn btn-info" id="btnRecalcularMulta">
                    <i class="fas fa-calculator me-2"></i>Recalcular Tabela Multa
                </button>
                <button type="button" class="btn btn-success" id="btnSalvarFasesMulta">
                    <i class="fas fa-layer-group me-2"></i>Salvar Multas em Fases
                </button>
            </div>
            <!-- Card com informações resumidas -->
            <div class="card mb-3" id="card-resumo-multa">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Resumo de Multas</h5>
                </div>
                <div class="card-body p-2">
                    <table class="table table-sm table-bordered mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 25%;">Diferença de Impostos a serem recolhidos</th>
                                <th class="text-center" style="width: 25%;">Multa fiscal 711</th>
                                <th class="text-center" style="width: 25%;">Multa Oficio 725</th>
                                <th class="text-center  text-white" style="width: 25%;">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-end fw-bold" id="diferenca-impostos-multa">R$ 0,00</td>
                                <td class="text-end fw-bold" id="multa-fiscal-711-multa">R$ 0,00</td>
                                <td class="text-end fw-bold" id="multa-oficio-725-multa">R$ 0,00</td>
                                <td class="text-end fw-bold " id="total-multa">R$ 0,00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Campos CPT -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="valor_cpt_usd_multa" class="form-label fw-bold">Valor CPT USD (Multa)</label>
                            <input type="text" class="form-control" name="valor_cpt_usd_multa"
                                id="valor_cpt_usd_multa" value="" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="valor_cpt_brl_multa" class="form-label fw-bold">Valor CPT BRL (Multa)</label>
                            <input type="text" class="form-control" name="valor_cpt_brl_multa"
                                id="valor_cpt_brl_multa" value="" readonly>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $colunasMulta = [
                    ['name' => 'adicao', 'label' => 'ADIÇÃO', 'class' => 'form-control'],
                    ['name' => 'item', 'label' => 'ITEM', 'class' => 'form-control', 'type' => 'number'],
                    ['name' => 'codigo', 'label' => 'CODIGO', 'class' => 'form-control', 'readonly' => true],
                    ['name' => 'ncm', 'label' => 'NCM', 'class' => 'form-control', 'readonly' => true],
                    ['name' => 'descricao', 'label' => 'DESCRIÇÃO', 'class' => 'form-control', 'readonly' => true],
                    ['name' => 'quantidade', 'label' => 'QUANTD', 'class' => 'form-control', 'type' => 'number'],
                    ['name' => 'peso_liquido_unitario', 'label' => 'PESO LIQ. UNIT', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'peso_liquido_total', 'label' => 'PESO LIQ TOTAL', 'class' => 'form-control moneyReal pesoLiqTotalMulta'],
                    ['name' => 'fator_peso', 'label' => 'FATOR PESO', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'fob_unit_usd', 'label' => 'FOB UNIT USD', 'class' => 'form-control moneyReal7 fobUnitarioMulta'],
                    ['name' => 'fob_total_usd', 'label' => 'FOB TOTAL USD', 'class' => 'form-control moneyReal7', 'readonly' => true],
                    ['name' => 'fob_total_brl', 'label' => 'VLR TOTALFOB R$', 'class' => 'form-control moneyReal7', 'readonly' => true],
                    ['name' => 'service_charges', 'label' => 'SERVICE CHARGES', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'service_charges_brl', 'label' => 'SERVICE CHARGES R$', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'frete_usd', 'label' => 'FRETE INT.USD', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'frete_brl', 'label' => 'FRETE INT.R$', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'acresc_frete_usd', 'label' => 'ACRESC. FRETE USD', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'acresc_frete_brl', 'label' => 'ACRESC. FRETE R$', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'vlr_crf_unit', 'label' => 'VLR CFR UNIT', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'vlr_crf_total', 'label' => 'VLR CFR TOTAL', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'seguro_usd', 'label' => 'SEGURO INT.USD', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'seguro_brl', 'label' => 'SEGURO INT.R$', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'thc_usd', 'label' => 'THC USD', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'thc_brl', 'label' => 'THC R$', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'valor_aduaneiro_usd', 'label' => 'VLR ADUANEIRO USD', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'valor_aduaneiro_brl', 'label' => 'VLR ADUANEIRO R$', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'ii_percent', 'label' => 'II', 'class' => 'form-control percentage2'],
                    ['name' => 'ipi_percent', 'label' => 'IPI', 'class' => 'form-control percentage2'],
                    ['name' => 'pis_percent', 'label' => 'PIS', 'class' => 'form-control percentage2'],
                    ['name' => 'cofins_percent', 'label' => 'COFINS', 'class' => 'form-control percentage2'],
                    ['name' => 'icms_percent', 'label' => 'ICMS', 'class' => 'form-control percentage2 icms_percent_multa'],
                    ['name' => 'icms_reduzido_percent', 'label' => 'ICMS REDUZIDO', 'class' => 'form-control percentage2 icms_reduzido_percent_multa'],
                    ['name' => 'reducao', 'label' => 'REDUÇÃO', 'class' => 'form-control moneyReal8', 'readonly' => true],
                    ['name' => 'valor_ii', 'label' => 'VLR II', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'base_ipi', 'label' => 'BC IPI', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'valor_ipi', 'label' => 'VLR IPI', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'base_pis_cofins', 'label' => 'BC PIS/COFINS', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'valor_pis', 'label' => 'VLR PIS', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'valor_cofins', 'label' => 'VLR COFINS', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'despesa_aduaneira', 'label' => 'DESP. ADUANEIRA', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'vlr_ii_pos_despesa', 'label' => 'VLR II', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'vlr_ipi_pos_despesa', 'label' => 'VLR IPI', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'vlr_pis_pos_despesa', 'label' => 'VLR PIS', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'vlr_cofins_pos_despesa', 'label' => 'VLR COFINS', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'nova_ncm', 'label' => 'NOVA NCM', 'class' => 'form-control'],
                    ['name' => 'ii_nova_ncm_percent', 'label' => 'II', 'class' => 'form-control percentage2'],
                    ['name' => 'ipi_nova_ncm_percent', 'label' => 'IPI', 'class' => 'form-control percentage2'],
                    ['name' => 'pis_nova_ncm_percent', 'label' => 'PIS', 'class' => 'form-control percentage2'],
                    ['name' => 'cofins_nova_ncm_percent', 'label' => 'COFINS', 'class' => 'form-control percentage2'],
                    ['name' => 'vlr_ii_nova_ncm', 'label' => 'VLR II', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'vlr_ipi_nova_ncm', 'label' => 'VLR IPI', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'vlr_pis_nova_ncm', 'label' => 'VLR PIS', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'vlr_cofins_nova_ncm', 'label' => 'VLR COFINS', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'vlr_ii_recalc', 'label' => 'VLR II', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'vlr_ipi_recalc', 'label' => 'VLR IPI', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'vlr_pis_recalc', 'label' => 'VLR PIS', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'vlr_cofins_recalc', 'label' => 'VLR COFINS', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'valor_aduaneiro_multa', 'label' => 'VALOR ADUANEIRO', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'ii_percent_aduaneiro', 'label' => 'II', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'ipi_percent_aduaneiro', 'label' => 'IPI', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'pis_percent_aduaneiro', 'label' => 'PIS', 'class' => 'form-control moneyReal', 'readonly' => true],
                    ['name' => 'cofins_percent_aduaneiro', 'label' => 'COFINS', 'class' => 'form-control moneyReal', 'readonly' => true],
                ];

                $camposMultaHidden = [
                    'fob_unit_moeda_estrangeira',
                    'fob_total_moeda_estrangeira',
                    'frete_moeda_estrangeira',
                    'seguro_moeda_estrangeira',
                    'acrescimo_moeda_estrangeira',
                    'service_charges_moeda_estrangeira',
                    'valor_unit_nf',
                    'valor_total_nf',
                    'base_icms_st',
                    'valor_icms_st',
                    'valor_total_nf_com_icms_st',
                    'icms_st',
                    'fator_valor_fob',
                    'fator_tx_siscomex',
                    'taxa_siscomex',
                    'multa',
                    'tx_def_li',
                    'outras_taxas_agente',
                    'liberacao_bl',
                    'desconsolidacao',
                    'isps_code',
                    'handling',
                    'capatazia',
                    'tx_correcao_lacre',
                    'afrmm',
                    'armazenagem_sts',
                    'frete_dta_sts_ana',
                    'sda',
                    'rep_sts',
                    'armaz_ana',
                    'lavagem_container',
                    'rep_anapolis',
                    'correios',
                    'li_dta_honor_nix',
                    'honorarios_nix',
                    'custo_unitario_final',
                    'custo_total_final',
                ];

                $camposPercentuais = [
                    'ii_percent',
                    'ipi_percent',
                    'pis_percent',
                    'cofins_percent',
                    'icms_percent',
                    'icms_reduzido_percent',
                    'ii_nova_ncm_percent',
                    'ipi_nova_ncm_percent',
                    'pis_nova_ncm_percent',
                    'cofins_nova_ncm_percent',
                ];
            @endphp

            <div class="table-products-wrapper">
                <div class="table-products-container" style="overflow-x: auto; overflow-y: auto; max-height: 80vh;">
                    <table class="table table-bordered table-striped table-products table-products-multa" style="min-width: 3200px;">
                        <thead class="text-center">
                            <tr>
                                <th class="d-flex align-items-center justify-content-center"
                                    style="background-color: #212529 !important;">
                                    Ações <input type="checkbox" style="margin-left: 10px" id="select-all-produtos-multa" title="Selecionar todos">
                                </th>
                                <th style="min-width: 300px !important;">PRODUTO</th>
                                @foreach ($colunasMulta as $coluna)
                                    <th>{{ $coluna['label'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody id="productsBodyMulta">
                            @foreach ($processoProdutosMulta ?? [] as $index => $processoProdutoMulta)
                                <tr class="linhas-input" id="row-multa-{{ $index }}">
                                    <td class="d-flex align-items-center justify-content-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-danger btn-sm btn-remove-multa" data-id="{{ $index }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                        <input type="checkbox" style="margin-left: 10px" class="select-produto-multa" value="{{ $processoProdutoMulta->id ?? '' }}">
                                    </td>

                                    <input type="hidden" name="produtos_multa[{{ $index }}][processo_produto_multa_id]"
                                        id="processo_produto_multa_id-{{ $index }}" value="{{ $processoProdutoMulta->id ?? '' }}">
                                    @foreach ($camposMultaHidden as $campoHidden)
                                        <input type="hidden" data-row="{{ $index }}"
                                            name="produtos_multa[{{ $index }}][{{ $campoHidden }}]"
                                            id="{{ $campoHidden }}_multa-{{ $index }}"
                                            value="{{ $processoProdutoMulta->$campoHidden ?? '' }}">
                                    @endforeach

                                    <td>
                                        <select data-row="{{ $index }}"
                                            class="custom-select selectProductMulta w-100 select2"
                                            name="produtos_multa[{{ $index }}][produto_id]"
                                            id="produto_multa_id-{{ $index }}">
                                            <option selected disabled>Selecione uma opção</option>
                                            @foreach ($productsClient as $produto)
                                                <option value="{{ $produto->id }}"
                                                    {{ ($processoProdutoMulta->produto_id ?? null) == $produto->id ? 'selected' : '' }}>
                                                    {{ $produto->modelo }} - {{ $produto->codigo }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    @foreach ($colunasMulta as $coluna)
                                        @php
                                            $campo = $coluna['name'];
                                            $readonly = $coluna['readonly'] ?? false;
                                            $type = $coluna['type'] ?? 'text';
                                            $class = $coluna['class'] ?? 'form-control';

                                            if ($campo === 'codigo') {
                                                $valor = optional($processoProdutoMulta->produto)->codigo ?? '';
                                            } elseif ($campo === 'ncm') {
                                                $valor = optional($processoProdutoMulta->produto)->ncm ?? '';
                                            } elseif ($campo === 'descricao') {
                                                $valor = optional($processoProdutoMulta->produto)->descricao ?? '';
                                            } else {
                                                $valor = $processoProdutoMulta->$campo ?? '';
                                            }

                                            // Campos inteiros (adicao, item, quantidade) não devem ser formatados com decimais
                                            if (in_array($campo, ['adicao', 'item', 'quantidade'])) {
                                                $valor = $valor !== '' && $valor !== null ? (int)$valor : '';
                                            } elseif (in_array($campo, $camposPercentuais, true)) {
                                                $valor = $valor !== '' && $valor !== null ? number_format((float) $valor, 2, ',', '.') . ' %' : '';
                                            } elseif ($campo === 'peso_liquido_unitario') {
                                                $valor = $valor !== '' && $valor !== null ? number_format((float) $valor, 6, ',', '.') : '';
                                            } elseif ($campo === 'fator_peso') {
                                                $valor = $valor !== '' && $valor !== null ? number_format((float) $valor, 8, ',', '.') : '';
                                            } elseif (is_numeric($valor)) {
                                                $valor = number_format((float) $valor, 2, ',', '.');
                                            }
                                        @endphp
                                        <td>
                                            @php
                                                $campoId = $campo;
                                                if (!str_ends_with($campo, '_multa')) {
                                                    $campoId = $campo . '_multa';
                                                }
                                            @endphp
                                            <input data-row="{{ $index }}" type="{{ $type }}"
                                                class="{{ $class }}"
                                                name="produtos_multa[{{ $index }}][{{ $campo }}]"
                                                id="{{ $campoId }}-{{ $index }}"
                                                value="{{ $valor }}"
                                                {{ $readonly ? 'readonly' : '' }}>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot id="resultado-totalizadores-multa">
                        </tfoot>
                    </table>
                </div>
            </div>
        </form>
    @endif
</div>
