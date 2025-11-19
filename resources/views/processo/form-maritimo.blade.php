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
            /* text-align: center; */
            vertical-align: middle;
        }

        /* Removido sticky da primeira coluna - agora o cabeçalho inteiro será fixo */
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

        /* Fixa o cabeçalho inteiro durante scroll vertical */
        .table-products thead th {
            position: sticky;
            top: 0;
            z-index: 100;
        }

        /* Ajusta a segunda linha do cabeçalho (middleRow) */
        .table-products thead tr.middleRow th {
            background-color: transparent;
        }

        /* Estilos para a barra de scroll extra acima do cabeçalho */
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
        let products = JSON.parse($('#productsClient').val());
        let debugStore = {};
        let debugGlobals = {};

        function resetDebugStore() {
            debugStore = {};
            debugGlobals = {};
        }

        function setDebugGlobals(payload) {
            debugGlobals = {
                ...(payload || {})
            };
        }

        function addDebugEntry(rowId, payload) {
            debugStore[rowId] = {
                ...(debugStore[rowId] || {}),
                ...payload,
                atualizadoEm: new Date().toLocaleString('pt-BR')
            };
        }

        function getDebugFobData() {
            if (!debugStore || Object.keys(debugStore).length === 0) {
                return [];
            }

            const dados = [];
            Object.entries(debugStore).forEach(([rowId, dadosLinha]) => {
                const elementoLinha = document.getElementById(`row-${rowId}`);
                if (!elementoLinha) {
                    return;
                }
                const fobTotal = toNumber(dadosLinha?.fobTotal);
                if (!isNaN(fobTotal)) {
                    dados.push({
                        rowId,
                        fobTotal
                    });
                }
            });
            return dados;
        }

        function normalizeNumericValue(value) {
            if (value === null || value === undefined || value === '') return 0;
            if (typeof value === 'number') return value;
            if (typeof value === 'string') {
                let normalized = value.trim()
                    .replace(/\s/g, '')
                    .replace(/\./g, '')
                    .replace(',', '.')
                    .replace(/[^\d.-]/g, '');
                const parsed = parseFloat(normalized);
                if (!isNaN(parsed)) {
                    return parsed;
                }
            }
            return Number(value) || 0;
        }

        function truncateNumber(value, decimals = 2) {
            const num = normalizeNumericValue(value);
            if (!isFinite(num)) return 0;
            if (decimals <= 0) {
                return num >= 0 ? Math.floor(num) : Math.ceil(num);
            }

            const factor = Math.pow(10, decimals);
            if (num >= 0) {
                return Math.floor(num * factor) / factor;
            }
            return Math.ceil(num * factor) / factor;
        }

        function formatTruncatedNumber(value, decimals = 2, options = {}) {
            const truncated = truncateNumber(value, decimals);
            return truncated.toLocaleString('pt-BR', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals,
                useGrouping: options.useGrouping !== false
            });
        }

        function toNumber(value) {
            return normalizeNumericValue(value);
        }

        function formatPlainNumber(value, decimals = 4, options = {}) {
            if (value === null || value === undefined || value === '') return '-';
            const num = toNumber(value);
            if (!isFinite(num)) return '-';
            const truncated = truncateNumber(num, decimals);
            let text = truncated.toFixed(decimals);
            if (!options.keepTrailingZeros) {
                text = text.replace(/\.?0+$/, '');
            }
            text = text.replace('.', ',');
            return text || '0';
        }

        function formatRawValue(value, decimals = 10) {
            if (value === null || value === undefined || value === '') return '-';
            const num = toNumber(value);
            if (!isFinite(num)) return '-';
            const truncated = truncateNumber(num, decimals);
            let text = truncated.toFixed(decimals).replace(/\.?0+$/, '');
            return text.replace('.', ',');
        }

        function formatComponent(label, value, decimals = 10) {
            return `${label} (${formatRawValue(value, decimals)})`;
        }

        function formatCalcDetail(result, parts, decimals = 10) {
            const filteredParts = (parts || []).filter(part => part !== null && part !== undefined && part !== '');
            const expression = filteredParts.join(' ');
            const formattedResult = formatRawValue(result, decimals);
            if (!expression) {
                return formattedResult;
            }
            return `${expression} = ${formattedResult}`;
        }

        function formatDebugMoney(value, decimals = 10) {
            return formatRawValue(value, decimals);
        }

        function formatDebugPercentage(value, decimals = 6) {
            if (value === undefined || value === null) {
                return '-';
            }
            const percentValue = toNumber(value) * 100;
            return `${formatRawValue(percentValue, decimals)} %`;
        }

        function buildRow(label, value, formula, detail = '-') {
            return { label, value, formula, detail: detail || '-' };
        }

        function buildGlobalRows(globais) {
            if (!globais || Object.keys(globais).length === 0) return [];
            return [
                buildRow(
                    'FOB Total do processo (USD)',
                    formatDebugMoney(globais.fobTotalProcesso, 4),
                    'Soma de todos os FOB TOTAL USD das linhas.',
                    formatCalcDetail(globais.fobTotalProcesso, [formatComponent('Σ FOB linhas', globais.fobTotalProcesso, 4)], 4)
                ),
                buildRow(
                    'Peso Líq. Total do processo',
                    globais.pesoTotalProcesso ?? '-',
                    'Soma de todos os pesos líquidos totais.',
                    formatCalcDetail(globais.pesoTotalProcesso, [formatComponent('Σ Pesos líquidos', globais.pesoTotalProcesso, 4)], 4)
                ),
                buildRow(
                    'Cotação USD utilizada',
                    formatDebugMoney(globais.cotacaoUSD, 4),
                    'Cotação usada nos cálculos do processo.',
                    formatCalcDetail(globais.cotacaoUSD, [formatComponent('Cotação USD', globais.cotacaoUSD, 4)], 4)
                ),
                buildRow(
                    'Taxa SISCOMEX do processo (R$)',
                    formatDebugMoney(globais.taxaSiscomexProcesso, 2),
                    'Valor informado em Taxa SISCOMEX (processo).',
                    formatCalcDetail(globais.taxaSiscomexProcesso, [formatComponent('Valor digitado', globais.taxaSiscomexProcesso, 2)], 2)
                ),
                buildRow(
                    'Frete total do processo (USD)',
                    formatDebugMoney(globais.freteProcessoUSD, 4),
                    'Frete informado convertido para USD.',
                    formatCalcDetail(globais.freteProcessoUSD, [formatComponent('Frete convertido', globais.freteProcessoUSD, 4)], 4)
                ),
                buildRow(
                    'Seguro total do processo (USD)',
                    formatDebugMoney(globais.seguroProcessoUSD, 4),
                    'Seguro informado convertido para USD.',
                    formatCalcDetail(globais.seguroProcessoUSD, [formatComponent('Seguro convertido', globais.seguroProcessoUSD, 4)], 4)
                ),
                buildRow(
                    'Acréscimo frete total (USD)',
                    formatDebugMoney(globais.acrescimoProcessoUSD, 4),
                    'Acréscimo informado convertido para USD.',
                    formatCalcDetail(globais.acrescimoProcessoUSD, [formatComponent('Acréscimo convertido', globais.acrescimoProcessoUSD, 4)], 4)
                ),
                buildRow(
                    'Service charges total (USD)',
                    formatDebugMoney(globais.serviceChargesProcessoUSD, 4),
                    'Service charges informados convertidos para USD.',
                    formatCalcDetail(globais.serviceChargesProcessoUSD, [formatComponent('Service charges convertidos', globais.serviceChargesProcessoUSD, 4)], 4)
                ),
            ];
        }

        function buildDebugRows(dados, globais) {
            const pesoTotalProcesso = toNumber(globais?.pesoTotalProcesso);
            const valorAduaneiroBrl = toNumber(dados.valorAduaneiroBrl);
            const quantidade = toNumber(dados.quantidade || 0) || 0;
            const fobUnitario = toNumber(dados.fobUnitario);
            const fobTotal = toNumber(dados.fobTotal);
            const fatorPeso = toNumber(dados.fatorPeso);
            const freteProcessoUSD = toNumber(globais?.freteProcessoUSD);
            const seguroProcessoUSD = toNumber(globais?.seguroProcessoUSD);
            const acrescimoProcessoUSD = toNumber(globais?.acrescimoProcessoUSD);
            const serviceChargesProcessoUSD = toNumber(globais?.serviceChargesProcessoUSD);
            const fobTotalProcesso = toNumber(globais?.fobTotalProcesso);
            const cotacaoUSD = toNumber(globais?.cotacaoUSD);
            const taxaSiscomexProcesso = toNumber(globais?.taxaSiscomexProcesso);
            const vlrII = toNumber(dados.vlrII);
            const bcIpiVal = toNumber(dados.bcIpi);
            const vlrIpi = toNumber(dados.vlrIpi);
            const bcPisCofinsVal = toNumber(dados.bcPisCofins);
            const vlrPis = toNumber(dados.vlrPis);
            const vlrCofins = toNumber(dados.vlrCofins);
            const despesaAduaneiraVal = toNumber(dados.despesaAduaneira);
            const bcIcmsSemReducaoVal = toNumber(dados.bcIcmsSemReducao);
            const vlrIcmsSemReducaoVal = toNumber(dados.vlrIcmsSemReducao);
            const bcIcmsReduzidoVal = toNumber(dados.bcIcmsReduzido);
            const vlrIcmsReduzidoVal = toNumber(dados.vlrIcmsReduzido);
            const vlrTotalProdNfVal = toNumber(dados.vlrTotalProdNf);
            const vlrTotalNfSemIcmsVal = toNumber(dados.vlrTotalNfSemIcms);
            const baseIcmsStVal = toNumber(dados.baseIcmsSt);
            const vlrIcmsStVal = toNumber(dados.valorIcmsSt);
            const icmsStPercent = toNumber(dados.icmsStPercent);
            const fatorReducaoAplicado = dados.reducao || 1;
            const fatorMva = 1 + (dados.mva || 0);
            const quantidadeSafe = quantidade > 0 ? quantidade : 1;
            const formulaDespesa = (globais?.nacionalizacao === 'santos')
                ? 'Multa + (Valor Aduaneiro BRL × % DEF/L.I.) + Taxa SISCOMEX da linha + AFRMM + Honorários NIX.'
                : 'Multa + (Valor Aduaneiro BRL × % DEF/L.I.) + Taxa SISCOMEX da linha + AFRMM + Armazenagem STS + Frete DTA STS/ANA + Honorários NIX.';
            const despComp = dados.despesasComponentes || {};
            const detailDespesaExpr = (globais?.nacionalizacao === 'santos')
                ? `${formatComponent('Multa', despComp.multa, 2)} + ${formatComponent('% DEF/L.I.', despComp.txDefLi, 2)} + ${formatComponent('Taxa SISCOMEX', despComp.taxaSiscomex, 2)} + ${formatComponent('AFRMM', despComp.afrmm, 2)} + ${formatComponent('Honorários NIX', despComp.honorarios_nix, 2)}`
                : `${formatComponent('Multa', despComp.multa, 2)} + ${formatComponent('% DEF/L.I.', despComp.txDefLi, 2)} + ${formatComponent('Taxa SISCOMEX', despComp.taxaSiscomex, 2)} + ${formatComponent('AFRMM', despComp.afrmm, 2)} + ${formatComponent('Armazenagem STS', despComp.armazenagem_sts, 2)} + ${formatComponent('Frete DTA STS/ANA', despComp.frete_dta_sts_ana, 2)} + ${formatComponent('Honorários NIX', despComp.honorarios_nix, 2)}`;
            const detailDespesa = formatCalcDetail(despesaAduaneiraVal, [detailDespesaExpr], 2);
            const numeradorBcIcms = valorAduaneiroBrl + vlrII + vlrIpi + vlrPis + vlrCofins + despesaAduaneiraVal;
            const fatorIcmsDivisor = 1 - toNumber(dados.aliquotaIcms);

            const rows = [
                buildRow('Produto / Descrição', dados.produto ?? '-', 'Valor informado nas colunas Produto e Descrição.', `Valor informado: ${dados.produto ?? '-'}`),
                buildRow('Quantidade', dados.quantidade ?? '-', 'Valor digitado na coluna Quantidade.', formatComponent('Quantidade', quantidade, 4)),
                buildRow('Peso Líq. Total', dados.pesoTotal ?? '-', 'Campo Peso Líquido Total da linha.', formatComponent('Peso da linha', dados.pesoTotal, 4)),
                buildRow('FOB Unit USD', formatDebugMoney(dados.fobUnitario, 4), 'Valor digitado em FOB UNIT USD.', formatComponent('FOB Unit USD', fobUnitario, 4)),
                buildRow('FOB Total USD', formatDebugMoney(dados.fobTotal, 4), 'FOB Unit USD × Quantidade.', formatCalcDetail(fobTotal, [formatComponent('FOB Unit USD', fobUnitario, 4), '×', formatComponent('Quantidade', quantidade, 4)], 4)),
                buildRow('Fator Peso', formatDebugMoney(dados.fatorPeso, 6), 'Peso Líq. Total da linha ÷ Peso Líq. Total do processo.', formatCalcDetail(fatorPeso, [formatComponent('Peso da linha', dados.pesoTotal, 4), '÷', formatComponent('Peso total processo', pesoTotalProcesso, 4)], 6)),
                buildRow('Frete USD', formatDebugMoney(dados.freteUsd, 4), 'Frete do processo (USD) × Fator Peso da linha.', formatCalcDetail(dados.freteUsd, [formatComponent('Frete processo USD', freteProcessoUSD, 4), '×', formatComponent('Fator Peso', fatorPeso, 6)], 4)),
                buildRow('Seguro USD', formatDebugMoney(dados.seguroUsd, 4), '(Seguro do processo ÷ FOB total do processo) × FOB total da linha.', formatCalcDetail(dados.seguroUsd, ['(', formatComponent('Seguro processo USD', seguroProcessoUSD, 4), '÷', formatComponent('FOB total processo', fobTotalProcesso, 4), ')', '×', formatComponent('FOB total linha', fobTotal, 4)], 4)),
                buildRow('Acréscimo Frete USD', formatDebugMoney(dados.acrescimoUsd, 4), '(Acréscimo do processo ÷ FOB total do processo) × FOB total da linha.', formatCalcDetail(dados.acrescimoUsd, ['(', formatComponent('Acréscimo processo USD', acrescimoProcessoUSD, 4), '÷', formatComponent('FOB total processo', fobTotalProcesso, 4), ')', '×', formatComponent('FOB total linha', fobTotal, 4)], 4)),
                buildRow('Service Charges USD', formatDebugMoney(dados.serviceChargesUsd, 4), 'Service charges do processo × Fator Peso da linha.', formatCalcDetail(dados.serviceChargesUsd, [formatComponent('Service charges processo USD', serviceChargesProcessoUSD, 4), '×', formatComponent('Fator Peso', fatorPeso, 6)], 4)),
                buildRow('THC (R$ → USD)', formatDebugMoney(dados.thc, 4), 'THC/Capatazia informado × Fator Peso (convertido para USD).', formatCalcDetail(dados.thc, [formatComponent('THC processo', dados.thcBaseProcesso, 4), '×', formatComponent('Fator Peso', fatorPeso, 6)], 4)),
                buildRow('VLR CRF Total', formatDebugMoney(dados.vlrCrfTotal, 4), 'FOB Total USD + Frete USD.', formatCalcDetail(dados.vlrCrfTotal, [formatComponent('FOB Total USD', fobTotal, 4), '+', formatComponent('Frete USD', dados.freteUsd, 4)], 4)),
                buildRow('Valor Aduaneiro USD', formatDebugMoney(dados.vlrAduaneiroUsd, 4), 'VLR CRF Total + Service Charges USD + Acréscimo USD + Seguro USD + THC (USD).', formatCalcDetail(dados.vlrAduaneiroUsd, [formatComponent('VLR CRF Total', dados.vlrCrfTotal, 4), '+', formatComponent('Service Charges USD', dados.serviceChargesUsd, 4), '+', formatComponent('Acréscimo USD', dados.acrescimoUsd, 4), '+', formatComponent('Seguro USD', dados.seguroUsd, 4), '+', formatComponent('THC USD', dados.thc, 4)], 4)),
                buildRow('Fator Valor FOB', formatDebugMoney(dados.fatorVlrFob, 6), 'FOB Total USD da linha ÷ FOB Total USD do processo.', formatCalcDetail(dados.fatorVlrFob, [formatComponent('FOB Total linha', fobTotal, 10), '÷', formatComponent('FOB Total processo', fobTotalProcesso, 10)], 6)),
                buildRow('Fator Taxa SISCOMEX', formatDebugMoney(dados.fatorSiscomex, 6), 'Taxa SISCOMEX do processo ÷ (FOB Total USD do processo × Cotação USD).', formatCalcDetail(dados.fatorSiscomex, [formatComponent('Taxa SISCOMEX processo', globais?.taxaSiscomexProcesso ?? 0, 10), '÷', '(', formatComponent('FOB Total processo', fobTotalProcesso, 10), '×', formatComponent('Cotação USD', cotacaoUSD, 10), ')'], 6)),
                buildRow('Taxa Siscomex (linha)', formatDebugMoney(dados.taxaSiscomexUnit, 6), 'Fator Taxa Siscomex × (FOB Total da linha × Cotação USD).', formatCalcDetail(dados.taxaSiscomexUnit, [formatComponent('Fator Taxa Siscomex', dados.fatorSiscomex, 6), '×', '(', formatComponent('FOB Total linha', fobTotal, 10), '×', formatComponent('Cotação USD', cotacaoUSD, 10), ')'], 6)),
                buildRow('Dif. Cambial Frete', formatDebugMoney(dados.diferencaCambialFrete, 4), '(Frete USD da linha × Dif. cambial frete processo) - (Frete USD × cotação).', formatCalcDetail(dados.diferencaCambialFrete, ['(', formatComponent('Frete USD linha', dados.freteUsd, 4), '×', formatComponent('Dif. cambial frete (processo)', dados.diferencaCambialFreteProcesso, 4), ')', '-', '(', formatComponent('Frete USD linha', dados.freteUsd, 4), '×', formatComponent('Cotação USD', cotacaoUSD, 4), ')'], 4)),
                buildRow('Dif. Cambial FOB', formatDebugMoney(dados.diferencaCambialFob, 4), '(Fator Valor FOB × Dif. cambial FOB processo) - (FOB Total × cotação).', formatCalcDetail(dados.diferencaCambialFob, ['(', formatComponent('Fator Valor FOB', dados.fatorVlrFob, 6), '×', formatComponent('Dif. cambial FOB (processo)', dados.diferencaCambialFobProcesso, 4), ')', '-', '(', formatComponent('FOB Total USD', fobTotal, 4), '×', formatComponent('Cotação USD', cotacaoUSD, 4), ')'], 4)),
                buildRow('Redução ICMS', formatDebugPercentage(dados.reducao, 2), 'Percentual informado em Redução na linha.', `Percentual: ${formatDebugPercentage(dados.reducao, 2)} / Fração aplicada: ${formatRawValue(fatorReducaoAplicado, 10)}`),
                buildRow('VLR II', formatDebugMoney(dados.vlrII, 2), 'Valor Aduaneiro BRL × Alíquota de II.', formatCalcDetail(vlrII, [formatComponent('Valor Aduaneiro BRL', valorAduaneiroBrl, 2), '×', formatComponent('Alíquota II', dados.aliquotaIi, 4)], 2)),
                buildRow('BC IPI', formatDebugMoney(dados.bcIpi, 2), 'Valor Aduaneiro BRL + VLR II.', formatCalcDetail(bcIpiVal, [formatComponent('Valor Aduaneiro BRL', valorAduaneiroBrl, 2), '+', formatComponent('VLR II', vlrII, 2)], 2)),
                buildRow('VLR IPI', formatDebugMoney(dados.vlrIpi, 2), 'BC IPI × Alíquota de IPI.', formatCalcDetail(vlrIpi, [formatComponent('BC IPI', bcIpiVal, 2), '×', formatComponent('Alíquota IPI', dados.aliquotaIpi, 4)], 2)),
                buildRow('BC PIS/COFINS', formatDebugMoney(dados.bcPisCofins, 2), 'Base igual ao Valor Aduaneiro BRL.', formatComponent('Valor Aduaneiro BRL', valorAduaneiroBrl, 2)),
                buildRow('VLR PIS', formatDebugMoney(dados.vlrPis, 2), 'BC PIS/COFINS × Alíquota PIS.', formatCalcDetail(vlrPis, [formatComponent('BC PIS/COFINS', bcPisCofinsVal, 2), '×', formatComponent('Alíquota PIS', dados.aliquotaPis, 4)], 2)),
                buildRow('VLR COFINS', formatDebugMoney(dados.vlrCofins, 2), 'BC PIS/COFINS × Alíquota COFINS.', formatCalcDetail(vlrCofins, [formatComponent('BC PIS/COFINS', bcPisCofinsVal, 2), '×', formatComponent('Alíquota COFINS', dados.aliquotaCofins, 4)], 2)),
                buildRow('Desp. Aduaneira', formatDebugMoney(dados.despesaAduaneira, 2), `${formulaDespesa} [Nacionalização: ${(globais?.nacionalizacao || '').toUpperCase()}]`, detailDespesa),
                buildRow('BC ICMS s/Redução', formatDebugMoney(dados.bcIcmsSemReducao, 2), '[(Base + II + IPI + PIS + COFINS + Despesas)] ÷ (1 - % ICMS).', formatCalcDetail(bcIcmsSemReducaoVal, ['(', formatComponent('Valor Aduaneiro BRL', valorAduaneiroBrl, 2), '+', formatComponent('II', vlrII, 2), '+', formatComponent('IPI', vlrIpi, 2), '+', formatComponent('PIS', vlrPis, 2), '+', formatComponent('COFINS', vlrCofins, 2), '+', formatComponent('Despesas Aduaneiras', despesaAduaneiraVal, 2), ')', '÷', formatComponent('(1 - % ICMS)', fatorIcmsDivisor, 4)], 2)),
                buildRow('VLR ICMS s/Redução', formatDebugMoney(dados.vlrIcmsSemReducao, 2), 'BC ICMS s/Redução × % ICMS.', formatCalcDetail(vlrIcmsSemReducaoVal, [formatComponent('BC ICMS s/Redução', bcIcmsSemReducaoVal, 2), '×', formatComponent('% ICMS', dados.aliquotaIcms, 4)], 2)),
                buildRow('BC ICMS reduzido', formatDebugMoney(dados.bcIcmsReduzido, 2), 'Resultado de BC ICMS após aplicar o percentual de redução.', formatCalcDetail(bcIcmsReduzidoVal, [formatComponent('BC ICMS s/Redução', bcIcmsSemReducaoVal, 2), '×', formatComponent('Fator Redução', fatorReducaoAplicado, 4)], 2)),
                buildRow('VLR ICMS reduzido', formatDebugMoney(dados.vlrIcmsReduzido, 2), 'BC ICMS reduzido × % ICMS.', formatCalcDetail(vlrIcmsReduzidoVal, [formatComponent('BC ICMS reduzido', bcIcmsReduzidoVal, 2), '×', formatComponent('% ICMS', dados.aliquotaIcms, 4)], 2)),
                buildRow('VLR Unit. Prod. NF', formatDebugMoney(dados.vlrUnitProdNf, 2), 'Valor Total Produto NF ÷ Quantidade.', quantidade > 0
                    ? formatCalcDetail(dados.vlrUnitProdNf, [formatComponent('VLR Total Prod. NF', vlrTotalProdNfVal, 2), '÷', formatComponent('Quantidade', quantidade, 4)], 2)
                    : `Quantidade informada igual a 0; sistema assume 1 unidade. ${formatCalcDetail(dados.vlrUnitProdNf, [formatComponent('VLR Total Prod. NF', vlrTotalProdNfVal, 2), '÷', formatComponent('Quantidade assumida', quantidadeSafe, 4)], 2)}`),
                buildRow('VLR Total Prod. NF', formatDebugMoney(dados.vlrTotalProdNf, 2), 'Base Aduaneira BRL + VLR II.', formatCalcDetail(vlrTotalProdNfVal, [formatComponent('Valor Aduaneiro BRL', valorAduaneiroBrl, 2), '+', formatComponent('VLR II', vlrII, 2)], 2)),
                buildRow('VLR Total NF s/ICMS ST', formatDebugMoney(dados.vlrTotalNfSemIcms, 2), 'VLR Total Prod. NF + IPI + PIS + COFINS + Desp. Aduaneira + VLR ICMS reduzido.', formatCalcDetail(vlrTotalNfSemIcmsVal, [formatComponent('VLR Total Prod. NF', vlrTotalProdNfVal, 2), '+', formatComponent('IPI', vlrIpi, 2), '+', formatComponent('PIS', vlrPis, 2), '+', formatComponent('COFINS', vlrCofins, 2), '+', formatComponent('Desp. Aduaneira', despesaAduaneiraVal, 2), '+', formatComponent('VLR ICMS reduzido', vlrIcmsReduzidoVal, 2)], 2)),
                buildRow('BC ICMS-ST', formatDebugMoney(dados.baseIcmsSt, 2), 'VLR Total NF s/ICMS ST × (1 + MVA).', formatCalcDetail(baseIcmsStVal, [formatComponent('VLR Total NF s/ICMS ST', vlrTotalNfSemIcmsVal, 2), '×', formatComponent('(1 + MVA)', fatorMva, 4)], 2)),
                buildRow('VLR ICMS-ST', formatDebugMoney(dados.valorIcmsSt, 2), 'Base ICMS-ST × % ICMS-ST - VLR ICMS reduzido (quando aplicável).', icmsStPercent > 0 ? formatCalcDetail(vlrIcmsStVal, ['(', formatComponent('BC ICMS-ST', baseIcmsStVal, 2), '×', formatComponent('% ICMS-ST', icmsStPercent, 4), ')', '-', formatComponent('VLR ICMS reduzido', vlrIcmsReduzidoVal, 2)], 2) : 'Percentual ICMS-ST não informado.'),
                buildRow('Custo Unit. Final', formatDebugMoney(dados.custoUnitarioFinal, 2), '[(Total NF c/ICMS + Desp. Desembaraço + Dif. Cambial FOB + Dif. Cambial Frete) - ICMS reduzido] ÷ Quantidade.', formatCalcDetail(dados.custoUnitarioFinal, ['(', formatComponent('Total NF c/ICMS', dados.vlrTotalNfComIcms ?? dados.vlrTotalNfSemIcms ?? vlrTotalNfSemIcmsVal, 2), '+', formatComponent('Desp. Desembaraço', dados.despesaDesembaraco ?? 0, 2), '+', formatComponent('Dif. Cambial FOB', dados.diferencaCambialFob, 2), '+', formatComponent('Dif. Cambial Frete', dados.diferencaCambialFrete, 2), '-', formatComponent('ICMS reduzido', vlrIcmsReduzidoVal, 2), ')', '÷', formatComponent('Quantidade', quantidadeSafe, 4)], 2)),
                buildRow('Custo Total Final', formatDebugMoney(dados.custoTotalFinal, 2), 'Custo unitário final × Quantidade.', formatCalcDetail(dados.custoTotalFinal, [formatComponent('Custo Unit. Final', dados.custoUnitarioFinal, 2), '×', formatComponent('Quantidade', quantidade, 4)], 2))
            ];
            if (globais && globais.fobTotalProcesso) {
                rows.splice(5, 0, buildRow(
                    'FOB Total do processo (USD)',
                    formatDebugMoney(globais.fobTotalProcesso, 4),
                    'Soma dos FOB TOTAL USD de todas as linhas, usada como base para rateios.',
                    formatCalcDetail(globais.fobTotalProcesso, [formatComponent('Σ FOB linhas', globais.fobTotalProcesso, 4)], 4)
                ));
            }
            return rows;
        }

        function buildSectionHtml(titulo, linhas) {
            if (!linhas || linhas.length === 0) {
                return '';
            }

            let html = `<div class="debug-section">
                <div class="debug-section-title">${titulo}</div>
                <div class="debug-grid debug-grid-header">
                    <div>Campo</div>
                    <div>Valor</div>
                    <div>Fórmula utilizada</div>
                    <div>Detalhamento</div>
                </div>`;

            linhas.forEach(linha => {
                html += `<div class="debug-grid debug-grid-row">
                    <div class="debug-cell-label">${linha.label}</div>
                    <div class="debug-cell-value">${linha.value ?? '-'}</div>
                    <div class="debug-cell-text">${linha.formula ?? '-'}</div>
                    <div class="debug-cell-text">${linha.detail || '-'}</div>
                </div>`;
            });

            html += '</div>';
            return html;
        }

        function renderDebugModal(rowId) {
            const dados = debugStore[rowId];
            const container = $('#debugLinhaConteudo');

            if (!dados) {
                container.html('<p class="text-muted mb-0">Ainda não há informações calculadas para esta linha. Clique em "Recalcular tabela" e tente novamente.</p>');
                return;
            }

            let html = '<p class="text-muted small mb-3"><i class="fas fa-info-circle mr-2"></i>Os valores abaixo exibem todas as casas decimais utilizadas nos cálculos, sem qualquer arredondamento. Na tabela principal mostramos apenas duas casas para facilitar a leitura.</p>';

            const globais = buildGlobalRows(debugGlobals);
            html += buildSectionHtml('Totais do processo', globais);

            const linhas = buildDebugRows(dados, debugGlobals);
            html += buildSectionHtml('Detalhes da linha', linhas);

            container.html(html);
        }

        $(document).on('click', '.btn-debug-linha', function() {
            const rowId = $(this).data('row');
            const numeroItem = $(`#item-${rowId}`).val();
            const label = numeroItem ? `Cálculo do item ${numeroItem}` : `Cálculo da linha #${rowId}`;
            $('#debugLinhaModalLabel').text(label);
            renderDebugModal(rowId);
            $('#debugLinhaModal').modal('show');
        });

        $(document).on('click', '.btn-close-debug', function() {
            $('#debugLinhaModal').modal('hide');
        });

        function atualizarTotalizadores() {

            const rows = $('#productsBody tr:not(.separador-adicao)');
            if (rows.length === 0) return;

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
                frete_dta_sts_ana: 0,
                sda: 0,
                rep_sts: 0,
                armaz_ana: 0,
                lavagem_container: 0,
                rep_anapolis: 0,
                desp_anapolis: 0,
                correios: 0,
                li_dta_honor_nix: 0,
                honorarios_nix: 0,
                desp_desenbaraco: 0,
                diferenca_cambial_frete: 0,
                diferenca_cambial_fob: 0,
                custo_total_final: 0
            };

            // Calcular médias para fatores
            let fatorPesoSum = 0;
            let fatorValorFobSum = 0;
            let fatorTxSiscomexSum = 0;

            const moedaServiceCharges = $('#service_charges_moeda').val();
            
            rows.each(function() {
                const rowId = this.id.replace('row-', '')
                // Somar valores
                Object.keys(totais).forEach(campo => {
                    // Para service_charges, se a moeda não for USD, usar service_charges_moeda_estrangeira
                    if (campo === 'service_charges' && moedaServiceCharges && moedaServiceCharges !== 'USD') {
                        const elemento = $(`#service_charges_moeda_estrangeira-${rowId}`);
                        if (elemento.length > 0) {
                            const valor = MoneyUtils.parseMoney(elemento.val()) || 0;
                            totais[campo] += valor;
                        }
                    } else {
                        const elemento = $(`#${campo}-${rowId}`);
                        if (elemento.length > 0) {
                            let valor = MoneyUtils.parseMoney(elemento.val()) || 0;
                            // Validar diferenca_cambial_frete antes de somar
                            if (campo === 'diferenca_cambial_frete') {
                                valor = validarDiferencaCambialFrete(valor);
                            }
                            totais[campo] += valor;
                        }
                    }
                });

                // Acumular para médias
                fatorPesoSum += MoneyUtils.parseMoney($(`#fator_peso-${rowId}`).val()) || 0;
                fatorValorFobSum += MoneyUtils.parseMoney($(`#fator_valor_fob-${rowId}`).val()) || 0;
                fatorTxSiscomexSum += MoneyUtils.parseMoney($(`#fator_tx_siscomex-${rowId}`).val()) || 0;
            });
            
            // Atualizar peso líquido total do processo
            atualizarPesoLiquidoTotal(totais.peso_liquido_total);

            // Atualizar o TFOOT
            const tfoot = $('#resultado-totalizadores');

            // Limpar e criar nova linha de totais
            tfoot.empty();
            let tr = '<tr><td colspan="7" style="text-align: right; font-weight: bold;">TOTAIS:</td>';

            // QUANTIDADE
            tr += `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.quantidade, 2)}</td>`;

            // PESO LIQ. UNIT (vazio)
            tr += '<td></td>';

            // PESO LIQ TOTAL
            tr +=
                `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.peso_liquido_total, 2)}</td>`;

            // FATOR PESO (média)
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

            // COLUNAS FRETE
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

            // COLUNAS CFR (após FRETE)
            tr += `<td></td>`; // VLR CFR UNIT não é somado (é unitário)
            tr += `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.vlr_crf_total, 2)}</td>`;
            
            // COLUNAS SERVICE CHARGES (após CFR)
            if (moedaServiceCharges && moedaServiceCharges !== 'USD') {
                tr += `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.service_charges, 2)}</td>`;
            }
            
            // Para service_charges USD, precisamos somar o campo service_charges (que é USD) quando a moeda não for USD
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

            // COLUNAS ACRÉSCIMO (após SERVICE CHARGES)
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

            // COLUNAS SEGURO (após ACRÉSCIMO)
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

            // Continuar com as demais colunas...
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
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(fatorValorFobSum , 6)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(fatorTxSiscomexSum , 6)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.multa, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.tx_def_li, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.taxa_siscomex, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.outras_taxas_agente, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.liberacao_bl, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.desconsolidacao, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.isps_code, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.handling, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.capatazia, 2)}</td>
        ${getNacionalizacaoAtual() === 'santos' ? '<td data-campo="tx_correcao_lacre" style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.tx_correcao_lacre, 2) + '</td>' : ''}
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.afrmm, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.armazenagem_sts, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.frete_dta_sts_ana, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.sda, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.rep_sts, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.armaz_ana, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.lavagem_container, 2)}</td>
        ${getNacionalizacaoAtual() !== 'santos' ? '<td data-campo="rep_anapolis" style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.rep_anapolis, 2) + '</td><td data-campo="desp_anapolis" style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.desp_anapolis, 2) + '</td><td data-campo="correios" style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.correios, 2) + '</td>' : ''}
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.li_dta_honor_nix, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.honorarios_nix, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.desp_desenbaraco, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(validarDiferencaCambialFrete(totais.diferenca_cambial_frete), 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.diferenca_cambial_fob, 2)}</td>
        <td></td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.custo_total_final, 2)}</td>
    </tr>`;

            tfoot.append(tr);
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


            // Atualizar títulos das colunas
            atualizarTitulosColunas(moedaFrete, moedaSeguro, moedaAcrescimo, moedaProcesso);

            // Mostrar/ocultar colunas baseado na moeda do processo
            if (moedaProcesso && moedaProcesso !== 'USD') {
                // Mostrar colunas da moeda estrangeira
                $('[id*="fob_unit_moeda_estrangeira-"]').closest('td').show();
                $('[id*="fob_total_moeda_estrangeira-"]').closest('td').show();

                // Ocultar colunas USD
                $('[id*="fob_unit_usd-"]').closest('td').hide();

                // Atualizar títulos
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
                // Mostrar colunas USD
                $('[id*="fob_unit_usd-"]').closest('td').show();

                // Ocultar colunas da moeda estrangeira
                $('[id*="fob_unit_moeda_estrangeira-"]').closest('td').hide();
                $('[id*="fob_total_moeda_estrangeira-"]').closest('td').hide();

                // Atualizar títulos
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

            // Mostrar/ocultar colunas de frete
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

            // Mostrar/ocultar colunas de seguro
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

            // Mostrar/ocultar colunas de acréscimo
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
            
            // Mostrar/ocultar colunas de service_charges
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

            // VERIFICAR SE AS COTAÇÕES SÃO VÁLIDAS
            if (!cotacaoMoeda || cotacaoMoeda === 0 || !cotacaoUSD || cotacaoUSD === 0) {
                console.warn(`Cotações inválidas para conversão: ${moedaProcesso} = ${cotacaoMoeda}, USD = ${cotacaoUSD}`);
                return valor; // Retorna o valor original se não puder converter
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

            // VERIFICAR SE AS COTAÇÕES SÃO VÁLIDAS
            if (!cotacaoMoeda || cotacaoMoeda === 0 || !cotacaoUSD || cotacaoUSD === 0) {
                console.warn(`Cotações inválidas para conversão: ${moedaProcesso} = ${cotacaoMoeda}, USD = ${cotacaoUSD}`);
                return valor; // Retorna o valor original se não puder converter
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
        // Modifique a função que atualiza os campos FOB
        function atualizarCamposFOB(rowId, valores) {
            let moedaProcesso = $('#moeda_processo').val();
            let quantidade = MoneyUtils.parseMoney($(`#quantidade-${rowId}`).val()) || 0;
            // Garantir que os valores são válidos
            if (isNaN(valores.fobUnitario)) valores.fobUnitario = 0;
            if (isNaN(valores.fobTotal)) valores.fobTotal = 0;
            if (isNaN(valores.dolar)) valores.dolar = 1;

            // Calcular totais
            let fobTotalUSD = valores.fobTotal;
            let fobTotalBRL = valores.fobTotal * valores.dolar;

            if (moedaProcesso && moedaProcesso !== 'USD') {
                // Calcular valores na moeda do processo
                let fobTotalMoedaEstrangeira = valores.fobUnitarioMoedaEstrangeira * quantidade;

                // Atualizar campos da moeda estrangeira
                // $(`#fob_unit_moeda_estrangeira-${rowId}`).val(MoneyUtils.formatMoney(valores.fobUnitarioMoedaEstrangeira, 7));
                $(`#fob_total_moeda_estrangeira-${rowId}`).val(MoneyUtils.formatMoney(fobTotalMoedaEstrangeira, 7));

                // Para moeda diferente de USD, o fobTotalUSD já está convertido na função obterValoresBase
            } else {
                // Moeda é USD - atualizar campo USD diretamente
                const $campoFobUsd = $(`#fob_unit_usd-${rowId}`);
                // Evitar sobrescrever enquanto o usuário digita (formatação fica apenas no blur)
                if (!$campoFobUsd.is(':focus')) {
                    $campoFobUsd.val(MoneyUtils.formatMoney(valores.fobUnitario, 7));
                }
            }

            // Valores totais (sempre em USD e BRL)
            $(`#fob_total_usd-${rowId}`).val(MoneyUtils.formatMoney(fobTotalUSD, 7));
            $(`#fob_total_brl-${rowId}`).val(MoneyUtils.formatMoney(fobTotalBRL, 7));
        }
        // Função para atualizar títulos
        function atualizarTitulosColunas(moedaFrete, moedaSeguro, moedaAcrescimo, moedaProcesso) {
            // Atualizar títulos dinamicamente se necessário
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
                // Novos para FOB
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
            // Chamar quando as moedas mudarem
            $('#frete_internacional_moeda, #seguro_internacional_moeda, #acrescimo_frete_moeda, #service_charges_moeda, #moeda_processo')
                .on('change', function() {
                    setTimeout(atualizarVisibilidadeColunasMoeda, 100);
                });
            $('.moneyReal').on('blur', function() {
                const val = $(this).val();
                if (val && val.trim() !== '') {
                    const numero = normalizeNumericValue(val);
                    $(this).val(formatTruncatedNumber(numero, 5));
                } else {
                    $(this).val('');
                }
            });
            // Dinheiro com 2 casas decimais
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

            // Dinheiro com 7 casas decimais (usando mesma lógica do moneyReal2 para consistência)
            $('.moneyReal7').on('blur', function() {
                const val = $(this).val();
                if (val && val.trim() !== '') {
                    const numero = normalizeNumericValue(val);
                    $(this).val(formatTruncatedNumber(numero, 7));
                } else {
                    $(this).val('');
                }
            });

            // Percentuais (7 casas decimais igual migration)
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
            $('.select2').select2({
                width: '100%'
            });

            // Listener para SERVICE_CHARGES do processo - recalcular todos os produtos
            $('#service_charges').on('blur change input', function() {
                // Converter imediatamente quando o valor mudar
                convertToUSDAndBRL('service_charges');
                // Recalcular toda a tabela quando service_charges do processo mudar
                setTimeout(function() {
                    recalcularTodaTabela();
                }, 100);
            });

            $('form').on('submit', function(e) {
                // Atualizar peso líquido antes de enviar
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
                // Mostrar indicador de carregamento
                const btn = $(this);
                const originalText = btn.html();
                btn.html('<i class="fas fa-spinner fa-spin"></i> Calculando...').prop('disabled', true);

                // Pequeno delay para permitir a atualização visual do botão
                setTimeout(function() {
                    try {
                        recalcularTodaTabela();
                        Toast.fire({
                            icon: 'success',
                            title: 'Tabela recalculada com sucesso!'
                        });
                    } catch (error) {
                        console.error('Erro ao recalcular tabela:', error);
                        Toast.fire({
                            icon: 'error',
                            title: 'Erro ao recalcular tabela'
                        });
                    } finally {
                        // Restaurar o botão
                        btn.html(originalText).prop('disabled', false);
                    }
                }, 100);
            });

            recalcularTodaTabela()
            setTimeout(atualizarTotalizadores, 500);


        });

        // Função para obter símbolo da moeda
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

        // Função para atualizar símbolo da moeda no input
        function updateCurrencySymbol(inputId) {
            const codigoMoeda = $(`#${inputId}_moeda`).val();
            const symbol = getCurrencySymbol(codigoMoeda);
            $(`#${inputId}_symbol`).text(symbol || '-');
        }

        // Função para converter valor: moeda original -> USD -> BRL
        function convertToUSDAndBRL(inputId) {
            const cotacoes = getCotacaoesProcesso();
            const valor = MoneyUtils.parseMoney($(`#${inputId}`).val()) || 0;
            const codigoMoeda = $(`#${inputId}_moeda`).val();
            
            // Primeiro tentar pegar a cotação do campo, depois do objeto de cotações
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

            // Se a moeda já for USD
            if (codigoMoeda === 'USD') {
                valorUSD = valor;
                valorBRL = valor * cotacaoUSD;
            } else {
                // Se não tem cotação, não pode converter
                if (!cotacaoMoeda || cotacaoMoeda === 0) {
                    $(`#${inputId}_usd`).val('');
                    $(`#${inputId}_brl`).val('');
                    return;
                }

                // Converter de moeda original para BRL primeiro
                valorBRL = valor * cotacaoMoeda;
                
                // Converter de BRL para USD: valor BRL / cotação USD
                if (cotacaoUSD > 0) {
                    valorUSD = valorBRL / cotacaoUSD;
                }
            }

            // Atualizar campos
            const usdField = $(`#${inputId}_usd`);
            const brlField = $(`#${inputId}_brl`);
            
            // Atualizar campos com máxima precisão (7 casas decimais)
            if (usdField.length) {
                usdField.val(MoneyUtils.formatMoney(valorUSD, 2));
            }
            
            if (brlField.length) {
                brlField.val(MoneyUtils.formatMoney(valorBRL, 2));
            }
        }

        function updateValorReal(inputId, spanId, automatic = true) {
            // Esta função é mantida para compatibilidade, mas agora usamos convertToUSDAndBRL
            convertToUSDAndBRL(inputId);
        };

        function updateValorCotacao(inputId, spanId) {
            let dolar = getCotacaoesProcesso();

            let valor = MoneyUtils.parseMoney($(`#${inputId}`).val());
            let codigoMoeda = $(`#${inputId}_moeda`).val();
            let nome = $(`#${inputId}_moeda option:selected`).text();
            console.log({
                inputId,
                spanId,
                dolar,
                codigoMoeda,
                nome,
                valor
            })
            // Atualizar símbolo da moeda se existir o elemento
            if ($(`#description_moeda_${inputId}`).length) {
                $(`#description_moeda_${inputId}`).text(`Taxa: ${nome}`);
            }

            if (codigoMoeda && dolar && dolar[codigoMoeda]) {
                let convertido = dolar[codigoMoeda].venda;
                $(`#${spanId}`).val(MoneyUtils.formatMoney(convertido, 4));
                
                // Log apenas para service_charges quando setar a cotação
                if (inputId === 'service_charges') {
                    console.log(`Cotação setada para service_charges: ${codigoMoeda} = ${convertido}`);
                }

                // Tentar obter a data da cotação
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
                        // Erro silencioso
                    }
                }
            } else {
                // Se não encontrou a cotação
                if (inputId === 'service_charges') {
                    console.log(`Cotação NÃO encontrada para service_charges. Moeda: ${codigoMoeda}, Cotações disponíveis:`, Object.keys(dolar || {}));
                }
                $(`#${spanId}`).val('');
                $(`#data_moeda_${inputId}`).val('');
            }
        };
        const initialInputs = new Set(
            Array.from(document.querySelectorAll('#productsBody input, #productsBody select, #productsBody textarea'))
        );

        setTimeout(function() {
            $(document).on('change', '.selectProduct', function(e) {
                let products = JSON.parse($('#productsClient').val());
                let productObject = products.find(el => el.id == this.value);
                let rowId = $(this).data('row');

                if (productObject) {
                    $(`#codigo-${rowId}`).val(productObject.codigo);
                    $(`#ncm-${rowId}`).val(productObject.ncm);
                    $(`#descricao-${rowId}`).val(productObject.descricao);
                    debouncedRecalcular();
                }
            });
            $(document).on('keyup',
                '#productsBody input, #productsBody select, #productsBody textarea, #productsBody .form-control',
                function(e) {
                    if (!initialInputs.has(e.target)) {
                        return; // Ignora inputs criados dinamicamente
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
                
                // Garantir que a conversão seja feita após atualizar a cotação
                setTimeout(function() {
                    convertToUSDAndBRL('frete_internacional');
                    convertToUSDAndBRL('seguro_internacional');
                    convertToUSDAndBRL('acrescimo_frete');
                    convertToUSDAndBRL('service_charges');
                }, 100);
            });
            
            // Listener específico para service_charges_moeda - FORÇAR CONVERSÃO
            $('#service_charges_moeda').on('select2:select', function(e) {
                const moeda = $(this).val();
                updateCurrencySymbol('service_charges');
                // Forçar atualização da cotação
                updateValorCotacao('service_charges', 'cotacao_service_charges');
                // Aguardar e FORÇAR conversão
                setTimeout(function() {
                    convertToUSDAndBRL('service_charges');
                    atualizarVisibilidadeColunasMoeda();
                    recalcularTodaTabela();
                }, 200);
            });
            
            // Listener para cotacao_service_charges - FORÇAR CONVERSÃO quando cotação mudar
            $('#cotacao_service_charges').on('blur change input', function() {
                setTimeout(function() {
                    convertToUSDAndBRL('service_charges');
                    setTimeout(recalcularTodaTabela, 100);
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

                // sempre mostra a cotação em BRL
                $('#display_cotacao').val(MoneyUtils.formatMoney(cotacaoMoeda, 4));

                if (moeda && moeda !== 'USD') {
                    // Moeda -> USD
                    let moedaEmUSD = cotacaoMoeda / cotacaoUSD;
                    $('#moeda_processo_usd').val(MoneyUtils.formatMoney(moedaEmUSD, 4));
                    $('#visualizacaoMoedaDolar').removeClass('d-none').addClass('col-12');

                    // cria/atualiza versão USD
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

                // atualiza o hidden sempre
                $('#cotacao_moeda_processo').val(JSON.stringify(cotacaoProcesso));

                // ADICIONAR ESTA LINHA PARA ATUALIZAR A VISIBILIDADE DAS COLUNAS
                setTimeout(atualizarVisibilidadeColunasMoeda, 100);

                // ADICIONAR ESTA LINHA PARA RECALCULAR A TABELA COM A NOVA MOEDA
                setTimeout(recalcularTodaTabela, 200);

                $('#formProcesso').submit();

            });


            $('#display_cotacao').on('change', function() {
                let cotacaoProcesso = getCotacaoesProcesso();
                let cotacaoMoedaFloat = MoneyUtils.parseMoney(this.value);
                let moeda = $('#moeda_processo').val();
                let data = $('#data_cotacao_processo').val();

                // se a moeda não existir, cria
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

                // atualiza a data
                if (data) {
                    let [dia, mes, ano] = data.split('/');
                    cotacaoProcesso[moeda].data = `${mes}/${dia}/${ano}`;
                } else if (!cotacaoProcesso[moeda].data) {
                    let hoje = new Date();
                    cotacaoProcesso[moeda].data =
                        `${String(hoje.getMonth()+1).padStart(2,'0')}/${String(hoje.getDate()).padStart(2,'0')}/${hoje.getFullYear()}`;
                }

                // se não for USD, cria/atualiza versão moeda -> USD
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

                // persiste tudo no hidden
                $('#cotacao_moeda_processo').val(JSON.stringify(cotacaoProcesso));
            });

            // Atualizar símbolos e conversões quando a moeda mudar
            $(document).on('change', '#frete_internacional_moeda, #seguro_internacional_moeda, #acrescimo_frete_moeda, #service_charges_moeda', function() {
                const inputId = this.id.replace('_moeda', '');
                updateCurrencySymbol(inputId);
                updateValorCotacao(inputId, `cotacao_${inputId}`);
                // Aguardar um pouco para garantir que a cotação foi atualizada
                setTimeout(function() {
                    convertToUSDAndBRL(inputId);
                }, 150);
            });

            // Atualizar conversões quando o valor mudar
            $('#frete_internacional, #seguro_internacional, #acrescimo_frete, #service_charges').trigger('change');
            $(document).on('change', '#frete_internacional, #seguro_internacional, #acrescimo_frete, #service_charges', function() {
                const inputId = this.id;
                convertToUSDAndBRL(inputId);
            });

            // Atualizar conversões quando a cotação mudar
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

            // Inicializar símbolos ao carregar a página
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
            const deleteUrl = '/destroy-produto-processo/' + documentId; // Ajuste a URL conforme necessário

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

                // Converter para string e remover espaços
                let stringValue = value.toString().trim();

                // Remover o símbolo de porcentagem se existir
                stringValue = stringValue.replace(/%/g, '');

                // Substituir vírgula por ponto para decimal
                stringValue = stringValue.replace(',', '.');

                // Remover espaços extras que possam ter sobrado
                stringValue = stringValue.replace(/\s/g, '');

                // Converter para float e dividir por 100 para obter a fração
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

                // Se já for número, retorna direto
                if (typeof value === "number") {
                    return value;
                }

                if (value.toString().includes('.') && !value.toString().includes(',')) {
                    return parseFloat(value) || 0;
                }


                // Se for string, trata a formatação
                let cleanValue = value.toString()
                    .replace(/\./g, '') // Remove todos os pontos
                    .replace(/,/g, '.'); // Substitui vírgula por ponto

                // Remover caracteres não numéricos exceto ponto decimal
                cleanValue = cleanValue.replace(/[^\d.]/g, '');

                return parseFloat(cleanValue) || 0;
            },

            formatMoney: function(value, decimals = 6) {
                const num = normalizeNumericValue(value);
                const truncated = this.truncate(num, decimals);
                return truncated.toLocaleString('pt-BR', {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals
                });
            }
        };



        function recalcularTodaTabela() {
            // Proteção contra recálculos simultâneos
            if (isRecalculating) {
                return;
            }
            isRecalculating = true;
            resetDebugStore();
            
            const rows = $('.linhas-input');
            const moedasOBject = getCotacaoesProcesso();
            // Garantir máxima precisão na obtenção do dólar
            let moedaDolar = moedasOBject['USD']?.venda;
            if (!moedaDolar || moedaDolar === null || moedaDolar === undefined) {
                const cotacaoFrete = $(`#cotacao_frete_internacional`).val();
                moedaDolar = cotacaoFrete ? cotacaoFrete.replace(',', '.') : 1;
            }
            // Se já for número, usar direto; senão, parsear com máxima precisão
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
            // PRIMEIRA PASSADA: Atualizar FOBs e calcular novo FOB Total Geral
            let fobTotalGeralAtualizado = 0;
            const fobTotaisPorLinha = {};

            rows.each(function() {
                const rowId = this.id.toString().replace('row-', '');
                if (rowId) {
                    const {
                        pesoTotal,
                        fobUnitario,
                        quantidade
                    } = obterValoresBase(rowId);

                    const fatorPesoRow = recalcularFatorPeso(totalPesoLiq, rowId);
                    const fobTotal = fobUnitario * quantidade;

                    // Armazena os valores para usar depois
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

                    // Atualiza campos básicos
                    $(`#peso_liquido_unitario-${rowId}`).val(pesoTotal / (quantidade || 1));
                    $(`#fob_total_usd-${rowId}`).val(MoneyUtils.formatMoney(fobTotal, 7));
                    $(`#fob_total_brl-${rowId}`).val(MoneyUtils.formatMoney(fobTotal * dolar, 7));
                }
            });

            // SEGUNDA PASSADA: Calcular fatores e atualizar campos que dependem do FOB Total Geral atualizado
            rows.each(function() {
                const rowId = this.id.toString().replace('row-', '');
                if (rowId) {
                    const linha = fobTotaisPorLinha[rowId];

                    const fobTotal = linha.fobTotal;
                    const fobUnitario = linha.fobUnitario;
                    const quantidade = linha.quantidade;
                    const fatorPesoRow = linha.fatorPesoRow;
                    const pesoTotal = linha.pesoTotal;
                    
                    // Garantir que quantidade está disponível para os cálculos
                    const quantidadeAtual = quantidade || MoneyUtils.parseMoney($(`#quantidade-${rowId}`).val()) || 0;

                    // AGORA calcula o fator com o FOB Total Geral atualizado
                    const fatorVlrFob_AX = fobTotal / (fobTotalGeralAtualizado || 1);
                    $(`#fator_valor_fob-${rowId}`).val(MoneyUtils.formatMoney(fatorVlrFob_AX, 6));

                    // Resto dos cálculos usando o FOB Total Geral ATUALIZADO
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
                    
                    // Calcular SERVICE_CHARGES seguindo a mesma lógica do cálculo por linha
                    const serviceChargesBaseRow = MoneyUtils.parseMoney($('#service_charges').val()) || 0;
                    const serviceChargesRowAtual = serviceChargesBaseRow * fatorPesoRow;

                    // Calcular VLR_CRF_TOTAL = FOB_TOTAL_USD + FRETE_INT_USD
                    const vlrCrfTotal = fobTotal + freteUsdInt;
                    // Calcular VLR_CRF_UNIT = VLR_CRF_TOTAL / QUANTIDADE
                    const vlrCrfUnit = quantidadeAtual > 0 ? vlrCrfTotal / quantidadeAtual : 0;

                    // IMPORTANTE: Usar fobTotalGeralAtualizado aqui
                    const seguroIntUsdRow = calcularSeguro(fobTotal, fobTotalGeralAtualizado);


                    const acrescimoFreteUsdRow = calcularAcrescimoFrete(fobTotal, fobTotalGeralAtualizado, dolar);
                    const vlrAduaneiroUsd = calcularValorAduaneiro(fobTotal, freteUsdInt, acrescimoFreteUsdRow,
                        seguroIntUsdRow, thcRow, dolar, vlrCrfTotal, serviceChargesRowAtual);
                    // Multiplicação exata sem arredondamento - manter precisão máxima
                    const vlrAduaneiroBrl = vlrAduaneiroUsd * dolar;

                    const impostos = calcularImpostos(rowId, vlrAduaneiroBrl);
                    const fatorTaxaSiscomex_AY = taxaSisComex / ((fobTotalGeralAtualizado || 1) * (dolar || 1));
                    const taxaSiscomexUnitaria_BB = fatorTaxaSiscomex_AY * (fobTotal * dolar);
                    $(`#fator_tx_siscomex-${rowId}`).val(MoneyUtils.formatMoney(fatorTaxaSiscomex_AY, 6));

                    const despesasInfo = calcularDespesas(rowId, fatorVlrFob_AX, fatorTaxaSiscomex_AY,
                        (taxaSiscomexUnitaria_BB ?? 0), vlrAduaneiroBrl);
                    const despesas = despesasInfo.total;

                    const bcIcmsSReducao = calcularBcIcmsSemReducao(vlrAduaneiroBrl, impostos, despesas);
                    const vlrIcmsSReducao = bcIcmsSReducao * impostos.icms;
                    const bcImcsReduzido = calcularBcIcmsReduzido(rowId, vlrAduaneiroBrl, impostos, despesas);
                    const vlrIcmsReduzido = bcImcsReduzido * impostos.icms;
                    const totais = calcularTotais(vlrAduaneiroBrl, impostos, despesas, quantidade, vlrIcmsReduzido,
                        rowId);

                    const mva = $(`#mva-${rowId}`).val() ? MoneyUtils.parsePercentage($(`#mva-${rowId}`).val()) : 0;
                    
                    // Calcular base_icms_st usando a fórmula: valor_total_nf_sem_icms * (1 + MVA)
                    // NOTA: parsePercentage já retorna a fração (ex: 45.5% vira 0.455)
                    const valorTotalNfSemIcms = totais.vlrTotalNfSemIcms || 0;
                    let base_icms_st = 0;
                    
                    if (mva > 0 && valorTotalNfSemIcms > 0) {
                        // MVA já vem como fração de parsePercentage (ex: 0.455 para 45,5%)
                        base_icms_st = valorTotalNfSemIcms * (1 + mva);
                    } else {
                        // Se não há MVA, base_icms_st é igual ao valor_total_nf_sem_icms
                        base_icms_st = valorTotalNfSemIcms;
                    }
                    
                    let icms_st_percent = MoneyUtils.parsePercentage($(`#icms_st-${rowId}`).val());
                    let vlrIcmsSt = icms_st_percent > 0 ? (base_icms_st * icms_st_percent) - vlrIcmsReduzido : 0;
                    const dif_cambial_frete_processo = MoneyUtils.parseMoney($('#diferenca_cambial_frete').val()) || 0;
                    const dif_cambial_fob_processo =  MoneyUtils.parseMoney($('#diferenca_cambial_fob').val()) || 0;
                    let diferenca_cambial_frete = (freteUsdInt * dif_cambial_frete_processo) - (freteUsdInt *
                        dolar);
                    diferenca_cambial_frete = validarDiferencaCambialFrete(diferenca_cambial_frete);
                    const diferenca_cambial_fob = dif_cambial_fob_processo > 0 ? (fatorVlrFob_AX * dif_cambial_fob_processo) - (fobTotal * dolar) : 0;
                    const reducaoPercent = MoneyUtils.parsePercentage($(`#reducao-${rowId}`).val());
                    const custoUnitarioFinal = MoneyUtils.parseMoney($(`#custo_unitario_final-${rowId}`).val()) || 0;
                    const custoTotalFinal = MoneyUtils.parseMoney($(`#custo_total_final-${rowId}`).val()) || (custoUnitarioFinal * quantidadeAtual);

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
                        custoTotalFinal
                    });

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
                        fobTotalGeral: fobTotalGeralAtualizado, // Usar o valor atualizado
                        fobUnitario,
                        diferenca_cambial_frete,
                        diferenca_cambial_fob
                    });
                }
            });

            atualizarCamposCabecalho();
            atualizarTotaisGlobais(fobTotalGeralAtualizado, dolar); // Usar o valor atualizado
            atualizarFatoresFob(); // Atualizar fatores FOB após todos os cálculos
            atualizarTotalizadores();
            setDebugGlobals({
                ...globaisProcesso,
                fobTotalProcesso: fobTotalGeralAtualizado,
                nacionalizacao: getNacionalizacaoAtual()
            });
            
            // Liberar flag após conclusão
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
                const diferenca_cambial_fob = (fatorVlrFob_AX * dif_cambial_fob_processo) - (fobTotal * dolar);

                // Se o valor for inválido ou negativo, não mostra nada no campo, caso contrário formata
                if (diferenca_cambial_frete === 0 || isNaN(diferenca_cambial_frete) || !isFinite(diferenca_cambial_frete) || diferenca_cambial_frete < 0) {
                    $(`#diferenca_cambial_frete-${rowId}`).val('');
                } else {
                    $(`#diferenca_cambial_frete-${rowId}`).val(MoneyUtils.formatMoney(diferenca_cambial_frete, 2));
                }
                $(`#diferenca_cambial_fob-${rowId}`).val(MoneyUtils.formatMoney(diferenca_cambial_fob, 2));


            }
        }

        const CAMPOS_EXCLUSIVOS_ANAPOLIS = ['rep_anapolis', 'desp_anapolis', 'correios'];
        const CAMPO_CORRECAO_LACRE = 'tx_correcao_lacre';
        const CAMPOS_EXTERNOS_BASE = [
            'outras_taxas_agente', 'liberacao_bl', 'desconsolidacao', 'isps_code', 'handling', 'capatazia',
            'tx_correcao_lacre', 'afrmm', 'armazenagem_sts', 'frete_dta_sts_ana', 'sda', 'rep_sts', 'armaz_ana',
            'lavagem_container', 'rep_anapolis', 'desp_anapolis', 'correios', 'li_dta_honor_nix', 'honorarios_nix'
        ];

        function getNacionalizacaoAtual() {
            const valor = $('#nacionalizacao').val();
            return (valor ? valor.toLowerCase() : 'outros');
        }

        function getCamposExternos() {
            const nacionalizacao = getNacionalizacaoAtual();
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

        function atualizarVisibilidadeNacionalizacao(options = {}) {
            const { recalcular = false } = options;
            const nacionalizacao = getNacionalizacaoAtual();
            const mostrarCamposAnapolis = nacionalizacao !== 'santos';
            const mostrarTxCorrecao = nacionalizacao === 'santos';

            // Ocultar/mostrar colunas no cabeçalho (middleRow)
            $('th[data-campo="tx_correcao_lacre"]').each(function() {
                if (mostrarTxCorrecao) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            $('th[data-campo="rep_anapolis"], th[data-campo="desp_anapolis"], th[data-campo="correios"]').each(function() {
                if (mostrarCamposAnapolis) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            // Ocultar/mostrar colunas nas linhas
            $('td[data-campo="tx_correcao_lacre"]').each(function() {
                if (mostrarTxCorrecao) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            $('td[data-campo="rep_anapolis"], td[data-campo="desp_anapolis"], td[data-campo="correios"]').each(function() {
                if (mostrarCamposAnapolis) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            // Manter compatibilidade com classes antigas
            toggleColunas('.coluna-anapolis', mostrarCamposAnapolis);
            toggleColunas('.coluna-tx-correcao-lacre', mostrarTxCorrecao);

            if (mostrarCamposAnapolis) {
                limparCamposEspecificos([CAMPO_CORRECAO_LACRE]);
            } else {
                limparCamposEspecificos(CAMPOS_EXCLUSIVOS_ANAPOLIS);
            }
            
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
                // Salvar a nacionalização primeiro
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

                // Recarregar a página para que as colunas sumam completamente
                window.location.reload();
            } catch (error) {
                console.error('Erro ao atualizar nacionalização:', error);
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
        // Função para obter valores base com a nova lógica
        function obterValoresBase(rowId) {
            let moedaProcesso = $('#moeda_processo').val();
            let fobUnitario;
            let fobUnitarioMoedaEstrangeira;

            let pesoTotal = MoneyUtils.parseMoney($(`#peso_liquido_total-${rowId}`).val()) || 0;
            let quantidade = MoneyUtils.parseMoney($(`#quantidade-${rowId}`).val()) || 0;

            if (moedaProcesso && moedaProcesso !== 'USD') {
                // Se tem moeda estrangeira, usar o valor dela como base
                fobUnitarioMoedaEstrangeira = MoneyUtils.parseMoney($(`#fob_unit_moeda_estrangeira-${rowId}`).val()) || 0;

                if (fobUnitarioMoedaEstrangeira > 0) {
                    // Converter da moeda estrangeira para USD
                    fobUnitario = converterMoedaProcessoParaUSD(fobUnitarioMoedaEstrangeira, moedaProcesso);
                } else {
                    // Se não tem valor na moeda estrangeira, tentar usar USD como fallback
                    fobUnitario = MoneyUtils.parseMoney($(`#fob_unit_usd-${rowId}`).val()) || 0;
                    // E converter USD para moeda estrangeira
                    if (fobUnitario > 0) {
                        fobUnitarioMoedaEstrangeira = converterUSDParaMoedaProcesso(fobUnitario, moedaProcesso);
                    }
                }
            } else {
                // Moeda é USD - usar valor direto
                fobUnitario = MoneyUtils.parseMoney($(`#fob_unit_usd-${rowId}`).val()) || 0;
                fobUnitarioMoedaEstrangeira = fobUnitario; // Para USD, são iguais
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

            // Só calcular se o valor for válido e não estiver vazio
            if (valor && valor.trim() !== '' && !isNaN(MoneyUtils.parseMoney(valor))) {
                // Pequeno delay para garantir que o usuário terminou de digitar
                clearTimeout(window.fobMoedaTimeout);
                window.fobMoedaTimeout = setTimeout(() => {
                    debouncedRecalcular();
                }, 500);
            }
        });

        // E também no fobUnitario normal
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
                // Pequeno delay para garantir processamento
                setTimeout(() => {
                    const {
                        pesoTotal,
                        fobUnitario,
                        quantidade,
                        fobUnitarioMoedaEstrangeira
                    } = obterValoresBase(rowId);
                    const fobTotal = fobUnitario * quantidade;

                    let cotacaoProcesso = getCotacaoesProcesso();

                    // Usar a cotação específica do seguro, não do frete
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
            // Atualizar o campo peso_liquido no formulário
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

                // Usar a cotação específica do seguro, não do frete
                let cotacaoMoedaSeguro = MoneyUtils.parseMoney($('#cotacao_seguro_internacional').val());
                let cotacaoUSD = cotacaoProcesso['USD']?.venda ?? 1;

                // Se não temos cotação específica, usar a cotação padrão da moeda
                if (!cotacaoMoedaSeguro && cotacaoProcesso[moedaSeguro]) {
                    cotacaoMoedaSeguro = cotacaoProcesso[moedaSeguro].venda;
                }

                if (cotacaoMoedaSeguro) {
                    let moedaEmUSD = cotacaoMoedaSeguro / cotacaoUSD;
                    valorSeguroInternacionalDolar = total * moedaEmUSD;
                } else {
                    // Fallback: usar cotação 1:1 se não encontrada
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

                // Usar a cotação específica do acréscimo de frete, se houver
                let cotacaoMoedaFrete = MoneyUtils.parseMoney($('#cotacao_acrescimo_frete').val());
                let cotacaoUSD = cotacaoProcesso['USD']?.venda ?? 1;

                // Se não tem cotação específica, usar a cotação padrão da moeda
                if (!cotacaoMoedaFrete && cotacaoProcesso[moedaFrete]) {
                    cotacaoMoedaFrete = cotacaoProcesso[moedaFrete].venda;
                }

                if (cotacaoMoedaFrete) {
                    let moedaEmUSD = cotacaoMoedaFrete / cotacaoUSD;
                    valorFreteUSD = base * moedaEmUSD;
                } else {
                    // Fallback 1:1
                    valorFreteUSD = base;
                }
            } else {
                valorFreteUSD = base;
            }

            return (valorFreteUSD / fobGeral) * fobTotal;
        }


        function calcularValorAduaneiro(fob, frete, acrescimo, seguro, thc, dolar, vlrCrfTotal = 0, serviceCharges = 0) {
            // Função para validar e converter valores com máxima precisão
            const parseSafe = (value, defaultValue = 0) => {
                if (value === null || value === undefined) return defaultValue;
                // Se já for número, usar direto para manter precisão
                if (typeof value === 'number') {
                    return isNaN(value) || !isFinite(value) ? defaultValue : value;
                }
                // Se for string, parsear com máxima precisão
                const num = parseFloat(value);
                return isNaN(num) || !isFinite(num) ? defaultValue : num;
            };

            // Valor aduaneiro considera apenas: vlr_crf_total, service_charges_usd, acresc_frete_usd, seguro_usd e thc_usd
            // Usar valores exatos sem arredondamento durante cálculos
            const safeAcrescimo = parseSafe(acrescimo); // acresc_frete_usd
            const safeSeguro = parseSafe(seguro); // seguro_usd
            const safeThc = parseSafe(thc); // thc em BRL
            const safeDolar = parseSafe(dolar, 1);
            const safeVlrCrfTotal = parseSafe(vlrCrfTotal); // vlr_crf_total
            const safeServiceCharges = parseSafe(serviceCharges); // service_charges_usd

            // THC precisa ser convertido de BRL para USD
            // Usar divisão exata sem arredondamento
            const thcUsd = safeDolar > 0 ? safeThc / safeDolar : 0;

            // Soma exata sem arredondamento - manter precisão máxima
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
            // Valida e trata o valor de diferença cambial frete
            // Retorna 0 se o valor for inválido (NaN, null, undefined, Infinity) ou negativo
            if (valor === null || valor === undefined || isNaN(valor) || !isFinite(valor) || valor < 0) {
                return 0;
            }
            return valor;
        }

        function calcularDespesas(rowId, fatorVlrFob_AX, fatorSiscomex, taxaSiscomexUnit, vlrAduaneiroBrl = null) {
            const multa = $(`#multa-${rowId}`).val() ? MoneyUtils.parseMoney($(`#multa-${rowId}`).val()) : 0;
            
            // tx_def_li é uma porcentagem, precisa calcular sobre uma base
            // Vamos usar o valor aduaneiro BRL como base
            // Se não foi passado como parâmetro, tenta ler do DOM
            if (vlrAduaneiroBrl === null) {
                vlrAduaneiroBrl = MoneyUtils.parseMoney($(`#valor_aduaneiro_brl-${rowId}`).val()) || 0;
            }
            const txDefLiPercent = $(`#tx_def_li-${rowId}`).val() ? MoneyUtils.parsePercentage($(`#tx_def_li-${rowId}`).val()) : 0;
            const txDefLi = vlrAduaneiroBrl * txDefLiPercent;
            
            // Taxa SISCOMEX da linha (já vem como parâmetro taxaSiscomexUnit)
            const taxaSiscomex = taxaSiscomexUnit || 0;
            
            // AFrmm
            const afrmm = $(`#afrmm-${rowId}`).val() ? MoneyUtils.parseMoney($(`#afrmm-${rowId}`).val()) : 0;
            
            // Armazenagem STS
            let armazenagem_sts = $('#armazenagem_sts-' + rowId).val() ? MoneyUtils.parseMoney($('#armazenagem_sts-' +
                    rowId).val()) :
                0;
            
            // Frete DTA STS/GYN
            let frete_dta_sts_ana = $('#frete_dta_sts_ana-' + rowId).val() ? MoneyUtils.parseMoney($('#frete_dta_sts_ana-' +
                        rowId)
                    .val()) :
                0;
            
            // Honorários NIX
            const honorarios_nix = $(`#honorarios_nix-${rowId}`).val() ? MoneyUtils.parseMoney($(`#honorarios_nix-${rowId}`).val()) : 0;

            let despesas = multa + txDefLi + taxaSiscomex;
            if (getNacionalizacaoAtual() === 'santos') {
                // Para santos: multa + txdefli + txsiscomex + afrmm + honorarios
                despesas += afrmm + honorarios_nix;
            } else {
                // Para outros: manter cálculo atual
                despesas += afrmm + armazenagem_sts + frete_dta_sts_ana + honorarios_nix;
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
                    honorarios_nix
                },
                tipoCalculo: getNacionalizacaoAtual()
            };
        }

        function calcularBcIcmsSemReducao(base, impostos, despesas) {
            const bcIpi = base + (base * impostos.ii);
            const vlrIpi = bcIpi * impostos.ipi;

            const resultado = (base + (base * impostos.ii) + vlrIpi + (base * impostos.pis) + (base * impostos.cofins) +
                despesas) / (1 - impostos.icms);
            return resultado;
        }

        function calcularBcIcmsReduzido(rowId, base, impostos, despesas) {
            atualizarReducao(rowId);
            const bcIpi = base + (base * impostos.ii);
            const vlrIpi = bcIpi * impostos.ipi;
            let reducao = 1;

            if ($(`#reducao-${rowId}`).val() && MoneyUtils.parsePercentage($(`#reducao-${rowId}`).val()) > 0) {
                reducao = MoneyUtils.parsePercentage($(`#reducao-${rowId}`).val());
            }
            const resultado = (base + (base * impostos.ii) + vlrIpi + (base * impostos.pis) + (base * impostos.cofins) +
                despesas) / ((1 - impostos.icms));

            return resultado * reducao;
        }

        function calcularTotais(base, impostos, despesas, quantidade, vlrIcmsReduzido, rowId) {
            const vlrII = base * impostos.ii;
            const bcIpi = base + vlrII;
            const vlrIpi = bcIpi * impostos.ipi;
            const bcPisCofins = base;
            const vlrPis = bcPisCofins * impostos.pis;
            const vlrCofins = bcPisCofins * impostos.cofins;
            const vlrTotalProdutoNf = base + vlrII;
            const vlrUnitProdutNf = vlrTotalProdutoNf / (quantidade || 1);
            const vlrTotalNfSemIcms = vlrTotalProdutoNf + vlrIpi + vlrPis + vlrCofins + despesas + vlrIcmsReduzido;
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

            $(`#peso_liquido_unitario-${rowId}`).val(valores.pesoLiqUnit);

            let moedaFrete = $('#frete_internacional_moeda').val();
            let moedaSeguro = $('#seguro_internacional_moeda').val();
            let moedaAcrescimo = $('#acrescimo_frete_moeda').val();
            let moedaProcesso = $('#moeda_processo').val(); // Nova variável

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

            // Acréscimo - valor na moeda estrangeira original
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
                    // Converter de USD para a moeda estrangeira
                    let fobUnitMoedaEstrangeira = valores.fobUnitario * (cotacaoMoedaProcesso / cotacaoUSD);
                    let fobTotalMoedaEstrangeira = valores.fobTotal * (cotacaoMoedaProcesso / cotacaoUSD);
                    // $(`#fob_unit_moeda_estrangeira-${rowId}`).val(MoneyUtils.formatMoney(fobUnitMoedaEstrangeira));
                    $(`#fob_total_moeda_estrangeira-${rowId}`).val(MoneyUtils.formatMoney(fobTotalMoedaEstrangeira, 7));
                }
            }
            $(`#acresc_frete_usd-${rowId}`).val(MoneyUtils.formatMoney(valores.acrescimoFreteUsdRow, 2));
            $(`#acresc_frete_brl-${rowId}`).val(MoneyUtils.formatMoney(valores.acrescimoFreteUsdRow * valores.dolar, 2));
            
            // Calcular e atualizar VLR_CRF_TOTAL e VLR_CRF_UNIT
            if (valores.vlrCrfTotal !== undefined) {
                $(`#vlr_crf_total-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrCrfTotal, 2));
            } else {
                // Fallback: calcular se não foi passado
                const fobTotal = valores.fobTotal || MoneyUtils.parseMoney($(`#fob_total_usd-${rowId}`).val()) || 0;
                const freteUsd = valores.freteUsdInt || MoneyUtils.parseMoney($(`#frete_usd-${rowId}`).val()) || 0;
                const vlrCrfTotal = fobTotal + freteUsd;
                $(`#vlr_crf_total-${rowId}`).val(MoneyUtils.formatMoney(vlrCrfTotal, 2));
            }
            
            if (valores.vlrCrfUnit !== undefined) {
                $(`#vlr_crf_unit-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrCrfUnit, 2));
            } else {
                // Fallback: calcular se não foi passado
                const vlrCrfTotal = MoneyUtils.parseMoney($(`#vlr_crf_total-${rowId}`).val()) || 0;
                const quantidade = valores.quantidade || MoneyUtils.parseMoney($(`#quantidade-${rowId}`).val()) || 0;
                const vlrCrfUnit = quantidade > 0 ? vlrCrfTotal / quantidade : 0;
                $(`#vlr_crf_unit-${rowId}`).val(MoneyUtils.formatMoney(vlrCrfUnit, 2));
            }
            
            // Calcular SERVICE_CHARGES rateado do processo (não mais editável por produto)
            let moedaServiceCharges = $('#service_charges_moeda').val();
            if (moedaServiceCharges && moedaServiceCharges !== 'USD') {
                if (valores.serviceChargesMoedaEstrangeira !== undefined) {
                    $(`#service_charges_moeda_estrangeira-${rowId}`).val(MoneyUtils.formatMoney(valores.serviceChargesMoedaEstrangeira, 2));
                } else {
                    // Fallback: calcular se não foi passado
                    const serviceChargesBase = MoneyUtils.parseMoney($('#service_charges').val()) || 0;
                    const fatorPesoRow = valores.fatorPesoRow || MoneyUtils.parseMoney($(`#fator_peso-${rowId}`).val()) || 0;
                    const serviceChargesMoedaEstrangeira = serviceChargesBase * fatorPesoRow;
                    $(`#service_charges_moeda_estrangeira-${rowId}`).val(MoneyUtils.formatMoney(serviceChargesMoedaEstrangeira, 2));
                }
            }
            
            if (valores.serviceChargesRow !== undefined) {
                $(`#service_charges-${rowId}`).val(MoneyUtils.formatMoney(valores.serviceChargesRow, 2));
            } else {
                // Fallback: calcular se não foi passado
                const serviceChargesBase = MoneyUtils.parseMoney($('#service_charges').val()) || 0;
                const fatorPesoRow = valores.fatorPesoRow || MoneyUtils.parseMoney($(`#fator_peso-${rowId}`).val()) || 0;
                const serviceChargesRow = serviceChargesBase * fatorPesoRow;
                $(`#service_charges-${rowId}`).val(MoneyUtils.formatMoney(serviceChargesRow, 2));
            }
            
            if (valores.serviceChargesBrl !== undefined) {
                $(`#service_charges_brl-${rowId}`).val(MoneyUtils.formatMoney(valores.serviceChargesBrl, 2));
            } else {
                // Fallback: calcular se não foi passado
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
            $(`#despesa_aduaneira-${rowId}`).val(MoneyUtils.formatMoney(valores.despesas, 2));
            $(`#base_icms_sem_reducao-${rowId}`).val(MoneyUtils.formatMoney(valores.bcIcmsSReducao, 2));
            $(`#valor_icms_sem_reducao-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrIcmsSReducao, 2));
            $(`#base_icms_reduzido-${rowId}`).val(MoneyUtils.formatMoney(valores.bcImcsReduzido, 2));
            $(`#valor_icms_reduzido-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrIcmsReduzido, 2));
            $(`#valor_unit_nf-${rowId}`).val(MoneyUtils.formatMoney(valores.totais.vlrUnitProdutNf, 2));
            $(`#valor_total_nf-${rowId}`).val(MoneyUtils.formatMoney(valores.totais.vlrTotalProdutoNf, 2));
            $(`#valor_total_nf_sem_icms_st-${rowId}`).val(MoneyUtils.formatMoney(valores.totais.vlrTotalNfSemIcms, 2));
            // NOTA: valor_total_nf_com_icms_st será atualizado após calcular o vlrIcmsSt (veja mais abaixo)
            
            $(`#base_icms_st-${rowId}`).val(MoneyUtils.formatMoney(valores.base_icms_st, 2));
            $(`#valor_icms_st-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrIcmsSt, 2));
            
            // Recalcular valor_total_nf_com_icms_st com o vlrIcmsSt calculado
            const valorTotalNfComIcmsStRecalculado = valores.totais.vlrTotalNfSemIcms + (valores.vlrIcmsSt || 0);
            $(`#valor_total_nf_com_icms_st-${rowId}`).val(MoneyUtils.formatMoney(valorTotalNfComIcmsStRecalculado, 2));

            // Validar e tratar diferenca_cambial_frete antes de exibir
            const diferencaCambialFreteValidada = validarDiferencaCambialFrete(valores.diferenca_cambial_frete);
            if (diferencaCambialFreteValidada === 0 || isNaN(diferencaCambialFreteValidada) || !isFinite(diferencaCambialFreteValidada) || diferencaCambialFreteValidada < 0) {
                $(`#diferenca_cambial_frete-${rowId}`).val('');
            } else {
                $(`#diferenca_cambial_frete-${rowId}`).val(MoneyUtils.formatMoney(diferencaCambialFreteValidada, 2));
            }
            $(`#diferenca_cambial_fob-${rowId}`).val(MoneyUtils.formatMoney(valores.diferenca_cambial_fob, 2));



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

            // Preferir dados calculados via debug (mesma base do modal)
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

                $(`#fator_valor_fob-${rowId}`).val(MoneyUtils.formatMoney(fatorVlrFob_AX, 6));
                $(`#fator_tx_siscomex-${rowId}`).val(MoneyUtils.formatMoney(fatorTaxaSiscomex_AY, 6));
                $(`#taxa_siscomex-${rowId}`).val(MoneyUtils.formatMoney(taxaSiscomexUnitaria_BB, 2));

                $(`#fator_vlr_fob-${rowId}`).val(MoneyUtils.formatMoney(fatorVlrFob_AX, 6));
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

        $('.cabecalhoInputs').on('change keyup', function() {
            atualizarFatoresFob();
            atualizarCamposCabecalho();
            atualizarTotalizadores();
        });
        $(document).on('change', '.icms_reduzido_percent', function() {
            const rowId = $(this).data('row');
            atualizarReducao(rowId);
        });

        function atualizarReducao(rowId) {
            const valor = MoneyUtils.parsePercentage($(`#icms_reduzido_percent-${rowId}`).val());
            const icmsPercent = MoneyUtils.parsePercentage($(`#icms_percent-${rowId}`).val())
            const novoReducao = valor / icmsPercent
            $(`#reducao-${rowId}`).val(MoneyUtils.formatPercentage(novoReducao));
        }

        // Debounce para evitar múltiplos recálculos
        let recalcularTimeout = null;
        let isRecalculating = false;

        function debouncedRecalcular() {
            if (isRecalculating) return;
            
            clearTimeout(recalcularTimeout);
            recalcularTimeout = setTimeout(() => {
                if (!isRecalculating) {
                    recalcularTodaTabela();
                }
            }, 300);
        }

        // Event listeners para multa e tx_def_li - apenas no blur para evitar loops
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

        // Debounce para atualizar campos cambiais
        let atualizarCambialTimeout = null;
        function debouncedAtualizarCambial() {
            clearTimeout(atualizarCambialTimeout);
            atualizarCambialTimeout = setTimeout(() => {
                atualizarFatoresFob();
                atualizarCamposCambial();
            }, 200);
        }

        $(document).on('change blur', '.difCambial', function() {
            debouncedAtualizarCambial();
        });

        $(document).on('focusin', '#nacionalizacao', function() {
            $(this).data('valor-anterior', $(this).val());
        });

        $(document).on('change', '#nacionalizacao', function() {
            processarMudancaNacionalizacao(this);
        });



        function atualizarCamposCabecalho() {
            const campos = getCamposExternos()
            const lengthTable = $('.linhas-input').length

            // Usar a função centralizada em vez de calcular manualmente
            let fobTotalGeral = calcularFobTotalGeral();

            // Para cada linha da tabela
            for (let i = 0; i < lengthTable; i++) {
                // Obter o fob_total_usd diretamente da coluna
                const fobTotal = MoneyUtils.parseMoney($(`#fob_total_usd-${i}`).val()) || 0;
                const fatorVlrFob_AX = fobTotalGeral ? (fobTotal / fobTotalGeral) : 0;
                let desp_desenbaraco_parte_1 = 0

                for (let campo of campos) {
                    const valorCampo = MoneyUtils.parseMoney($(`#${campo}`).val()) || 0
                    const valorDistribuido = MoneyUtils.formatMoney(valorCampo * fatorVlrFob_AX, 2)
                    desp_desenbaraco_parte_1 += valorCampo * fatorVlrFob_AX;
                    $(`#${campo}-${i}`).val(valorDistribuido)
                }

                let taxa_siscomex = $(`#taxa_siscomex-${i}`).val() ? MoneyUtils.parseMoney($(`#taxa_siscomex-${i}`).val()) :
                    0
                let multa = $(`#multa-${i}`).val() ? MoneyUtils.parseMoney($(`#multa-${i}`).val()) : 0
                // tx_def_li é uma porcentagem, calcular sobre valor aduaneiro BRL
                const vlrAduaneiroBrl = MoneyUtils.parseMoney($(`#valor_aduaneiro_brl-${i}`).val()) || 0;
                const txDefLiPercent = $(`#tx_def_li-${i}`).val() ? MoneyUtils.parsePercentage($(`#tx_def_li-${i}`).val()) : 0;
                let taxa_def = vlrAduaneiroBrl * txDefLiPercent;

                desp_desenbaraco_parte_1 += multa + taxa_def + taxa_siscomex;

                let capatazia = $('#capatazia-' + i).val() ? MoneyUtils.parseMoney($('#capatazia-' + i).val()) : 0
                let afrmm = $('#afrmm-' + i).val() ? MoneyUtils.parseMoney($('#afrmm-' + i).val()) : 0
                let armazenagem_sts = $('#armazenagem_sts-' + i).val() ? MoneyUtils.parseMoney($('#armazenagem_sts-' + i)
                    .val()) : 0
                let frete_dta_sts_ana = $('#frete_dta_sts_ana-' + i).val() ? MoneyUtils.parseMoney($('#frete_dta_sts_ana-' +
                        i)
                    .val()) : 0
                let honorarios_nix = $('#honorarios_nix-' + i).val() ? MoneyUtils.parseMoney($('#honorarios_nix-' + i)
                    .val()) : 0

                let desp_desenbaraco_parte_2 = multa + taxa_def + taxa_siscomex + capatazia + afrmm + honorarios_nix

                let despesa_desembaraco = desp_desenbaraco_parte_1 - desp_desenbaraco_parte_2
                const vlrIcmsReduzido = MoneyUtils.parseMoney($(`#valor_icms_reduzido-${i}`).val())
                let qquantidade = parseInt($(`#quantidade-${i}`).val()) || 0
                const vlrTotalNfComIcms = MoneyUtils.parseMoney($(`#valor_total_nf_com_icms_st-${i}`).val())
                let diferenca_cambial_frete = MoneyUtils.parseMoney($(`#diferenca_cambial_frete-${i}`).val());
                diferenca_cambial_frete = validarDiferencaCambialFrete(diferenca_cambial_frete);
                const diferenca_cambial_fob = MoneyUtils.parseMoney($(`#diferenca_cambial_fob-${i}`).val());
                const custo_unitario_final = ((vlrTotalNfComIcms + despesa_desembaraco + diferenca_cambial_fob +
                        diferenca_cambial_frete) - vlrIcmsReduzido) / qquantidade

                const custo_total_final = custo_unitario_final * qquantidade
                $(`#desp_desenbaraco-${i}`).val(MoneyUtils.formatMoney(despesa_desembaraco, 2))
                $(`#custo_unitario_final-${i}`).val(MoneyUtils.formatMoney(custo_unitario_final, 2))
                $(`#custo_total_final-${i}`).val(MoneyUtils.formatMoney(custo_total_final, 2))
            }

            // Atualizar totalizadores após recalcular campos externos
            atualizarTotalizadores();
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
                    setTimeout(atualizarTotalizadores, 100);

                } else {
                    Toast.fire({
                        icon: 'info',
                        title: 'Ação cancelada'
                    });
                }
            });
        })

        function calcularTaxaSiscomex() {
            // pega os valores dos inputs de adição
            const valores = $('input[name^="produtos["][name$="[adicao]"]')
                .map(function() {
                    return $(this).val();
                })
                .get();

            // remove vazios e duplica­tas
            const unicos = [...new Set(valores.filter(v => v !== ""))];
            const quantidade = unicos.length;

            const valorRegistroDI = 115.67;

            const faixas = [{
                    min: 1,
                    max: 1,
                    adicional: 38.56,
                    total: 154.23
                },
                {
                    min: 2,
                    max: 2,
                    adicional: 38.56,
                    total: 192.79
                },
                {
                    min: 3,
                    max: 3,
                    adicional: 30.85,
                    total: 223.64
                },
                {
                    min: 4,
                    max: 4,
                    adicional: 30.85,
                    total: 254.49
                },
                {
                    min: 5,
                    max: 5,
                    adicional: 30.85,
                    total: 285.34
                },
                {
                    min: 6,
                    max: 6,
                    adicional: 23.14,
                    total: 308.48
                },
                {
                    min: 7,
                    max: 7,
                    adicional: 23.14,
                    total: 331.62
                },
                {
                    min: 8,
                    max: 8,
                    adicional: 23.14,
                    total: 354.76
                },
                {
                    min: 9,
                    max: 9,
                    adicional: 23.14,
                    total: 377.90
                },
                {
                    min: 10,
                    max: 10,
                    adicional: 23.14,
                    total: 401.04
                },

                // A partir daqui NÃO tem total na tabela → usar fórmula B
                {
                    min: 11,
                    max: 20,
                    adicional: 15.42,
                    total: null
                },
                {
                    min: 21,
                    max: 50,
                    adicional: 7.71,
                    total: null
                },
                {
                    min: 51,
                    max: Infinity,
                    adicional: 3.86,
                    total: null
                },
            ];

            const faixa = faixas.find(f => quantidade >= f.min && quantidade <= f.max);
            if (!faixa) return valorRegistroDI;

            // Se a tabela já tiver total -> usa ele (sem arredondar, manter precisão máxima)
            if (faixa.total !== null) {
                return faixa.total; // Retornar valor exato sem arredondar
            }

            // Se não tiver total na tabela -> aplica fórmula B (sem arredondar)
            const total = valorRegistroDI + (quantidade * faixa.adicional);
            return total; // Retornar valor exato sem arredondar
        }


        $(document).on('click', '.addProduct', function() {
            let lengthOptions = $('#productsBody tr').length;
            let newIndex = lengthOptions;

            let select = `<select required data-row="${newIndex}" class="custom-select selectProduct select2" name="produtos[${newIndex}][produto_id]" id="produto_id-${newIndex}">
        <option selected disabled>Selecione uma opção</option>`;

            for (let produto of products) {
                select += `<option value="${produto.id}">${produto.modelo} - ${produto.codigo}</option>`;
            }
            select += '</select>';

            // Obter moedas atuais
            let moedaFrete = $('#frete_internacional_moeda').val();
            let moedaSeguro = $('#seguro_internacional_moeda').val();
            let moedaAcrescimo = $('#acrescimo_frete_moeda').val();
            let moedaProcesso = 'USD'; // Nova variável
            // let moedaProcesso = $('#moeda_processo').val(); // Nova variável
            // Gerar colunas condicionais
            let colunaFreteMoeda = '';
            let colunaSeguroMoeda = '';
            let colunaAcrescimoMoeda = '';

            if (moedaFrete && moedaFrete !== 'USD') {
                colunaFreteMoeda =
                    `<td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][frete_moeda_estrangeira]" id="frete_moeda_estrangeira-${newIndex}" value=""></td>`;
            }

            if (moedaSeguro && moedaSeguro !== 'USD') {
                colunaSeguroMoeda =
                    `<td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][seguro_moeda_estrangeira]" id="seguro_moeda_estrangeira-${newIndex}" value=""></td>`;
            }

            if (moedaAcrescimo && moedaAcrescimo !== 'USD') {
                colunaAcrescimoMoeda =
                    `<td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][acrescimo_moeda_estrangeira]" id="acrescimo_moeda_estrangeira-${newIndex}" value=""></td>`;
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
        <td><input data-row="${newIndex}" type="number" class="form-control" name="produtos[${newIndex}][item]" id="item-${newIndex}" value=""></td>
        <td><input type="text" class="form-control" readonly name="produtos[${newIndex}][codigo]" id="codigo-${newIndex}" value=""></td>
        <td><input type="text" class="form-control" readonly name="produtos[${newIndex}][ncm]" id="ncm-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" name="produtos[${newIndex}][quantidade]" id="quantidade-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][peso_liquido_unitario]" id="peso_liquido_unitario-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal pesoLiqTotal" name="produtos[${newIndex}][peso_liquido_total]" id="peso_liquido_total-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][fator_peso]" id="fator_peso-${newIndex}" value=""></td>
        ${colunasFOB}
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal7 fobUnitario" name="produtos[${newIndex}][fob_unit_usd]" id="fob_unit_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal7" readonly name="produtos[${newIndex}][fob_total_usd]" id="fob_total_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal7" readonly name="produtos[${newIndex}][fob_total_brl]" id="fob_total_brl-${newIndex}" value=""></td>
        
        <!-- FRETE -->
        ${colunaFreteMoeda}
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][frete_usd]" id="frete_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][frete_brl]" id="frete_brl-${newIndex}" value=""></td>
        
        <!-- SEGURO -->
        ${colunaSeguroMoeda}
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][seguro_usd]" id="seguro_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][seguro_brl]" id="seguro_brl-${newIndex}" value=""></td>
        
        <!-- ACRÉSCIMO -->
        ${colunaAcrescimoMoeda}
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][acresc_frete_usd]" id="acresc_frete_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][acresc_frete_brl]" id="acresc_frete_brl-${newIndex}" value=""></td>
        
        <!-- VLR CFR e SERVICE CHARGES -->
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][vlr_crf_total]" id="vlr_crf_total-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][vlr_crf_unit]" id="vlr_crf_unit-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][service_charges]" id="service_charges-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal" readonly name="produtos[${newIndex}][service_charges_brl]" id="service_charges_brl-${newIndex}" value=""></td>
        
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
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal7" name="produtos[${newIndex}][multa]" id="multa-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control percentage2" name="produtos[${newIndex}][tx_def_li]" id="tx_def_li-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][taxa_siscomex]" id="taxa_siscomex-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][outras_taxas_agente]" id="outras_taxas_agente-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][liberacao_bl]" id="liberacao_bl-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][desconsolidacao]" id="desconsolidacao-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][isps_code]" id="isps_code-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][handling]" id="handling-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][capatazia]" id="capatazia-${newIndex}" value=""></td>
        ${getNacionalizacaoAtual() === 'santos' ? '<td data-campo="tx_correcao_lacre"><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][tx_correcao_lacre]" id="tx_correcao_lacre-${newIndex}" value=""></td>' : ''}
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][afrmm]" id="afrmm-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][armazenagem_sts]" id="armazenagem_sts-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][frete_dta_sts_ana]" id="frete_dta_sts_ana-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][sda]" id="sda-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][rep_sts]" id="rep_sts-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][armaz_ana]" id="armaz_ana-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][lavagem_container]" id="lavagem_container-${newIndex}" value=""></td>
        ${getNacionalizacaoAtual() !== 'santos' ? '<td data-campo="rep_anapolis"><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][rep_anapolis]" id="rep_anapolis-${newIndex}" value=""></td><td data-campo="desp_anapolis"><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][desp_anapolis]" id="desp_anapolis-${newIndex}" value=""></td><td data-campo="correios"><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][correios]" id="correios-${newIndex}" value=""></td>' : ''}
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][li_dta_honor_nix]" id="li_dta_honor_nix-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][honorarios_nix]" id="honorarios_nix-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][desp_desenbaraco]" id="desp_desenbaraco-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][diferenca_cambial_frete]" id="diferenca_cambial_frete-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][diferenca_cambial_fob]" id="diferenca_cambial_fob-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][custo_unitario_final]" id="custo_unitario_final-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][custo_total_final]" id="custo_total_final-${newIndex}" value=""></td>
    </tr>`;

            $('#productsBody').append(tr);

            $(`#produto_id-${newIndex}`).select2({
                width: '100%'
            });
            $('input[data-row="' + newIndex + '"]').trigger('change');

            $(`#row-${newIndex} input, #row-${newIndex} select, #row-${newIndex} textarea`).each(function() {
                initialInputs.add(this);
            });
            setTimeout(atualizarTotalizadores, 100);

        });

        $(document).ready(function($) {

            var activeTab = localStorage.getItem('activeTab_processo');
            if (activeTab) {
                $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
            }
            
            // Atualizar peso líquido quando a página carregar
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

        })
        $('.nav-link').on('click', function(e) {
            var currentTab = $(e.target).attr('href');
            localStorage.setItem('activeTab_processo', currentTab);
        });

        function adicionarSeparadoresAdicao() {
            const tbody = document.getElementById('productsBody');
            const linhas = Array.from(tbody.querySelectorAll('tr:not(.separador-adicao)'));

            // Primeiro remove todos os separadores existentes
            document.querySelectorAll('.separador-adicao').forEach(el => el.remove());

            if (linhas.length === 0) return;

            // Ordenar as linhas por adição e depois por item
            linhas.sort((a, b) => {
                const adicaoA = parseFloat(a.querySelector('input[name*="[adicao]"]').value) || 0;
                const adicaoB = parseFloat(b.querySelector('input[name*="[adicao]"]').value) || 0;

                if (adicaoA !== adicaoB) {
                    return adicaoA - adicaoB; // primeiro pela adição
                }

                const itemA = parseFloat(a.querySelector('input[name*="[item]"]').value) || 0;
                const itemB = parseFloat(b.querySelector('input[name*="[item]"]').value) || 0;
                return itemA - itemB; // depois pelo item
            });

            // Agrupar linhas por adição
            const grupos = {};
            linhas.forEach(linha => {
                const adicao = parseFloat(linha.querySelector('input[name*="[adicao]"]').value) || 0;
                if (!grupos[adicao]) grupos[adicao] = [];
                grupos[adicao].push(linha);
            });

            // Limpar o tbody
            while (tbody.firstChild) {
                tbody.removeChild(tbody.firstChild);
            }

            // Adicionar linhas com separadores
            Object.keys(grupos).sort((a, b) => a - b).forEach((adicao, index) => {
                if (index > 0) {
                    const separador = document.createElement('tr');
                    separador.className = 'separador-adicao';
                    separador.innerHTML =
                        `<td colspan="100" style="background-color: #000 !important; height: 5px; padding: 0;"></td>`;
                    tbody.appendChild(separador);
                }

                grupos[adicao].forEach(linha => {
                    tbody.appendChild(linha);
                });
            });
        }



        // Atualizar a função reordenarLinhas para usar os separadores
        function reordenarLinhas() {
            // Se já estiver reordenando, ignora
            if (reordenando) return;

            reordenando = true;

            adicionarSeparadoresAdicao();

            $('#productsBody input:not([name*="[adicao]"])').trigger('change');
            $('#productsBody select').trigger('change');

            reordenando = false;
        }

        // Adicionar CSS para os separadores
        const style = document.createElement('style');
        style.textContent = `
    .separador-adicao td {
        background-color: #000 !important;
        border: none !important;
        height: 5px;
        padding: 0 !important;
    }
    .separador-adicao:hover {
        background-color: transparent !important;
    }
`;
        document.head.appendChild(style);

        $(document).on('change', 'input[name*="[adicao]"]', reordenarLinhas);
        $(document).on('click', '.btn-reordenar', reordenarLinhas);
    </script>
@endsection
