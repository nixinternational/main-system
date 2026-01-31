@extends('layouts.app')
@section('title', isset($processo) ? '' : 'Cadastrar Processo')

@section('content')
    @php
        $nacionalizacaoAtual = $processo->nacionalizacao ?? 'outros';
    @endphp
    <style>
        [class^="col"] {
            display: flex !important;
            flex-direction: column !important;
        }

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
            overflow-y: auto
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
            font-size: 12px;
        }

        tr {
            min-height: 300px;
            

        }

        .table-products td {
            
            vertical-align: middle;
        }

        
        .table-dados-complementares td:first-child,
        .table-dados-basicos td:first-child {
            color: var(--theme-text) !important;
        }
        
        [data-background="black"] .table-dados-complementares td:first-child,
        [data-background="black"] .table-dados-basicos td:first-child {
            color: var(--theme-primary) !important;
        }

        .table-products th {
            background-color: #212529;
            color: white
        }

        
        /* Fixa apenas a última linha do thead (middleRow) */
        .table-products thead tr:last-child th {
            position: sticky;
            top: 0;
            z-index: 100;
        }

        /* Remove sticky da primeira linha do thead */
        .table-products thead tr:first-child th {
            position: static;
        }
        
        .table-products thead tr.middleRow th {
            background-color: transparent;
        }

        
        .table-products-wrapper {
            position: relative;
            width: 100%;
        }

        .table-products-scrollbar {
            overflow-x: auto;
            overflow-y: hidden;
            width: 100%;
            height: 17px;
            margin-bottom: 0;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-bottom: none;
            border-radius: 4px 4px 0 0;
        }

        .table-products-scrollbar::-webkit-scrollbar {
            height: 17px;
        }

        .table-products-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .table-products-scrollbar::-webkit-scrollbar-thumb {
            background: var(--theme-scrollbar-thumb);
            border-radius: 4px;
        }

        .table-products-scrollbar::-webkit-scrollbar-thumb:hover {
            background: var(--theme-scrollbar-thumb-hover);
        }

        .table-products-scrollbar-content {
            height: 1px;
            min-width: 3000px;
        }

        .table-products-container {
            position: relative;
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 4px 4px;
        }

        .middleRowInputTh {
            background-color: #B6A909 !important;
        }

        
        .btn-remove {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        
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
            
        }

        .info-complementares .bold {
            font-weight: bold;
        }
    </style>

    <div class="row">
        <div class="col-12 dd a shadow-lg px-0">
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
                                aria-selected="false">Dados Processo</a>
                        </li>
                        @if (isset($processo))
                            <li class="nav-item">
                                <a class="nav-link " id="custom-tabs-three-home-tab" data-toggle="pill"
                                    href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home"
                                    aria-selected="false">Produtos</a>
                            </li>
                            @if (($processo->nacionalizacao ?? null) === 'santa_catarina')
                                <li class="nav-item">
                                    <a class="nav-link " id="custom-tabs-three-multa-tab" data-toggle="pill"
                                        href="#custom-tabs-three-multa" role="tab" aria-controls="custom-tabs-three-multa"
                                        aria-selected="false">Produtos Multa</a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link " id="custom-tabs-four-home-tab" data-toggle="pill"
                                    href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home"
                                    aria-selected="false">Esboço da NF</a>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-two-tabContent">
                        <div id="avisoProcessoAlterado" class="alert d-none alert-warning" role="alert">
                            Processo alterado, pressione o botão salvar para persistir as alterações</div>

                        @include('processo.includes.dados-processo-maritimo')

                        @if (isset($processo))
                            @include('processo.includes.tabela-produtos-maritimo')
                            @if (($processo->nacionalizacao ?? null) === 'santa_catarina')
                                @include('processo.includes.tabela-produtos-maritimo-multa')
                            @endif
                            <div class="tab-pane fade" id="custom-tabs-four-home"
                                aria-labelledby="custom-tabs-four-home-tab" role="tabpanel">
                                @include('processo.includes.esboco-tab-content', [
                                    'processo' => $processo,
                                    'pdfRoute' => route('processo.esboco.pdf', $processo->id),
                                    'fornecedoresEsboco' => $fornecedoresEsboco ?? collect(),
                                    'podeSelecionarFornecedor' => $podeSelecionarFornecedor ?? false,
                                ])
                            </div>
                        @endif
                    </div>



                </div>
            </div>
        </div>
    </div>
    @if (isset($productsClient))
        <input type="hidden" name="productsClient" id="productsClient" value="{{ $productsClient }}">
        <input type="hidden" id="useProductsAjax" value="{{ !empty($useProductsAjax) ? '1' : '0' }}">
        <input type="hidden" id="catalogoId" value="{{ $catalogoId ?? '' }}">
        <input type="hidden" id="productsSearchUrl" value="{{ route('produtos.search') }}">
        <input type="hidden" name="dolarHoje" id="dolarHoje" value="{{ json_encode($dolar) }}">
        <input type="hidden" id="processoAlterado" name="processoAlterado" value="0">
    @endif
    <form id="delete-form" method="POST" action="{{ route('documento.cliente.destroy', 'document_id') }}"
        style="display:none;">
        @method('DELETE')
        @csrf
    </form>
    <script>
        let reordenando = false;
        const useProductsAjax = $('#useProductsAjax').val() === '1';
        const productsSearchUrl = $('#productsSearchUrl').val();
        const catalogoId = $('#catalogoId').val();
        let products = [];
        try {
            products = JSON.parse($('#productsClient').val() || '[]');
        } catch (e) {
            products = [];
        }
        let productOptionsHtml = '';
        if (Array.isArray(products) && !useProductsAjax) {
            const options = [];
            for (const produto of products) {
                options.push(`<option value="${produto.id}">${produto.modelo} - ${produto.codigo}</option>`);
            }
            productOptionsHtml = options.join('');
        }
        let debugStore = {};
        let debugGlobals = {};
        let contadorRecalculos = 0;
        let contadorEventosCampos = 0;

        {{-- Include de funções de debug e formatação --}}
        @include('processo.includes.scripts.processo-debug')

        function initProductSelectAjax($select) {
            if (!useProductsAjax || !$select?.length || typeof $.fn.select2 !== 'function') {
                return;
            }
            $select.each(function() {
                const $el = $(this);
                if ($el.hasClass('select2-hidden-accessible')) {
                    return;
                }
                $el.select2({
                    width: '100%',
                    allowClear: true,
                    ajax: {
                        url: productsSearchUrl,
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term,
                                page: params.page || 1,
                                catalogo_id: catalogoId
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.results || [],
                                pagination: data.pagination || {}
                            };
                        }
                    }
                });
            });
        }

        function atualizarTotalizadores() {

            const rows = $('#productsBody tr:not(.separador-adicao)');
            
            // Sempre criar o totalizador, mesmo sem linhas
            const tfoot = $('#resultado-totalizadores');
            if (tfoot.length === 0) {
                return;
            }
            
            if (rows.length === 0) {
                // Se não houver linhas, criar totalizador vazio
                tfoot.empty();
                tfoot.append('<tr><td colspan="100" style="text-align: center; font-weight: bold;">Nenhum produto cadastrado</td></tr>');
                return;
            }

            let totais = {
                quantidade: 0,
                peso_liquido_total: 0,
                fob_total_usd: 0,
                fob_total_brl: 0,
                frete_usd: 0,
                frete_brl: 0,
                seguro_usd: 0,
                seguro_brl: 0,
                acresc_frete_usd: 0,
                acresc_frete_brl: 0,
                vlr_crf_total: 0,
                vlr_crf_unit: 0,
                service_charges: 0,
                service_charges_brl: 0,
                thc_usd: 0,
                thc_brl: 0,
                valor_aduaneiro_usd: 0,
                valor_aduaneiro_brl: 0,
                valor_ii: 0,
                base_ipi: 0,
                valor_ipi: 0,
                base_pis_cofins: 0,
                valor_pis: 0,
                valor_cofins: 0,
                despesa_aduaneira: 0,
                base_icms_sem_reducao: 0,
                valor_icms_sem_reducao: 0,
                base_icms_reduzido: 0,
                valor_icms_reduzido: 0,
                valor_total_nf: 0,
                valor_total_nf_sem_icms_st: 0,
                base_icms_st: 0,
                valor_icms_st: 0,
                valor_total_nf_com_icms_st: 0,
                multa: 0,
                multa_complem: 0,
                dif_impostos: 0,
                tx_def_li: 0,
                taxa_siscomex: 0,
                outras_taxas_agente: 0,
                liberacao_bl: 0,
                desconsolidacao: 0,
                isps_code: 0,
                handling: 0,
                capatazia: 0,
                tx_correcao_lacre: 0,
                afrmm: 0,
                armazenagem_sts: 0,
                armazenagem_porto: 0,
                frete_dta_sts_ana: 0,
                frete_sts_cgb: 0,
                diarias: 0,
                frete_rodoviario: 0,
                dif_frete_rodoviario: 0,
                sda: 0,
                rep_sts: 0,
                armaz_cgb: 0,
                rep_cgb: 0,
                demurrage: 0,
                rep_porto: 0,
                armaz_ana: 0,
                lavagem_container: 0,
                rep_anapolis: 0,

                correios: 0,
                li_dta_honor_nix: 0,
                honorarios_nix: 0,
                desp_desenbaraco: 0,
                diferenca_cambial_frete: 0,
                diferenca_cambial_fob: 0,
                opcional_1_valor: 0,
                opcional_2_valor: 0,
                custo_unitario_final: 0,
                custo_total_final: 0,
                dez_porcento: 0,
                custo_com_margem: 0,
                vlr_ipi_mg: 0,
                vlr_icms_mg: 0,
                pis_mg: 0,
                cofins_mg: 0,
                custo_total_final_credito: 0,
                custo_unit_credito: 0,
                bc_icms_st_mg: 0,
                vlr_icms_st_mg: 0,
                custo_total_c_icms_st: 0,
                custo_unit_c_icms_st: 0,
                exportador_mg: 0,
                tributos_mg: 0,
                despesas_mg: 0,
                total_pago_mg: 0
            };


            let fatorPesoSum = 0;
            let fatorValorFobSum = 0;
            let fatorTxSiscomexSum = 0;

            const moedaServiceCharges = $('#service_charges_moeda').val();
            
            rows.each(function() {
                const rowId = this.id.replace('row-', '');

                // Usar valores brutos armazenados se disponíveis, caso contrário usar valores dos inputs
                const valoresBrutos = window.valoresBrutosPorLinha && window.valoresBrutosPorLinha[rowId];
                if (valoresBrutos) {
                    // Usar valores brutos (sem arredondamento) para máxima precisão
                    Object.keys(totais).forEach(campo => {
                        if (valoresBrutos[campo] !== undefined) {
                            let valor = valoresBrutos[campo];
                            
                            // Aplicar validação apenas para diferença cambial
                            if (campo === 'diferenca_cambial_frete') {
                                valor = validarDiferencaCambialFrete(valor);
                            }
                            
                            totais[campo] += valor;
                        }
                    });
                } else {
                    // Fallback: usar valores dos inputs (comportamento original)
                    const isSantaCatarina = getNacionalizacaoAtual() === 'santa_catarina';
                    Object.keys(totais).forEach(campo => {
                        // Para Santa Catarina, calcular multa_complem e dif_impostos da tabela de multa
                        if (isSantaCatarina && campo === 'multa_complem') {
                            totais[campo] += obterMultaComplementarPorAdicaoItemProduto(rowId);
                            return;
                        }
                        if (isSantaCatarina && campo === 'dif_impostos') {
                            totais[campo] += obterDiferencaImpostosPorAdicaoItemProduto(rowId);
                            return;
                        }
                        
                        if (campo === 'service_charges' && moedaServiceCharges && moedaServiceCharges !== 'USD') {
                            const elemento = $(`#service_charges_moeda_estrangeira-${rowId}`);
                            if (elemento.length > 0) {
                                const valor = MoneyUtils.parseMoney(elemento.val()) || 0;
                                totais[campo] += valor;
                            }
                        } else {
                            if (window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos[campo] && 
                                window.valoresBrutosCamposExternos[campo][rowId] !== undefined) {
                                let valor = window.valoresBrutosCamposExternos[campo][rowId] || 0;
                                if (campo === 'diferenca_cambial_frete') {
                                    valor = validarDiferencaCambialFrete(valor);
                                }
                                totais[campo] += valor;
                            } else {
                                const elemento = $(`#${campo}-${rowId}`);
                                if (elemento.length > 0) {
                                    let valor = MoneyUtils.parseMoney(elemento.val()) || 0;
                                    if (campo === 'diferenca_cambial_frete') {
                                        valor = validarDiferencaCambialFrete(valor);
                                    }
                                    totais[campo] += valor;
                                }
                            }
                        }
                    });
                }

                fatorPesoSum += MoneyUtils.parseMoney($(`#fator_peso-${rowId}`).val()) || 0;
                fatorValorFobSum += MoneyUtils.parseMoney($(`#fator_valor_fob-${rowId}`).val()) || 0;
                fatorTxSiscomexSum += MoneyUtils.parseMoney($(`#fator_tx_siscomex-${rowId}`).val()) || 0;
            });
            

            atualizarPesoLiquidoTotal(totais.peso_liquido_total);

            // tfoot já foi obtido no início da função, garantir que está vazio antes de adicionar
            if (tfoot.length > 0) {
                tfoot.empty();
            } else {
                return;
            }
            
            let tr = '<tr><td colspan="7" style="text-align: right; font-weight: bold;">TOTAIS:</td>';


            tr += `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.quantidade, 2)}</td>`;


            tr += '<td></td>';


            tr +=
                `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.peso_liquido_total, 2)}</td>`;


            tr +=
                `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(fatorPesoSum, 2)}</td>`;

            tr += '<td></td>';
            const moedaProcesso = $('#moeda_processo').val();
            if (moedaProcesso && moedaProcesso !== 'USD') {
                let totalFobMoeda = 0;
                rows.each(function() {
                    const rowId = this.id.replace('row-', '')

                    totalFobMoeda += MoneyUtils.parseMoney($(`#fob_total_moeda_estrangeira-${rowId}`).val()) || 0;
                });
                tr += `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totalFobMoeda, 2)}</td>`;
            }

            tr +=
                `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.fob_total_usd, 2)}</td>`;
            tr +=
                `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.fob_total_brl, 2)}</td>`;


            const moedaFrete = $('#frete_internacional_moeda').val();
            if (moedaFrete && moedaFrete !== 'USD') {
                let totalFreteMoeda = 0;
                rows.each(function() {
                    const rowId = this.id.replace('row-', '')


                    totalFreteMoeda += MoneyUtils.parseMoney($(`#frete_moeda_estrangeira-${rowId}`).val()) || 0;
                });
                tr +=
                    `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totalFreteMoeda, 2)}</td>`;
            }

            tr += `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.frete_usd, 2)}</td>`;
            tr += `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.frete_brl, 2)}</td>`;


            tr += `<td></td>`; 
            tr += `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.vlr_crf_total, 2)}</td>`;
            

            if (moedaServiceCharges && moedaServiceCharges !== 'USD') {
                tr += `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.service_charges, 2)}</td>`;
            }
            

            let totalServiceChargesUSD = 0;
            if (moedaServiceCharges && moedaServiceCharges !== 'USD') {
                rows.each(function() {
                    const rowId = this.id.replace('row-', '');
                    totalServiceChargesUSD += MoneyUtils.parseMoney($(`#service_charges-${rowId}`).val()) || 0;
                });
            } else {
                totalServiceChargesUSD = totais.service_charges;
            }
            
            tr += `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totalServiceChargesUSD, 2)}</td>`;
            tr += `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.service_charges_brl, 2)}</td>`;


            const moedaAcrescimo = $('#acrescimo_frete_moeda').val();
            if (moedaAcrescimo && moedaAcrescimo !== 'USD') {
                let totalAcrescimoMoeda = 0;
                rows.each(function() {
                    const rowId = this.id.replace('row-', '')
                    totalAcrescimoMoeda += MoneyUtils.parseMoney($(`#acrescimo_moeda_estrangeira-${rowId}`)
                        .val()) || 0;
                });
                tr +=
                    `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totalAcrescimoMoeda, 2)}</td>`;
            }

            tr +=
                `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.acresc_frete_usd, 2)}</td>`;
            tr +=
                `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.acresc_frete_brl, 2)}</td>`;


            const moedaSeguro = $('#seguro_internacional_moeda').val();
            if (moedaSeguro && moedaSeguro !== 'USD') {
                let totalSeguroMoeda = 0;
                rows.each(function() {
                    const rowId = this.id.replace('row-', '')
                    totalSeguroMoeda += MoneyUtils.parseMoney($(`#seguro_moeda_estrangeira-${rowId}`).val()) || 0;
                });
                tr +=
                    `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totalSeguroMoeda, 2)}</td>`;
            }

            tr += `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.seguro_usd, 2)}</td>`;
            tr += `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.seguro_brl, 2)}</td>`;


            tr += `
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.thc_usd, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.thc_brl, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.valor_aduaneiro_usd, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.valor_aduaneiro_brl, 2)}</td>
        <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.valor_ii, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.base_ipi, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.valor_ipi, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.base_pis_cofins, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.valor_pis, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.valor_cofins, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.despesa_aduaneira, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.base_icms_sem_reducao, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.valor_icms_sem_reducao, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.base_icms_reduzido, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.valor_icms_reduzido, 2)}</td>
        <td></td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.valor_total_nf, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.valor_total_nf_sem_icms_st, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.base_icms_st, 2)}</td>
        <td></td><td></td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.valor_icms_st, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.valor_total_nf_com_icms_st, 2)}</td>
        <td style="font-weight: bold; text-align: right;">1.000000</td>
        <td></td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.multa, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.tx_def_li, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.taxa_siscomex, 2)}</td>
        ${getNacionalizacaoAtual() === 'santa_catarina' ? 
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.multa_complem || 0, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.dif_impostos || 0, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.outras_taxas_agente, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.liberacao_bl, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.desconsolidacao, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.isps_code, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.handling, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.capatazia, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.afrmm, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.armazenagem_porto || 0, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.frete_rodoviario || 0, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.dif_frete_rodoviario || 0, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.sda, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.rep_porto || 0, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.tx_correcao_lacre, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.li_dta_honor_nix, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.honorarios_nix, 2) + '</td>'
        : getNacionalizacaoAtual() === 'santos' ?
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.outras_taxas_agente, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.liberacao_bl, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.desconsolidacao, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.isps_code, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.handling, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.capatazia, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.afrmm, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.armazenagem_sts, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.frete_dta_sts_ana, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.sda, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.rep_sts, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.tx_correcao_lacre, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.li_dta_honor_nix, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.honorarios_nix, 2) + '</td>'
        : getNacionalizacaoAtual() === 'anapolis' ?
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.outras_taxas_agente, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.liberacao_bl, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.desconsolidacao, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.isps_code, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.handling, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.capatazia, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.afrmm, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.armazenagem_sts, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.frete_dta_sts_ana, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.sda, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.rep_sts, 2) + '</td>' +
            '<td data-campo="desp_anapolis" style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.desp_anapolis || 0, 2) + '</td>' +
            '<td data-campo="rep_anapolis" style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.rep_anapolis, 2) + '</td>' +
            '<td data-campo="correios" style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.correios, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.li_dta_honor_nix, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.honorarios_nix, 2) + '</td>'
        : getNacionalizacaoAtual() === 'santos' ?
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.outras_taxas_agente, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.liberacao_bl, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.desconsolidacao, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.isps_code, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.handling, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.capatazia, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.afrmm, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.armazenagem_sts, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.frete_dta_sts_ana, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.sda, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.rep_sts, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.tx_correcao_lacre, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.li_dta_honor_nix, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.honorarios_nix, 2) + '</td>'
        : getNacionalizacaoAtual() === 'anapolis' ?
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.outras_taxas_agente, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.liberacao_bl, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.desconsolidacao, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.isps_code, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.handling, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.capatazia, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.afrmm, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.armazenagem_sts, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.frete_dta_sts_ana, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.sda, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.rep_sts, 2) + '</td>' +
            '<td data-campo="desp_anapolis" style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.desp_anapolis || 0, 2) + '</td>' +
            '<td data-campo="rep_anapolis" style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.rep_anapolis, 2) + '</td>' +
            '<td data-campo="correios" style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.correios, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.li_dta_honor_nix, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.honorarios_nix, 2) + '</td>'
        : getNacionalizacaoAtual() === 'mato_grosso' ?
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.outras_taxas_agente, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.liberacao_bl, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.desconsolidacao, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.isps_code, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.handling, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.capatazia, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.afrmm, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.armazenagem_sts, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.frete_sts_cgb || 0, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.diarias || 0, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.sda, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.rep_sts, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.armaz_cgb || 0, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.rep_cgb || 0, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.demurrage || 0, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.li_dta_honor_nix, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.honorarios_nix, 2) + '</td>'
        :
            // Ordem padrão para outras nacionalizações
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.outras_taxas_agente, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.liberacao_bl, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.desconsolidacao, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.isps_code, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.handling, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.capatazia, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.afrmm, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.armazenagem_sts, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.frete_dta_sts_ana, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.sda, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.rep_sts, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.armaz_ana, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.lavagem_container, 2) + '</td>' +
            '<td data-campo="rep_anapolis" style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.rep_anapolis, 2) + '</td>' +
            '<td data-campo="desp_anapolis" style="display: none; font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.desp_anapolis || 0, 2) + '</td>' +
            '<td data-campo="correios" style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.correios, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.li_dta_honor_nix, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.honorarios_nix, 2) + '</td>'
        }
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.desp_desenbaraco, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(validarDiferencaCambialFrete(totais.diferenca_cambial_frete), 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.diferenca_cambial_fob, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.opcional_1_valor || 0, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.opcional_2_valor || 0, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.custo_unitario_final, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.custo_total_final, 2)}</td>
        ${getNacionalizacaoAtual() === 'mato_grosso' ? 
            '<td></td>' + // DEZ POR CENTO (sem totalizador)
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.custo_com_margem || 0, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.vlr_ipi_mg || 0, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.vlr_icms_mg || 0, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.pis_mg || 0, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.cofins_mg || 0, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.custo_total_final_credito || 0, 2) + '</td>' +
            '<td></td>' + // CUSTO UNIT CREDITO (sem totalizador)
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.bc_icms_st_mg || 0, 2) + '</td>' +
            '<td></td>' + // MVA (sem totalizador)
            '<td></td>' + // ICMS-ST (sem totalizador)
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.vlr_icms_st_mg || 0, 2) + '</td>' +
            '<td></td>' + // CUSTO TOTAL C/ICMS ST (sem totalizador)
            '<td style="font-weight: bold; text-align: right;">' + (totais.quantidade > 0 ? MoneyUtils.formatMoney(totais.custo_total_c_icms_st / totais.quantidade, 2) : '0,00') + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.exportador_mg || 0, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.tributos_mg || 0, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.despesas_mg || 0, 2) + '</td>' +
            '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.total_pago_mg || 0, 2) + '</td>' +
            '<td></td>' // PERCENTUAL S/FOB (sem totalizador)
            : ''
        }
    </tr>`;

            try {
                if (tfoot.length === 0) {
                    return;
                }
                // tfoot já foi esvaziado anteriormente, apenas adicionar o conteúdo
                tfoot.append(tr);
            } catch (error) {
                // Erro silencioso ao adicionar totalizador
            }
        }

        function getCotacaoesProcesso() {
            let cotacaoProcesso = '';
            if (!$('#cotacao_moeda_processo').val()) {
                $('#cotacao_moeda_processo').val($('#dolarHoje').val())
                cotacaoProcesso = JSON.parse($('#dolarHoje').val())
            } else {
                cotacaoProcesso = JSON.parse($('#cotacao_moeda_processo').val())
            }
            return cotacaoProcesso;
        }

        function atualizarVisibilidadeColunasMoeda() {
            let moedaFrete = $('#frete_internacional_moeda').val();
            let moedaSeguro = $('#seguro_internacional_moeda').val();
            let moedaAcrescimo = $('#acrescimo_frete_moeda').val();
            let moedaProcesso = $('#moeda_processo').val();



            atualizarTitulosColunas(moedaFrete, moedaSeguro, moedaAcrescimo, moedaProcesso);


            if (moedaProcesso && moedaProcesso !== 'USD') {

                $('[id*="fob_unit_moeda_estrangeira-"]').closest('td').show();
                $('[id*="fob_total_moeda_estrangeira-"]').closest('td').show();


                $('[id*="fob_unit_usd-"]').closest('td').hide();


                $('th').each(function() {
                    let texto = $(this).text();
                    if (texto.includes('FOB UNIT')) {
                        $(this).text('FOB UNIT ' + moedaProcesso);
                    }
                    if (texto.includes('FOB TOTAL') && !texto.includes('USD') && !texto.includes('BRL')) {
                        $(this).text('FOB TOTAL ' + moedaProcesso);
                    }
                });
            } else {

                $('[id*="fob_unit_usd-"]').closest('td').show();


                $('[id*="fob_unit_moeda_estrangeira-"]').closest('td').hide();
                $('[id*="fob_total_moeda_estrangeira-"]').closest('td').hide();


                $('th').each(function() {
                    let texto = $(this).text();
                    if (texto.includes('FOB UNIT')) {
                        $(this).text('FOB UNIT USD');
                    }
                    if (texto.includes('FOB TOTAL') && !texto.includes('USD') && !texto.includes('BRL')) {
                        $(this).text('FOB TOTAL USD');
                    }
                });
            }


            if (moedaFrete && moedaFrete !== 'USD') {
                $('[id*="frete_moeda_estrangeira-"]').closest('td').show();
                $('th').each(function() {
                    let texto = $(this).text();
                    if (texto.includes('FRETE INT.') && !texto.includes('USD') && !texto.includes('BRL')) {
                        $(this).text('FRETE INT.' + moedaFrete).show();
                    }
                });
            } else {
                $('[id*="frete_moeda_estrangeira-"]').closest('td').hide();
            }


            if (moedaSeguro && moedaSeguro !== 'USD') {
                $('[id*="seguro_moeda_estrangeira-"]').closest('td').show();
                $('th').each(function() {
                    let texto = $(this).text();
                    if (texto.includes('SEGURO INT.') && !texto.includes('USD') && !texto.includes('BRL')) {
                        $(this).text('SEGURO INT.' + moedaSeguro).show();
                    }
                });
            } else {
                $('[id*="seguro_moeda_estrangeira-"]').closest('td').hide();
            }


            if (moedaAcrescimo && moedaAcrescimo !== 'USD') {
                $('[id*="acrescimo_moeda_estrangeira-"]').closest('td').show();
                $('th').each(function() {
                    let texto = $(this).text();
                    if (texto.includes('ACRESC. FRETE') && !texto.includes('USD') && !texto.includes('BRL')) {
                        $(this).text('ACRESC. FRETE ' + moedaAcrescimo).show();
                    }
                });
            } else {
                $('[id*="acrescimo_moeda_estrangeira-"]').closest('td').hide();
            }
            

            const moedaServiceCharges = $('#service_charges_moeda').val();
            if (moedaServiceCharges && moedaServiceCharges !== 'USD') {
                $('[id*="service_charges_moeda_estrangeira-"]').closest('td').show();
                $('th').each(function() {
                    let texto = $(this).text();
                    if (texto.includes('SERVICE CHARGES') && !texto.includes('USD') && !texto.includes('R$')) {
                        $(this).text('SERVICE CHARGES ' + moedaServiceCharges).show();
                    }
                });
            } else {
                $('[id*="service_charges_moeda_estrangeira-"]').closest('td').hide();
            }
        }

        function converterMoedaProcessoParaUSD(valor, moedaProcesso) {
            if (!moedaProcesso || moedaProcesso === 'USD') {
                return valor;
            }

            let cotacaoProcesso = getCotacaoesProcesso();
            let cotacaoMoeda = cotacaoProcesso[moedaProcesso]?.venda ?? 0;
            let cotacaoUSD = cotacaoProcesso['USD']?.venda ?? 1;


            if (!cotacaoMoeda || cotacaoMoeda === 0 || !cotacaoUSD || cotacaoUSD === 0) {
                return valor; 
            }

            return valor * (cotacaoMoeda / cotacaoUSD);
        }

        function converterUSDParaMoedaProcesso(valor, moedaProcesso) {
            if (!moedaProcesso || moedaProcesso === 'USD') {
                return valor;
            }

            let cotacaoProcesso = getCotacaoesProcesso();
            let cotacaoMoeda = cotacaoProcesso[moedaProcesso]?.venda ?? 0;
            let cotacaoUSD = cotacaoProcesso['USD']?.venda ?? 1;


            if (!cotacaoMoeda || cotacaoMoeda === 0 || !cotacaoUSD || cotacaoUSD === 0) {
                return valor; 
            }

            return valor * (cotacaoUSD / cotacaoMoeda);
        }

        function obterValorProcessoUSD(valorSelector, moedaSelector, cotacaoSelector) {
            const total = MoneyUtils.parseMoney($(valorSelector).val()) || 0;
            const moeda = $(moedaSelector).val();
            if (!moeda || moeda === 'USD') {
                return total;
            }

            let cotacaoProcesso = getCotacaoesProcesso();
            let cotacaoMoeda = MoneyUtils.parseMoney($(cotacaoSelector).val());
            let cotacaoUSD = cotacaoProcesso['USD']?.venda ?? 1;

            if (!cotacaoMoeda && cotacaoProcesso[moeda]) {
                cotacaoMoeda = cotacaoProcesso[moeda].venda;
            }

            if (!cotacaoMoeda || cotacaoMoeda === 0 || !cotacaoUSD || cotacaoUSD === 0) {
                return total;
            }

            const moedaEmUSD = cotacaoMoeda / cotacaoUSD;
            return total * moedaEmUSD;
        }

        function atualizarCamposFOB(rowId, valores) {
            let moedaProcesso = $('#moeda_processo').val();
            let quantidade = MoneyUtils.parseMoney($(`#quantidade-${rowId}`).val()) || 0;

            if (isNaN(valores.fobUnitario)) valores.fobUnitario = 0;
            if (isNaN(valores.fobTotal)) valores.fobTotal = 0;
            if (isNaN(valores.dolar)) valores.dolar = 1;


            let fobTotalUSD = valores.fobTotal;
            let fobTotalBRL = valores.fobTotal * valores.dolar;

            if (moedaProcesso && moedaProcesso !== 'USD') {

                let fobTotalMoedaEstrangeira = valores.fobUnitarioMoedaEstrangeira * quantidade;


                $(`#fob_total_moeda_estrangeira-${rowId}`).val(MoneyUtils.formatMoney(fobTotalMoedaEstrangeira, 7));


            } else {

                const $campoFobUsd = $(`#fob_unit_usd-${rowId}`);

                if (!$campoFobUsd.is(':focus')) {
                    $campoFobUsd.val(MoneyUtils.formatMoney(valores.fobUnitario, 7));
                }
            }


            $(`#fob_total_usd-${rowId}`).val(MoneyUtils.formatMoney(fobTotalUSD, 7));
            $(`#fob_total_brl-${rowId}`).val(MoneyUtils.formatMoney(fobTotalBRL, 7));
        }

        function atualizarTitulosColunas(moedaFrete, moedaSeguro, moedaAcrescimo, moedaProcesso) {

            $('th').each(function() {
                let texto = $(this).text();

                if (texto.includes('FRETE INT.') && !texto.includes('USD') && !texto.includes('R$')) {
                    if (moedaFrete && moedaFrete !== 'USD') {
                        $(this).text(`FRETE INT.${moedaFrete}`).show();
                    } else {
                        $(this).hide();
                    }
                }
                if (texto.includes('SEGURO INT.') && !texto.includes('USD') && !texto.includes('R$')) {
                    if (moedaSeguro && moedaSeguro !== 'USD') {
                        $(this).text(`SEGURO INT.${moedaSeguro}`).show();
                    } else {
                        $(this).hide();
                    }
                }
                if (texto.includes('ACRESC. FRETE') && !texto.includes('USD') && !texto.includes('R$')) {
                    if (moedaAcrescimo && moedaAcrescimo !== 'USD') {
                        $(this).text(`ACRESC. FRETE ${moedaAcrescimo}`).show();
                    } else {
                        $(this).hide();
                    }
                }

                if (texto.includes('FOB UNIT') && !texto.includes('USD') && !texto.includes('R$')) {
                    if (moedaProcesso && moedaProcesso !== 'USD') {
                        $(this).text(`FOB UNIT ${moedaProcesso}`).show();
                    } else {
                        $(this).hide();
                    }
                }
                if (texto.includes('FOB TOTAL') && !texto.includes('USD') && !texto.includes('R$')) {
                    if (moedaProcesso && moedaProcesso !== 'USD') {
                        $(this).text(`FOB TOTAL ${moedaProcesso}`).show();
                    } else {
                        $(this).hide();
                    }
                }
            });
        }

        $(document).ready(function() {
            reordenarLinhas();

            $('#frete_internacional_moeda, #seguro_internacional_moeda, #acrescimo_frete_moeda, #service_charges_moeda, #moeda_processo')
                .on('change', function() {
                    setTimeout(atualizarVisibilidadeColunasMoeda, 100);
                });
            $('.moneyReal').on('blur', function() {
                // Não formatar se for cabeçalho input (já tem formatação específica)
                if ($(this).hasClass('cabecalhoInputs')) {
                    return;
                }
                const val = $(this).val();
                if (val && val.trim() !== '') {
                    const numero = normalizeNumericValue(val);
                    $(this).val(formatTruncatedNumber(numero, 5));
                } else {
                    $(this).val('');
                }
            });

            $('.moneyReal2').on('blur', function() {
                const val = $(this).val();
                if (val && val.trim() !== '') {
                    const numero = normalizeNumericValue(val);
                    $(this).val(formatTruncatedNumber(numero, 2));
                } else {
                    $(this).val('');
                }
            });
            $('.cotacao').on('blur', function() {
                const val = $(this).val();
                if (val && val.trim() !== '') {
                    const numero = normalizeNumericValue(val);
                    $(this).val(formatTruncatedNumber(numero, 4));
                } else {
                    $(this).val('');
                }
            });


            $('.moneyReal7').on('blur', function() {
                // Não formatar se for cabeçalho input (já tem formatação específica)
                if ($(this).hasClass('cabecalhoInputs')) {
                    return;
                }
                const val = $(this).val();
                if (val && val.trim() !== '') {
                    const numero = normalizeNumericValue(val);
                    $(this).val(formatTruncatedNumber(numero, 7));
                } else {
                    $(this).val('');
                }
            });


            $('.moneyReal8').on('blur', function() {
                const val = $(this).val();
                if (val && val.trim() !== '') {
                    const numero = normalizeNumericValue(val);
                    $(this).val(formatTruncatedNumber(numero, 8));
                } else {
                    $(this).val('');
                }
            });


            $('.percentage').on('blur', function() {
                const val = $(this).val();
                if (val && val.trim() !== '') {
                    const numero = normalizeNumericValue(val.replace('%', ''));
                    $(this).val(`${formatTruncatedNumber(numero, 7)} %`);
                } else {
                    $(this).val('');
                }
            });
            $('.percentage2').on('blur', function() {
                const val = $(this).val();
                if (val && val.trim() !== '') {
                    const numero = normalizeNumericValue(val.replace('%', ''));
                    $(this).val(`${formatTruncatedNumber(numero, 2)} %`);
                } else {
                    $(this).val('');
                }
            });
            if (useProductsAjax) {
                $('.select2').not('.selectProduct, .selectProductMulta').select2({
                    width: '100%'
                });
                initProductSelectAjax($('.selectProduct'));
                initProductSelectAjax($('.selectProductMulta'));
            } else {
            $('.select2').select2({
                width: '100%'
            });
            }


            $('#service_charges').on('blur change input', function() {

                convertToUSDAndBRL('service_charges');
                agendarRecalculo(150);
            });

            $('form').on('submit', function(e) {

                const pesoLiquidoTotal = calcularPesoTotal();
                $('#peso_liquido').val(MoneyUtils.formatMoney(pesoLiquidoTotal, 4));
                
                $('.percentage').each(function() {
                    let originalValue = $(this).val();
                    let unformattedValue = originalValue
                        .replace(/\./g, '')
                        .replace(',', '.')
                        .replace('%', '');
                    $(this).val(unformattedValue);
                });
            });

            $('#recalcularTabela').on('click', function() {

                const btn = $(this);
                const originalText = btn.html();
                btn.html('<i class="fas fa-spinner fa-spin"></i> Calculando...').prop('disabled', true);


                setTimeout(function() {
                    try {
                        recalcularTodaTabela();
                        Toast.fire({
                            icon: 'success',
                            title: 'Tabela recalculada com sucesso!'
                        });
                    } catch (error) {
                        Toast.fire({
                            icon: 'error',
                            title: 'Erro ao recalcular tabela'
                        });
                    } finally {

                        btn.html(originalText).prop('disabled', false);
                    }
                }, 100);
            });


            

            if (window.valoresBrutosCamposExternos === undefined) {
                window.valoresBrutosCamposExternos = {};
            }
            const camposExternos = getCamposExternos();
            const lengthTable = $('.linhas-input').length;
            camposExternos.forEach(campo => {
                if (!window.valoresBrutosCamposExternos[campo]) {
                    window.valoresBrutosCamposExternos[campo] = [];
                }
                for (let i = 0; i < lengthTable; i++) {
                    const valor = MoneyUtils.parseMoney($(`#${campo}-${i}`).val()) || 0;
                    window.valoresBrutosCamposExternos[campo][i] = valor;
              
                }
            });


        });


        function getCurrencySymbol(codigoMoeda) {
            const symbols = {
                'USD': '$',
                'EUR': '€',
                'BRL': 'R$',
                'GBP': '£',
                'JPY': '¥',
                'CNY': '¥',
                'CHF': 'CHF',
                'CAD': 'C$',
                'AUD': 'A$',
                'ARS': '$',
                'MXN': '$',
                'INR': '₹',
                'RUB': '₽',
                'ZAR': 'R',
                'TRY': '₺',
                'BRL': 'R$'
            };
            return symbols[codigoMoeda] || codigoMoeda;
        }


        function updateCurrencySymbol(inputId) {
            const codigoMoeda = $(`#${inputId}_moeda`).val();
            const symbol = getCurrencySymbol(codigoMoeda);
            $(`#${inputId}_symbol`).text(symbol || '-');
        }


        function convertToUSDAndBRL(inputId) {
            const cotacoes = getCotacaoesProcesso();
            const valor = MoneyUtils.parseMoney($(`#${inputId}`).val()) || 0;
            const codigoMoeda = $(`#${inputId}_moeda`).val();
            

            let cotacaoMoeda = MoneyUtils.parseMoney($(`#cotacao_${inputId}`).val());
            
            if (!cotacaoMoeda || cotacaoMoeda === 0) {
                cotacaoMoeda = (cotacoes && cotacoes[codigoMoeda]) ? cotacoes[codigoMoeda].venda : 0;
            }

            if (!codigoMoeda) {
                $(`#${inputId}_usd`).val('');
                $(`#${inputId}_brl`).val('');
                return;
            }

            if (valor === 0 || !valor) {
                $(`#${inputId}_usd`).val('0,0000000');
                $(`#${inputId}_brl`).val('0,0000000');
                return;
            }

            let valorUSD = 0;
            let valorBRL = 0;
            const cotacaoUSD = (cotacoes && cotacoes['USD']) ? cotacoes['USD'].venda : 1;


            if (codigoMoeda === 'USD') {
                valorUSD = valor;
                valorBRL = valor * cotacaoUSD;
            } else {

                if (!cotacaoMoeda || cotacaoMoeda === 0) {
                    $(`#${inputId}_usd`).val('');
                    $(`#${inputId}_brl`).val('');
                    return;
                }


                valorBRL = valor * cotacaoMoeda;
                

                if (cotacaoUSD > 0) {
                    valorUSD = valorBRL / cotacaoUSD;
                }
            }


            const usdField = $(`#${inputId}_usd`);
            const brlField = $(`#${inputId}_brl`);
            

            if (usdField.length) {
                usdField.val(MoneyUtils.formatMoney(valorUSD, 2));
            }
            
            if (brlField.length) {
                brlField.val(MoneyUtils.formatMoney(valorBRL, 2));
            }
            
            // Recalcular valores CPT quando os valores USD mudarem
            calcularValoresCPT();
            calcularValoresCIF();
        }

        function updateValorReal(inputId, spanId, automatic = true) {

            convertToUSDAndBRL(inputId);
        };

        function updateValorCotacao(inputId, spanId) {
            let dolar = getCotacaoesProcesso();

            let valor = MoneyUtils.parseMoney($(`#${inputId}`).val());
            let codigoMoeda = $(`#${inputId}_moeda`).val();
            let nome = $(`#${inputId}_moeda option:selected`).text();

            if ($(`#description_moeda_${inputId}`).length) {
                $(`#description_moeda_${inputId}`).text(`Taxa: ${nome}`);
            }

            if (codigoMoeda && dolar && dolar[codigoMoeda]) {
                let convertido = dolar[codigoMoeda].venda;
                $(`#${spanId}`).val(MoneyUtils.formatMoney(convertido, 4));
                



                if (dolar[codigoMoeda].data) {
                    try {
                        const data = new Date(dolar[codigoMoeda].data);
                        if (!isNaN(data.getTime())) {
                            const formatada = data.getFullYear() + '-' +
                                String(data.getMonth() + 1).padStart(2, '0') + '-' +
                                String(data.getDate()).padStart(2, '0');
                            $(`#data_moeda_${inputId}`).val(formatada);
                        }
                    } catch (e) {

                    }
                }
            } else {

                $(`#${spanId}`).val('');
                $(`#data_moeda_${inputId}`).val('');
            }
        };
        const initialInputs = new Set(
            Array.from(document.querySelectorAll('#productsBody input, #productsBody select, #productsBody textarea'))
        );

        setTimeout(function() {
            $(document).on('change select2:select', '.selectProduct', function(e) {
                const rowId = $(this).data('row');
                let productObject = null;
                if (useProductsAjax && e?.params?.data) {
                    productObject = e.params.data;
                } else {
                    const productsList = Array.isArray(products) ? products : [];
                    productObject = productsList.find(el => String(el.id) === String(this.value));
                }

                if (productObject) {
                    $(`#codigo-${rowId}`).val(productObject.codigo || '');
                    $(`#ncm-${rowId}`).val(productObject.ncm || '');
                    $(`#descricao-${rowId}`).val(productObject.descricao || '');
                    debouncedRecalcular();
                }
            });
            $(document).on('keyup',
                '#productsBody input, #productsBody select, #productsBody textarea, #productsBody .form-control',
                function(e) {
                    if (!initialInputs.has(e.target)) {
                        return; 
                    }
                    $('#avisoProcessoAlterado').removeClass('d-none');
                    const rowId = $(this).data('row');
                    const nome = $(this).attr('name');
                    const camposExternos = getCamposExternos();

                    if (camposExternos.includes(nome) || rowId != null) {
                        debouncedRecalcular();
                    }

                });




            $('.moedas').on('select2:select', function(e) {
                const inputId = this.id.replace('_moeda', '');
                updateValorReal('frete_internacional', 'frete_internacional_visualizacao');
                updateValorReal('seguro_internacional', 'seguro_internacional_visualizacao');
                updateValorReal('acrescimo_frete', 'acrescimo_frete_visualizacao');
                updateValorReal('service_charges', 'service_charges_visualizacao');
                updateValorCotacao('frete_internacional', 'cotacao_frete_internacional');
                updateValorCotacao('seguro_internacional', 'cotacao_seguro_internacional');
                updateValorCotacao('acrescimo_frete', 'cotacao_acrescimo_frete');
                updateValorCotacao('service_charges', 'cotacao_service_charges');
                

                setTimeout(function() {
                    convertToUSDAndBRL('frete_internacional');
                    convertToUSDAndBRL('seguro_internacional');
                    convertToUSDAndBRL('acrescimo_frete');
                    convertToUSDAndBRL('service_charges');
                }, 100);
            });
            

            $('#service_charges_moeda').on('select2:select', function(e) {
                const moeda = $(this).val();
                updateCurrencySymbol('service_charges');

                updateValorCotacao('service_charges', 'cotacao_service_charges');

                setTimeout(function() {
                    convertToUSDAndBRL('service_charges');
                    atualizarVisibilidadeColunasMoeda();
                    agendarRecalculo(200);
                }, 200);
            });
            

            $('#cotacao_service_charges').on('blur change input', function() {
                setTimeout(function() {
                    convertToUSDAndBRL('service_charges');
                    agendarRecalculo(150);
                }, 50);
            });


            $('#moeda_processo').on('select2:select', function(e) {

                let cotacaoProcesso = getCotacaoesProcesso();
                let moeda = e.params.data.id;

                if (!cotacaoProcesso[moeda]) {
                    cotacaoProcesso[moeda] = {
                        nome: moeda,
                        moeda: moeda,
                        compra: 0,
                        venda: 0,
                        data: null
                    };
                }

                let cotacaoMoeda = cotacaoProcesso[moeda]?.venda ?? 0;
                let cotacaoUSD = cotacaoProcesso['USD']?.venda ?? 1;


                $('#display_cotacao').val(MoneyUtils.formatMoney(cotacaoMoeda, 4));

                if (moeda && moeda !== 'USD') {

                    let moedaEmUSD = cotacaoMoeda / cotacaoUSD;
                    $('#moeda_processo_usd').val(MoneyUtils.formatMoney(moedaEmUSD, 4));
                    $('#visualizacaoMoedaDolar').removeClass('d-none').addClass('col-12');


                    cotacaoProcesso[`${moeda}_USD`] = {
                        nome: `${cotacaoProcesso[moeda].nome} em USD`,
                        data: cotacaoProcesso[moeda].data || (() => {
                            let hoje = new Date();
                            return `${String(hoje.getMonth()+1).padStart(2,'0')}/${String(hoje.getDate()).padStart(2,'0')}/${hoje.getFullYear()}`;
                        })(),
                        moeda: `${moeda}_USD`,
                        compra: moedaEmUSD,
                        venda: moedaEmUSD
                    };
                } else {
                    $('#visualizacaoMoedaDolar').addClass('d-none').removeClass('col-12');
                    $('#moeda_processo_usd').val('');
                }


                $('#cotacao_moeda_processo').val(JSON.stringify(cotacaoProcesso));


                setTimeout(atualizarVisibilidadeColunasMoeda, 100);


                agendarRecalculo(200);

                $('#formProcesso').submit();

            });


            $('#display_cotacao').on('change', function() {
                let cotacaoProcesso = getCotacaoesProcesso();
                let cotacaoMoedaFloat = MoneyUtils.parseMoney(this.value);
                let moeda = $('#moeda_processo').val();
                let data = $('#data_cotacao_processo').val();


                if (!cotacaoProcesso[moeda]) {
                    cotacaoProcesso[moeda] = {
                        nome: moeda,
                        moeda: moeda,
                        compra: cotacaoMoedaFloat,
                        venda: cotacaoMoedaFloat,
                        data: null
                    };
                } else {
                    cotacaoProcesso[moeda].venda = cotacaoMoedaFloat;
                    cotacaoProcesso[moeda].compra = cotacaoMoedaFloat;
                }


                if (data) {
                    let [dia, mes, ano] = data.split('/');
                    cotacaoProcesso[moeda].data = `${mes}/${dia}/${ano}`;
                } else if (!cotacaoProcesso[moeda].data) {
                    let hoje = new Date();
                    cotacaoProcesso[moeda].data =
                        `${String(hoje.getMonth()+1).padStart(2,'0')}/${String(hoje.getDate()).padStart(2,'0')}/${hoje.getFullYear()}`;
                }
                
                // Recalcular valores CPT quando a cotação mudar
                calcularValoresCPT();
                calcularValoresCIF();


                if (moeda && moeda !== 'USD') {
                    let cotacaoUSD = cotacaoProcesso['USD']?.venda ?? 1;

                    let moedaEmUSD = cotacaoMoedaFloat / cotacaoUSD;

                    $('#moeda_processo_usd').val(MoneyUtils.formatMoney(moedaEmUSD, 4));
                    $('#visualizacaoMoedaDolar').removeClass('d-none').addClass('col-12');
                    cotacaoProcesso[`${moeda}_USD`] = {
                        nome: `${cotacaoProcesso[moeda].nome} em USD`,
                        data: cotacaoProcesso[moeda].data,
                        moeda: `${moeda}_USD`,
                        compra: moedaEmUSD,
                        venda: moedaEmUSD
                    };
                } else {
                    $('#visualizacaoMoedaDolar').addClass('d-none').removeClass('col-12');
                    $('#moeda_processo_usd').val('');
                }


                $('#cotacao_moeda_processo').val(JSON.stringify(cotacaoProcesso));
            });


            $(document).on('change', '#frete_internacional_moeda, #seguro_internacional_moeda, #acrescimo_frete_moeda, #service_charges_moeda', function() {
                const inputId = this.id.replace('_moeda', '');
                updateCurrencySymbol(inputId);
                updateValorCotacao(inputId, `cotacao_${inputId}`);

                setTimeout(function() {
                    convertToUSDAndBRL(inputId);
                }, 150);
            });


            $('#frete_internacional, #seguro_internacional, #acrescimo_frete, #service_charges').trigger('change');
            $(document).on('change', '#frete_internacional, #seguro_internacional, #acrescimo_frete, #service_charges', function() {
                const inputId = this.id;
                convertToUSDAndBRL(inputId);
            });


            $(document).on('change', '#cotacao_frete_internacional, #cotacao_seguro_internacional, #cotacao_acrescimo_frete, #cotacao_service_charges', function() {
                let inputId = '';
                if (this.id === 'cotacao_frete_internacional') {
                    inputId = 'frete_internacional';
                } else if (this.id === 'cotacao_seguro_internacional') {
                    inputId = 'seguro_internacional';
                } else if (this.id === 'cotacao_acrescimo_frete') {
                    inputId = 'acrescimo_frete';
                } else if (this.id === 'cotacao_service_charges') {
                    inputId = 'service_charges';
                }
                if (inputId) {
                    convertToUSDAndBRL(inputId);
                }
            });


            setTimeout(function() {
                updateCurrencySymbol('frete_internacional');
                updateCurrencySymbol('seguro_internacional');
                updateCurrencySymbol('acrescimo_frete');
                updateCurrencySymbol('service_charges');
                updateValorCotacao('frete_internacional', 'cotacao_frete_internacional');
                updateValorCotacao('seguro_internacional', 'cotacao_seguro_internacional');
                updateValorCotacao('acrescimo_frete', 'cotacao_acrescimo_frete');
                updateValorCotacao('service_charges', 'cotacao_service_charges');
                convertToUSDAndBRL('frete_internacional');
                convertToUSDAndBRL('seguro_internacional');
                convertToUSDAndBRL('acrescimo_frete');
                convertToUSDAndBRL('service_charges');
            }, 500);

        }, 1000);

        $('#atualizarCotacoes').on('click', function() {
            convertToUSDAndBRL('frete_internacional');
            convertToUSDAndBRL('seguro_internacional');
            convertToUSDAndBRL('acrescimo_frete');
            convertToUSDAndBRL('service_charges');
            updateValorCotacao('frete_internacional', 'cotacao_frete_internacional');
            updateValorCotacao('seguro_internacional', 'cotacao_seguro_internacional');
            updateValorCotacao('acrescimo_frete', 'cotacao_acrescimo_frete');
            updateValorCotacao('service_charges', 'cotacao_service_charges');
        })

        function showDeleteConfirmation(documentId) {
            const deleteUrl = '/destroy-produto-processo/' + documentId + '?tipo_processo=maritimo'; 

            Swal.fire({
                title: 'Você tem certeza que deseja excluir este registro?',
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
                    $('#delete-form').attr('action', deleteUrl);

                    $('#delete-form').submit();
                } else {
                    Toast.fire({
                        icon: 'info',
                        title: 'Ação cancelada'
                    });
                }
            });
        }
        const MoneyUtils = {


            parsePercentage: function(value) {
                if (!value || value === "") return 0;


                let stringValue = value.toString().trim();


                stringValue = stringValue.replace(/%/g, '');


                stringValue = stringValue.replace(',', '.');


                stringValue = stringValue.replace(/\s/g, '');


                const parsedValue = parseFloat(stringValue) || 0;

                return parsedValue / 100;
            },

            truncate: function(value, decimals = 2) {
                return truncateNumber(value, decimals);
            },


            formatUSD: function(value, decimals = 2) {
                if (value === null || value === undefined) return "0.00";

                let num = typeof value === 'string' ? parseFloat(value.replace(',', '.')) : value;
                let fixedDecimals = num.toFixed(decimals);

                let parts = fixedDecimals.split('.');
                let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                let decimalPart = parts[1] || '00';

                return `${integerPart}.${decimalPart}`;
            },

            formatPercentage: function(value, decimals = 2) {
                if (value === null || value === undefined) {
                    return formatTruncatedNumber(0, decimals) + '%';
                }

                const percentageValue = normalizeNumericValue(value) * 100;
                const truncated = this.truncate(percentageValue, decimals);
                return `${truncated.toLocaleString('pt-BR', {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals
                })}%`;
            },
            parseMoney: function(value) {
                if (value === null || value === undefined || value === "") return 0;


                if (typeof value === "number") {
                    return value;
                }

                if (value.toString().includes('.') && !value.toString().includes(',')) {
                    return parseFloat(value) || 0;
                }



                let cleanValue = value.toString()
                    .replace(/\./g, '') 
                    .replace(/,/g, '.'); 


                cleanValue = cleanValue.replace(/[^\d.]/g, '');

                return parseFloat(cleanValue) || 0;
            },

            formatMoney: function(value, decimals = 6) {
                const num = normalizeNumericValue(value);
                if (!isFinite(num)) return '0';
                
                let processedValue;


                if (decimals <= 2) {
                    processedValue = Math.round(num * Math.pow(10, decimals)) / Math.pow(10, decimals);
                } else {
                    processedValue = this.truncate(num, decimals);
                }
                

                return processedValue.toLocaleString('pt-BR', {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals
                });
            },
            
            formatMoneyExato: function(value) {

                const num = normalizeNumericValue(value);
                if (!isFinite(num)) return '0';
                


                let str = num.toFixed(20);
                

                str = str.replace(/\.?0+$/, '');
                

                let parts = str.split('.');
                let integerPart = parts[0];
                let decimalPart = parts[1] || '';
                

                integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                

                if (decimalPart) {
                    return `${integerPart},${decimalPart}`;
                }
                return integerPart;
            }
        };



        function recalcularTodaTabela() {

            if (isRecalculating) {
                return;
            }
            isRecalculating = true;
            contadorRecalculos++;
            resetDebugStore();
            
            // Inicializar ou limpar valores brutos por linha para garantir precisão máxima
            window.valoresBrutosPorLinha = {};
            
            // Filtrar apenas linhas da tabela principal, excluindo linhas da tabela de multa
            const rows = $('#productsBody .linhas-input');
            const moedasOBject = getCotacaoesProcesso();

            let moedaDolar = moedasOBject['USD']?.venda;
            if (!moedaDolar || moedaDolar === null || moedaDolar === undefined) {
                const cotacaoFrete = $(`#cotacao_frete_internacional`).val();
                moedaDolar = cotacaoFrete ? cotacaoFrete.replace(',', '.') : 1;
            }

            const dolar = typeof moedaDolar === 'number' ? moedaDolar : MoneyUtils.parseMoney(moedaDolar);
            const totalPesoLiq = calcularPesoTotal();
            const taxaSisComex = calcularTaxaSiscomex();
            const freteProcessoUSD = obterValorProcessoUSD('#frete_internacional', '#frete_internacional_moeda', '#cotacao_frete_internacional');
            const seguroProcessoUSD = obterValorProcessoUSD('#seguro_internacional', '#seguro_internacional_moeda', '#cotacao_seguro_internacional');
            const acrescimoProcessoUSD = obterValorProcessoUSD('#acrescimo_frete', '#acrescimo_frete_moeda', '#cotacao_acrescimo_frete');
            const serviceChargesProcessoUSD = obterValorProcessoUSD('#service_charges', '#service_charges_moeda', '#cotacao_service_charges');
            const globaisProcesso = {
                pesoTotalProcesso: totalPesoLiq,
                cotacaoUSD: dolar,
                taxaSiscomexProcesso: taxaSisComex,
                freteProcessoUSD,
                seguroProcessoUSD,
                acrescimoProcessoUSD,
                serviceChargesProcessoUSD
            };

            let fobTotalGeralAtualizado = 0;
            const fobTotaisPorLinha = {};

            rows.each(function() {
                const rowId = this.id.toString().replace('row-', '');
                // Pular linhas da tabela de multa (IDs contendo "multa")
                if (rowId && rowId.includes('multa')) {
                    return; // continue para próxima iteração
                }
                if (rowId) {
                    const {
                        pesoTotal,
                        fobUnitario,
                        quantidade
                    } = obterValoresBase(rowId);

                    const fatorPesoRow = recalcularFatorPeso(totalPesoLiq, rowId);
                    const fobTotal = fobUnitario * quantidade;


                    fobTotaisPorLinha[rowId] = {
                        fobTotal,
                        fobUnitario,
                        quantidade,
                        fatorPesoRow,
                        pesoTotal
                    };
                    addDebugEntry(rowId, {
                        produto: $(`#descricao-${rowId}`).val() || $(`#produto_id-${rowId} option:selected`).text(),
                        quantidade,
                        pesoTotal,
                        fobUnitario,
                        fobTotal,
                        fatorPeso: fatorPesoRow
                    });

                    fobTotalGeralAtualizado += fobTotal;


                    const pesoLiqUnit = pesoTotal / (quantidade || 1);
                    $(`#peso_liquido_unitario-${rowId}`).val(MoneyUtils.formatMoney(pesoLiqUnit, 6));
                    $(`#fob_total_usd-${rowId}`).val(MoneyUtils.formatMoney(fobTotal, 7));
                    $(`#fob_total_brl-${rowId}`).val(MoneyUtils.formatMoney(fobTotal * dolar, 7));
                }
            });


            rows.each(function() {
                const rowId = this.id.toString().replace('row-', '');
                // Pular linhas da tabela de multa (IDs contendo "multa")
                if (rowId && rowId.includes('multa')) {
                    return; // continue para próxima iteração
                }
                if (rowId) {
                    const linha = fobTotaisPorLinha[rowId];

                    const fobTotal = linha.fobTotal;
                    const fobUnitario = linha.fobUnitario;
                    const quantidade = linha.quantidade;
                    const fatorPesoRow = linha.fatorPesoRow;
                    const pesoTotal = linha.pesoTotal;
                    

                    const quantidadeAtual = quantidade || MoneyUtils.parseMoney($(`#quantidade-${rowId}`).val()) || 0;


                    const fatorVlrFob_AX = fobTotal / (fobTotalGeralAtualizado || 1);
                    $(`#fator_valor_fob-${rowId}`).val(MoneyUtils.formatMoney(fatorVlrFob_AX, 8));


                    const moedaFrete = $('#frete_internacional_moeda').val();
                    let valorFreteInternacional = MoneyUtils.parseMoney($('#frete_internacional').val());
                    let valorFreteInternacionalDolar = 0;

                    if (moedaFrete != 'USD') {
                        let cotacaoProcesso = getCotacaoesProcesso();
                        let cotacaoMoedaFloat = MoneyUtils.parseMoney($('#cotacao_frete_internacional').val());
                        let moeda = $('#moeda_processo').val();
                        let cotacaoUSD = cotacaoProcesso['USD']?.venda ?? 1;
                        let moedaEmUSD = cotacaoMoedaFloat / cotacaoUSD;
                        valorFreteInternacionalDolar = valorFreteInternacional * moedaEmUSD;
                    } else {
                        valorFreteInternacionalDolar = valorFreteInternacional;
                    }

                    const freteUsdInt = valorFreteInternacionalDolar * fatorPesoRow;
                    const thc_capataziaBase = MoneyUtils.parseMoney($('#thc_capatazia').val());
                    const thcRow = thc_capataziaBase * fatorPesoRow;
                    

                    const serviceChargesBaseRow = MoneyUtils.parseMoney($('#service_charges').val()) || 0;
                    const serviceChargesRowAtual = serviceChargesBaseRow * fatorPesoRow;
                    
                    // Calcular service_charges em USD se a moeda não for USD
                    const moedaServiceChargesRow = $('#service_charges_moeda').val();
                    let serviceChargesRowAtualUSD = serviceChargesRowAtual;
                    if (moedaServiceChargesRow && moedaServiceChargesRow !== 'USD') {
                        const cotacoesProcesso = getCotacaoesProcesso();
                        const cotacaoServiceCharges = MoneyUtils.parseMoney($('#cotacao_service_charges').val()) || 0;
                        const cotacaoUSD = cotacoesProcesso['USD']?.venda ?? 1;
                        if (cotacaoServiceCharges > 0 && cotacaoUSD > 0) {
                            const moedaEmUSD = cotacaoServiceCharges / cotacaoUSD;
                            serviceChargesRowAtualUSD = serviceChargesRowAtual * moedaEmUSD;
                        }
                    }

                    const seguroIntUsdRow = calcularSeguro(fobTotal, fobTotalGeralAtualizado);
                    const acrescimoFreteUsdRow = calcularAcrescimoFrete(fobTotal, fobTotalGeralAtualizado, dolar);
                    
                    // Calcular CRF Total - varia conforme nacionalização
                    const nacionalizacao = getNacionalizacaoAtual();
                    let vlrCrfTotal;
                    if (nacionalizacao === 'santa_catarina') {
                        vlrCrfTotal = fobTotal + freteUsdInt + serviceChargesRowAtualUSD + acrescimoFreteUsdRow;
                    } else if (nacionalizacao === 'mato_grosso') {
                        // Para Mato Grosso: FOB Total USD + Frete Internacional USD + Seguro Internacional USD
                        vlrCrfTotal = fobTotal + freteUsdInt + seguroIntUsdRow;
                    } else {
                        vlrCrfTotal = fobTotal + freteUsdInt;
                    }

                    const vlrCrfUnit = quantidadeAtual > 0 ? vlrCrfTotal / quantidadeAtual : 0;
                    
                    
                    // Calcular Valor Aduaneiro - varia conforme nacionalização
                    let vlrAduaneiroUsd;
                    if (nacionalizacao === 'santa_catarina') {
                        const thcUsd = dolar > 0 ? thcRow / dolar : 0;
                        vlrAduaneiroUsd = vlrCrfTotal + seguroIntUsdRow + thcUsd;
                    } else if (nacionalizacao === 'mato_grosso') {
                        // Para Mato Grosso: VLR CFR Total + Acréscimo Frete USD + THC USD
                        const thcUsd = dolar > 0 ? thcRow / dolar : 0;
                        vlrAduaneiroUsd = vlrCrfTotal + acrescimoFreteUsdRow + thcUsd;
                    } else {
                        vlrAduaneiroUsd = calcularValorAduaneiro(fobTotal, freteUsdInt, acrescimoFreteUsdRow,
                            seguroIntUsdRow, thcRow, dolar, vlrCrfTotal, serviceChargesRowAtual);
                    }

                    const vlrAduaneiroBrl = vlrAduaneiroUsd * dolar;

                    const impostos = calcularImpostos(rowId, vlrAduaneiroBrl);
                    const fatorTaxaSiscomex_AY = taxaSisComex / ((fobTotalGeralAtualizado || 1) * (dolar || 1));
                    const taxaSiscomexUnitaria_BB = fatorTaxaSiscomex_AY * (fobTotal * dolar);
                    $(`#fator_tx_siscomex-${rowId}`).val(MoneyUtils.formatMoney(fatorTaxaSiscomex_AY, 6));

                    const despesasInfo = calcularDespesas(rowId, fatorVlrFob_AX, fatorTaxaSiscomex_AY,
                        (taxaSiscomexUnitaria_BB ?? 0), vlrAduaneiroBrl);
                    const despesas = despesasInfo.total;

                    // Despesa aduaneira = taxa siscomex linha + afrmm (apenas para Mato Grosso)
                    let despesaAduaneira;
                    if (nacionalizacao === 'mato_grosso') {
                        const afrmm = $(`#afrmm-${rowId}`).val() ? MoneyUtils.parseMoney($(`#afrmm-${rowId}`).val()) : 0;
                        despesaAduaneira = (taxaSiscomexUnitaria_BB ?? 0) + afrmm;
                    } else {
                        despesaAduaneira = despesas;
                    }

                    // Para Mato Grosso, passar despesaAduaneira; para outros, passar despesas
                    const despesasParaBcIcms = nacionalizacao === 'mato_grosso' ? despesaAduaneira : despesas;
                    const bcIcmsSReducao = calcularBcIcmsSemReducao(vlrAduaneiroBrl, impostos, despesasParaBcIcms);
                    const vlrIcmsSReducao = bcIcmsSReducao * impostos.icms;
                    const despesasParaBcIcmsReduzido = nacionalizacao === 'mato_grosso' ? despesaAduaneira : despesas;
                    const bcImcsReduzido = calcularBcIcmsReduzido(rowId, vlrAduaneiroBrl, impostos, despesasParaBcIcmsReduzido);
                    const vlrIcmsReduzido = bcImcsReduzido * impostos.icms;
                    // Para Mato Grosso, passar despesaAduaneira para calcularTotais; para outros, passar despesas
                    const despesasParaTotais = nacionalizacao === 'mato_grosso' ? despesaAduaneira : despesas;
                    const totais = calcularTotais(vlrAduaneiroBrl, impostos, despesasParaTotais, quantidade, vlrIcmsReduzido,
                        rowId);

                    const mva = $(`#mva-${rowId}`).val() ? MoneyUtils.parsePercentage($(`#mva-${rowId}`).val()) : 0;
                    


                    const valorTotalNfSemIcms = totais.vlrTotalNfSemIcms || 0;
                    let base_icms_st = 0;
                    
                    if (mva > 0 && valorTotalNfSemIcms > 0) {

                        base_icms_st = valorTotalNfSemIcms * (1 + mva);
                    } else {

                        base_icms_st = valorTotalNfSemIcms;
                    }
                    
                    let icms_st_percent = MoneyUtils.parsePercentage($(`#icms_st-${rowId}`).val());
                    let vlrIcmsSt = icms_st_percent > 0 ? (base_icms_st * icms_st_percent) - vlrIcmsReduzido : 0;
                    const dif_cambial_frete_processo = MoneyUtils.parseMoney($('#diferenca_cambial_frete').val()) || 0;
                    const dif_cambial_fob_processo =  MoneyUtils.parseMoney($('#diferenca_cambial_fob').val()) || 0;
                    let diferenca_cambial_frete = (freteUsdInt * dif_cambial_frete_processo) - (freteUsdInt *
                        dolar);
                    diferenca_cambial_frete = validarDiferencaCambialFrete(diferenca_cambial_frete);
                    
                    // Calcular diferenca_cambial_fob conforme nacionalização
                    let diferenca_cambial_fob;
                    if (nacionalizacao === 'mato_grosso') {
                        // Para Mato Grosso: (diferenca_cambial_fob_cabecalho * fator_vlr_fob) - (fob_total_brl + frete_brl + seguro_brl)
                        const fobTotalBrl = fobTotal * dolar;
                        const freteBrl = freteUsdInt * dolar;
                        const seguroBrl = seguroIntUsdRow * dolar;
                        diferenca_cambial_fob = (fatorVlrFob_AX * dif_cambial_fob_processo) - (fobTotalBrl + freteBrl + seguroBrl);
                    } else {
                        diferenca_cambial_fob = dif_cambial_fob_processo > 0 ? (fatorVlrFob_AX * dif_cambial_fob_processo) - (fobTotal * dolar) : 0;
                    }

                    const reducaoPercent = MoneyUtils.parsePercentage($(`#reducao-${rowId}`).val()) || 0;

                    const camposExternos = getCamposExternos();
                    let desp_desenbaraco_parte_1 = 0;
                    let desp_desenbaraco_parte_2 = 0;
                    let despesaDesembaraco = 0;
                    
                    const multaDesp = $(`#multa-${rowId}`).val() ? MoneyUtils.parseMoney($(`#multa-${rowId}`).val()) : 0;
                    const vlrAduaneiroBrlDesp = vlrAduaneiroBrl;
                    const nacionalizacaoAtualDesp = getNacionalizacaoAtual();
                    let taxa_def_desp;
                    if (nacionalizacaoAtualDesp === 'mato_grosso') {
                        // Para Mato Grosso: usar valor rateado do cabeçalho
                        const valorCampo = MoneyUtils.parseMoney($(`#tx_def_li`).val()) || 0;
                        const valorDistribuido = window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['tx_def_li'] && window.valoresBrutosCamposExternos['tx_def_li'][rowId] !== undefined
                            ? window.valoresBrutosCamposExternos['tx_def_li'][rowId]
                            : (valorCampo * fatorVlrFob_AX);
                        taxa_def_desp = MoneyUtils.parseMoney($(`#tx_def_li-${rowId}`).val()) || valorDistribuido;
                    } else {
                        // Para outras nacionalizações: calcular como porcentagem
                    const txDefLiPercentDesp = $(`#tx_def_li-${rowId}`).val() ? MoneyUtils.parsePercentage($(`#tx_def_li-${rowId}`).val()) : 0;
                        taxa_def_desp = vlrAduaneiroBrlDesp * txDefLiPercentDesp;
                    }
                    const taxa_siscomex_desp = taxaSiscomexUnitaria_BB || 0;
                    const capatazia_desp = $(`#capatazia-${rowId}`).val() ? MoneyUtils.parseMoney($(`#capatazia-${rowId}`).val()) : 0;
                    const afrmm_desp = $(`#afrmm-${rowId}`).val() ? MoneyUtils.parseMoney($(`#afrmm-${rowId}`).val()) : 0;
                    const honorarios_nix_desp = $(`#honorarios_nix-${rowId}`).val() ? MoneyUtils.parseMoney($(`#honorarios_nix-${rowId}`).val()) : 0;
                    
                    const nacionalizacaoDesp = getNacionalizacaoAtual();
                    
                    if (nacionalizacaoDesp === 'santa_catarina') {
                        // Fórmula específica para Santa Catarina
                        const multa_complem_desp = MoneyUtils.parseMoney($(`#multa_complem-${rowId}`).val()) || 0;
                        const dif_impostos_desp = MoneyUtils.parseMoney($(`#dif_impostos-${rowId}`).val()) || 0;
                        const outras_taxas_agente_desp = MoneyUtils.parseMoney($(`#outras_taxas_agente-${rowId}`).val()) || 0;
                        const liberacao_bl_desp = MoneyUtils.parseMoney($(`#liberacao_bl-${rowId}`).val()) || 0;
                        const desconsolidacao_desp = MoneyUtils.parseMoney($(`#desconsolidacao-${rowId}`).val()) || 0;
                        const isps_code_desp = MoneyUtils.parseMoney($(`#isps_code-${rowId}`).val()) || 0;
                        const handling_desp = MoneyUtils.parseMoney($(`#handling-${rowId}`).val()) || 0;
                        const armazenagem_porto_desp = MoneyUtils.parseMoney($(`#armazenagem_porto-${rowId}`).val()) || 0;
                        const frete_rodoviario_desp = MoneyUtils.parseMoney($(`#frete_rodoviario-${rowId}`).val()) || 0;
                        const dif_frete_rodoviario_desp = MoneyUtils.parseMoney($(`#dif_frete_rodoviario-${rowId}`).val()) || 0;
                        const sda_desp = MoneyUtils.parseMoney($(`#sda-${rowId}`).val()) || 0;
                        const rep_porto_desp = MoneyUtils.parseMoney($(`#rep_porto-${rowId}`).val()) || 0;
                        const tx_correcao_lacre_desp = MoneyUtils.parseMoney($(`#tx_correcao_lacre-${rowId}`).val()) || 0;
                        const li_dta_honor_nix_desp = MoneyUtils.parseMoney($(`#li_dta_honor_nix-${rowId}`).val()) || 0;
                        
                        // Parte 1: SOMA(BD23:BW23) = MULTA + TX DEF. LI + TAXA SISCOMEX + MULTA COMPLEM + DIF IMPOSTOS + 
                        // OUTRAS TX AGENTE + LIBERAÇÃO BL + DESCONS. + ISPS CODE + HANDLING + CAPATAZIA + AFRMM + 
                        // ARMAZENAGEM PORTO + FRETE RODOVIARIO + DIF FRETE RODOVIARIO + S.D.A + REP.PORTO + 
                        // TX CORREÇÃO LACRE + LI+DTA+HONOR.NIX + HONORÁRIOS NIX
                        // BD: MULTA, BE: TX DEF. LI, BF: TAXA SISCOMEX, BG: MULTA COMPLEM, BH: DIF IMPOSTOS,
                        // BI: OUTRAS TX AGENTE, BJ: LIBERAÇÃO BL, BK: DESCONS., BL: ISPS CODE, BM: HANDLING,
                        // BN: CAPATAZIA, BO: AFRMM, BP: ARMAZENAGEM PORTO, BQ: FRETE RODOVIARIO, BR: DIF FRETE RODOVIARIO,
                        // BS: S.D.A, BT: REP.PORTO, BU: TX CORREÇÃO LACRE, BV: LI+DTA+HONOR.NIX, BW: HONORÁRIOS NIX
                        desp_desenbaraco_parte_1 = multaDesp + taxa_def_desp + taxa_siscomex_desp + multa_complem_desp + 
                            dif_impostos_desp + outras_taxas_agente_desp + liberacao_bl_desp + desconsolidacao_desp + 
                            isps_code_desp + handling_desp + capatazia_desp + afrmm_desp + armazenagem_porto_desp + 
                            frete_rodoviario_desp + dif_frete_rodoviario_desp + sda_desp + rep_porto_desp + 
                            tx_correcao_lacre_desp + li_dta_honor_nix_desp + honorarios_nix_desp;
                        
                        // Parte 2: (BD23+BE23+BF23+BN23+BO23+BP23+BQ23+BW23)
                        // BD: MULTA, BE: TX DEF. LI, BF: TAXA SISCOMEX, BN: CAPATAZIA, BO: AFRMM, 
                        // BP: ARMAZENAGEM PORTO, BQ: FRETE RODOVIARIO, BW: HONORÁRIOS NIX
                        desp_desenbaraco_parte_2 = multaDesp + taxa_def_desp + taxa_siscomex_desp + capatazia_desp + 
                            afrmm_desp + armazenagem_porto_desp + frete_rodoviario_desp + honorarios_nix_desp;
                        
                        despesaDesembaraco = desp_desenbaraco_parte_1 - desp_desenbaraco_parte_2;
                        
                    } else if (nacionalizacaoDesp === 'mato_grosso') {
                        // Para Mato Grosso: DESP. DESEMBARAÇO = SOMA(multa:honorario_nix) - (multa+taxa_siscomex+capatazia+afrmm)
                        
                        // Ler todos os campos de MULTA até HONORÁRIOS NIX
                        const outras_taxas_agente_desp = MoneyUtils.parseMoney($(`#outras_taxas_agente-${rowId}`).val()) || 0;
                        const liberacao_bl_desp = MoneyUtils.parseMoney($(`#liberacao_bl-${rowId}`).val()) || 0;
                        const desconsolidacao_desp = MoneyUtils.parseMoney($(`#desconsolidacao-${rowId}`).val()) || 0;
                        const isps_code_desp = MoneyUtils.parseMoney($(`#isps_code-${rowId}`).val()) || 0;
                        const handling_desp = MoneyUtils.parseMoney($(`#handling-${rowId}`).val()) || 0;
                        const armazenagem_sts_desp = MoneyUtils.parseMoney($(`#armazenagem_sts-${rowId}`).val()) || 0;
                        const frete_sts_cgb_desp = MoneyUtils.parseMoney($(`#frete_sts_cgb-${rowId}`).val()) || 0;
                        const diarias_desp = MoneyUtils.parseMoney($(`#diarias-${rowId}`).val()) || 0;
                        const sda_desp = MoneyUtils.parseMoney($(`#sda-${rowId}`).val()) || 0;
                        const rep_sts_desp = MoneyUtils.parseMoney($(`#rep_sts-${rowId}`).val()) || 0;
                        const armaz_cgb_desp = MoneyUtils.parseMoney($(`#armaz_cgb-${rowId}`).val()) || 0;
                        const rep_cgb_desp = MoneyUtils.parseMoney($(`#rep_cgb-${rowId}`).val()) || 0;
                        const demurrage_desp = MoneyUtils.parseMoney($(`#demurrage-${rowId}`).val()) || 0;
                        const li_dta_honor_nix_desp = MoneyUtils.parseMoney($(`#li_dta_honor_nix-${rowId}`).val()) || 0;
                        const honorarios_nix_desp_mg = MoneyUtils.parseMoney($(`#honorarios_nix-${rowId}`).val()) || 0;
                        
                        // Parte 1: SOMA(multa:honorario_nix) = MULTA + TX DEF. LI + TAXA SISCOMEX + OUTRAS TX AGENTE + ... + HONORÁRIOS NIX
                        desp_desenbaraco_parte_1 = multaDesp + taxa_def_desp + taxa_siscomex_desp + outras_taxas_agente_desp + 
                            liberacao_bl_desp + desconsolidacao_desp + isps_code_desp + handling_desp + capatazia_desp + 
                            afrmm_desp + armazenagem_sts_desp + frete_sts_cgb_desp + diarias_desp + sda_desp + 
                            rep_sts_desp + armaz_cgb_desp + rep_cgb_desp + demurrage_desp + li_dta_honor_nix_desp + honorarios_nix_desp_mg;
                        
                        // Parte 2: (multa+taxa_siscomex+capatazia+afrmm)
                        desp_desenbaraco_parte_2 = multaDesp + taxa_siscomex_desp + capatazia_desp + afrmm_desp;
                        
                        despesaDesembaraco = desp_desenbaraco_parte_1 - desp_desenbaraco_parte_2;
                        
                    } else {
                        // Fórmula padrão para outras nacionalizações
                        for (let campo of camposExternos) {
                            const valorCampo = MoneyUtils.parseMoney($(`#${campo}`).val()) || 0;
                            const valorDistribuido = window.valoresBrutosCamposExternos[campo]?.[rowId] ?? (valorCampo * fatorVlrFob_AX);
                            desp_desenbaraco_parte_1 += valorDistribuido;
                        }
                        desp_desenbaraco_parte_1 += multaDesp + taxa_def_desp + taxa_siscomex_desp;
                        desp_desenbaraco_parte_2 = multaDesp + taxa_def_desp + taxa_siscomex_desp + capatazia_desp + afrmm_desp + honorarios_nix_desp;
                        despesaDesembaraco = desp_desenbaraco_parte_1 - desp_desenbaraco_parte_2;
                        
                    }
                    
                    // Calcular custo_unitario_final e custo_total_final (após calcular despesaDesembaraco)
                    // Usar valores brutos de window.valoresBrutosPorLinha quando disponíveis para máxima precisão
                    const valoresBrutosAtuais = window.valoresBrutosPorLinha && window.valoresBrutosPorLinha[rowId];
                    
                    // Obter valores brutos (priorizar valores já armazenados, caso contrário usar valores calculados)
                    const vlrTotalNfComIcmsSt = valoresBrutosAtuais?.valor_total_nf_com_icms_st ?? totais.vlrTotalNfComIcms;
                    const despesaDesembaracoBruto = valoresBrutosAtuais?.desp_desenbaraco ?? despesaDesembaraco;
                    const diferenca_cambial_fob_bruto = valoresBrutosAtuais?.diferenca_cambial_fob ?? diferenca_cambial_fob;
                    const diferenca_cambial_frete_bruto = valoresBrutosAtuais?.diferenca_cambial_frete ?? diferenca_cambial_frete;
                    const vlrIcmsReduzidoBruto = valoresBrutosAtuais?.valor_icms_reduzido ?? vlrIcmsReduzido;
                    const quantidadeBruta = valoresBrutosAtuais?.quantidade ?? quantidadeAtual;
                    
                    // Adicionar campos opcionais se checkbox marcado - usar valores brutos
                    const opcional1CompoeCalc = $('#opcional_1_compoe_despesas').is(':checked');
                    const opcional2CompoeCalc = $('#opcional_2_compoe_despesas').is(':checked');
                    const opcional1ValorCalc = valoresBrutosAtuais?.opcional_1_valor ?? (MoneyUtils.parseMoney($(`#opcional_1_valor-${rowId}`).val()) || 0);
                    const opcional2ValorCalc = valoresBrutosAtuais?.opcional_2_valor ?? (MoneyUtils.parseMoney($(`#opcional_2_valor-${rowId}`).val()) || 0);
                    
                    let despesasAdicionaisCalc = 0;
                    if (opcional1CompoeCalc) {
                        despesasAdicionaisCalc += opcional1ValorCalc;
                    }
                    if (opcional2CompoeCalc) {
                        despesasAdicionaisCalc += opcional2ValorCalc;
                    }
                    
                    // Para Santos e Mato Grosso: (VLR TOTAL NF C/ICMS-ST + DESP. DESEMBARAÇO + DIF.CAMBIAL FOB + DIF. CAMBIAL FRETE) / quantidade
                    // Para outras: ((VLR TOTAL NF C/ICMS-ST + DESP. DESEMBARAÇO + DIF.CAMBIAL FOB + DIF. CAMBIAL FRETE) - VLR ICMS REDUZIDO) / quantidade
                    // Usar sempre valores brutos para máxima precisão
                    const nacionalizacaoCusto = getNacionalizacaoAtual();
                    let custoUnitarioFinal;
                    if (nacionalizacaoCusto === 'santos' || nacionalizacaoCusto === 'mato_grosso') {
                        // EM MATO GROSSO CUSTO UNIT FINAL É =((AX19+BV19+BW19+BX19)/F19)
                        // AX = VLR TOTAL NF C/ICMS-ST
                        // BV = DESP DESEMBARACO
                        // BW = DIF CAMBIAL FRETE
                        // BX = DIF CAMBIAL FOB
                        // F = QUANTIDADE
                        custoUnitarioFinal = quantidadeBruta > 0 
                            ? (vlrTotalNfComIcmsSt + despesaDesembaracoBruto + diferenca_cambial_fob_bruto + diferenca_cambial_frete_bruto) / quantidadeBruta 
                            : 0;
                      
                    } else if (nacionalizacaoCusto === 'santa_catarina') {
                        // EM SANTA CATARINA CUSTO UNIT FINAL É =(((AZ23+BX23+BY23+BZ23)-AR23)/F23)
                        // AZ = VLR TOTAL NF C/ICMS-ST
                        // BX = DESP. DESEMBARAÇO
                        // BY = DIF. CAMBIAL FRETE
                        // BZ = DIF CAMBIAL FOB
                        // AR = VLR ICMS REDUZ.
                        // F = QUANTIDADE
                        custoUnitarioFinal = quantidadeBruta > 0 
                            ? ((vlrTotalNfComIcmsSt + despesaDesembaracoBruto + diferenca_cambial_frete_bruto + diferenca_cambial_fob_bruto) - vlrIcmsReduzidoBruto) / quantidadeBruta 
                            : 0;
                    } else {
                        custoUnitarioFinal = quantidadeBruta > 0 
                            ? ((vlrTotalNfComIcmsSt + despesaDesembaracoBruto + diferenca_cambial_fob_bruto + diferenca_cambial_frete_bruto + despesasAdicionaisCalc) - vlrIcmsReduzidoBruto) / quantidadeBruta 
                            : 0;
                    }
                    
                    // CUSTO TOTAL FINAL deve ser calculado com o valor bruto de CUSTO UNIT FINAL (não arredondado)
                    const custoTotalFinal = custoUnitarioFinal * quantidadeBruta;
                    
                    // Calcular novas colunas para Mato Grosso
                    let dezPorcento = 0;
                    let custoComMargem = 0;
                    let vlrIpiMg = 0;
                    let vlrIcmsMg = 0;
                    let pisMg = 0;
                    let cofinsMg = 0;
                    let custoTotalFinalCredito = 0;
                    let custoUnitCredito = 0;
                    
                    if (nacionalizacaoCusto === 'mato_grosso') {
                        // Obter valores brutos da linha atual (valores já calculados anteriormente no loop)
                        const valorIpi = totais.vlrIpi || 0;
                        const valorPis = totais.vlrPis || 0;
                        const valorCofins = totais.vlrCofins || 0;
                        
                        // DEZ POR CENTO = (custo_unitario_final * 0.1) + custo_unitario_final
                        dezPorcento = (custoUnitarioFinal * 0.1) + custoUnitarioFinal;
                        
                        // CUSTO COM MARGEM = dez_porcento * quantidade
                        custoComMargem = dezPorcento * quantidadeAtual;
                        
                        // VLR IPI = valor_ipi (já calculado em totais)
                        vlrIpiMg = valorIpi;
                        
                        // VLR ICMS = 0 (vazio)
                        vlrIcmsMg = 0;
                        
                        // PIS = valor_pis (já calculado em totais)
                        pisMg = valorPis;
                        
                        // COFINS = valor_cofins (já calculado em totais)
                        cofinsMg = valorCofins;
                        
                        // CUSTO TOTAL FINAL = custo_com_margem - (vlr_ipi + vlr_icms + vlr_pis + vlr_cofins)
                        custoTotalFinalCredito = custoComMargem - (vlrIpiMg + vlrIcmsMg + pisMg + cofinsMg);
                        
                        // CUSTO UNIT CREDITO = custo_total_final_credito / quantidade
                        if (quantidadeAtual > 0) {
                            custoUnitCredito = custoTotalFinalCredito / quantidadeAtual;
                        }
                        
                        // Calcular novas colunas ICMS-ST para Mato Grosso
                        // Ler MVA e ICMS-ST dos inputs (porcentagem) - usar campos específicos _mg
                        const mvaPercent = MoneyUtils.parsePercentage($(`#mva_mg-${rowId}`).val()) || 0;
                        const icmsStPercent = MoneyUtils.parsePercentage($(`#icms_st_mg-${rowId}`).val()) || 0;
                        
                        // BC ICMS-ST = custo_total_final_credito * (1 + mva_percent)
                        const bcIcmsStMg = custoTotalFinalCredito * (1 + mvaPercent);
                        
                        // VLR ICMS-ST = bc_icms_st_mg * icms_st_percent
                        const vlrIcmsStMg = bcIcmsStMg * icmsStPercent;
                        
                        // CUSTO TOTAL C/ICMS ST = custo_total_final_credito + vlr_icms_st_mg
                        const custoTotalCIcmsSt = custoTotalFinalCredito + vlrIcmsStMg;
                        
                        // CUSTO UNIT C/ICMS ST = custo_total_c_icms_st / quantidade
                        let custoUnitCIcmsSt = 0;
                        if (quantidadeAtual > 0) {
                            custoUnitCIcmsSt = custoTotalCIcmsSt / quantidadeAtual;
                        }
                        
                        // Armazenar valores brutos
                        if (!window.valoresBrutosPorLinha[rowId]) {
                            window.valoresBrutosPorLinha[rowId] = {};
                        }
                        window.valoresBrutosPorLinha[rowId].bc_icms_st_mg = bcIcmsStMg;
                        window.valoresBrutosPorLinha[rowId].mva_mg = mvaPercent;
                        window.valoresBrutosPorLinha[rowId].icms_st_mg = icmsStPercent;
                        window.valoresBrutosPorLinha[rowId].vlr_icms_st_mg = vlrIcmsStMg;
                        window.valoresBrutosPorLinha[rowId].custo_total_c_icms_st = custoTotalCIcmsSt;
                        window.valoresBrutosPorLinha[rowId].custo_unit_c_icms_st = custoUnitCIcmsSt;
                        
                        // Calcular novas colunas: EXPORTADOR, TRIBUTOS, DESPESAS, TOTAL PAGO, PERCENTUAL S/FOB
                        calcularColunasExportadorTributosDespesas(rowId);
                    }
                    
     

                    addDebugEntry(rowId, {
                        freteUsd: freteUsdInt,
                        seguroUsd: seguroIntUsdRow,
                        acrescimoUsd: acrescimoFreteUsdRow,
                        serviceChargesUsd: serviceChargesRowAtual,
                        thc: thcRow,
                        vlrCrfTotal: vlrCrfTotal,
                        vlrAduaneiroUsd: vlrAduaneiroUsd,
                        fatorVlrFob: fatorVlrFob_AX,
                        fatorSiscomex: fatorTaxaSiscomex_AY,
                        taxaSiscomexUnit: taxaSiscomexUnitaria_BB,
                        fatorSiscomex: fatorTaxaSiscomex_AY,
                        taxaSiscomexUnit: taxaSiscomexUnitaria_BB,
                        diferencaCambialFrete: diferenca_cambial_frete,
                        diferencaCambialFob: diferenca_cambial_fob,
                        diferencaCambialFreteProcesso: dif_cambial_frete_processo,
                        diferencaCambialFobProcesso: dif_cambial_fob_processo,
                        reducao: reducaoPercent,
                        vlrII: totais.vlrII,
                        bcIpi: totais.bcIpi,
                        vlrIpi: totais.vlrIpi,
                        bcPisCofins: totais.bcPisCofins,
                        vlrPis: totais.vlrPis,
                        vlrCofins: totais.vlrCofins,
                        despesaAduaneira: despesas,
                        bcIcmsSemReducao: bcIcmsSReducao,
                        vlrIcmsSemReducao: vlrIcmsSReducao,
                        bcIcmsReduzido: bcImcsReduzido,
                        vlrIcmsReduzido,
                        vlrUnitProdNf: totais.vlrUnitProdutNf,
                        vlrTotalProdNf: totais.vlrTotalProdutoNf,
                        vlrTotalNfSemIcms: totais.vlrTotalNfSemIcms,
                        baseIcmsSt: base_icms_st,
                        valorAduaneiroBrl: vlrAduaneiroBrl,
                        aliquotaIi: impostos.ii,
                        aliquotaIpi: impostos.ipi,
                        aliquotaPis: impostos.pis,
                        aliquotaCofins: impostos.cofins,
                        aliquotaIcms: impostos.icms,
                        thcBaseProcesso: thc_capataziaBase,
                        despesasComponentes: despesasInfo.componentes,
                        mva,
                        icmsStPercent: icms_st_percent,
                        vlrIcmsSt,
                        valorIcmsSt: vlrIcmsSt,
                        custoUnitarioFinal,
                        custoTotalFinal,
                        despesaDesembaraco,
                        despesaDesembaracoParte1: desp_desenbaraco_parte_1,
                        despesaDesembaracoParte2: desp_desenbaraco_parte_2,
                        despesaDesembaracoDetalhes: {
                            camposExternos: camposExternos.reduce((acc, campo) => {
                                const valorCampo = MoneyUtils.parseMoney($(`#${campo}`).val()) || 0;
                                const valorDistribuido = window.valoresBrutosCamposExternos[campo]?.[rowId] ?? (valorCampo * fatorVlrFob_AX);
                                acc[campo] = valorDistribuido;
                                return acc;
                            }, {}),
                            multa: multaDesp,
                            taxaDef: taxa_def_desp,
                            taxaSiscomex: taxa_siscomex_desp,
                            capatazia: capatazia_desp,
                            afrmm: afrmm_desp,
                            honorariosNix: honorarios_nix_desp
                        }
                    });

                    // Armazenar valores brutos (sem arredondamento) para uso nos totalizadores
                    if (!window.valoresBrutosPorLinha) {
                        window.valoresBrutosPorLinha = {};
                    }
                    
                    window.valoresBrutosPorLinha[rowId] = {
                        quantidade: quantidadeAtual,
                        peso_liquido_total: pesoTotal,
                        fob_total_usd: fobTotal,
                        fob_total_brl: fobTotal * dolar,
                        frete_usd: freteUsdInt,
                        frete_brl: freteUsdInt * dolar,
                        seguro_usd: seguroIntUsdRow,
                        seguro_brl: seguroIntUsdRow * dolar,
                        acresc_frete_usd: acrescimoFreteUsdRow,
                        acresc_frete_brl: acrescimoFreteUsdRow * dolar,
                        vlr_crf_total: vlrCrfTotal,
                        vlr_crf_unit: vlrCrfUnit,
                        service_charges: serviceChargesRowAtualUSD,
                        service_charges_brl: serviceChargesRowAtualUSD * dolar,
                        thc_usd: thcRow / dolar,
                        thc_brl: thcRow,
                        valor_aduaneiro_usd: vlrAduaneiroUsd,
                        valor_aduaneiro_brl: vlrAduaneiroBrl,
                        valor_ii: totais.vlrII,
                        base_ipi: totais.bcIpi,
                        valor_ipi: totais.vlrIpi,
                        base_pis_cofins: totais.bcPisCofins,
                        valor_pis: totais.vlrPis,
                        valor_cofins: totais.vlrCofins,
                        despesa_aduaneira: despesaAduaneira,
                        base_icms_sem_reducao: bcIcmsSReducao,
                        valor_icms_sem_reducao: vlrIcmsSReducao,
                        base_icms_reduzido: bcImcsReduzido,
                        valor_icms_reduzido: vlrIcmsReduzido,
                        valor_total_nf: totais.vlrTotalProdutoNf,
                        valor_total_nf_sem_icms_st: totais.vlrTotalNfSemIcms,
                        base_icms_st: base_icms_st,
                        valor_icms_st: vlrIcmsSt,
                        valor_total_nf_com_icms_st: totais.vlrTotalNfComIcms,
                        multa: multaDesp,
                        multa_complem: getNacionalizacaoAtual() === 'santa_catarina' 
                            ? obterMultaComplementarPorAdicaoItemProduto(rowId) 
                            : (MoneyUtils.parseMoney($(`#multa_complem-${rowId}`).val()) || 0),
                        dif_impostos: getNacionalizacaoAtual() === 'santa_catarina' 
                            ? obterDiferencaImpostosPorAdicaoItemProduto(rowId) 
                            : (MoneyUtils.parseMoney($(`#dif_impostos-${rowId}`).val()) || 0),
                        tx_def_li: (() => {
                            const nacionalizacaoAtual = getNacionalizacaoAtual();
                            if (nacionalizacaoAtual === 'mato_grosso') {
                                // Para Mato Grosso: usar valor distribuído do cabeçalho
                                if (window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['tx_def_li'] && 
                                    window.valoresBrutosCamposExternos['tx_def_li'][rowId] !== undefined) {
                                    return window.valoresBrutosCamposExternos['tx_def_li'][rowId];
                                }
                                return MoneyUtils.parseMoney($(`#tx_def_li-${rowId}`).val()) || 0;
                            } else {
                                // Para outras nacionalizações: usar valor calculado como porcentagem
                                return taxa_def_desp;
                            }
                        })(),
                        taxa_siscomex: taxa_siscomex_desp,
                        outras_taxas_agente: (() => {
                            // Usar valor distribuído se disponível, caso contrário usar valor do input da linha
                            if (window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['outras_taxas_agente'] && 
                                window.valoresBrutosCamposExternos['outras_taxas_agente'][rowId] !== undefined) {
                                return window.valoresBrutosCamposExternos['outras_taxas_agente'][rowId];
                            }
                            return MoneyUtils.parseMoney($(`#outras_taxas_agente-${rowId}`).val()) || 0;
                        })(),
                        liberacao_bl: (() => {
                            const valorCampo = MoneyUtils.parseMoney($(`#liberacao_bl`).val()) || 0;
                            const valorDistribuido = window.valoresBrutosCamposExternos['liberacao_bl']?.[rowId] ?? (valorCampo * fatorVlrFob_AX);
                            return MoneyUtils.parseMoney($(`#liberacao_bl-${rowId}`).val()) || valorDistribuido;
                        })(),
                        desconsolidacao: MoneyUtils.parseMoney($(`#desconsolidacao-${rowId}`).val()) || 0,
                        isps_code: MoneyUtils.parseMoney($(`#isps_code-${rowId}`).val()) || 0,
                        handling: MoneyUtils.parseMoney($(`#handling-${rowId}`).val()) || 0,
                        capatazia: capatazia_desp,
                        tx_correcao_lacre: MoneyUtils.parseMoney($(`#tx_correcao_lacre-${rowId}`).val()) || 0,
                        afrmm: afrmm_desp,
                        armazenagem_sts: MoneyUtils.parseMoney($(`#armazenagem_sts-${rowId}`).val()) || 0,
                        armazenagem_porto: MoneyUtils.parseMoney($(`#armazenagem_porto-${rowId}`).val()) || 0,
                        frete_dta_sts_ana: MoneyUtils.parseMoney($(`#frete_dta_sts_ana-${rowId}`).val()) || 0,
                        frete_sts_cgb: MoneyUtils.parseMoney($(`#frete_sts_cgb-${rowId}`).val()) || 0,
                        diarias: MoneyUtils.parseMoney($(`#diarias-${rowId}`).val()) || 0,
                        frete_rodoviario: MoneyUtils.parseMoney($(`#frete_rodoviario-${rowId}`).val()) || 0,
                        dif_frete_rodoviario: MoneyUtils.parseMoney($(`#dif_frete_rodoviario-${rowId}`).val()) || 0,
                        sda: MoneyUtils.parseMoney($(`#sda-${rowId}`).val()) || 0,
                        rep_sts: MoneyUtils.parseMoney($(`#rep_sts-${rowId}`).val()) || 0,
                        armaz_cgb: MoneyUtils.parseMoney($(`#armaz_cgb-${rowId}`).val()) || 0,
                        rep_cgb: MoneyUtils.parseMoney($(`#rep_cgb-${rowId}`).val()) || 0,
                        demurrage: MoneyUtils.parseMoney($(`#demurrage-${rowId}`).val()) || 0,
                        tx_def_li: (() => {
                            const nacionalizacaoAtual = getNacionalizacaoAtual();
                            if (nacionalizacaoAtual === 'mato_grosso') {
                                // Para Mato Grosso: usar valor distribuído do cabeçalho
                                if (window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['tx_def_li'] && 
                                    window.valoresBrutosCamposExternos['tx_def_li'][rowId] !== undefined) {
                                    return window.valoresBrutosCamposExternos['tx_def_li'][rowId];
                                }
                                return MoneyUtils.parseMoney($(`#tx_def_li-${rowId}`).val()) || 0;
                            } else {
                                // Para outras nacionalizações: usar valor calculado como porcentagem
                                return taxa_def_desp;
                            }
                        })(),
                        rep_porto: MoneyUtils.parseMoney($(`#rep_porto-${rowId}`).val()) || 0,
                        armaz_ana: MoneyUtils.parseMoney($(`#armaz_ana-${rowId}`).val()) || 0,
                        lavagem_container: MoneyUtils.parseMoney($(`#lavagem_container-${rowId}`).val()) || 0,
                        rep_anapolis: MoneyUtils.parseMoney($(`#rep_anapolis-${rowId}`).val()) || 0,
                        correios: MoneyUtils.parseMoney($(`#correios-${rowId}`).val()) || 0,
                        li_dta_honor_nix: MoneyUtils.parseMoney($(`#li_dta_honor_nix-${rowId}`).val()) || 0,
                        honorarios_nix: honorarios_nix_desp,
                        desp_desenbaraco: despesaDesembaraco,
                        diferenca_cambial_frete: diferenca_cambial_frete,
                        diferenca_cambial_fob: diferenca_cambial_fob,
                        opcional_1_valor: MoneyUtils.parseMoney($(`#opcional_1_valor-${rowId}`).val()) || 0,
                        opcional_2_valor: MoneyUtils.parseMoney($(`#opcional_2_valor-${rowId}`).val()) || 0,
                        custo_unitario_final: custoUnitarioFinal,
                        custo_total_final: custoTotalFinal,
                        dez_porcento: dezPorcento,
                        custo_com_margem: custoComMargem,
                        vlr_ipi_mg: vlrIpiMg,
                        vlr_icms_mg: vlrIcmsMg,
                        pis_mg: pisMg,
                        cofins_mg: cofinsMg,
                        custo_total_final_credito: custoTotalFinalCredito,
                        custo_unit_credito: custoUnitCredito,
                        bc_icms_st_mg: nacionalizacaoCusto === 'mato_grosso' ? (window.valoresBrutosPorLinha[rowId]?.bc_icms_st_mg || 0) : 0,
                        mva_mg: nacionalizacaoCusto === 'mato_grosso' ? (MoneyUtils.parsePercentage($(`#mva_mg-${rowId}`).val()) || 0) : 0,
                        icms_st_mg: nacionalizacaoCusto === 'mato_grosso' ? (MoneyUtils.parsePercentage($(`#icms_st_mg-${rowId}`).val()) || 0) : 0,
                        vlr_icms_st_mg: nacionalizacaoCusto === 'mato_grosso' ? (window.valoresBrutosPorLinha[rowId]?.vlr_icms_st_mg || 0) : 0,
                        custo_total_c_icms_st: nacionalizacaoCusto === 'mato_grosso' ? (window.valoresBrutosPorLinha[rowId]?.custo_total_c_icms_st || 0) : 0,
                        custo_unit_c_icms_st: nacionalizacaoCusto === 'mato_grosso' ? (window.valoresBrutosPorLinha[rowId]?.custo_unit_c_icms_st || 0) : 0
                    };

                    atualizarCampos(rowId, {
                        pesoLiqUnit: MoneyUtils.parseMoney($(`#peso_liquido_unitario-${rowId}`).val()),
                        fobTotal,
                        dolar,
                        freteUsdInt,
                        seguroIntUsdRow,
                        acrescimoFreteUsdRow,
                        thcRow,
                        vlrAduaneiroUsd,
                        vlrAduaneiroBrl,
                        impostos,
                        despesas,
                        bcIcmsSReducao,
                        vlrIcmsSReducao,
                        bcImcsReduzido,
                        vlrIcmsReduzido,
                        totais,
                        fatorVlrFob_AX,
                        fatorTaxaSiscomex_AY,
                        taxaSisComex,
                        vlrIcmsSt,
                        base_icms_st,
                        fatorPesoRow,
                        fobTotalGeral: fobTotalGeralAtualizado, 
                        fobUnitario,
                        diferenca_cambial_frete,
                        diferenca_cambial_fob,
                        custoUnitarioFinal: custoUnitarioFinal,
                        custoTotalFinal: custoTotalFinal
                    });
                }
            });

            atualizarCamposCabecalho();
            atualizarTotaisGlobais(fobTotalGeralAtualizado, dolar); 
            atualizarFatoresFob(); 
            atualizarMultaProdutosPorMulta(); // Atualiza multa_complem e dif_impostos para Santa Catarina
            atualizarTotalizadores();
            calcularValoresCPT();
            calcularValoresCIF();
            setDebugGlobals({
                ...globaisProcesso,
                fobTotalProcesso: fobTotalGeralAtualizado,
                nacionalizacao: getNacionalizacaoAtual()
            });
            

            setTimeout(() => {
                isRecalculating = false;
            }, 50);
        }

        function atualizarCamposCambial() {
            const campos = getCamposDiferencaCambial()
            const lengthTable = $('.linhas-input').length
            const totalPesoLiq = calcularPesoTotal();
            const fobTotalGeral = calcularFobTotalGeral();
            const moedasOBject = $('#cotacao_moeda_processo').val() ? JSON.parse($(
                '#cotacao_moeda_processo').val()) : JSON.parse($('#dolarHoje').val())
            const moedaDolar = moedasOBject['USD'].venda
            const dolar = MoneyUtils.parseMoney(moedaDolar);
            let valorFreteInternacionalDolar = 0;
            let valorFreteInternacional = MoneyUtils.parseMoney($('#frete_internacional').val());
            const moedaFrete = $('#frete_internacional_moeda').val();

            if (moedaFrete != 'USD') {
                let cotacaoProcesso = getCotacaoesProcesso();
                let cotacaoMoedaFloat = MoneyUtils.parseMoney($('#cotacao_frete_internacional').val());
                let moeda = $('#moeda_processo').val();
                let cotacaoUSD = cotacaoProcesso['USD']?.venda ?? 1;
                let moedaEmUSD = cotacaoMoedaFloat / cotacaoUSD;
                valorFreteInternacionalDolar = valorFreteInternacional * moedaEmUSD;
            } else {
                valorFreteInternacionalDolar = valorFreteInternacional;
            }

            for (let rowId = 0; rowId < lengthTable; rowId++) {
                const {
                    pesoTotal,
                    fobUnitario,
                    quantidade
                } = obterValoresBase(rowId);
                const fatorPesoRow = recalcularFatorPeso(totalPesoLiq, rowId);
                const freteUsdInt = valorFreteInternacionalDolar * fatorPesoRow;

                const fobTotal = fobUnitario * quantidade;

                const fatorVlrFob_AX = fobTotal / fobTotalGeral;
                const dif_cambial_frete_processo = MoneyUtils.parseMoney($('#diferenca_cambial_frete').val()) || 0;
                const dif_cambial_fob_processo = MoneyUtils.parseMoney($('#diferenca_cambial_fob').val()) || 0;
                let diferenca_cambial_frete = (freteUsdInt * dif_cambial_frete_processo) - (freteUsdInt *
                    dolar);
                diferenca_cambial_frete = validarDiferencaCambialFrete(diferenca_cambial_frete);
                
                // Calcular diferenca_cambial_fob conforme nacionalização
                const nacionalizacaoCambial = getNacionalizacaoAtual();
                let diferenca_cambial_fob;
                if (nacionalizacaoCambial === 'mato_grosso') {
                    // Para Mato Grosso: (diferenca_cambial_fob_cabecalho * fator_vlr_fob) - (fob_total_brl + frete_brl + seguro_brl)
                    const fobTotalBrl = fobTotal * dolar;
                    const freteBrl = freteUsdInt * dolar;
                    // Calcular seguro proporcional ao FOB
                    const seguroIntUsdRow = calcularSeguro(fobTotal, fobTotalGeral);
                    const seguroBrl = seguroIntUsdRow * dolar;
                    diferenca_cambial_fob = (fatorVlrFob_AX * dif_cambial_fob_processo) - (fobTotalBrl + freteBrl + seguroBrl);
                } else {
                    diferenca_cambial_fob = (fatorVlrFob_AX * dif_cambial_fob_processo) - (fobTotal * dolar);
                }


                if (diferenca_cambial_frete === 0 || isNaN(diferenca_cambial_frete) || !isFinite(diferenca_cambial_frete) || diferenca_cambial_frete < 0) {
                    $(`#diferenca_cambial_frete-${rowId}`).val('');
                } else {
                    $(`#diferenca_cambial_frete-${rowId}`).val(MoneyUtils.formatMoney(diferenca_cambial_frete, 2));
                }
                $(`#diferenca_cambial_fob-${rowId}`).val(MoneyUtils.formatMoney(diferenca_cambial_fob, 2));


            }
        }

        const CAMPOS_EXCLUSIVOS_ANAPOLIS = ['rep_anapolis', 'correios']; 
        const CAMPO_CORRECAO_LACRE = 'tx_correcao_lacre';
        const CAMPOS_EXTERNOS_BASE = [
            'outras_taxas_agente', 'liberacao_bl', 'desconsolidacao', 'isps_code', 'handling', 'capatazia',
            'afrmm', 'armazenagem_sts', 'frete_dta_sts_ana', 'sda', 'rep_sts', 'armaz_ana',
            'tx_correcao_lacre', 'lavagem_container', 'rep_anapolis', 'correios', 'li_dta_honor_nix', 'honorarios_nix' 
        ];

        // Ordem específica para Anápolis
        const CAMPOS_EXTERNOS_ANAPOLIS = [
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
            'desp_anapolis',
            'rep_anapolis',
            'correios',
            'li_dta_honor_nix',
            'honorarios_nix'
        ];

        // Ordem específica para Santa Catarina
        // multa_complem e dif_impostos são calculados automaticamente (não devem ser rateados)
        const CAMPOS_EXTERNOS_SANTA_CATARINA = [
            'outras_taxas_agente',
            'liberacao_bl',
            'desconsolidacao',
            'isps_code',
            'handling',
            'capatazia',
            'afrmm',
            'armazenagem_porto',
            'frete_rodoviario',
            'dif_frete_rodoviario',
            'sda',
            'rep_porto',
            'tx_correcao_lacre',
            'li_dta_honor_nix',
            'honorarios_nix'
        ];

        // Ordem específica para Mato Grosso
        const CAMPOS_EXTERNOS_MATO_GROSSO = [
            'outras_taxas_agente',
            'liberacao_bl',
            'desconsolidacao',
            'isps_code',
            'handling',
            'capatazia',
            'afrmm',
            'armazenagem_sts',
            'frete_sts_cgb',
            'diarias',
            'sda',
            'rep_sts',
            'armaz_cgb',
            'rep_cgb',
            'demurrage',
            'tx_def_li',
            'li_dta_honor_nix',
            'honorarios_nix'
        ];

        function getNacionalizacaoAtual() {
            const valor = $('#nacionalizacao').val();
            return (valor ? valor.toLowerCase() : 'outros');
        }

        function getCamposExternos() {
            const nacionalizacao = getNacionalizacaoAtual();
            
            // Se for Anápolis, usar ordem específica
            if (nacionalizacao === 'anapolis') {
                return CAMPOS_EXTERNOS_ANAPOLIS;
            }
            
            // Se for Santa Catarina, usar ordem específica
            if (nacionalizacao === 'santa_catarina') {
                return CAMPOS_EXTERNOS_SANTA_CATARINA;
            }
            
            // Se for Mato Grosso, usar ordem específica
            if (nacionalizacao === 'mato_grosso') {
                return CAMPOS_EXTERNOS_MATO_GROSSO;
            }
            
            // Para outros tipos, filtrar da ordem base
            return CAMPOS_EXTERNOS_BASE.filter((campo) => {
                if (campo === CAMPO_CORRECAO_LACRE) {
                    return nacionalizacao === 'santos';
                }
                if (CAMPOS_EXCLUSIVOS_ANAPOLIS.includes(campo)) {
                    return nacionalizacao !== 'santos';
                }
                return true;
            });
        }

        function limparCamposEspecificos(campos) {
            campos.forEach((campo) => {
                const cabecalho = $(`#${campo}`);
                if (cabecalho.length) {
                    cabecalho.val('');
                }
                $(`[id^="${campo}-"]`).each(function() {
                    $(this).val('');
                });
            });
        }

        function toggleColunas(selector, mostrar) {
            if (mostrar) {
                $(selector).show();
            } else {
                $(selector).hide();
            }
        }

        function calcularValoresCPT() {
            const nacionalizacao = getNacionalizacaoAtual();
            const tipoProcesso = '{{ $tipoProcesso ?? "maritimo" }}';
            
            // Só calcular se for Anápolis ou Santa Catarina e processo marítimo
            if ((nacionalizacao !== 'anapolis' && nacionalizacao !== 'santa_catarina') || tipoProcesso !== 'maritimo') {
                $('#campos-cpt-anapolis').hide();
                return;
            }
            
            // Verificar se os campos existem
            if ($('#campos-cpt-anapolis').length === 0) {
                return;
            }
            
            // Mostrar os campos
            $('#campos-cpt-anapolis').show();
            
            // Obter valores totais do processo
            const rows = $('#productsBody tr:not(.separador-adicao)');
            
            // Calcular valor total FOB USD
            let valorTotalFobUsd = 0;
            rows.each(function() {
                const rowId = this.id.replace('row-', '');
                const fobTotalUsd = MoneyUtils.parseMoney($(`#fob_total_usd-${rowId}`).val()) || 0;
                valorTotalFobUsd += fobTotalUsd;
            });
            
            // Calcular valor total Service Charges USD
            let valorTotalServiceChargesUsd = 0;
            const moedaServiceCharges = $('#service_charges_moeda').val();
            if (moedaServiceCharges && moedaServiceCharges !== 'USD') {
                rows.each(function() {
                    const rowId = this.id.replace('row-', '');
                    const serviceChargesUsd = MoneyUtils.parseMoney($(`#service_charges-${rowId}`).val()) || 0;
                    valorTotalServiceChargesUsd += serviceChargesUsd;
                });
            } else {
                valorTotalServiceChargesUsd = MoneyUtils.parseMoney($('#service_charges_usd').val()) || 0;
            }
            
            // Obter frete internacional total USD
            const freteInternacionalTotalUsd = MoneyUtils.parseMoney($('#frete_internacional_usd').val()) || 0;
            
            // Obter seguro internacional total USD
            const seguroInternacionalTotalUsd = MoneyUtils.parseMoney($('#seguro_internacional_usd').val()) || 0;
            
            // Obter acréscimo frete dolar
            const acrescimoFreteDolar = MoneyUtils.parseMoney($('#acrescimo_frete_usd').val()) || 0;
            

            const valorCptUsd = valorTotalFobUsd + valorTotalServiceChargesUsd + freteInternacionalTotalUsd + 
                              seguroInternacionalTotalUsd + acrescimoFreteDolar;
            
            // Obter cotação do dólar do processo
            const cotacoesProcesso = getCotacaoesProcesso();
            let cotacaoDolarProcesso = 1;
            if (cotacoesProcesso && cotacoesProcesso['USD'] && cotacoesProcesso['USD'].venda) {
                cotacaoDolarProcesso = cotacoesProcesso['USD'].venda;
            } else {
                // Tentar obter do campo dolarHoje ou display_cotacao
                const dolarHoje = $('#dolarHoje').val();
                if (dolarHoje) {
                    try {
                        const dolarObj = JSON.parse(dolarHoje);
                        if (dolarObj['USD'] && dolarObj['USD'].venda) {
                            cotacaoDolarProcesso = dolarObj['USD'].venda;
                        }
                    } catch (e) {
                        // Se não conseguir parsear, usar 1 como padrão
                    }
                }
            }
            
            // Calcular Valor CPT BRL
            const valorCptBrl = valorCptUsd * cotacaoDolarProcesso;
            
            $('#valor_cpt_usd').val(MoneyUtils.formatMoney(valorCptUsd, 2));
            $('#valor_cpt_brl').val(MoneyUtils.formatMoney(valorCptBrl, 2));
        }

        function calcularValoresCIF() {
            const nacionalizacao = getNacionalizacaoAtual();
            const tipoProcesso = '{{ $tipoProcesso ?? "maritimo" }}';
            
            // Só calcular se for Mato Grosso e processo marítimo
            if (nacionalizacao !== 'mato_grosso' || tipoProcesso !== 'maritimo') {
                $('#campos-cif-mato-grosso').hide();
                return;
            }
            
            // Verificar se os campos existem
            if ($('#campos-cif-mato-grosso').length === 0) {
                return;
            }
            
            // Mostrar os campos
            $('#campos-cif-mato-grosso').show();
            
            // Obter valores totais do processo
            const rows = $('#productsBody tr:not(.separador-adicao)');
            
            // Calcular valor total FOB USD
            let valorTotalFobUsd = 0;
            rows.each(function() {
                const rowId = this.id.replace('row-', '');
                const fobTotalUsd = MoneyUtils.parseMoney($(`#fob_total_usd-${rowId}`).val()) || 0;
                valorTotalFobUsd += fobTotalUsd;
            });
            
            // Obter frete internacional total USD
            const freteInternacionalTotalUsd = MoneyUtils.parseMoney($('#frete_internacional_usd').val()) || 0;
            
            // Obter seguro internacional total USD
            const seguroInternacionalTotalUsd = MoneyUtils.parseMoney($('#seguro_internacional_usd').val()) || 0;
            
            // Obter acréscimo frete dolar
            const acrescimoFreteDolar = MoneyUtils.parseMoney($('#acrescimo_frete_usd').val()) || 0;
            
            // CIF = FOB Total + Frete Internacional + Seguro + Acréscimo Frete
            const valorCifUsd = valorTotalFobUsd + freteInternacionalTotalUsd + seguroInternacionalTotalUsd + acrescimoFreteDolar;
            
            // Obter cotação do dólar do processo
            const cotacoesProcesso = getCotacaoesProcesso();
            let cotacaoDolarProcesso = 1;
            if (cotacoesProcesso && cotacoesProcesso['USD'] && cotacoesProcesso['USD'].venda) {
                cotacaoDolarProcesso = cotacoesProcesso['USD'].venda;
            } else {
                // Tentar obter do campo dolarHoje ou display_cotacao
                const dolarHoje = $('#dolarHoje').val();
                if (dolarHoje) {
                    try {
                        const dolarObj = JSON.parse(dolarHoje);
                        if (dolarObj['USD'] && dolarObj['USD'].venda) {
                            cotacaoDolarProcesso = dolarObj['USD'].venda;
                        }
                    } catch (e) {
                        // Se não conseguir parsear, usar 1 como padrão
                    }
                }
            }
            
            // Calcular Valor CIF BRL
            const valorCifBrl = valorCifUsd * cotacaoDolarProcesso;
            
            $('#valor_cif_usd').val(MoneyUtils.formatMoney(valorCifUsd, 2));
            $('#valor_cif_brl').val(MoneyUtils.formatMoney(valorCifBrl, 2));
        }

        function atualizarVisibilidadeNacionalizacao(options = {}) {
            const { recalcular = false } = options;
            const nacionalizacao = getNacionalizacaoAtual();
            const mostrarCamposAnapolis = nacionalizacao !== 'santos';
            const mostrarTxCorrecao = nacionalizacao === 'santos' || nacionalizacao === 'santa_catarina';


            $('th[data-campo="tx_correcao_lacre"]').each(function() {
                if (mostrarTxCorrecao) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            $('th[data-campo="rep_anapolis"], th[data-campo="correios"]').each(function() { 
                if (mostrarCamposAnapolis) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });


            $('td[data-campo="tx_correcao_lacre"]').each(function() {
                if (mostrarTxCorrecao) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            $('td[data-campo="rep_anapolis"], td[data-campo="correios"]').each(function() { 
                if (mostrarCamposAnapolis) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });


            toggleColunas('.coluna-anapolis', mostrarCamposAnapolis);
            toggleColunas('.coluna-tx-correcao-lacre', mostrarTxCorrecao);

            if (mostrarCamposAnapolis) {
                limparCamposEspecificos([CAMPO_CORRECAO_LACRE]);
            } else {
                limparCamposEspecificos(CAMPOS_EXCLUSIVOS_ANAPOLIS);
            }
            
            // Atualizar visibilidade e calcular valores CPT
            calcularValoresCPT();
            calcularValoresCIF();
            
            if (recalcular) {
                debouncedRecalcular();
            }
        }

        const processoId = {{ $processo->id }};
        let salvandoAutomaticamenteNacionalizacao = false;

        function aguardarConclusaoRecalculo() {
            return new Promise((resolve) => {
                const checar = () => {
                    if (!isRecalculating) {
                        resolve();
                    } else {
                        setTimeout(checar, 150);
                    }
                };
                checar();
            });
        }

        async function salvarProdutosAutomaticamente() {
            if (typeof SalvamentoProdutosFases !== 'function') {
                throw new Error('Rotina de salvamento não disponível.');
            }
            const salvamento = new SalvamentoProdutosFases(processoId);
            return await salvamento.salvarProdutosEmFases(true);
        }

        async function processarMudancaNacionalizacao(selectElement) {
            if (salvandoAutomaticamenteNacionalizacao) {
                return;
            }
            salvandoAutomaticamenteNacionalizacao = true;

            const $select = $(selectElement);
            const valorAnterior = $select.data('valor-anterior') || $select.val();
            const novoValor = $select.val();
            $select.prop('disabled', true);

            try {

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'PUT');
                formData.append('nacionalizacao', novoValor);

                const response = await fetch('{{ route("update.processo", $processo->id) }}', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Não foi possível salvar a nacionalização.');
                }


                window.location.reload();
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao atualizar nacionalização',
                    text: error.message || 'Verifique os dados e tente novamente.'
                });
                $select.prop('disabled', false);
                if (valorAnterior !== undefined) {
                    $select.val(valorAnterior);
                }
                salvandoAutomaticamenteNacionalizacao = false;
            }
        }

        function getCamposDiferencaCambial() {
            return [
                'diferenca_cambial_frete', 'diferenca_cambial_fob'
            ];
        }

        function obterValoresBase(rowId) {
            let moedaProcesso = $('#moeda_processo').val();
            let fobUnitario;
            let fobUnitarioMoedaEstrangeira;

            let pesoTotal = MoneyUtils.parseMoney($(`#peso_liquido_total-${rowId}`).val()) || 0;
            let quantidade = MoneyUtils.parseMoney($(`#quantidade-${rowId}`).val()) || 0;

            if (moedaProcesso && moedaProcesso !== 'USD') {

                fobUnitarioMoedaEstrangeira = MoneyUtils.parseMoney($(`#fob_unit_moeda_estrangeira-${rowId}`).val()) || 0;

                if (fobUnitarioMoedaEstrangeira > 0) {

                    fobUnitario = converterMoedaProcessoParaUSD(fobUnitarioMoedaEstrangeira, moedaProcesso);
                } else {

                    fobUnitario = MoneyUtils.parseMoney($(`#fob_unit_usd-${rowId}`).val()) || 0;

                    if (fobUnitario > 0) {
                        fobUnitarioMoedaEstrangeira = converterUSDParaMoedaProcesso(fobUnitario, moedaProcesso);
                    }
                }
            } else {

                fobUnitario = MoneyUtils.parseMoney($(`#fob_unit_usd-${rowId}`).val()) || 0;
                fobUnitarioMoedaEstrangeira = fobUnitario; 
            }

            if (isNaN(fobUnitario)) fobUnitario = 0;
            if (isNaN(fobUnitarioMoedaEstrangeira)) fobUnitarioMoedaEstrangeira = 0;
            if (isNaN(pesoTotal)) pesoTotal = 0;
            if (isNaN(quantidade)) quantidade = 0;

            return {
                pesoTotal: pesoTotal,
                fobUnitario: fobUnitario,
                fobUnitarioMoedaEstrangeira: fobUnitarioMoedaEstrangeira,
                quantidade: quantidade
            };
        }

        function calcularPesoTotal() {
            let total = 0;
            $('.pesoLiqTotal').each(function() {
                total += MoneyUtils.parseMoney($(this).val());
            });
            return total;
        }
        $(document).on('keyup', '.fobUnitarioMoedaEstrangeira', function(e) {
            const rowId = $(this).data('row');
            const valor = $(this).val();


            if (valor && valor.trim() !== '' && !isNaN(MoneyUtils.parseMoney(valor))) {

                clearTimeout(window.fobMoedaTimeout);
                window.fobMoedaTimeout = setTimeout(() => {
                    debouncedRecalcular();
                }, 500);
            }
        });


        $(document).on('keyup', '.fobUnitario', function(e) {
            const rowId = $(this).data('row');
            const valor = $(this).val();

            if (valor && valor.trim() !== '' && !isNaN(MoneyUtils.parseMoney(valor))) {
                clearTimeout(window.fobUsdTimeout);
                window.fobUsdTimeout = setTimeout(() => {
                    debouncedRecalcular();
                }, 500);
            }
        });

        $(document).on('change', '.fobUnitarioMoedaEstrangeira, .fobUnitario', function() {
            debouncedRecalcular();
        });

        function getColunasFOBCondicionais(newIndex, moedaProcesso) {
            if (moedaProcesso && moedaProcesso !== 'USD') {
                return `
            <td>
                <input data-row="${newIndex}" type="text" 
                    class="form-control fobUnitarioMoedaEstrangeira moneyReal7"
                    name="produtos[${newIndex}][fob_unit_moeda_estrangeira]" 
                    id="fob_unit_moeda_estrangeira-${newIndex}" value="">
            </td>
            <td>
                <input data-row="${newIndex}" type="text" 
                    class="form-control moneyReal7" readonly
                    name="produtos[${newIndex}][fob_total_moeda_estrangeira]" 
                    id="fob_total_moeda_estrangeira-${newIndex}" value="">
            </td>
        `;
            } else {
                return `
            <td>
                <input data-row="${newIndex}" type="text" 
                    class="form-control fobUnitario moneyReal7"
                    name="produtos[${newIndex}][fob_unit_usd]" 
                    id="fob_unit_usd-${newIndex}" value="">
            </td>
        `;
            }
        }
        $(document).on('change keyup', '.fobUnitarioMoedaEstrangeira, .fobUnitario', function(e) {
            const rowId = $(this).data('row');
            if (rowId != null) {

                setTimeout(() => {
                    const {
                        pesoTotal,
                        fobUnitario,
                        quantidade,
                        fobUnitarioMoedaEstrangeira
                    } = obterValoresBase(rowId);
                    const fobTotal = fobUnitario * quantidade;

                    let cotacaoProcesso = getCotacaoesProcesso();


                    let cotacaoUSD = cotacaoProcesso['USD']?.venda ?? 1;
                    atualizarCamposFOB(rowId, {
                        fobUnitarioMoedaEstrangeira: fobUnitarioMoedaEstrangeira,
                        fobUnitario: fobUnitario,
                        fobTotal: fobTotal,
                        dolar: cotacaoUSD
                    });
                }, 100);
            }
        });

        function recalcularFatorPeso(totalPeso, currentRowId) {
            let fator = 0;
            $('.pesoLiqTotal').each(function() {
                const rowId = $(this).data('row');
                const valor = MoneyUtils.parseMoney($(this).val());
                const fatorLinha = valor / (totalPeso || 1);
                $(`#fator_peso-${rowId}`).val(MoneyUtils.formatMoney(fatorLinha, 6));
                if (rowId == currentRowId) fator = fatorLinha;
            });
            return fator;
        }

        function calcularFobTotalGeral() {
            let total = 0;
            $('[id^="fob_total_usd-"]').each(function() {
                total += MoneyUtils.parseMoney($(this).val()) || 0;
            });
            return total || 0;
        }

        function atualizarPesoLiquidoTotal(pesoTotal) {

            const pesoLiquidoElement = $('#peso_liquido');
            if (pesoLiquidoElement.length > 0) {
                pesoLiquidoElement.val(MoneyUtils.formatMoney(pesoTotal, 4));
            }
        }

        function atualizarTotaisGlobais(fobTotalGeral, dolar) {
            $('#fobTotalProcesso').text(MoneyUtils.formatMoney(fobTotalGeral));
            $('#fobTotalProcessoReal').text(MoneyUtils.formatMoney(fobTotalGeral * dolar));
        }

        function calcularSeguro(fobTotal, fobGeral) {
            if (fobGeral == 0) {
                return 0;
            }

            const total = MoneyUtils.parseMoney($('#seguro_internacional').val());
            const moedaSeguro = $('#seguro_internacional_moeda').val();
            let valorSeguroInternacionalDolar = 0;

            if (moedaSeguro && moedaSeguro !== 'USD') {
                let cotacaoProcesso = getCotacaoesProcesso();


                let cotacaoMoedaSeguro = MoneyUtils.parseMoney($('#cotacao_seguro_internacional').val());
                let cotacaoUSD = cotacaoProcesso['USD']?.venda ?? 1;


                if (!cotacaoMoedaSeguro && cotacaoProcesso[moedaSeguro]) {
                    cotacaoMoedaSeguro = cotacaoProcesso[moedaSeguro].venda;
                }

                if (cotacaoMoedaSeguro) {
                    let moedaEmUSD = cotacaoMoedaSeguro / cotacaoUSD;
                    valorSeguroInternacionalDolar = total * moedaEmUSD;
                } else {

                    valorSeguroInternacionalDolar = total;
                }
            } else {
                valorSeguroInternacionalDolar = total;
            }

            return (valorSeguroInternacionalDolar / fobGeral) * fobTotal;
        }

        function calcularAcrescimoFrete(fobTotal, fobGeral, dolar) {
            if (fobGeral == 0) {
                return 0;
            }

            const base = MoneyUtils.parseMoney($('#acrescimo_frete').val());
            const moedaFrete = $('#acrescimo_frete_moeda').val();
            let valorFreteUSD = 0;

            if (moedaFrete && moedaFrete !== 'USD') {
                let cotacaoProcesso = getCotacaoesProcesso();


                let cotacaoMoedaFrete = MoneyUtils.parseMoney($('#cotacao_acrescimo_frete').val());
                let cotacaoUSD = cotacaoProcesso['USD']?.venda ?? 1;


                if (!cotacaoMoedaFrete && cotacaoProcesso[moedaFrete]) {
                    cotacaoMoedaFrete = cotacaoProcesso[moedaFrete].venda;
                }

                if (cotacaoMoedaFrete) {
                    let moedaEmUSD = cotacaoMoedaFrete / cotacaoUSD;
                    valorFreteUSD = base * moedaEmUSD;
                } else {

                    valorFreteUSD = base;
                }
            } else {
                valorFreteUSD = base;
            }

            return (valorFreteUSD / fobGeral) * fobTotal;
        }


        function calcularValorAduaneiro(fob, frete, acrescimo, seguro, thc, dolar, vlrCrfTotal = 0, serviceCharges = 0) {

            const parseSafe = (value, defaultValue = 0) => {
                if (value === null || value === undefined) return defaultValue;

                if (typeof value === 'number') {
                    return isNaN(value) || !isFinite(value) ? defaultValue : value;
                }

                const num = parseFloat(value);
                return isNaN(num) || !isFinite(num) ? defaultValue : num;
            };



            const safeAcrescimo = parseSafe(acrescimo); 
            const safeSeguro = parseSafe(seguro); 
            const safeThc = parseSafe(thc); 
            const safeDolar = parseSafe(dolar, 1);
            const safeVlrCrfTotal = parseSafe(vlrCrfTotal); 
            const safeServiceCharges = parseSafe(serviceCharges); 



            const thcUsd = safeDolar > 0 ? safeThc / safeDolar : 0;


            return safeVlrCrfTotal + safeServiceCharges + safeAcrescimo + safeSeguro + thcUsd;
        }

        function calcularImpostos(rowId, base) {
            return {
                ii: $(`#ii_percent-${rowId}`).val() ? MoneyUtils.parsePercentage($(`#ii_percent-${rowId}`).val()) : 0,
                ipi: $(`#ipi_percent-${rowId}`).val() ? MoneyUtils.parsePercentage($(`#ipi_percent-${rowId}`).val()) : 0,
                pis: $(`#pis_percent-${rowId}`).val() ? MoneyUtils.parsePercentage($(`#pis_percent-${rowId}`).val()) : 0,
                cofins: $(`#cofins_percent-${rowId}`).val() ? MoneyUtils.parsePercentage($(`#cofins_percent-${rowId}`)
                    .val()) : 0,
                icms: $(`#icms_percent-${rowId}`).val() ? MoneyUtils.parsePercentage($(`#icms_percent-${rowId}`).val()) : 0
            };
        }

        function validarDiferencaCambialFrete(valor) {


            if (valor === null || valor === undefined || isNaN(valor) || !isFinite(valor) || valor < 0) {
                return 0;
            }
            return valor;
        }

        function limparNumero(valor) {
            if (valor === null || valor === undefined) {
                return '';
            }
            return valor.toString().replace(/[^0-9]/g, '');
        }

        function obterMultaPorAdicaoItemProduto(rowId) {
            const adicao = limparNumero($(`#adicao-${rowId}`).val());
            const item = limparNumero($(`#item-${rowId}`).val());
            if (!adicao || !item) {
                return 0;
            }

            let multaEncontrada = 0;
            $('#productsBodyMulta tr.linhas-input').each(function() {
                const multaRowId = $(this).attr('id') ? $(this).attr('id').replace('row-multa-', '') : null;
                if (!multaRowId) return;

                const adicaoMulta = limparNumero($(`#adicao_multa-${multaRowId}`).val());
                const itemMulta = limparNumero($(`#item_multa-${multaRowId}`).val());

                if (adicaoMulta === adicao && itemMulta === item) {
                    const ii = MoneyUtils.parseMoney($(`#ii_percent_aduaneiro_multa-${multaRowId}`).val()) || 0;
                    const ipi = MoneyUtils.parseMoney($(`#ipi_percent_aduaneiro_multa-${multaRowId}`).val()) || 0;
                    const pis = MoneyUtils.parseMoney($(`#pis_percent_aduaneiro_multa-${multaRowId}`).val()) || 0;
                    const cofins = MoneyUtils.parseMoney($(`#cofins_percent_aduaneiro_multa-${multaRowId}`).val()) || 0;
                    multaEncontrada = ii + ipi + pis + cofins;
                    return false;
                }
            });

            return multaEncontrada;
        }

        function obterMultaComplementarPorAdicaoItemProduto(rowId) {
            const adicao = limparNumero($(`#adicao-${rowId}`).val());
            const item = limparNumero($(`#item-${rowId}`).val());
            if (!adicao || !item) {
                return 0;
            }

            let valorAduaneiroMultaEncontrado = 0;
            $('#productsBodyMulta tr.linhas-input').each(function() {
                const multaRowId = $(this).attr('id') ? $(this).attr('id').replace('row-multa-', '') : null;
                if (!multaRowId) return;

                const adicaoMulta = limparNumero($(`#adicao_multa-${multaRowId}`).val());
                const itemMulta = limparNumero($(`#item_multa-${multaRowId}`).val());

                if (adicaoMulta === adicao && itemMulta === item) {
                    valorAduaneiroMultaEncontrado = MoneyUtils.parseMoney($(`#valor_aduaneiro_multa-${multaRowId}`).val()) || 0;
                    return false;
                }
            });

            return valorAduaneiroMultaEncontrado;
        }

        function obterDiferencaImpostosPorAdicaoItemProduto(rowId) {
            const adicao = limparNumero($(`#adicao-${rowId}`).val());
            const item = limparNumero($(`#item-${rowId}`).val());
            if (!adicao || !item) {
                return 0;
            }

            let somaRecalc = 0;
            $('#productsBodyMulta tr.linhas-input').each(function() {
                const multaRowId = $(this).attr('id') ? $(this).attr('id').replace('row-multa-', '') : null;
                if (!multaRowId) return;

                const adicaoMulta = limparNumero($(`#adicao_multa-${multaRowId}`).val());
                const itemMulta = limparNumero($(`#item_multa-${multaRowId}`).val());

                if (adicaoMulta === adicao && itemMulta === item) {
                    const iiRecalc = MoneyUtils.parseMoney($(`#vlr_ii_recalc_multa-${multaRowId}`).val()) || 0;
                    const ipiRecalc = MoneyUtils.parseMoney($(`#vlr_ipi_recalc_multa-${multaRowId}`).val()) || 0;
                    const pisRecalc = MoneyUtils.parseMoney($(`#vlr_pis_recalc_multa-${multaRowId}`).val()) || 0;
                    const cofinsRecalc = MoneyUtils.parseMoney($(`#vlr_cofins_recalc_multa-${multaRowId}`).val()) || 0;
                    somaRecalc += iiRecalc + ipiRecalc + pisRecalc + cofinsRecalc;
                }
            });

            return somaRecalc;
        }

        function atualizarMultaProdutoPorMulta(rowId) {
            if (getNacionalizacaoAtual() !== 'santa_catarina') {
                $(`#multa-${rowId}`).prop('readonly', false);
                return;
            }

            const multaCalculada = obterMultaPorAdicaoItemProduto(rowId);
            $(`#multa-${rowId}`).prop('readonly', true).val(MoneyUtils.formatMoney(multaCalculada, 2));
        }

        function atualizarMultaComplementarProdutoPorMulta(rowId) {
            if (getNacionalizacaoAtual() !== 'santa_catarina') {
                $(`#multa_complem-${rowId}`).prop('readonly', false);
                return;
            }

            const valorAduaneiroMulta = obterMultaComplementarPorAdicaoItemProduto(rowId);
            $(`#multa_complem-${rowId}`).prop('readonly', true).val(MoneyUtils.formatMoney(valorAduaneiroMulta, 2));
        }

        function atualizarDiferencaImpostosProdutoPorMulta(rowId) {
            if (getNacionalizacaoAtual() !== 'santa_catarina') {
                $(`#dif_impostos-${rowId}`).prop('readonly', false);
                return;
            }

            const difImpostos = obterDiferencaImpostosPorAdicaoItemProduto(rowId);
            $(`#dif_impostos-${rowId}`).prop('readonly', true).val(MoneyUtils.formatMoney(difImpostos, 2));
        }

        function atualizarMultaProdutosPorMulta() {
            if (getNacionalizacaoAtual() !== 'santa_catarina') {
                return;
            }

            $('#productsBody tr.linhas-input').each(function() {
                const rowId = this.id.toString().replace('row-', '');
                if (rowId) {
                    atualizarMultaProdutoPorMulta(rowId);
                    atualizarMultaComplementarProdutoPorMulta(rowId);
                    atualizarDiferencaImpostosProdutoPorMulta(rowId);
                }
            });
        }

        function calcularDespesas(rowId, fatorVlrFob_AX, fatorSiscomex, taxaSiscomexUnit, vlrAduaneiroBrl = null) {
            const nacionalizacao = getNacionalizacaoAtual();
            let multa = $(`#multa-${rowId}`).val() ? MoneyUtils.parseMoney($(`#multa-${rowId}`).val()) : 0;
            if (nacionalizacao === 'santa_catarina') {
                multa = obterMultaPorAdicaoItemProduto(rowId);
                $(`#multa-${rowId}`).prop('readonly', true).val(MoneyUtils.formatMoney(multa, 2));
            } else {
                $(`#multa-${rowId}`).prop('readonly', false);
            }

            if (vlrAduaneiroBrl === null) {
                vlrAduaneiroBrl = MoneyUtils.parseMoney($(`#valor_aduaneiro_brl-${rowId}`).val()) || 0;
            }
            let txDefLi;
            if (nacionalizacao === 'mato_grosso') {
                // Para Mato Grosso: usar valor rateado do cabeçalho
                const valorCampo = MoneyUtils.parseMoney($(`#tx_def_li`).val()) || 0;
                const valorDistribuido = window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['tx_def_li'] && window.valoresBrutosCamposExternos['tx_def_li'][rowId] !== undefined
                    ? window.valoresBrutosCamposExternos['tx_def_li'][rowId]
                    : (valorCampo * fatorVlrFob_AX);
                txDefLi = MoneyUtils.parseMoney($(`#tx_def_li-${rowId}`).val()) || valorDistribuido;
            } else {
                // Para outras nacionalizações: calcular como porcentagem
            const txDefLiPercent = $(`#tx_def_li-${rowId}`).val() ? MoneyUtils.parsePercentage($(`#tx_def_li-${rowId}`).val()) : 0;
                txDefLi = vlrAduaneiroBrl * txDefLiPercent;
            }
            
            const taxaSiscomex = taxaSiscomexUnit || 0;
            
            if (nacionalizacao === 'santa_catarina') {
                const despesasSantaCatarina = multa + txDefLi + taxaSiscomex;
                return {
                    total: despesasSantaCatarina,
                    componentes: {
                        multa,
                        txDefLi,
                        taxaSiscomex,
                        afrmm: 0,
                        armazenagem_sts: 0,
                        frete_dta_sts_ana: 0,
                        honorarios_nix: 0,
                        opcional1: 0,
                        opcional2: 0
                    },
                    tipoCalculo: nacionalizacao
                };
            }

            const afrmm = $(`#afrmm-${rowId}`).val() ? MoneyUtils.parseMoney($(`#afrmm-${rowId}`).val()) : 0;
            
            let armazenagem_sts = $('#armazenagem_sts-' + rowId).val() ? MoneyUtils.parseMoney($('#armazenagem_sts-' +
                    rowId).val()) :
                0;
            
            let frete_dta_sts_ana = $('#frete_dta_sts_ana-' + rowId).val() ? MoneyUtils.parseMoney($('#frete_dta_sts_ana-' +
                        rowId)
                    .val()) :
                0;
            
            const honorarios_nix_raw = $(`#honorarios_nix-${rowId}`).val();
            const honorarios_nix = honorarios_nix_raw ? MoneyUtils.parseMoney(honorarios_nix_raw) : 0;

            let despesas = multa + txDefLi + taxaSiscomex;
            if (nacionalizacao === 'santos') {
                despesas += afrmm + honorarios_nix;
            } else if (nacionalizacao === 'anapolis') {
                // Para Anápolis: Multa + tx.def.li + tx sistcomex + afrmm + armazenagem sts + frete sts/gyn + honorarios nix
                despesas += afrmm + armazenagem_sts + frete_dta_sts_ana + honorarios_nix;
            } else {
                despesas += afrmm + armazenagem_sts + frete_dta_sts_ana + honorarios_nix;
            }
            
            // Adicionar campos opcionais se checkbox marcado
            const opcional1Compoe = $('#opcional_1_compoe_despesas').is(':checked');
            const opcional2Compoe = $('#opcional_2_compoe_despesas').is(':checked');
            
            const opcional1Valor = $(`#opcional_1_valor-${rowId}`).val() ? MoneyUtils.parseMoney($(`#opcional_1_valor-${rowId}`).val()) : 0;
            const opcional2Valor = $(`#opcional_2_valor-${rowId}`).val() ? MoneyUtils.parseMoney($(`#opcional_2_valor-${rowId}`).val()) : 0;
            
            if (opcional1Compoe) {
                despesas += opcional1Valor;
            }
            if (opcional2Compoe) {
                despesas += opcional2Valor;
            }


            return {
                total: despesas,
                componentes: {
                    multa,
                    txDefLi,
                    taxaSiscomex,
                    afrmm,
                    armazenagem_sts,
                    frete_dta_sts_ana,
                    honorarios_nix,
                    opcional1: opcional1Compoe ? opcional1Valor : 0,
                    opcional2: opcional2Compoe ? opcional2Valor : 0
                },
                tipoCalculo: getNacionalizacaoAtual()
            };
        }

        function calcularBcIcmsSemReducao(vlrAduaneiroBrl, impostos, despesas) {
            const nacionalizacao = getNacionalizacaoAtual();
            
            // Calcular valores de impostos
            const vlrII = vlrAduaneiroBrl * impostos.ii;
            const bcIpi = vlrAduaneiroBrl + vlrII;
            const vlrIpi = bcIpi * impostos.ipi;
            const vlrPis = vlrAduaneiroBrl * impostos.pis;
            const vlrCofins = vlrAduaneiroBrl * impostos.cofins;
            
            // Para Mato Grosso: BC ICMS S/REDUÇÃO = soma simples (sem divisão)
            // = vlr aduaneiro brl + vlr II + vlr IPI + vlr PIS + vlr COFINS + desp aduaneira
            if (nacionalizacao === 'mato_grosso') {
                // Para Mato Grosso, despesas já é a despesa aduaneira (taxa siscomex + afrmm)
                const despAduaneira = despesas;
                const resultado = vlrAduaneiroBrl + vlrII + vlrIpi + vlrPis + vlrCofins + despAduaneira;
                return resultado;
            }
            
            // Para outras nacionalizações: fórmula padrão com divisão
            const divisor = nacionalizacao === 'santa_catarina' ? 0.96 : (1 - impostos.icms);
            const resultado = (vlrAduaneiroBrl + vlrII + vlrIpi + vlrPis + vlrCofins + despesas) / divisor;
            return resultado;
        }

        function calcularBcIcmsReduzido(rowId, base, impostos, despesas) {
            atualizarReducao(rowId);
            const nacionalizacao = getNacionalizacaoAtual();
            
            // Tentar obter valores brutos para máxima precisão
            const valoresBrutos = window.valoresBrutosPorLinha && window.valoresBrutosPorLinha[rowId];
            
            let vlrAduaneiroBrl, vlrII, vlrIpi, vlrPis, vlrCofins, despAduaneira;
            
            if (valoresBrutos) {
                // Usar valores brutos armazenados (sem perda de precisão)
                vlrAduaneiroBrl = valoresBrutos.valor_aduaneiro_brl || base;
                vlrII = valoresBrutos.valor_ii || (base * impostos.ii);
                vlrIpi = valoresBrutos.valor_ipi || ((base + (base * impostos.ii)) * impostos.ipi);
                vlrPis = valoresBrutos.valor_pis || (base * impostos.pis);
                vlrCofins = valoresBrutos.valor_cofins || (base * impostos.cofins);
                despAduaneira = valoresBrutos.despesa_aduaneira || despesas;
            } else {
                // Calcular valores diretamente (fallback)
                vlrAduaneiroBrl = base;
                vlrII = base * impostos.ii;
                const bcIpi = base + vlrII;
                vlrIpi = bcIpi * impostos.ipi;
                vlrPis = base * impostos.pis;
                vlrCofins = base * impostos.cofins;
                despAduaneira = despesas;
            }
            
            // Para Santa Catarina, BC ICMS REDUZIDO = BC ICMS S/REDUÇÃO (divisor 0.96, sem aplicar redução)
            if (nacionalizacao === 'santa_catarina') {
                const divisor = 0.96;
                const resultado = (vlrAduaneiroBrl + vlrII + vlrIpi + vlrPis + vlrCofins + despAduaneira) / divisor;
                return resultado;
            }
            
            // Para Mato Grosso, aplicar fórmula específica:
            // =((X+AF+AH+AJ+AK+AL)/(1-AC))*(SE(AE=0;1;AE))
            // Onde: X=vlr aduaneiro brl, AF=vlr II, AH=vlr IPI, AJ=vlr PIS, AK=vlr COFINS, AL=desp aduaneira, AC=ICMS_PERCENT, AE=reducao
            if (nacionalizacao === 'mato_grosso') {
                // Usar valor bruto de redução armazenado (com todas as casas decimais)
                let reducao = 1;
                if (valoresBrutos && valoresBrutos.reducao !== undefined && valoresBrutos.reducao > 0) {
                    reducao = valoresBrutos.reducao;
                } else if ($(`#reducao-${rowId}`).val() && MoneyUtils.parsePercentage($(`#reducao-${rowId}`).val()) > 0) {
                    reducao = MoneyUtils.parsePercentage($(`#reducao-${rowId}`).val());
                }
               
                const resultado = ((vlrAduaneiroBrl + vlrII + vlrIpi + vlrPis + vlrCofins + despAduaneira) / (1 - impostos.icms)) * (reducao === 0 ? 1 : reducao);
                return resultado;
            }
            
            // Para Santos, aplicar fórmula específica:
            // =((vlrAduaneiro+vlrII+vlrIPI+vlrPIS+vlrCOFINS+despAduaneira)/(1-icmsReduzido))*(SE(reducao=0;1;reducao))
            if (nacionalizacao === 'santos') {
                // Usar valor bruto de redução armazenado (com todas as casas decimais)
                let reducao = 1;
                if (valoresBrutos && valoresBrutos.reducao !== undefined && valoresBrutos.reducao > 0) {
                    reducao = valoresBrutos.reducao;
                } else if ($(`#reducao-${rowId}`).val() && MoneyUtils.parsePercentage($(`#reducao-${rowId}`).val()) > 0) {
                    reducao = MoneyUtils.parsePercentage($(`#reducao-${rowId}`).val());
                }
               
                const resultado = ((vlrAduaneiroBrl + vlrII + vlrIpi + vlrPis + vlrCofins + despAduaneira) / (1 - impostos.icms)) * (reducao === 0 ? 1 : reducao);
                return resultado;
            }
            
            // Para outras nacionalizações, aplicar a redução normalmente
            // Usar valor bruto de redução armazenado (com todas as casas decimais)
            let reducao = 1;
            if (valoresBrutos && valoresBrutos.reducao !== undefined && valoresBrutos.reducao > 0) {
                reducao = valoresBrutos.reducao;
            } else if ($(`#reducao-${rowId}`).val() && MoneyUtils.parsePercentage($(`#reducao-${rowId}`).val()) > 0) {
                reducao = MoneyUtils.parsePercentage($(`#reducao-${rowId}`).val());
            }
            const resultado = (vlrAduaneiroBrl + vlrII + vlrIpi + vlrPis + vlrCofins + despAduaneira) / ((1 - impostos.icms));

            return resultado * reducao;
        }

        function calcularColunasExportadorTributosDespesas(rowId) {
            const nacionalizacao = getNacionalizacaoAtual();
            if (nacionalizacao !== 'mato_grosso') {
                return;
            }

            // 1. EXPORTADOR = DIF CAMBIAL FOB (cabecalho) × FATOR VLR FOB
            const diferencaCambialFobCabecalho = MoneyUtils.parseMoney($('#diferenca_cambial_fob').val()) || 0;
            const fatorValorFob = MoneyUtils.parseMoney($(`#fator_valor_fob-${rowId}`).val()) || 0;
            const exportador = diferencaCambialFobCabecalho * fatorValorFob;

            // 2. TRIBUTOS = VLR II + VLR IPI + VLR PIS + VLR COFINS + vlr_icms_st_mg
            const valoresBrutos = window.valoresBrutosPorLinha[rowId] || {};
            const valorII = valoresBrutos.valor_ii || MoneyUtils.parseMoney($(`#valor_ii-${rowId}`).val()) || 0;
            const valorIPI = valoresBrutos.valor_ipi || MoneyUtils.parseMoney($(`#valor_ipi-${rowId}`).val()) || 0;
            const valorPIS = valoresBrutos.valor_pis || MoneyUtils.parseMoney($(`#valor_pis-${rowId}`).val()) || 0;
            const valorCOFINS = valoresBrutos.valor_cofins || MoneyUtils.parseMoney($(`#valor_cofins-${rowId}`).val()) || 0;
            const vlrIcmsStMg = valoresBrutos.vlr_icms_st_mg || 0;
            const tributos = valorII + valorIPI + valorPIS + valorCOFINS + vlrIcmsStMg;

            // 3. DESPESAS = SOMA de todos os campos de despesas (BB19:BU19)
            // Ordem: multa, tx_def_li, taxa_siscomex, outras_taxas_agente, liberacao_bl, desconsolidacao,
            // isps_code, handling, capatazia, afrmm, armazenagem_sts, frete_sts_cgb, diarias, sda,
            // rep_sts, armaz_cgb, rep_cgb, demurrage, li_dta_honor_nix, honorarios_nix
            const multa = valoresBrutos.multa || MoneyUtils.parseMoney($(`#multa-${rowId}`).val()) || 0;
            const txDefLi = window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['tx_def_li'] && window.valoresBrutosCamposExternos['tx_def_li'][rowId] !== undefined
                ? window.valoresBrutosCamposExternos['tx_def_li'][rowId]
                : MoneyUtils.parseMoney($(`#tx_def_li-${rowId}`).val()) || 0;
            const taxaSiscomex = valoresBrutos.taxa_siscomex || MoneyUtils.parseMoney($(`#taxa_siscomex-${rowId}`).val()) || 0;
            const outrasTaxasAgente = window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['outras_taxas_agente'] && window.valoresBrutosCamposExternos['outras_taxas_agente'][rowId] !== undefined
                ? window.valoresBrutosCamposExternos['outras_taxas_agente'][rowId]
                : MoneyUtils.parseMoney($(`#outras_taxas_agente-${rowId}`).val()) || 0;
            const liberacaoBl = window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['liberacao_bl'] && window.valoresBrutosCamposExternos['liberacao_bl'][rowId] !== undefined
                ? window.valoresBrutosCamposExternos['liberacao_bl'][rowId]
                : MoneyUtils.parseMoney($(`#liberacao_bl-${rowId}`).val()) || 0;
            const desconsolidacao = window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['desconsolidacao'] && window.valoresBrutosCamposExternos['desconsolidacao'][rowId] !== undefined
                ? window.valoresBrutosCamposExternos['desconsolidacao'][rowId]
                : MoneyUtils.parseMoney($(`#desconsolidacao-${rowId}`).val()) || 0;
            const ispsCode = window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['isps_code'] && window.valoresBrutosCamposExternos['isps_code'][rowId] !== undefined
                ? window.valoresBrutosCamposExternos['isps_code'][rowId]
                : MoneyUtils.parseMoney($(`#isps_code-${rowId}`).val()) || 0;
            const handling = window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['handling'] && window.valoresBrutosCamposExternos['handling'][rowId] !== undefined
                ? window.valoresBrutosCamposExternos['handling'][rowId]
                : MoneyUtils.parseMoney($(`#handling-${rowId}`).val()) || 0;
            const capatazia = window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['capatazia'] && window.valoresBrutosCamposExternos['capatazia'][rowId] !== undefined
                ? window.valoresBrutosCamposExternos['capatazia'][rowId]
                : MoneyUtils.parseMoney($(`#capatazia-${rowId}`).val()) || 0;
            const afrmm = window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['afrmm'] && window.valoresBrutosCamposExternos['afrmm'][rowId] !== undefined
                ? window.valoresBrutosCamposExternos['afrmm'][rowId]
                : MoneyUtils.parseMoney($(`#afrmm-${rowId}`).val()) || 0;
            const armazenagemSts = window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['armazenagem_sts'] && window.valoresBrutosCamposExternos['armazenagem_sts'][rowId] !== undefined
                ? window.valoresBrutosCamposExternos['armazenagem_sts'][rowId]
                : MoneyUtils.parseMoney($(`#armazenagem_sts-${rowId}`).val()) || 0;
            const freteStsCgb = window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['frete_sts_cgb'] && window.valoresBrutosCamposExternos['frete_sts_cgb'][rowId] !== undefined
                ? window.valoresBrutosCamposExternos['frete_sts_cgb'][rowId]
                : MoneyUtils.parseMoney($(`#frete_sts_cgb-${rowId}`).val()) || 0;
            const diarias = window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['diarias'] && window.valoresBrutosCamposExternos['diarias'][rowId] !== undefined
                ? window.valoresBrutosCamposExternos['diarias'][rowId]
                : MoneyUtils.parseMoney($(`#diarias-${rowId}`).val()) || 0;
            const sda = window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['sda'] && window.valoresBrutosCamposExternos['sda'][rowId] !== undefined
                ? window.valoresBrutosCamposExternos['sda'][rowId]
                : MoneyUtils.parseMoney($(`#sda-${rowId}`).val()) || 0;
            const repSts = window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['rep_sts'] && window.valoresBrutosCamposExternos['rep_sts'][rowId] !== undefined
                ? window.valoresBrutosCamposExternos['rep_sts'][rowId]
                : MoneyUtils.parseMoney($(`#rep_sts-${rowId}`).val()) || 0;
            const armazCgb = window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['armaz_cgb'] && window.valoresBrutosCamposExternos['armaz_cgb'][rowId] !== undefined
                ? window.valoresBrutosCamposExternos['armaz_cgb'][rowId]
                : MoneyUtils.parseMoney($(`#armaz_cgb-${rowId}`).val()) || 0;
            const repCgb = window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['rep_cgb'] && window.valoresBrutosCamposExternos['rep_cgb'][rowId] !== undefined
                ? window.valoresBrutosCamposExternos['rep_cgb'][rowId]
                : MoneyUtils.parseMoney($(`#rep_cgb-${rowId}`).val()) || 0;
            const demurrage = window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['demurrage'] && window.valoresBrutosCamposExternos['demurrage'][rowId] !== undefined
                ? window.valoresBrutosCamposExternos['demurrage'][rowId]
                : MoneyUtils.parseMoney($(`#demurrage-${rowId}`).val()) || 0;
            const liDtaHonorNix = window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['li_dta_honor_nix'] && window.valoresBrutosCamposExternos['li_dta_honor_nix'][rowId] !== undefined
                ? window.valoresBrutosCamposExternos['li_dta_honor_nix'][rowId]
                : MoneyUtils.parseMoney($(`#li_dta_honor_nix-${rowId}`).val()) || 0;
            const honorariosNix = window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos['honorarios_nix'] && window.valoresBrutosCamposExternos['honorarios_nix'][rowId] !== undefined
                ? window.valoresBrutosCamposExternos['honorarios_nix'][rowId]
                : MoneyUtils.parseMoney($(`#honorarios_nix-${rowId}`).val()) || 0;

            const despesas = multa + txDefLi + taxaSiscomex + outrasTaxasAgente + liberacaoBl + desconsolidacao +
                ispsCode + handling + capatazia + afrmm + armazenagemSts + freteStsCgb + diarias + sda +
                repSts + armazCgb + repCgb + demurrage + liDtaHonorNix + honorariosNix;

            // 4. TOTAL PAGO = EXPORTADOR + TRIBUTOS + DESPESAS
            const totalPago = exportador + tributos + despesas;

            // 5. PERCENTUAL S/FOB = (TOTAL PAGO / VLR TOTAL FOB R$ LINHA) / 100
            const fobTotalBrl = valoresBrutos.fob_total_brl || MoneyUtils.parseMoney($(`#fob_total_brl-${rowId}`).val()) || 0;
            let percentualSFob = 0;
            if (fobTotalBrl > 0) {
                percentualSFob = (totalPago / fobTotalBrl) / 100;
            }

            // Armazenar valores brutos
            if (!window.valoresBrutosPorLinha[rowId]) {
                window.valoresBrutosPorLinha[rowId] = {};
            }
                        window.valoresBrutosPorLinha[rowId].exportador_mg = exportador;
                        window.valoresBrutosPorLinha[rowId].tributos_mg = tributos;
                        window.valoresBrutosPorLinha[rowId].despesas_mg = despesas;
                        window.valoresBrutosPorLinha[rowId].total_pago_mg = totalPago;
                        window.valoresBrutosPorLinha[rowId].percentual_s_fob_mg = percentualSFob;
            
            // Atualizar campos diretamente após calcular
            const exportadorFormatted = MoneyUtils.formatMoney(exportador, 2);
            const tributosFormatted = MoneyUtils.formatMoney(tributos, 2);
            const despesasFormatted = MoneyUtils.formatMoney(despesas, 2);
            const totalPagoFormatted = MoneyUtils.formatMoney(totalPago, 2);
            const percentualSFobFormatted = MoneyUtils.formatPercentage(percentualSFob);
        
            $(`#exportador_mg-${rowId}`).val(exportadorFormatted);
            $(`#tributos_mg-${rowId}`).val(tributosFormatted);
            $(`#despesas_mg-${rowId}`).val(despesasFormatted);
            $(`#total_pago_mg-${rowId}`).val(totalPagoFormatted);
            $(`#percentual_s_fob_mg-${rowId}`).val(percentualSFobFormatted);
            
        }

        function calcularTotais(base, impostos, despesas, quantidade, vlrIcmsReduzido, rowId) {
            const nacionalizacao = getNacionalizacaoAtual();
            const vlrII = base * impostos.ii;
            const bcIpi = base + vlrII;
            const vlrIpi = bcIpi * impostos.ipi;
            const bcPisCofins = base;
            const vlrPis = bcPisCofins * impostos.pis;
            const vlrCofins = bcPisCofins * impostos.cofins;
            const vlrTotalProdutoNf = base + vlrII;
            const vlrUnitProdutNf = vlrTotalProdutoNf / (quantidade || 1);
            
            // Para Mato Grosso: VLR TOTAL NF S/ICMS ST = AR+AH+AJ+AK+AL
            // Onde: AR=vlrTotalProdutoNf, AH=vlrIpi, AJ=vlrPis, AK=vlrCofins, AL=despAduaneira
            let vlrTotalNfSemIcms;
            if (nacionalizacao === 'mato_grosso') {
                // Para Mato Grosso: soma simples sem vlrIcmsReduzido
                // O parâmetro 'despesas' já contém despesaAduaneira quando chamado para Mato Grosso
                vlrTotalNfSemIcms = vlrTotalProdutoNf + vlrIpi + vlrPis + vlrCofins + despesas;
            } else {
                vlrTotalNfSemIcms = vlrTotalProdutoNf + vlrIpi + vlrPis + vlrCofins + despesas + vlrIcmsReduzido;
            }
        
            const vlrTotalNfComIcms = vlrTotalNfSemIcms + ($(`#valor_icms_st-${rowId}`).val() ? MoneyUtils.parseMoney($(
                `#valor_icms_st-${rowId}`).val()) : 0);
            
            return {
                vlrII: vlrII ?? 0,
                bcIpi: bcIpi ?? 0,
                vlrIpi: vlrIpi ?? 0,
                bcPisCofins: bcPisCofins ?? 0,
                vlrPis: vlrPis ?? 0,
                vlrCofins: vlrCofins ?? 0,
                vlrTotalProdutoNf: vlrTotalProdutoNf ?? 0,
                vlrUnitProdutNf: vlrUnitProdutNf ?? 0,
                vlrTotalNfSemIcms: vlrTotalNfSemIcms ?? 0,
                vlrTotalNfComIcms: vlrTotalNfComIcms ?? 0
            };
        }

        function atualizarCampos(rowId, valores) {

            $(`#peso_liquido_unitario-${rowId}`).val(MoneyUtils.formatMoney(valores.pesoLiqUnit || 0, 6));

            let moedaFrete = $('#frete_internacional_moeda').val();
            let moedaSeguro = $('#seguro_internacional_moeda').val();
            let moedaAcrescimo = $('#acrescimo_frete_moeda').val();
            let moedaProcesso = $('#moeda_processo').val(); 

            if (moedaFrete && moedaFrete !== 'USD') {
                let valorFreteMoedaEstrangeira = MoneyUtils.parseMoney($('#frete_internacional').val()) * valores
                    .fatorPesoRow;

                $(`#frete_moeda_estrangeira-${rowId}`).val(MoneyUtils.formatMoney(valorFreteMoedaEstrangeira, 2));
            }
            $(`#frete_usd-${rowId}`).val(MoneyUtils.formatMoney(valores.freteUsdInt, 2));
            $(`#frete_brl-${rowId}`).val(MoneyUtils.formatMoney(valores.freteUsdInt * valores.dolar, 2));

            if (moedaSeguro && moedaSeguro !== 'USD') {

                let valorSeguroMoedaEstrangeira = MoneyUtils.parseMoney($('#seguro_internacional').val()) * (valores
                    .fobTotal / valores.fobTotalGeral);


                $(`#seguro_moeda_estrangeira-${rowId}`).val(MoneyUtils.formatMoney(valorSeguroMoedaEstrangeira, 2));
            }

            $(`#seguro_usd-${rowId}`).val(MoneyUtils.formatMoney(valores.seguroIntUsdRow, 2));
            $(`#seguro_brl-${rowId}`).val(MoneyUtils.formatMoney(valores.seguroIntUsdRow * valores.dolar, 2));


            if (moedaAcrescimo && moedaAcrescimo !== 'USD') {
                let valorAcrescimoMoedaEstrangeira = MoneyUtils.parseMoney($('#acrescimo_frete').val()) * valores
                    .fatorVlrFob_AX;
                $(`#acrescimo_moeda_estrangeira-${rowId}`).val(MoneyUtils.formatMoney(valorAcrescimoMoedaEstrangeira, 2));
            }

            let cotacaoProcesso = getCotacaoesProcesso();
            let cotacaoUSD = cotacaoProcesso['USD']?.venda ?? 1;
            if (moedaProcesso && moedaProcesso !== 'USD') {
                let cotacaoMoedaProcesso = MoneyUtils.parseMoney($('#display_cotacao').val());
                if (!cotacaoMoedaProcesso && cotacaoProcesso[moedaProcesso]) {
                    cotacaoMoedaProcesso = cotacaoProcesso[moedaProcesso].venda;
                }

                if (cotacaoMoedaProcesso) {

                    let fobTotalMoedaEstrangeira = valores.fobTotal * (cotacaoMoedaProcesso / cotacaoUSD);
                    // $(`#fob_total_moeda_estrangeira-${rowId}`).val(MoneyUtils.formatMoney(fobTotalMoedaEstrangeira, 7));
                }
            }
            $(`#acresc_frete_usd-${rowId}`).val(MoneyUtils.formatMoney(valores.acrescimoFreteUsdRow, 2));
            $(`#acresc_frete_brl-${rowId}`).val(MoneyUtils.formatMoney(valores.acrescimoFreteUsdRow * valores.dolar, 2));
            

            if (valores.vlrCrfTotal !== undefined) {
                const valorFormatado = MoneyUtils.formatMoney(valores.vlrCrfTotal, 4);
                $(`#vlr_crf_total-${rowId}`).val(valorFormatado);
            } else {

                const fobTotal = valores.fobTotal || MoneyUtils.parseMoney($(`#fob_total_usd-${rowId}`).val()) || 0;
                const freteUsd = valores.freteUsdInt || MoneyUtils.parseMoney($(`#frete_usd-${rowId}`).val()) || 0;
                const nacionalizacao = getNacionalizacaoAtual();
                let vlrCrfTotal;
                if (nacionalizacao === 'santa_catarina') {
                    const serviceChargesUsd = MoneyUtils.parseMoney($(`#service_charges-${rowId}`).val()) || 0;
                    const acrescimoFreteUsd = valores.acrescimoFreteUsdRow || MoneyUtils.parseMoney($(`#acresc_frete_usd-${rowId}`).val()) || 0;
                    vlrCrfTotal = fobTotal + freteUsd + serviceChargesUsd + acrescimoFreteUsd;
                } else if (nacionalizacao === 'mato_grosso') {
                    // Para Mato Grosso: FOB Total USD + Frete Internacional USD + Seguro Internacional USD
                    const seguroUsd = valores.seguroIntUsdRow || MoneyUtils.parseMoney($(`#seguro_usd-${rowId}`).val()) || 0;
                    vlrCrfTotal = fobTotal + freteUsd + seguroUsd;
                } else {
                    vlrCrfTotal = fobTotal + freteUsd;
                }
                const valorFormatado = MoneyUtils.formatMoney(vlrCrfTotal, 4);
                $(`#vlr_crf_total-${rowId}`).val(valorFormatado);
            }
            
            if (valores.vlrCrfUnit !== undefined) {
                const valorFormatado = MoneyUtils.formatMoney(valores.vlrCrfUnit, 4);
                $(`#vlr_crf_unit-${rowId}`).val(valorFormatado);
            } else {

                const vlrCrfTotal = MoneyUtils.parseMoney($(`#vlr_crf_total-${rowId}`).val()) || 0;
                const quantidade = valores.quantidade || MoneyUtils.parseMoney($(`#quantidade-${rowId}`).val()) || 0;
                const vlrCrfUnit = quantidade > 0 ? vlrCrfTotal / quantidade : 0;
                const valorFormatado = MoneyUtils.formatMoney(vlrCrfUnit, 4);
                $(`#vlr_crf_unit-${rowId}`).val(valorFormatado);
            }
            

            let moedaServiceCharges = $('#service_charges_moeda').val();
            if (moedaServiceCharges && moedaServiceCharges !== 'USD') {
                if (valores.serviceChargesMoedaEstrangeira !== undefined) {
                    $(`#service_charges_moeda_estrangeira-${rowId}`).val(MoneyUtils.formatMoney(valores.serviceChargesMoedaEstrangeira, 2));
                } else {

                    const serviceChargesBase = MoneyUtils.parseMoney($('#service_charges').val()) || 0;
                    const fatorPesoRow = valores.fatorPesoRow || MoneyUtils.parseMoney($(`#fator_peso-${rowId}`).val()) || 0;
                    const serviceChargesMoedaEstrangeira = serviceChargesBase * fatorPesoRow;
                    $(`#service_charges_moeda_estrangeira-${rowId}`).val(MoneyUtils.formatMoney(serviceChargesMoedaEstrangeira, 2));
                }
            }
            
            if (valores.serviceChargesRow !== undefined) {
                $(`#service_charges-${rowId}`).val(MoneyUtils.formatMoney(valores.serviceChargesRow, 2));
            } else {

                const serviceChargesBase = MoneyUtils.parseMoney($('#service_charges').val()) || 0;
                const fatorPesoRow = valores.fatorPesoRow || MoneyUtils.parseMoney($(`#fator_peso-${rowId}`).val()) || 0;
                const serviceChargesRow = serviceChargesBase * fatorPesoRow;
                $(`#service_charges-${rowId}`).val(MoneyUtils.formatMoney(serviceChargesRow, 2));
            }
            
            if (valores.serviceChargesBrl !== undefined) {
                $(`#service_charges_brl-${rowId}`).val(MoneyUtils.formatMoney(valores.serviceChargesBrl, 2));
            } else {

                const serviceChargesRow = MoneyUtils.parseMoney($(`#service_charges-${rowId}`).val()) || 0;
                const serviceChargesBrl = serviceChargesRow * valores.dolar;
                $(`#service_charges_brl-${rowId}`).val(MoneyUtils.formatMoney(serviceChargesBrl, 2));
            }
            
            $(`#thc_usd-${rowId}`).val(MoneyUtils.formatMoney(valores.thcRow / valores.dolar, 2));
            $(`#thc_brl-${rowId}`).val(MoneyUtils.formatMoney(valores.thcRow, 2));
            $(`#valor_aduaneiro_usd-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrAduaneiroUsd, 2));
            $(`#valor_aduaneiro_brl-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrAduaneiroBrl, 2));
            $(`#valor_ii-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrAduaneiroBrl * valores.impostos.ii, 2));
            $(`#base_ipi-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrAduaneiroBrl + valores.vlrAduaneiroBrl * valores
                .impostos.ii, 2));
            $(`#valor_ipi-${rowId}`).val(MoneyUtils.formatMoney((valores.vlrAduaneiroBrl + valores.vlrAduaneiroBrl * valores
                .impostos.ii) * valores.impostos.ipi, 2));
            $(`#base_pis_cofins-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrAduaneiroBrl, 2));
            $(`#valor_pis-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrAduaneiroBrl * valores.impostos.pis, 2));
            $(`#valor_cofins-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrAduaneiroBrl * valores.impostos.cofins, 2));
            // Despesa aduaneira = taxa siscomex linha + afrmm (apenas para Mato Grosso)
            const nacionalizacao = getNacionalizacaoAtual();
            let despesaAduaneira;
            if (nacionalizacao === 'mato_grosso') {
                const taxaSiscomexLinha = $(`#taxa_siscomex-${rowId}`).val() ? MoneyUtils.parseMoney($(`#taxa_siscomex-${rowId}`).val()) : 0;
                const afrmm = $(`#afrmm-${rowId}`).val() ? MoneyUtils.parseMoney($(`#afrmm-${rowId}`).val()) : 0;
                despesaAduaneira = taxaSiscomexLinha + afrmm;
            } else {
                despesaAduaneira = valores.despesas;
            }
            $(`#despesa_aduaneira-${rowId}`).val(MoneyUtils.formatMoney(despesaAduaneira, 2));
            $(`#base_icms_sem_reducao-${rowId}`).val(MoneyUtils.formatMoney(valores.bcIcmsSReducao, 2));
            $(`#valor_icms_sem_reducao-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrIcmsSReducao, 2));
            $(`#base_icms_reduzido-${rowId}`).val(MoneyUtils.formatMoney(valores.bcImcsReduzido, 2));
            $(`#valor_icms_reduzido-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrIcmsReduzido, 2));
            $(`#valor_unit_nf-${rowId}`).val(MoneyUtils.formatMoney(valores.totais.vlrUnitProdutNf, 2));
            $(`#valor_total_nf-${rowId}`).val(MoneyUtils.formatMoney(valores.totais.vlrTotalProdutoNf, 2));
            $(`#valor_total_nf_sem_icms_st-${rowId}`).val(MoneyUtils.formatMoney(valores.totais.vlrTotalNfSemIcms, 2));

            
            $(`#base_icms_st-${rowId}`).val(MoneyUtils.formatMoney(valores.base_icms_st, 2));
            $(`#valor_icms_st-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrIcmsSt, 2));
            

            const valorTotalNfComIcmsStRecalculado = valores.totais.vlrTotalNfSemIcms + (valores.vlrIcmsSt || 0);
            $(`#valor_total_nf_com_icms_st-${rowId}`).val(MoneyUtils.formatMoney(valorTotalNfComIcmsStRecalculado, 2));


            const diferencaCambialFreteValidada = validarDiferencaCambialFrete(valores.diferenca_cambial_frete);
            if (diferencaCambialFreteValidada === 0 || isNaN(diferencaCambialFreteValidada) || !isFinite(diferencaCambialFreteValidada) || diferencaCambialFreteValidada < 0) {
                $(`#diferenca_cambial_frete-${rowId}`).val('');
            } else {
                $(`#diferenca_cambial_frete-${rowId}`).val(MoneyUtils.formatMoney(diferencaCambialFreteValidada, 2));
            }
            $(`#diferenca_cambial_fob-${rowId}`).val(MoneyUtils.formatMoney(valores.diferenca_cambial_fob, 2));

            // Atualizar custo_unitario_final e custo_total_final
            // Priorizar valores de window.valoresBrutosPorLinha se disponíveis (mesma fonte do totalizador)
            let custoUnitarioFinal = 0;
            let custoTotalFinal = 0;
            
            // Tentar obter de window.valoresBrutosPorLinha primeiro (mesma fonte do totalizador)
            if (window.valoresBrutosPorLinha && window.valoresBrutosPorLinha[rowId]) {
                custoUnitarioFinal = window.valoresBrutosPorLinha[rowId].custo_unitario_final || 0;
                custoTotalFinal = window.valoresBrutosPorLinha[rowId].custo_total_final || 0;
            }
            
            // Fallback para valores passados como parâmetro
            if ((!custoUnitarioFinal || custoUnitarioFinal === 0) && valores.custoUnitarioFinal !== undefined && valores.custoUnitarioFinal !== null) {
                custoUnitarioFinal = valores.custoUnitarioFinal;
            }
            if ((!custoTotalFinal || custoTotalFinal === 0) && valores.custoTotalFinal !== undefined && valores.custoTotalFinal !== null) {
                custoTotalFinal = valores.custoTotalFinal;
            }
            
            // Validar se os valores são números válidos
            if (isNaN(custoUnitarioFinal) || !isFinite(custoUnitarioFinal)) {
                custoUnitarioFinal = 0;
            }
            if (isNaN(custoTotalFinal) || !isFinite(custoTotalFinal)) {
                custoTotalFinal = 0;
            }
            
            
            // Garantir que os campos existam antes de atualizar
            const campoCustoUnitario = $(`#custo_unitario_final-${rowId}`);
            const campoCustoTotal = $(`#custo_total_final-${rowId}`);
            
            if (campoCustoUnitario.length > 0) {
                campoCustoUnitario.val(MoneyUtils.formatMoney(custoUnitarioFinal, 2));
            } else {
                console.warn(`Campo custo_unitario_final-${rowId} não encontrado`);
            }
            
            if (campoCustoTotal.length > 0) {
                campoCustoTotal.val(MoneyUtils.formatMoney(custoTotalFinal, 2));
            } else {
                console.warn(`Campo custo_total_final-${rowId} não encontrado`);
            }
            
            // Atualizar campos específicos de Mato Grosso
            const nacionalizacaoAtual = getNacionalizacaoAtual();
            if (nacionalizacaoAtual === 'mato_grosso') {
                // Obter valores brutos
                const valoresBrutos = window.valoresBrutosPorLinha && window.valoresBrutosPorLinha[rowId];
                if (valoresBrutos) {
                    const dezPorcento = valoresBrutos.dez_porcento || 0;
                    const custoComMargem = valoresBrutos.custo_com_margem || 0;
                    const vlrIpiMg = valoresBrutos.vlr_ipi_mg || 0;
                    const vlrIcmsMg = valoresBrutos.vlr_icms_mg || 0;
                    const pisMg = valoresBrutos.pis_mg || 0;
                    const cofinsMg = valoresBrutos.cofins_mg || 0;
                    const custoTotalFinalCredito = valoresBrutos.custo_total_final_credito || 0;
                    const custoUnitCredito = valoresBrutos.custo_unit_credito || 0;
                    
                    $(`#dez_porcento-${rowId}`).val(MoneyUtils.formatMoney(dezPorcento, 2));
                    $(`#custo_com_margem-${rowId}`).val(MoneyUtils.formatMoney(custoComMargem, 2));
                    $(`#vlr_ipi_mg-${rowId}`).val(MoneyUtils.formatMoney(vlrIpiMg, 2));
                    $(`#vlr_icms_mg-${rowId}`).val(MoneyUtils.formatMoney(vlrIcmsMg, 2));
                    $(`#pis_mg-${rowId}`).val(MoneyUtils.formatMoney(pisMg, 2));
                    $(`#cofins_mg-${rowId}`).val(MoneyUtils.formatMoney(cofinsMg, 2));
                    $(`#custo_total_final_credito-${rowId}`).val(MoneyUtils.formatMoney(custoTotalFinalCredito, 2));
                    $(`#custo_unit_credito-${rowId}`).val(MoneyUtils.formatMoney(custoUnitCredito, 2));
                    
                    // Atualizar campos ICMS-ST para Mato Grosso
                    const bcIcmsStMg = valoresBrutos.bc_icms_st_mg || 0;
                    const vlrIcmsStMg = valoresBrutos.vlr_icms_st_mg || 0;
                    const custoTotalCIcmsSt = valoresBrutos.custo_total_c_icms_st || 0;
                    const custoUnitCIcmsSt = valoresBrutos.custo_unit_c_icms_st || 0;
                    
                    
                    $(`#bc_icms_st_mg-${rowId}`).val(MoneyUtils.formatMoney(bcIcmsStMg, 2));
                    $(`#vlr_icms_st_mg-${rowId}`).val(MoneyUtils.formatMoney(vlrIcmsStMg, 2));
                    $(`#custo_total_c_icms_st-${rowId}`).val(MoneyUtils.formatMoney(custoTotalCIcmsSt, 2));
                    $(`#custo_unit_c_icms_st-${rowId}`).val(MoneyUtils.formatMoney(custoUnitCIcmsSt, 2));
                    
                    // Atualizar novas colunas: EXPORTADOR, TRIBUTOS, DESPESAS, TOTAL PAGO, PERCENTUAL S/FOB
                    // Tentar obter valores de window.valoresBrutosPorLinha primeiro
                    const valoresBrutosAtualizados = window.valoresBrutosPorLinha && window.valoresBrutosPorLinha[rowId];
                    const exportadorMg = valoresBrutosAtualizados?.exportador_mg !== undefined ? valoresBrutosAtualizados.exportador_mg : (valoresBrutos.exportador_mg || 0);
                    const tributosMg = valoresBrutosAtualizados?.tributos_mg !== undefined ? valoresBrutosAtualizados.tributos_mg : (valoresBrutos.tributos_mg || 0);
                    const despesasMg = valoresBrutosAtualizados?.despesas_mg !== undefined ? valoresBrutosAtualizados.despesas_mg : (valoresBrutos.despesas_mg || 0);
                    const totalPagoMg = valoresBrutosAtualizados?.total_pago_mg !== undefined ? valoresBrutosAtualizados.total_pago_mg : (valoresBrutos.total_pago_mg || 0);
                    const percentualSFobMg = valoresBrutosAtualizados?.percentual_s_fob_mg !== undefined ? valoresBrutosAtualizados.percentual_s_fob_mg : (valoresBrutos.percentual_s_fob_mg || 0);
                    
                    
                    $(`#exportador_mg-${rowId}`).val(MoneyUtils.formatMoney(exportadorMg, 2));
                    $(`#tributos_mg-${rowId}`).val(MoneyUtils.formatMoney(tributosMg, 2));
                    $(`#despesas_mg-${rowId}`).val(MoneyUtils.formatMoney(despesasMg, 2));
                    $(`#total_pago_mg-${rowId}`).val(MoneyUtils.formatMoney(totalPagoMg, 2));
                    $(`#percentual_s_fob_mg-${rowId}`).val(MoneyUtils.formatPercentage(percentualSFobMg * 100));
                }
            }

            atualizarFatoresFob()
        }

        function atualizarFatoresFob() {
            const moedasOBject = getCotacaoesProcesso();
            const dolarDebug = toNumber(debugGlobals?.cotacaoUSD);
            const moedaDolar = moedasOBject['USD']?.venda
                ?? $(`#cotacao_frete_internacional`).val()?.replace(',', '.')
                ?? dolarDebug
                ?? 1;
            const dolar = toNumber(moedaDolar) || 1;
            const taxaSiscomexDebug = toNumber(debugGlobals?.taxaSiscomexProcesso);
            const taxaSiscomex = taxaSiscomexDebug || calcularTaxaSiscomex() || 0;


            let dadosFob = getDebugFobData();

            if (!dadosFob.length) {
                dadosFob = [];
                $('[id^="fob_total_usd-"]').each(function() {
                    const rowId = $(this).data('row');
                    const fobTotal = MoneyUtils.parseMoney($(this).val());
                    dadosFob.push({
                        rowId,
                        fobTotal
                    });
                });
            }

            const fobTotalGeral = dadosFob.reduce((acc, linha) => acc + (linha.fobTotal || 0), 0);
            if (fobTotalGeral <= 0) {
                return;
            }

            dadosFob.forEach(({ rowId, fobTotal }) => {
                const fatorVlrFob_AX = (fobTotal || 0) / fobTotalGeral;
                const divisorSiscomex = fobTotalGeral * dolar || 1;
                const fatorTaxaSiscomex_AY = divisorSiscomex !== 0 ? (taxaSiscomex || 0) / divisorSiscomex : 0;
                const taxaSiscomexUnitaria_BB = fatorTaxaSiscomex_AY * (fobTotal || 0) * dolar;

                $(`#fator_valor_fob-${rowId}`).val(MoneyUtils.formatMoney(fatorVlrFob_AX, 8));
                $(`#fator_tx_siscomex-${rowId}`).val(MoneyUtils.formatMoney(fatorTaxaSiscomex_AY, 6));
                $(`#taxa_siscomex-${rowId}`).val(MoneyUtils.formatMoney(taxaSiscomexUnitaria_BB, 2));

                $(`#fator_vlr_fob-${rowId}`).val(MoneyUtils.formatMoney(fatorVlrFob_AX, 8));
                const camposExternos = getCamposExternos();
                camposExternos.forEach(campo => {
                    const campoEl = $(`#${campo}-${rowId}`);
                    if (!campoEl.length) return;
                    const valorOriginal = MoneyUtils.parseMoney(campoEl.val());
                    const valorComFator = valorOriginal * fatorVlrFob_AX;
                    campoEl.val(MoneyUtils.formatMoney(valorComFator, 6));
                });
            });

            $('#fobTotalProcesso').text(MoneyUtils.formatMoney(fobTotalGeral));
            $('#fobTotalProcessoReal').text(MoneyUtils.formatMoney(fobTotalGeral * dolar));
            atualizarCamposCabecalho();
        }

        $('.cabecalhoInputs').on('blur', function() {
            atualizarFatoresFob();
            atualizarCamposCabecalho();
            // Recalcular toda a tabela para atualizar valores brutos usados no totalizador
            agendarRecalculo();
        });
        
        // Listeners para campos opcionais
        $(document).on('change keyup', '.opcional-valor', function() {
            ratearCamposOpcionais();
            agendarRecalculo();
        });
        
        $(document).on('change', '#opcional_1_compoe_despesas, #opcional_2_compoe_despesas', function() {
            agendarRecalculo();
        });
        

        $(document).on('blur', '[id$="-0"], [id$="-1"], [id$="-2"], [id$="-3"], [id$="-4"], [id$="-5"], [id$="-6"], [id$="-7"], [id$="-8"], [id$="-9"]', function() {
            const campoId = $(this).attr('id');
            if (campoId) {

                const match = campoId.match(/^(.+?)-(\d+)$/);
                if (match) {
                    const campo = match[1];
                    const rowId = parseInt(match[2]);

                    const camposExternos = getCamposExternos();
                    if (camposExternos.includes(campo)) {

                        const valor = MoneyUtils.parseMoney($(this).val()) || 0;
                        if (!window.valoresBrutosCamposExternos[campo]) {
                            window.valoresBrutosCamposExternos[campo] = [];
                        }
                        window.valoresBrutosCamposExternos[campo][rowId] = valor;
                
                    }
                }
            }
        });
        $(document).on('change', '.icms_reduzido_percent', function() {
            const rowId = $(this).data('row');
            atualizarReducao(rowId);
        });

        function atualizarReducao(rowId) {
            const valor = MoneyUtils.parsePercentage($(`#icms_reduzido_percent-${rowId}`).val());
            const icmsPercent = MoneyUtils.parsePercentage($(`#icms_percent-${rowId}`).val())
            const novoReducao = valor / icmsPercent

            // Armazenar valor bruto de redução com máxima precisão
            if (!window.valoresBrutosPorLinha) {
                window.valoresBrutosPorLinha = {};
            }
            if (!window.valoresBrutosPorLinha[rowId]) {
                window.valoresBrutosPorLinha[rowId] = {};
            }
            window.valoresBrutosPorLinha[rowId].reducao = novoReducao;

            $(`#reducao-${rowId}`).val(MoneyUtils.formatPercentage(novoReducao , 2));
        }


        let recalcularTimeout = null;
        let isRecalculating = false;
        const RECALC_DEBOUNCE_MS = 300;

        function agendarRecalculo(delay = RECALC_DEBOUNCE_MS) {
            if (isRecalculating) return;
            contadorEventosCampos++;
            clearTimeout(recalcularTimeout);
            recalcularTimeout = setTimeout(() => {
                if (!isRecalculating) {
                    recalcularTodaTabela();
                }
            }, delay);
        }

        function debouncedRecalcular() {
            agendarRecalculo();
        }


        $(document).on('blur', '[id^="multa-"]', function() {
            const rowId = $(this).data('row');
            if (rowId != null) {
                debouncedRecalcular();
            }
        });

        $(document).on('blur', '[id^="tx_def_li-"]', function() {
            const rowId = $(this).data('row');
            if (rowId != null) {
                debouncedRecalcular();
            }
        });


        let atualizarCambialTimeout = null;
        function debouncedAtualizarCambial() {
            clearTimeout(atualizarCambialTimeout);
            atualizarCambialTimeout = setTimeout(() => {
                atualizarFatoresFob();
                atualizarCamposCambial();
                atualizarTotalizadores(); // Atualizar totalizadores após mudança nos campos cambiais
            }, 200);
        }

        $(document).on('change blur', '.difCambial', function() {
            debouncedAtualizarCambial();
        });
        
        // Event listeners para MVA e ICMS-ST em Mato Grosso (campos específicos _mg)
        $(document).on('change blur keyup', '#productsBody input[id^="mva_mg-"], #productsBody input[id^="icms_st_mg-"]', function() {
            const nacionalizacao = getNacionalizacaoAtual();
            if (nacionalizacao === 'mato_grosso') {
                const rowId = $(this).data('row');
                if (rowId !== undefined && rowId !== null && rowId !== '') {
                    // Recalcular apenas as colunas ICMS-ST para esta linha
                    setTimeout(function() {
                        recalcularTodaTabela();
                    }, 100);
                }
            }
        });

        $(document).on('focusin', '#nacionalizacao', function() {
            $(this).data('valor-anterior', $(this).val());
        });

        $(document).on('change', '#nacionalizacao', function() {
            processarMudancaNacionalizacao(this);
        });


        window.valoresBrutosCamposExternos = window.valoresBrutosCamposExternos || {};

        function atualizarCamposCabecalho() {
            const campos = getCamposExternos()
            const lengthTable = $('.linhas-input').length


            let fobTotalGeral = calcularFobTotalGeral();

            for (let campo of campos) {
                const campoElement = $(`#${campo}`);
                if (campoElement.length === 0) {
                    continue;
                }
                const valorCampo = MoneyUtils.parseMoney(campoElement.val()) || 0;
             
                
                if (valorCampo === 0) {
                    for (let i = 0; i < lengthTable; i++) {
                        const valorLinha = MoneyUtils.parseMoney($(`#${campo}-${i}`).val()) || 0;
                        if (valorLinha > 0) {
                            if (!window.valoresBrutosCamposExternos[campo]) {
                                window.valoresBrutosCamposExternos[campo] = [];
                            }
                            window.valoresBrutosCamposExternos[campo][i] = valorLinha;
                       
                        } else {
                            $(`#${campo}-${i}`).val('');
                            if (window.valoresBrutosCamposExternos[campo]) {
                                window.valoresBrutosCamposExternos[campo][i] = 0;
                              
                            }
                        }
                    }
                    continue;
                }


                if (!window.valoresBrutosCamposExternos[campo]) {
                    window.valoresBrutosCamposExternos[campo] = [];
                }
                
                let somaDistribuida = 0;
                const valoresPorLinha = [];


                for (let i = 0; i < lengthTable - 1; i++) {
                    const fobTotal = MoneyUtils.parseMoney($(`#fob_total_usd-${i}`).val()) || 0;
                    const fatorVlrFob_AX = fobTotalGeral > 0 ? (fobTotal / fobTotalGeral) : 0;
                    
                    const valorCalculado = valorCampo * fatorVlrFob_AX;

                    const valorArredondado = Math.ceil(valorCalculado * 100) / 100;
                    

                    const valorDisponivel = valorCampo - somaDistribuida;
                    const valorFinal = Math.min(valorArredondado, valorDisponivel);
                    
                    valoresPorLinha[i] = valorFinal;
                    somaDistribuida += valorFinal;
                    

                    window.valoresBrutosCamposExternos[campo][i] = valorFinal;
                 
                    
                    // Campos que precisam de mais precisão (7 casas decimais)
                    const camposPrecisao7 = ['li_dta_honor_nix', 'honorarios_nix'];
                    const decimais = camposPrecisao7.includes(campo) ? 7 : 2;

                    $(`#${campo}-${i}`).val(MoneyUtils.formatMoney(valorFinal, decimais));
                }
                

                const ultimaLinha = lengthTable - 1;
                

                let somaDistribuidaRecalculada = 0;
                for (let i = 0; i < lengthTable - 1; i++) {

                    somaDistribuidaRecalculada += window.valoresBrutosCamposExternos[campo][i] || 0;
                }
                


                let valorUltimaLinha = valorCampo - somaDistribuidaRecalculada;
                

                if (valorUltimaLinha < 0) {

                    const fatorAjuste = valorCampo / somaDistribuidaRecalculada;
                    somaDistribuidaRecalculada = 0;
                    for (let i = 0; i < lengthTable - 1; i++) {
                        if (valoresPorLinha[i] !== undefined) {
                            valoresPorLinha[i] = valoresPorLinha[i] * fatorAjuste;

                            valoresPorLinha[i] = Math.floor(valoresPorLinha[i] * 100) / 100;
                            somaDistribuidaRecalculada += valoresPorLinha[i];
                            

                            window.valoresBrutosCamposExternos[campo][i] = valoresPorLinha[i];
                       
                            // Campos que precisam de mais precisão (7 casas decimais)
                            const camposPrecisao7 = ['li_dta_honor_nix', 'honorarios_nix'];
                            const decimais = camposPrecisao7.includes(campo) ? 7 : 2;
                          
                            $(`#${campo}-${i}`).val(MoneyUtils.formatMoney(valoresPorLinha[i], decimais));
                        }
                    }

                    valorUltimaLinha = valorCampo - somaDistribuidaRecalculada;
                }
                

                valoresPorLinha[ultimaLinha] = valorUltimaLinha;
                


                window.valoresBrutosCamposExternos[campo][ultimaLinha] = valorUltimaLinha;
        

                let somaFinal = 0;
                for (let i = 0; i < lengthTable; i++) {
                    somaFinal += window.valoresBrutosCamposExternos[campo][i] || 0;
                }
                

                const diferencaFinal = valorCampo - somaFinal;
                if (Math.abs(diferencaFinal) > 0.000001) {
                    window.valoresBrutosCamposExternos[campo][ultimaLinha] += diferencaFinal;
                    valorUltimaLinha += diferencaFinal;
               
                }
                


                // Campos que precisam de mais precisão (7 casas decimais)
                const camposPrecisao7 = ['li_dta_honor_nix', 'honorarios_nix'];
                if (camposPrecisao7.includes(campo)) {
                    $(`#${campo}-${ultimaLinha}`).val(MoneyUtils.formatMoney(valorUltimaLinha, 7));
                } else {
                    // Usar formatMoney com 2 casas decimais para campos do cabeçalho (moneyReal2)
                    $(`#${campo}-${ultimaLinha}`).val(MoneyUtils.formatMoney(valorUltimaLinha, 2));
                }
            }
            
            // Console.log detalhado dos cabeçalho inputs calculados e distribuídos
            const valoresCabecalhoInputs = {};
            for (let campo of campos) {
                const campoElement = $(`#${campo}`);
                if (campoElement.length === 0) {
                    continue;
                }
                const valorCampo = MoneyUtils.parseMoney(campoElement.val()) || 0;
                if (valorCampo > 0) {
                    valoresCabecalhoInputs[campo] = {
                        valor_cabecalho: valorCampo,
                        valores_distribuidos: [],
                        valores_brutos: window.valoresBrutosCamposExternos[campo]
                    };
                    for (let i = 0; i < lengthTable; i++) {
                        const valorDistribuido = window.valoresBrutosCamposExternos[campo]?.[i] || 0;
                        const fobTotalLinha = MoneyUtils.parseMoney($(`#fob_total_usd-${i}`).val()) || 0;
                        const fatorVlrFobLinha = fobTotalGeral > 0 ? (fobTotalLinha / fobTotalGeral) : 0;
                        
                        valoresCabecalhoInputs[campo].valores_distribuidos.push({
                            linha: i,
                            valor: valorDistribuido,
                            fator_vlr_fob: fatorVlrFobLinha,
                            fob_total_usd: fobTotalLinha
                        });
                    }
                    // Calcular soma dos valores distribuídos
                    const somaDistribuida = valoresCabecalhoInputs[campo].valores_distribuidos.reduce((sum, item) => sum + item.valor, 0);
                    valoresCabecalhoInputs[campo].soma_distribuida = somaDistribuida;
                    valoresCabecalhoInputs[campo].diferenca = valorCampo - somaDistribuida;
                }
            }
            
            
            // Função auxiliar para parsear valores monetários de forma segura
            const parsearValorMonetario = (seletor) => {
                const valor = $(seletor).val();
                if (valor) {
                    return MoneyUtils.parseMoney(valor) || 0;
                }
                return 0;
            };

            for (let i = 0; i < lengthTable; i++) {
                // Obter valores brutos da linha (mesma fonte do totalizador)
                const valoresBrutos = window.valoresBrutosPorLinha && window.valoresBrutosPorLinha[i] ? window.valoresBrutosPorLinha[i] : {};
                
                // Obter valores base dos valores brutos
                const fobTotal = valoresBrutos.fob_total_usd || 0;
                
                // Calcular fator de valor FOB
                let fatorVlrFob_AX = 0;
                if (fobTotalGeral > 0) {
                    fatorVlrFob_AX = fobTotal / fobTotalGeral;
                }
                
                let desp_desenbaraco_parte_1 = 0;

                // Obter valores comuns dos valores brutos
                const taxa_siscomex = valoresBrutos.taxa_siscomex || 0;
                const multa = valoresBrutos.multa || 0;
                const vlrAduaneiroBrl = valoresBrutos.valor_aduaneiro_brl || 0;
                const capatazia = valoresBrutos.capatazia || 0;
                const afrmm = valoresBrutos.afrmm || 0;
                const armazenagem_sts = valoresBrutos.armazenagem_sts || 0;
                const frete_dta_sts_ana = valoresBrutos.frete_dta_sts_ana || 0;
                const honorarios_nix = valoresBrutos.honorarios_nix || 0;

                // Calcular taxa_def conforme nacionalização usando valores brutos
                let taxa_def = 0;
                const nacionalizacaoRecalc = getNacionalizacaoAtual();
                
                if (nacionalizacaoRecalc === 'mato_grosso') {
                    // Para Mato Grosso: usar valor rateado do cabeçalho (valores brutos)
                    if (window.valoresBrutosCamposExternos && 
                        window.valoresBrutosCamposExternos['tx_def_li'] && 
                        window.valoresBrutosCamposExternos['tx_def_li'][i] !== undefined) {
                        taxa_def = window.valoresBrutosCamposExternos['tx_def_li'][i];
                    } else {
                        taxa_def = valoresBrutos.tx_def_li || 0;
                    }
                } else {
                    // Para outras nacionalizações: usar valor dos valores brutos
                    taxa_def = valoresBrutos.tx_def_li || 0;
                }

                // Calcular despesa de desembaraço conforme nacionalização
                const nacionalizacao = getNacionalizacaoAtual();
                let despesa_desembaraco = 0;
                
                if (nacionalizacao === 'santa_catarina') {
                    // Fórmula específica para Santa Catarina - usar valores brutos
                    const multa_complem = valoresBrutos.multa_complem || 0;
                    const dif_impostos = valoresBrutos.dif_impostos || 0;
                    const outras_taxas_agente = valoresBrutos.outras_taxas_agente || 0;
                    const liberacao_bl = valoresBrutos.liberacao_bl || 0;
                    const desconsolidacao = valoresBrutos.desconsolidacao || 0;
                    const isps_code = valoresBrutos.isps_code || 0;
                    const handling = valoresBrutos.handling || 0;
                    const armazenagem_porto = valoresBrutos.armazenagem_porto || 0;
                    const frete_rodoviario = valoresBrutos.frete_rodoviario || 0;
                    const dif_frete_rodoviario = valoresBrutos.dif_frete_rodoviario || 0;
                    const sda = valoresBrutos.sda || 0;
                    const rep_porto = valoresBrutos.rep_porto || 0;
                    const tx_correcao_lacre = valoresBrutos.tx_correcao_lacre || 0;
                    const li_dta_honor_nix = valoresBrutos.li_dta_honor_nix || 0;

                    // Parte 1: soma de todos os campos
                    desp_desenbaraco_parte_1 = multa + taxa_def + taxa_siscomex + multa_complem + dif_impostos + 
                        outras_taxas_agente + liberacao_bl + desconsolidacao + isps_code + handling + capatazia + 
                        afrmm + armazenagem_porto + frete_rodoviario + dif_frete_rodoviario + sda + rep_porto + 
                        tx_correcao_lacre + li_dta_honor_nix + honorarios_nix;

                    const desp_desenbaraco_parte_2 = multa + taxa_def + taxa_siscomex + capatazia + afrmm + 
                        armazenagem_porto + frete_rodoviario + honorarios_nix;
                    
                    despesa_desembaraco = desp_desenbaraco_parte_1 - desp_desenbaraco_parte_2;
                    
                } else if (nacionalizacao === 'mato_grosso') {
                    // Para Mato Grosso: DESP. DESEMBARAÇO = SOMA(multa:honorario_nix) - (multa+taxa_siscomex+capatazia+afrmm)
                    // Usar valores brutos
                    const outras_taxas_agente = valoresBrutos.outras_taxas_agente || 0;
                    const liberacao_bl = valoresBrutos.liberacao_bl || 0;
                    const desconsolidacao = valoresBrutos.desconsolidacao || 0;
                    const isps_code = valoresBrutos.isps_code || 0;
                    const handling = valoresBrutos.handling || 0;
                    const frete_sts_cgb = valoresBrutos.frete_sts_cgb || 0;
                    const diarias = valoresBrutos.diarias || 0;
                    const sda = valoresBrutos.sda || 0;
                    const rep_sts = valoresBrutos.rep_sts || 0;
                    const armaz_cgb = valoresBrutos.armaz_cgb || 0;
                    const rep_cgb = valoresBrutos.rep_cgb || 0;
                    const demurrage = valoresBrutos.demurrage || 0;
                    const li_dta_honor_nix = valoresBrutos.li_dta_honor_nix || 0;
                    
                    // Parte 1: SOMA(multa:honorario_nix) = MULTA + TX DEF. LI + TAXA SISCOMEX + OUTRAS TX AGENTE + ... + HONORÁRIOS NIX
                    desp_desenbaraco_parte_1 = multa + taxa_def + taxa_siscomex + outras_taxas_agente + 
                        liberacao_bl + desconsolidacao + isps_code + handling + capatazia + afrmm + 
                        armazenagem_sts + frete_sts_cgb + diarias + sda + rep_sts + armaz_cgb + 
                        rep_cgb + demurrage + li_dta_honor_nix + honorarios_nix;
                    
                    // Parte 2: (multa+taxa_siscomex+capatazia+afrmm)
                    const desp_desenbaraco_parte_2 = multa + taxa_siscomex + capatazia + afrmm;
                    despesa_desembaraco = desp_desenbaraco_parte_1 - desp_desenbaraco_parte_2;
                    
                } else if (nacionalizacao !== 'santos') {
                    // Para outras nacionalizações (exceto Santos) - usar valores brutos
                    const outras_taxas_agente = valoresBrutos.outras_taxas_agente || 0;
                    const liberacao_bl = valoresBrutos.liberacao_bl || 0;
                    const desconsolidacao = valoresBrutos.desconsolidacao || 0;
                    const isps_code = valoresBrutos.isps_code || 0;
                    const handling = valoresBrutos.handling || 0;
                    const sda = valoresBrutos.sda || 0;
                    const rep_sts = valoresBrutos.rep_sts || 0;
                    const desp_anapolis = valoresBrutos.desp_anapolis || 0;
                    const rep_anapolis = valoresBrutos.rep_anapolis || 0;
                    const correios = valoresBrutos.correios || 0;
                    const li_dta_honor_nix = valoresBrutos.li_dta_honor_nix || 0;

                    desp_desenbaraco_parte_1 = multa + taxa_def + taxa_siscomex + outras_taxas_agente + liberacao_bl + 
                        desconsolidacao + isps_code + handling + capatazia + afrmm + armazenagem_sts + 
                        frete_dta_sts_ana + sda + rep_sts + desp_anapolis + rep_anapolis + correios + 
                        li_dta_honor_nix + honorarios_nix;

                    const desp_desenbaraco_parte_2 = taxa_siscomex + capatazia + afrmm + honorarios_nix;
                    despesa_desembaraco = desp_desenbaraco_parte_1 - desp_desenbaraco_parte_2;
                    
                } else {
                    // Para Santos - usar valores brutos dos campos externos
                    for (let campo of campos) {
                        let valorDistribuido = 0;
                        
                        if (window.valoresBrutosCamposExternos && 
                            window.valoresBrutosCamposExternos[campo] && 
                            window.valoresBrutosCamposExternos[campo][i] !== undefined) {
                            valorDistribuido = window.valoresBrutosCamposExternos[campo][i];
                        } else {
                            // Fallback: usar valor do objeto valoresBrutos se disponível
                            const nomeCampo = campo.replace(/-/g, '_');
                            valorDistribuido = valoresBrutos[nomeCampo] || 0;
                        }
                        
                        desp_desenbaraco_parte_1 += valorDistribuido;
                    }

                    desp_desenbaraco_parte_1 += multa + taxa_def + taxa_siscomex;

                    const desp_desenbaraco_parte_2 = multa + taxa_def + taxa_siscomex + capatazia + afrmm + honorarios_nix;
                    despesa_desembaraco = desp_desenbaraco_parte_1 - desp_desenbaraco_parte_2;
                }
                
                // Obter valores para cálculo de custo unitário final dos valores brutos
                // Priorizar valores brutos armazenados para máxima precisão
                const vlrIcmsReduzido = valoresBrutos.valor_icms_reduzido || 0;
                const qquantidade = valoresBrutos.quantidade || 0;
                const vlrTotalNfComIcms = valoresBrutos.valor_total_nf_com_icms_st || 0;
                
                // Usar valor bruto de despesa_desembaraco se disponível, caso contrário usar o calculado acima
                const despesa_desembaraco_bruto = valoresBrutos.desp_desenbaraco ?? despesa_desembaraco;
                
                let diferenca_cambial_frete = valoresBrutos.diferenca_cambial_frete || 0;
                diferenca_cambial_frete = validarDiferencaCambialFrete(diferenca_cambial_frete);
                
                const diferenca_cambial_fob = valoresBrutos.diferenca_cambial_fob || 0;
                
                // Adicionar campos opcionais se checkbox marcado - usar valores brutos
                const opcional1Compoe = $('#opcional_1_compoe_despesas').is(':checked');
                const opcional2Compoe = $('#opcional_2_compoe_despesas').is(':checked');
                const opcional1Valor = valoresBrutos.opcional_1_valor || 0;
                const opcional2Valor = valoresBrutos.opcional_2_valor || 0;
                
                let despesasAdicionais = 0;
                if (opcional1Compoe) {
                    despesasAdicionais += opcional1Valor;
                }
                if (opcional2Compoe) {
                    despesasAdicionais += opcional2Valor;
                }
                
                // Obter custo unitário final do valor bruto armazenado (prioridade) ou recalcular
                let valoresBrutosLinha = window.valoresBrutosPorLinha && window.valoresBrutosPorLinha[i];
                let custo_unitario_final = 0;
                
                // Priorizar valor bruto de window.valoresBrutosPorLinha se disponível
                if (valoresBrutosLinha && valoresBrutosLinha.custo_unitario_final !== undefined && valoresBrutosLinha.custo_unitario_final > 0) {
                    custo_unitario_final = valoresBrutosLinha.custo_unitario_final;
                } else {
                    // Calcular custo unitário final conforme nacionalização se não estiver disponível
                    const nacionalizacaoCustoTotalizador = getNacionalizacaoAtual();
                    
                    if (nacionalizacaoCustoTotalizador === 'santos' || nacionalizacaoCustoTotalizador === 'mato_grosso') {
                        // Para Santos e Mato Grosso: (VLR TOTAL NF C/ICMS-ST + DESP. DESEMBARAÇO + DIF.CAMBIAL FOB + DIF. CAMBIAL FRETE) / quantidade
                        // EM MATO GROSSO CUSTO UNIT FINAL É =((AX19+BV19+BW19+BX19)/F19)
                        // AX = VLR TOTAL NF C/ICMS-ST, BV = DESP DESEMBARACO, BW = DIF CAMBIAL FRETE, BX = DIF CAMBIAL FOB, F = QUANTIDADE
                        // Usar sempre valores brutos para máxima precisão
                        if (qquantidade > 0) {
                            custo_unitario_final = (vlrTotalNfComIcms + despesa_desembaraco_bruto + diferenca_cambial_fob + diferenca_cambial_frete) / qquantidade;
                        }
                    } else if (nacionalizacaoCustoTotalizador === 'santa_catarina') {
                        // Para Santa Catarina: ((VLR TOTAL NF C/ICMS-ST + DESP. DESEMBARAÇO + DIF. CAMBIAL FRETE + DIF CAMBIAL FOB) - VLR ICMS REDUZ.) / quantidade
                        // EM SANTA CATARINA CUSTO UNIT FINAL É =(((AZ23+BX23+BY23+BZ23)-AR23)/F23)
                        // AZ = VLR TOTAL NF C/ICMS-ST, BX = DESP. DESEMBARAÇO, BY = DIF. CAMBIAL FRETE, BZ = DIF CAMBIAL FOB, AR = VLR ICMS REDUZ., F = QUANTIDADE
                        // Usar sempre valores brutos para máxima precisão
                        if (qquantidade > 0) {
                            const numerador = (vlrTotalNfComIcms + despesa_desembaraco_bruto + diferenca_cambial_frete + diferenca_cambial_fob) - vlrIcmsReduzido;
                            custo_unitario_final = numerador / qquantidade;
                        }
                    } else {
                        // Para outras: ((VLR TOTAL NF C/ICMS-ST + DESP. DESEMBARAÇO + DIF.CAMBIAL FOB + DIF. CAMBIAL FRETE + DESPESAS_ADICIONAIS) - VLR ICMS REDUZIDO) / quantidade
                        // Usar sempre valores brutos para máxima precisão
                        if (qquantidade > 0) {
                            const numerador = (vlrTotalNfComIcms + despesa_desembaraco_bruto + diferenca_cambial_fob + diferenca_cambial_frete + despesasAdicionais) - vlrIcmsReduzido;
                            custo_unitario_final = numerador / qquantidade;
                        }
                    }
                }
                
                // CUSTO TOTAL FINAL deve ser calculado com o valor bruto de CUSTO UNIT FINAL (não arredondado)
                const custo_total_final = custo_unitario_final * qquantidade;
              
                // Garantir que valoresBrutosLinha existe e atualizar valores brutos de custo_unitario_final e custo_total_final
                if (!valoresBrutosLinha) {
                    if (!window.valoresBrutosPorLinha[i]) {
                        window.valoresBrutosPorLinha[i] = {};
                    }
                    valoresBrutosLinha = window.valoresBrutosPorLinha[i];
                }
                valoresBrutosLinha.custo_unitario_final = custo_unitario_final;
                valoresBrutosLinha.custo_total_final = custo_total_final;
                
                // Calcular novas colunas para Mato Grosso
                const nacionalizacaoAtual = getNacionalizacaoAtual();
                if (nacionalizacaoAtual === 'mato_grosso') {
                    const valorIpi = valoresBrutosLinha ? (valoresBrutosLinha.valor_ipi || 0) : 0;
                    const valorPis = valoresBrutosLinha ? (valoresBrutosLinha.valor_pis || 0) : 0;
                    const valorCofins = valoresBrutosLinha ? (valoresBrutosLinha.valor_cofins || 0) : 0;
                    
                    // DEZ POR CENTO = (custo_unitario_final * 0.1) + custo_unitario_final
                    const dez_porcento = (custo_unitario_final * 0.1) + custo_unitario_final;
                    
                    // CUSTO COM MARGEM = dez_porcento * quantidade
                    const custo_com_margem = dez_porcento * qquantidade;
                    
                    // VLR IPI = valor_ipi
                    const vlr_ipi_mg = valorIpi;
                    
                    // VLR ICMS = 0 (vazio)
                    const vlr_icms_mg = 0;
                    
                    // PIS = valor_pis
                    const pis_mg = valorPis;
                    
                    // COFINS = valor_cofins
                    const cofins_mg = valorCofins;
                    
                    // CUSTO TOTAL FINAL = custo_com_margem - (vlr_ipi + vlr_icms + vlr_pis + vlr_cofins)
                    const custo_total_final_credito = custo_com_margem - (vlr_ipi_mg + vlr_icms_mg + pis_mg + cofins_mg);
                    
                    // CUSTO UNIT CREDITO = custo_total_final_credito / quantidade
                    const custo_unit_credito = qquantidade > 0 ? custo_total_final_credito / qquantidade : 0;
                    
                    // Calcular novas colunas ICMS-ST para Mato Grosso
                    // Ler MVA e ICMS-ST dos inputs (porcentagem) - usar campos específicos _mg
                    const mvaPercent = MoneyUtils.parsePercentage($(`#mva_mg-${i}`).val()) || 0;
                    const icmsStPercent = MoneyUtils.parsePercentage($(`#icms_st_mg-${i}`).val()) || 0;
                    
                    // BC ICMS-ST = custo_total_final_credito * (1 + mva_percent)
                    const bc_icms_st_mg = custo_total_final_credito * (1 + mvaPercent);
                    
                    // VLR ICMS-ST = bc_icms_st_mg * icms_st_percent
                    const vlr_icms_st_mg = bc_icms_st_mg * icmsStPercent;
                    
                    // CUSTO TOTAL C/ICMS ST = custo_total_final_credito + vlr_icms_st_mg
                    const custo_total_c_icms_st = custo_total_final_credito + vlr_icms_st_mg;
                    
                    // CUSTO UNIT C/ICMS ST = custo_total_c_icms_st / quantidade
                    const custo_unit_c_icms_st = qquantidade > 0 ? custo_total_c_icms_st / qquantidade : 0;
                    
                   
                    
                    // Atualizar valores brutos
                    if (valoresBrutosLinha) {
                        valoresBrutosLinha.dez_porcento = dez_porcento;
                        valoresBrutosLinha.custo_com_margem = custo_com_margem;
                        valoresBrutosLinha.vlr_ipi_mg = vlr_ipi_mg;
                        valoresBrutosLinha.vlr_icms_mg = vlr_icms_mg;
                        valoresBrutosLinha.pis_mg = pis_mg;
                        valoresBrutosLinha.cofins_mg = cofins_mg;
                        valoresBrutosLinha.custo_total_final_credito = custo_total_final_credito;
                        valoresBrutosLinha.custo_unit_credito = custo_unit_credito;
                        valoresBrutosLinha.bc_icms_st_mg = bc_icms_st_mg;
                        valoresBrutosLinha.mva_mg = mvaPercent;
                        valoresBrutosLinha.icms_st_mg = icmsStPercent;
                        valoresBrutosLinha.vlr_icms_st_mg = vlr_icms_st_mg;
                        valoresBrutosLinha.custo_total_c_icms_st = custo_total_c_icms_st;
                        valoresBrutosLinha.custo_unit_c_icms_st = custo_unit_c_icms_st;
                    }
                    
                    // Atualizar campos na interface
                    $(`#dez_porcento-${i}`).val(MoneyUtils.formatMoney(dez_porcento, 2));
                    $(`#custo_com_margem-${i}`).val(MoneyUtils.formatMoney(custo_com_margem, 2));
                    $(`#vlr_ipi_mg-${i}`).val(MoneyUtils.formatMoney(vlr_ipi_mg, 2));
                    $(`#vlr_icms_mg-${i}`).val(MoneyUtils.formatMoney(vlr_icms_mg, 2));
                    $(`#pis_mg-${i}`).val(MoneyUtils.formatMoney(pis_mg, 2));
                    $(`#cofins_mg-${i}`).val(MoneyUtils.formatMoney(cofins_mg, 2));
                    $(`#custo_total_final_credito-${i}`).val(MoneyUtils.formatMoney(custo_total_final_credito, 2));
                    $(`#custo_unit_credito-${i}`).val(MoneyUtils.formatMoney(custo_unit_credito, 2));
                    $(`#bc_icms_st_mg-${i}`).val(MoneyUtils.formatMoney(bc_icms_st_mg, 2));
                    $(`#vlr_icms_st_mg-${i}`).val(MoneyUtils.formatMoney(vlr_icms_st_mg, 2));
                    $(`#custo_total_c_icms_st-${i}`).val(MoneyUtils.formatMoney(custo_total_c_icms_st, 2));
                    $(`#custo_unit_c_icms_st-${i}`).val(MoneyUtils.formatMoney(custo_unit_c_icms_st, 2));
                    
                    // Recalcular e atualizar novas colunas: EXPORTADOR, TRIBUTOS, DESPESAS, TOTAL PAGO, PERCENTUAL S/FOB
                    calcularColunasExportadorTributosDespesas(i);
                }
                
                // Atualizar campos na interface
                $(`#desp_desenbaraco-${i}`).val(MoneyUtils.formatMoney(despesa_desembaraco, 2));
                $(`#custo_unitario_final-${i}`).val(MoneyUtils.formatMoney(custo_unitario_final, 2));
                $(`#custo_total_final-${i}`).val(MoneyUtils.formatMoney(custo_total_final, 2));
            }


            atualizarMultaProdutosPorMulta(); // Atualiza multa_complem e dif_impostos para Santa Catarina
            atualizarTotalizadores();
            calcularValoresCPT();
            calcularValoresCIF();
            
            // Ratear campos opcionais
            ratearCamposOpcionais();
        }
        
        // Função para ratear campos opcionais (Opcional 1 e Opcional 2)
        function ratearCamposOpcionais() {
            const lengthTable = $('.linhas-input').length;
            let fobTotalGeral = calcularFobTotalGeral();
            
            // Processar Opcional 1 e Opcional 2
            for (let num = 1; num <= 2; num++) {
                const campoValor = `opcional_${num}_valor`;
                const valorCampo = MoneyUtils.parseMoney($(`#${campoValor}`).val()) || 0;
                
                if (valorCampo === 0) {
                    // Se o valor for zero, limpar todos os campos
                    for (let i = 0; i < lengthTable; i++) {
                        $(`#${campoValor}-${i}`).val('');
                    }
                    continue;
                }
                
                let somaDistribuida = 0;
                const valoresPorLinha = [];
                
                // Primeira passada: distribuir para todas as linhas exceto a última
                for (let i = 0; i < lengthTable - 1; i++) {
                    const fobTotal = MoneyUtils.parseMoney($(`#fob_total_usd-${i}`).val()) || 0;
                    const fator = fobTotalGeral > 0 ? (fobTotal / fobTotalGeral) : 0;
                    
                    const valorCalculado = valorCampo * fator;
                    const valorArredondado = Math.ceil(valorCalculado * 100) / 100;
                    
                    const valorDisponivel = valorCampo - somaDistribuida;
                    const valorFinal = Math.min(valorArredondado, valorDisponivel);
                    
                    valoresPorLinha[i] = valorFinal;
                    somaDistribuida += valorFinal;
                    
                    $(`#${campoValor}-${i}`).val(MoneyUtils.formatMoney(valorFinal, 2));
                }
                
                // Última linha recebe a diferença exata
                const ultimaLinha = lengthTable - 1;
                let somaRecalculada = 0;
                for (let i = 0; i < lengthTable - 1; i++) {
                    somaRecalculada += valoresPorLinha[i] || 0;
                }
                
                let valorUltimaLinha = valorCampo - somaRecalculada;
                if (valorUltimaLinha < 0) {
                    const fatorAjuste = valorCampo / somaRecalculada;
                    somaRecalculada = 0;
                    for (let i = 0; i < lengthTable - 1; i++) {
                        if (valoresPorLinha[i] !== undefined) {
                            valoresPorLinha[i] = Math.floor(valoresPorLinha[i] * fatorAjuste * 100) / 100;
                            somaRecalculada += valoresPorLinha[i];
                            $(`#${campoValor}-${i}`).val(MoneyUtils.formatMoney(valoresPorLinha[i], 2));
                        }
                    }
                    valorUltimaLinha = valorCampo - somaRecalculada;
                }
                
                $(`#${campoValor}-${ultimaLinha}`).val(MoneyUtils.formatMoney(valorUltimaLinha, 2));
            }
        }

        $(document).on('click', '.removeLine', function() {
            Swal.fire({
                title: 'Você tem certeza que deseja excluir este registro?',
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
                    const id = this.dataset.id
                    $(`#row-${id}`).remove();
                    setTimeout(() => {
                        atualizarMultaProdutosPorMulta();
                        atualizarTotalizadores();
                    }, 100);

                } else {
                    Toast.fire({
                        icon: 'info',
                        title: 'Ação cancelada'
                    });
                }
            });
        })

        function calcularTaxaSiscomex() {
            // Selecionar apenas inputs de adição que estão em linhas de produtos válidas (não separadores)
            const valores = $('#productsBody tr:not(.separador-adicao) input[name^="produtos["][name$="[adicao]"]')
                .map(function() {
                    const valor = $(this).val();
                    // Retornar apenas valores não vazios e que não sejam apenas espaços
                    return valor && valor.trim() !== '' ? valor.trim() : null;
                })
                .get()
                .filter(v => v !== null && v !== '');
            // Obter valores únicos de adição
            const unicos = [...new Set(valores)];
            const quantidade = unicos.length;


            const valorRegistroDI = 115.67;

            if (quantidade === 0) {
                return valorRegistroDI;
            }



            const faixas = [
                { min: 1, max: 2, valor: 38.56 },      
                { min: 3, max: 5, valor: 30.85 },      
                { min: 6, max: 10, valor: 23.14 },      
                { min: 11, max: 20, valor: 15.42 },   
                { min: 21, max: 50, valor: 7.71 },     
                { min: 51, max: Infinity, valor: 3.86 } 
            ];


            let total = valorRegistroDI;


            faixas.forEach(faixa => {
                if (quantidade < faixa.min) {
                    return;
                }
                
                const inicioFaixa = faixa.min;
                const fimFaixa = faixa.max === Infinity ? quantidade : Math.min(quantidade, faixa.max);
                const adicoesNaFaixa = fimFaixa - inicioFaixa + 1;

                if (adicoesNaFaixa > 0) {
                    total += adicoesNaFaixa * faixa.valor;
                }
            });

            return total; 
        }

        function obterProximoNumeroItem() {
            let maiorItem = 0;
            $('[id^="item-"]').each(function() {
                const valorItem = parseInt($(this).val()) || 0;
                if (valorItem > maiorItem) {
                    maiorItem = valorItem;
                }
            });
            return maiorItem + 1;
        }

        $(document).on('click', '.addProduct', function() {
            // Desabilitar todos os botões de adicionar produto para evitar múltiplos cliques
            const $btn = $(this);
            const $allAddButtons = $('.addProduct');
            
            // Verificar se já está processando
            if ($btn.prop('disabled') || $btn.data('processing')) {
                return false;
            }
            
            // Marcar como processando e desabilitar
            $allAddButtons.prop('disabled', true).data('processing', true);
            const originalHtml = $btn.html();
            $btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Adicionando...');
            
            try {
            let lengthOptions = $('#productsBody tr').length;
            let newIndex = lengthOptions;
            let proximoItem = obterProximoNumeroItem();

            const selectOptions = useProductsAjax
                ? '<option></option>'
                : `<option selected disabled>Selecione uma opção</option>${productOptionsHtml}`;
            let select = `<select required data-row="${newIndex}" class="custom-select selectProduct select2" name="produtos[${newIndex}][produto_id]" id="produto_id-${newIndex}">
        ${selectOptions}</select>`;


            let moedaFrete = $('#frete_internacional_moeda').val();
            let moedaSeguro = $('#seguro_internacional_moeda').val();
            let moedaAcrescimo = $('#acrescimo_frete_moeda').val();
            let moedaProcesso = 'USD'; 
            const multaReadonly = getNacionalizacaoAtual() === 'santa_catarina' ? 'readonly' : '';


            let moedaServiceCharges = $('#service_charges_moeda').val();
            
            let colunaFreteMoeda = '';
            let colunaSeguroMoeda = '';
            let colunaAcrescimoMoeda = '';
            let colunaServiceChargesMoeda = '';

            if (moedaFrete && moedaFrete !== 'USD') {
                colunaFreteMoeda =
                    `<td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][frete_moeda_estrangeira]" id="frete_moeda_estrangeira-${newIndex}" value=""></td>`;
            }

            if (moedaServiceCharges && moedaServiceCharges !== 'USD') {
                colunaServiceChargesMoeda =
                    `<td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][service_charges_moeda_estrangeira]" id="service_charges_moeda_estrangeira-${newIndex}" value=""></td>`;
            }

            if (moedaAcrescimo && moedaAcrescimo !== 'USD') {
                colunaAcrescimoMoeda =
                    `<td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][acrescimo_moeda_estrangeira]" id="acrescimo_moeda_estrangeira-${newIndex}" value=""></td>`;
            }

            if (moedaSeguro && moedaSeguro !== 'USD') {
                colunaSeguroMoeda =
                    `<td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][seguro_moeda_estrangeira]" id="seguro_moeda_estrangeira-${newIndex}" value=""></td>`;
            }

            let colunasFOB = getColunasFOBCondicionais(newIndex, moedaProcesso);

            let tr = `<tr class="linhas-input" id="row-${newIndex}">
        <td class="d-flex align-items-center justify-content-center">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-danger removeLine btn-sm btn-remove" data-id="${newIndex}">
                    <i class="fas fa-trash-alt"></i>
                </button>
                <button type="button" class="btn btn-outline-info btn-sm btn-debug-linha" data-row="${newIndex}" title="Cálculo da linha">
                    <i class="fas fa-info-circle"></i>
                </button>
            </div>
            <input type="checkbox" style="margin-left: 10px" class="select-produto" value="">
        </td>
        
        <input type="hidden" name="produtos[${newIndex}][processo_produto_id]" id="processo_produto_id-${newIndex}" value="">
        
        <td>${select}</td>
        <td><input data-row="${newIndex}" type="text" step="1" class="form-control" name="produtos[${newIndex}][descricao]" id="descricao-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control" name="produtos[${newIndex}][adicao]" id="adicao-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="number" class="form-control" name="produtos[${newIndex}][item]" id="item-${newIndex}" value="${proximoItem}"></td>
        <td><input type="text" class="form-control" readonly name="produtos[${newIndex}][codigo]" id="codigo-${newIndex}" value=""></td>
        <td><input type="text" class="form-control" readonly name="produtos[${newIndex}][ncm]" id="ncm-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" name="produtos[${newIndex}][quantidade]" id="quantidade-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][peso_liquido_unitario]" id="peso_liquido_unitario-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal pesoLiqTotal" name="produtos[${newIndex}][peso_liquido_total]" id="peso_liquido_total-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][fator_peso]" id="fator_peso-${newIndex}" value=""></td>
        ${colunasFOB}
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal7" readonly name="produtos[${newIndex}][fob_total_usd]" id="fob_total_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal7" readonly name="produtos[${newIndex}][fob_total_brl]" id="fob_total_brl-${newIndex}" value=""></td>
        
        <!-- FRETE -->
        ${colunaFreteMoeda}
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][frete_usd]" id="frete_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][frete_brl]" id="frete_brl-${newIndex}" value=""></td>
        
        <!-- VLR CFR - Após FRETE -->
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][vlr_crf_unit]" id="vlr_crf_unit-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][vlr_crf_total]" id="vlr_crf_total-${newIndex}" value=""></td>
        
        <!-- SERVICE CHARGES - Colunas condicionais (após CFR) -->
        ${colunaServiceChargesMoeda}
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][service_charges]" id="service_charges-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][service_charges_brl]" id="service_charges_brl-${newIndex}" value=""></td>
        
        <!-- ACRÉSCIMO - Colunas condicionais (após SERVICE CHARGES) -->
        ${colunaAcrescimoMoeda}
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][acresc_frete_usd]" id="acresc_frete_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][acresc_frete_brl]" id="acresc_frete_brl-${newIndex}" value=""></td>
        
        <!-- SEGURO - Colunas condicionais (após ACRÉSCIMO) -->
        ${colunaSeguroMoeda}
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][seguro_usd]" id="seguro_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][seguro_brl]" id="seguro_brl-${newIndex}" value=""></td>
        
        <!-- Resto das colunas permanecem iguais -->
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][thc_usd]" id="thc_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][thc_brl]" id="thc_brl-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_aduaneiro_usd]" id="valor_aduaneiro_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_aduaneiro_brl]" id="valor_aduaneiro_brl-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control percentage2" name="produtos[${newIndex}][ii_percent]" id="ii_percent-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control percentage2" name="produtos[${newIndex}][ipi_percent]" id="ipi_percent-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control percentage2" name="produtos[${newIndex}][pis_percent]" id="pis_percent-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control percentage2" name="produtos[${newIndex}][cofins_percent]" id="cofins_percent-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control percentage2" name="produtos[${newIndex}][icms_percent]" id="icms_percent-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control percentage2 icms_reduzido_percent" name="produtos[${newIndex}][icms_reduzido_percent]" id="icms_reduzido_percent-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][reducao]" id="reducao-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_ii]" id="valor_ii-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][base_ipi]" id="base_ipi-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_ipi]" id="valor_ipi-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][base_pis_cofins]" id="base_pis_cofins-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_pis]" id="valor_pis-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_cofins]" id="valor_cofins-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][despesa_aduaneira]" id="despesa_aduaneira-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][base_icms_sem_reducao]" id="base_icms_sem_reducao-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_icms_sem_reducao]" id="valor_icms_sem_reducao-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][base_icms_reduzido]" id="base_icms_reduzido-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_icms_reduzido]" id="valor_icms_reduzido-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_unit_nf]" id="valor_unit_nf-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_total_nf]" id="valor_total_nf-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_total_nf_sem_icms_st]" id="valor_total_nf_sem_icms_st-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][base_icms_st]" id="base_icms_st-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control percentage" name="produtos[${newIndex}][mva]" id="mva-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control percentage" name="produtos[${newIndex}][icms_st]" id="icms_st-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_icms_st]" id="valor_icms_st-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_total_nf_com_icms_st]" id="valor_total_nf_com_icms_st-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][fator_valor_fob]" id="fator_valor_fob-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][fator_tx_siscomex]" id="fator_tx_siscomex-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal7" name="produtos[${newIndex}][multa]" id="multa-${newIndex}" value="" ${multaReadonly}></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control ${getNacionalizacaoAtual() === 'mato_grosso' ? 'moneyReal' : 'percentage2'}" ${getNacionalizacaoAtual() === 'mato_grosso' ? 'readonly' : ''} name="produtos[${newIndex}][tx_def_li]" id="tx_def_li-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][taxa_siscomex]" id="taxa_siscomex-${newIndex}" value=""></td>
        ${(() => {
            const nacionalizacao = getNacionalizacaoAtual();
            let html = '';
            
            if (nacionalizacao === 'anapolis') {
                // Ordem específica para Anápolis
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][outras_taxas_agente]" id="outras_taxas_agente-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][liberacao_bl]" id="liberacao_bl-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][desconsolidacao]" id="desconsolidacao-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][isps_code]" id="isps_code-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][handling]" id="handling-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][capatazia]" id="capatazia-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][afrmm]" id="afrmm-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][armazenagem_sts]" id="armazenagem_sts-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][frete_dta_sts_ana]" id="frete_dta_sts_ana-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][sda]" id="sda-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][rep_sts]" id="rep_sts-' + newIndex + '" value=""></td>';
                html += '<td data-campo="desp_anapolis"><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][desp_anapolis]" id="desp_anapolis-' + newIndex + '" value=""></td>';
                html += '<td data-campo="rep_anapolis"><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][rep_anapolis]" id="rep_anapolis-' + newIndex + '" value=""></td>';
                html += '<td data-campo="correios"><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][correios]" id="correios-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal7" readonly name="produtos[' + newIndex + '][li_dta_honor_nix]" id="li_dta_honor_nix-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal7" readonly name="produtos[' + newIndex + '][honorarios_nix]" id="honorarios_nix-' + newIndex + '" value=""></td>';
            } else if (nacionalizacao === 'santa_catarina') {
                // Ordem específica para Santa Catarina
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][multa_complem]" id="multa_complem-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][dif_impostos]" id="dif_impostos-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][outras_taxas_agente]" id="outras_taxas_agente-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][liberacao_bl]" id="liberacao_bl-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][desconsolidacao]" id="desconsolidacao-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][isps_code]" id="isps_code-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][handling]" id="handling-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][capatazia]" id="capatazia-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][afrmm]" id="afrmm-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][armazenagem_porto]" id="armazenagem_porto-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][frete_rodoviario]" id="frete_rodoviario-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][dif_frete_rodoviario]" id="dif_frete_rodoviario-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][sda]" id="sda-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][rep_porto]" id="rep_porto-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][tx_correcao_lacre]" id="tx_correcao_lacre-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal7" readonly name="produtos[' + newIndex + '][li_dta_honor_nix]" id="li_dta_honor_nix-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal7" readonly name="produtos[' + newIndex + '][honorarios_nix]" id="honorarios_nix-' + newIndex + '" value=""></td>';
            } else if (nacionalizacao === 'mato_grosso') {
                // Ordem específica para Mato Grosso
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][outras_taxas_agente]" id="outras_taxas_agente-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][liberacao_bl]" id="liberacao_bl-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][desconsolidacao]" id="desconsolidacao-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][isps_code]" id="isps_code-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][handling]" id="handling-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][capatazia]" id="capatazia-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][afrmm]" id="afrmm-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][armazenagem_sts]" id="armazenagem_sts-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][frete_sts_cgb]" id="frete_sts_cgb-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][diarias]" id="diarias-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][sda]" id="sda-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][rep_sts]" id="rep_sts-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][armaz_cgb]" id="armaz_cgb-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][rep_cgb]" id="rep_cgb-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][demurrage]" id="demurrage-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][tx_def_li]" id="tx_def_li-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal7" readonly name="produtos[' + newIndex + '][li_dta_honor_nix]" id="li_dta_honor_nix-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal7" readonly name="produtos[' + newIndex + '][honorarios_nix]" id="honorarios_nix-' + newIndex + '" value=""></td>';
            } else {
                // Ordem padrão para outros tipos
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][outras_taxas_agente]" id="outras_taxas_agente-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][liberacao_bl]" id="liberacao_bl-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][desconsolidacao]" id="desconsolidacao-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][isps_code]" id="isps_code-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][handling]" id="handling-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][capatazia]" id="capatazia-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][afrmm]" id="afrmm-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][armazenagem_sts]" id="armazenagem_sts-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][frete_dta_sts_ana]" id="frete_dta_sts_ana-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][sda]" id="sda-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][rep_sts]" id="rep_sts-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][armaz_ana]" id="armaz_ana-' + newIndex + '" value=""></td>';
                if (nacionalizacao === 'santos') {
                    html += '<td data-campo="tx_correcao_lacre"><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][tx_correcao_lacre]" id="tx_correcao_lacre-' + newIndex + '" value=""></td>';
                }
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][lavagem_container]" id="lavagem_container-' + newIndex + '" value=""></td>';
                if (nacionalizacao !== 'santos') {
                    html += '<td data-campo="rep_anapolis"><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][rep_anapolis]" id="rep_anapolis-' + newIndex + '" value=""></td>';
                    html += '<td data-campo="desp_anapolis" style="display: none;"><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][desp_anapolis]" id="desp_anapolis-' + newIndex + '" value=""></td>';
                    html += '<td data-campo="correios"><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][correios]" id="correios-' + newIndex + '" value=""></td>';
                }
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal7" readonly name="produtos[' + newIndex + '][li_dta_honor_nix]" id="li_dta_honor_nix-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal7" readonly name="produtos[' + newIndex + '][honorarios_nix]" id="honorarios_nix-' + newIndex + '" value=""></td>';
            }
            
            return html;
        })()}
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][desp_desenbaraco]" id="desp_desenbaraco-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][diferenca_cambial_frete]" id="diferenca_cambial_frete-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][diferenca_cambial_fob]" id="diferenca_cambial_fob-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][opcional_1_valor]" id="opcional_1_valor-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][opcional_2_valor]" id="opcional_2_valor-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][custo_unitario_final]" id="custo_unitario_final-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][custo_total_final]" id="custo_total_final-${newIndex}" value=""></td>
        ${getNacionalizacaoAtual() === 'mato_grosso' ? 
            '<td><input type="text" data-row="' + newIndex + '" class="form-control moneyReal2" readonly name="produtos[' + newIndex + '][dez_porcento]" id="dez_porcento-' + newIndex + '" value=""></td>' +
            '<td><input type="text" data-row="' + newIndex + '" class="form-control moneyReal2" readonly name="produtos[' + newIndex + '][custo_com_margem]" id="custo_com_margem-' + newIndex + '" value=""></td>' +
            '<td><input type="text" data-row="' + newIndex + '" class="form-control moneyReal2" readonly name="produtos[' + newIndex + '][vlr_ipi_mg]" id="vlr_ipi_mg-' + newIndex + '" value=""></td>' +
            '<td><input type="text" data-row="' + newIndex + '" class="form-control moneyReal2" readonly name="produtos[' + newIndex + '][vlr_icms_mg]" id="vlr_icms_mg-' + newIndex + '" value=""></td>' +
            '<td><input type="text" data-row="' + newIndex + '" class="form-control moneyReal2" readonly name="produtos[' + newIndex + '][pis_mg]" id="pis_mg-' + newIndex + '" value=""></td>' +
            '<td><input type="text" data-row="' + newIndex + '" class="form-control moneyReal2" readonly name="produtos[' + newIndex + '][cofins_mg]" id="cofins_mg-' + newIndex + '" value=""></td>' +
            '<td><input type="text" data-row="' + newIndex + '" class="form-control moneyReal2" readonly name="produtos[' + newIndex + '][custo_total_final_credito]" id="custo_total_final_credito-' + newIndex + '" value=""></td>' +
            '<td><input type="text" data-row="' + newIndex + '" class="form-control moneyReal2" readonly name="produtos[' + newIndex + '][custo_unit_credito]" id="custo_unit_credito-' + newIndex + '" value=""></td>' +
            '<td><input type="text" data-row="' + newIndex + '" class="form-control moneyReal2" readonly name="produtos[' + newIndex + '][bc_icms_st_mg]" id="bc_icms_st_mg-' + newIndex + '" value=""></td>' +
            '<td><input type="text" data-row="' + newIndex + '" class="form-control percentage2" name="produtos[' + newIndex + '][mva_mg]" id="mva_mg-' + newIndex + '" value=""></td>' +
            '<td><input type="text" data-row="' + newIndex + '" class="form-control percentage2" name="produtos[' + newIndex + '][icms_st_mg]" id="icms_st_mg-' + newIndex + '" value=""></td>' +
            '<td><input type="text" data-row="' + newIndex + '" class="form-control moneyReal2" readonly name="produtos[' + newIndex + '][vlr_icms_st_mg]" id="vlr_icms_st_mg-' + newIndex + '" value=""></td>' +
            '<td><input type="text" data-row="' + newIndex + '" class="form-control moneyReal2" readonly name="produtos[' + newIndex + '][custo_total_c_icms_st]" id="custo_total_c_icms_st-' + newIndex + '" value=""></td>' +
            '<td><input type="text" data-row="' + newIndex + '" class="form-control moneyReal2" readonly name="produtos[' + newIndex + '][custo_unit_c_icms_st]" id="custo_unit_c_icms_st-' + newIndex + '" value=""></td>' +
            '<td><input type="text" data-row="' + newIndex + '" class="form-control moneyReal2" readonly name="produtos[' + newIndex + '][exportador_mg]" id="exportador_mg-' + newIndex + '" value=""></td>' +
            '<td><input type="text" data-row="' + newIndex + '" class="form-control moneyReal2" readonly name="produtos[' + newIndex + '][tributos_mg]" id="tributos_mg-' + newIndex + '" value=""></td>' +
            '<td><input type="text" data-row="' + newIndex + '" class="form-control moneyReal2" readonly name="produtos[' + newIndex + '][despesas_mg]" id="despesas_mg-' + newIndex + '" value=""></td>' +
            '<td><input type="text" data-row="' + newIndex + '" class="form-control moneyReal2" readonly name="produtos[' + newIndex + '][total_pago_mg]" id="total_pago_mg-' + newIndex + '" value=""></td>' +
            '<td><input type="text" data-row="' + newIndex + '" class="form-control percentage2" readonly name="produtos[' + newIndex + '][percentual_s_fob_mg]" id="percentual_s_fob_mg-' + newIndex + '" value=""></td>'
            : ''
        }
    </tr>`;

            $('#productsBody').append(tr);

            if (useProductsAjax) {
                initProductSelectAjax($(`#produto_id-${newIndex}`));
            } else {
            $(`#produto_id-${newIndex}`).select2({
                width: '100%'
            });
            }
            $('input[data-row="' + newIndex + '"]').trigger('change');

            $(`#row-${newIndex} input, #row-${newIndex} select, #row-${newIndex} textarea`).each(function() {
                initialInputs.add(this);
            });
            setTimeout(() => {
                atualizarMultaProdutosPorMulta();
                atualizarTotalizadores();
                
                // Reabilitar botões após processamento
                $allAddButtons.prop('disabled', false).data('processing', false);
                $btn.html(originalHtml);
            }, 100);

            } catch (error) {
                console.error('Erro ao adicionar produto:', error);
                // Reabilitar botões em caso de erro
                $allAddButtons.prop('disabled', false).data('processing', false);
                $btn.html(originalHtml);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Erro ao adicionar produto. Tente novamente.',
                    confirmButtonText: 'OK'
                });
            }

        });

        $(document).ready(function($) {

            var activeTab = localStorage.getItem('activeTab_processo');
            if (activeTab) {
                $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
            }
            

            setTimeout(function() {
                const pesoLiquidoTotal = calcularPesoTotal();
                if (pesoLiquidoTotal > 0) {
                    atualizarPesoLiquidoTotal(pesoLiquidoTotal);
                }
            }, 500);

            const $nacionalizacao = $('#nacionalizacao');
            if ($nacionalizacao.length) {
                $nacionalizacao.data('valor-anterior', $nacionalizacao.val());
            }

            atualizarVisibilidadeNacionalizacao();
            
            // Inicializar campo moeda_processo_usd ao carregar a página
            function inicializarMoedaProcessoUSD() {
                const moeda = $('#moeda_processo').val();
                const campoMoedaUSD = $('#moeda_processo_usd');
                
                if (!moeda || moeda === 'USD') {
                    $('#visualizacaoMoedaDolar').addClass('d-none').removeClass('col-12');
                    campoMoedaUSD.val('');
                    return;
                }
                
                // Sempre recalcular para garantir que está correto
                const cotacaoMoedaFloat = MoneyUtils.parseMoney($('#display_cotacao').val());
                
                if (cotacaoMoedaFloat) {
                    const cotacoesProcesso = getCotacaoesProcesso();
                    const cotacaoUSD = cotacoesProcesso['USD']?.venda ?? 1;
                    
                    if (cotacaoUSD > 0) {
                        const moedaEmUSD = cotacaoMoedaFloat / cotacaoUSD;
                        campoMoedaUSD.val(MoneyUtils.formatMoney(moedaEmUSD, 4));
                        $('#visualizacaoMoedaDolar').removeClass('d-none').addClass('col-12');
                    }
                } else {
                    // Se não tem cotação, tentar usar o valor salvo no campo
                    const valorSalvo = campoMoedaUSD.val();
                    if (valorSalvo && valorSalvo.trim() !== '') {
                        $('#visualizacaoMoedaDolar').removeClass('d-none').addClass('col-12');
                    }
                }
            }
            
            // Chamar inicialização após um pequeno delay para garantir que os campos estejam carregados
            setTimeout(function() {
                inicializarMoedaProcessoUSD();
            }, 500);
            
            // Inicializar window.valoresBrutosPorLinha se não existir
            if (!window.valoresBrutosPorLinha) {
                window.valoresBrutosPorLinha = {};
            }
            
            // Chamar recalcular toda tabela ao carregar a página (após 1.5 segundos para garantir que dados estejam carregados)
            setTimeout(function() {
                try {
                    // Verificar se há linhas antes de recalcular
                    const hasRows = $('#productsBody .linhas-input').length > 0;
                    if (hasRows) {
                    recalcularTodaTabela();
                calcularValoresCPT();
                        calcularValoresCIF();
                
                // Chamar novamente após a tabela de multa estar carregada para garantir valores corretos
                setTimeout(function() {
                    atualizarMultaProdutosPorMulta();
                    atualizarTotalizadores();
                            
                            // Verificar se valores ainda estão zerados e recalcular novamente se necessário
                            setTimeout(function() {
                                let valoresZerados = false;
                                $('#productsBody .linhas-input').each(function() {
                                    const rowId = this.id.toString().replace('row-', '');
                                    if (rowId && !rowId.includes('multa')) {
                                        const custoUnitFinal = MoneyUtils.parseMoney($(`#custo_unitario_final-${rowId}`).val()) || 0;
                                        if (custoUnitFinal === 0 && window.valoresBrutosPorLinha[rowId]?.custo_unitario_final > 0) {
                                            valoresZerados = true;
                                            return false; // break
                                        }
                                    }
                                });
                                
                                if (valoresZerados) {
                                    recalcularTodaTabela();
                                    calcularValoresCPT();
                                    calcularValoresCIF();
                                    atualizarTotalizadores();
                                }
                }, 500);
                        }, 500);
                    } else {
                        atualizarTotalizadores();
                    }
                } catch (error) {
                    console.error('Erro ao recalcular tabela ao carregar página:', error);
                    // Se houver erro, pelo menos atualizar totalizadores
                    atualizarTotalizadores();
                }
            }, 1500);

        })
        $('.nav-link').on('click', function(e) {
            var currentTab = $(e.target).attr('href');
            localStorage.setItem('activeTab_processo', currentTab);
        });

        function adicionarSeparadoresAdicao() {
            const tbody = document.getElementById('productsBody');
            const linhas = Array.from(tbody.querySelectorAll('tr:not(.separador-adicao)'));


            document.querySelectorAll('.separador-adicao').forEach(el => el.remove());

            if (linhas.length === 0) return;


            linhas.sort((a, b) => {
                const inputAdicaoA = a.querySelector('input[name*="[adicao]"]');
                const inputAdicaoB = b.querySelector('input[name*="[adicao]"]');
                const adicaoA = inputAdicaoA ? parseFloat(inputAdicaoA.value) || 0 : 0;
                const adicaoB = inputAdicaoB ? parseFloat(inputAdicaoB.value) || 0 : 0;

                if (adicaoA !== adicaoB) {
                    return adicaoA - adicaoB; 
                }

                const inputItemA = a.querySelector('input[name*="[item]"]');
                const inputItemB = b.querySelector('input[name*="[item]"]');
                const itemA = inputItemA ? parseFloat(inputItemA.value) || 0 : 0;
                const itemB = inputItemB ? parseFloat(inputItemB.value) || 0 : 0;
                return itemA - itemB; 
            });


            const grupos = {};
            linhas.forEach(linha => {
                const inputAdicao = linha.querySelector('input[name*="[adicao]"]');
                const adicao = inputAdicao ? parseFloat(inputAdicao.value) || 0 : 0;
                if (!grupos[adicao]) grupos[adicao] = [];
                grupos[adicao].push(linha);
            });


            while (tbody.firstChild) {
                tbody.removeChild(tbody.firstChild);
            }


            Object.keys(grupos).sort((a, b) => a - b).forEach((adicao, index) => {
                if (index > 0) {
                    const separador = document.createElement('tr');
                    separador.className = 'separador-adicao';
                    separador.innerHTML =
                        `<td colspan="100" style="background-color: #000 !important; height: 2px; padding: 0;"></td>`;
                    tbody.appendChild(separador);
                }

                grupos[adicao].forEach(linha => {
                    tbody.appendChild(linha);
                });
            });
        }




        function reordenarLinhas() {

            if (reordenando) return;

            reordenando = true;

            adicionarSeparadoresAdicao();

            $('#productsBody input:not([name*="[adicao]"])').trigger('change');
            $('#productsBody select').trigger('change');

            reordenando = false;
        }


        const style = document.createElement('style');
        style.textContent = `
    .separador-adicao td {
        background-color: #000 !important;
        border: none !important;
        height: 2px;
        padding: 0 !important;
    }
    .separador-adicao:hover {
        background-color: transparent !important;
    }
`;
        document.head.appendChild(style);

        $(document).on('change', 'input[name*="[adicao]"]', reordenarLinhas);
        $(document).on('click', '.btn-reordenar', reordenarLinhas);

        {{-- Include de lógica da tabela Multa --}}
        @include('processo.includes.scripts.processo-multa')
    </script>
@endsection
