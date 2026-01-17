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
                    'Valor calculado automaticamente baseado no número de adições únicas do processo, usando faixas progressivas.',
                    (function() {

                        const valores = $('input[name^="produtos["][name$="[adicao]"]')
                            .map(function() {
                                return $(this).val();
                            })
                            .get();
                        const unicos = [...new Set(valores.filter(v => v !== ""))];
                        const quantidade = unicos.length;
                        const valorRegistroDI = 115.67;
                        
                        let detalhes = [];
                        let totalCalculado = valorRegistroDI;
                        
                        detalhes.push(`Quantidade de adições únicas: ${quantidade}`);
                        detalhes.push(`Taxa base (Registro DI): ${formatRawValue(valorRegistroDI, 2)}`);
                        
                        if (quantidade === 0) {
                            detalhes.push(`Total: ${formatRawValue(totalCalculado, 2)}`);
                        } else {

                            const faixas = [
                                { limite: 2, valor: 38.56, inicio: 0, descricao: 'Adições 1-2' },
                                { limite: 3, valor: 30.85, inicio: 2, descricao: 'Adições 3-5' },
                                { limite: 5, valor: 23.14, inicio: 5, descricao: 'Adições 6-10' },
                                { limite: 10, valor: 15.42, inicio: 10, descricao: 'Adições 11-20' },
                                { limite: 30, valor: 7.71, inicio: 20, descricao: 'Adições 21-50' },
                                { limite: Infinity, valor: 3.86, inicio: 50, descricao: 'Adições acima de 50' }
                            ];
                            

                            faixas.forEach(faixa => {
                                let adicoesNaFaixa;
                                if (faixa.limite === Infinity) {
                                    adicoesNaFaixa = Math.max(quantidade - faixa.inicio, 0);
                                } else {
                                    adicoesNaFaixa = Math.min(
                                        Math.max(quantidade - faixa.inicio, 0),
                                        faixa.limite
                                    );
                                }
                                
                                if (adicoesNaFaixa > 0) {
                                    const valorFaixa = adicoesNaFaixa * faixa.valor;
                                    totalCalculado += valorFaixa;
                                    detalhes.push(`${faixa.descricao}: ${adicoesNaFaixa} × R$ ${faixa.valor.toFixed(2)} = ${formatRawValue(valorFaixa, 2)}`);
                                }
                            });
                            
                            detalhes.push(`TOTAL: ${formatRawValue(totalCalculado, 2)}`);
                        }
                        
                        return detalhes.join(' | ');
                    })()
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
            const nacionalizacaoDebug = globais?.nacionalizacao || '';
            let formulaDespesa = '';
            let detailDespesaExpr = '';
            const despComp = dados.despesasComponentes || {};
            
            if (nacionalizacaoDebug === 'santos') {
                formulaDespesa = 'Multa + (Valor Aduaneiro BRL × % DEF/L.I.) + Taxa SISCOMEX da linha + AFRMM + Honorários.';
                detailDespesaExpr = `${formatComponent('Multa', despComp.multa, 2)} + ${formatComponent('% DEF/L.I.', despComp.txDefLi, 2)} + ${formatComponent('Taxa SISCOMEX', despComp.taxaSiscomex, 2)} + ${formatComponent('AFRMM', despComp.afrmm, 2)} + ${formatComponent('Honorários', despComp.honorarios_nix, 2)}`;
            } else if (nacionalizacaoDebug === 'anapolis') {
                formulaDespesa = 'Multa + (Valor Aduaneiro BRL × % DEF/L.I.) + Taxa SISCOMEX da linha + AFRMM + Armazenagem STS + Frete STS/GYN + Honorários NIX.';
                detailDespesaExpr = `${formatComponent('Multa', despComp.multa, 2)} + ${formatComponent('% DEF/L.I.', despComp.txDefLi, 2)} + ${formatComponent('Taxa SISCOMEX', despComp.taxaSiscomex, 2)} + ${formatComponent('AFRMM', despComp.afrmm, 2)} + ${formatComponent('Armazenagem STS', despComp.armazenagem_sts, 2)} + ${formatComponent('Frete STS/GYN', despComp.frete_dta_sts_ana, 2)} + ${formatComponent('Honorários NIX', despComp.honorarios_nix, 2)}`;
            } else {
                formulaDespesa = 'Multa + (Valor Aduaneiro BRL × % DEF/L.I.) + Taxa SISCOMEX da linha + AFRMM + Armazenagem STS + Frete DTA STS/ANA + Honorários.';
                detailDespesaExpr = `${formatComponent('Multa', despComp.multa, 2)} + ${formatComponent('% DEF/L.I.', despComp.txDefLi, 2)} + ${formatComponent('Taxa SISCOMEX', despComp.taxaSiscomex, 2)} + ${formatComponent('AFRMM', despComp.afrmm, 2)} + ${formatComponent('Armazenagem Porto', despComp.armazenagem_sts, 2)} + ${formatComponent('Frete DTA', despComp.frete_dta_sts_ana, 2)} + ${formatComponent('Honorários', despComp.honorarios_nix, 2)}`;
            }
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
                buildRow('Desp. Desembaraço', formatDebugMoney(dados.despesaDesembaraco ?? 0, 2), 
                    'Parte 1 (Campos Externos + Multa + Taxa DEF + Taxa SISCOMEX) - Parte 2 (Multa + Taxa DEF + Taxa SISCOMEX + Capatazia + AFRMM + Honorários).',
                    (function() {
                        if (!dados.despesaDesembaracoDetalhes) return '-';
                        const det = dados.despesaDesembaracoDetalhes;
                        const camposExternosStr = Object.entries(det.camposExternos || {})
                            .map(([campo, valor]) => formatComponent(campo, valor, 2))
                            .join(' + ');
                        const parte1Expr = camposExternosStr + 
                            (camposExternosStr ? ' + ' : '') +
                            formatComponent('Multa', det.multa, 2) + ' + ' +
                            formatComponent('Taxa DEF', det.taxaDef, 2) + ' + ' +
                            formatComponent('Taxa SISCOMEX', det.taxaSiscomex, 2);
                        const parte2Expr = formatComponent('Multa', det.multa, 2) + ' + ' +
                            formatComponent('Taxa DEF', det.taxaDef, 2) + ' + ' +
                            formatComponent('Taxa SISCOMEX', det.taxaSiscomex, 2) + ' + ' +
                            formatComponent('Capatazia', det.capatazia, 2) + ' + ' +
                            formatComponent('AFRMM', det.afrmm, 2) + ' + ' +
                            formatComponent('Honorários', det.honorariosNix, 2);
                        return `(${parte1Expr}) - (${parte2Expr}) = ${formatRawValue(dados.despesaDesembaraco ?? 0, 2)}`;
                    })()),
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
                buildRow('Custo Unit. Final', formatDebugMoney(dados.custoUnitarioFinal, 2), 
                    (globais?.nacionalizacao === 'santos')
                        ? '(Total NF c/ICMS + Desp. Desembaraço + Dif. Cambial FOB + Dif. Cambial Frete) ÷ Quantidade.'
                        : '[(Total NF c/ICMS + Desp. Desembaraço + Dif. Cambial FOB + Dif. Cambial Frete) - ICMS reduzido] ÷ Quantidade.',
                    (globais?.nacionalizacao === 'santos')
                        ? formatCalcDetail(dados.custoUnitarioFinal, ['(', formatComponent('Total NF c/ICMS', dados.vlrTotalNfComIcms ?? dados.vlrTotalNfSemIcms ?? vlrTotalNfSemIcmsVal, 2), '+', formatComponent('Desp. Desembaraço', dados.despesaDesembaraco ?? 0, 2), '+', formatComponent('Dif. Cambial FOB', dados.diferencaCambialFob, 2), '+', formatComponent('Dif. Cambial Frete', dados.diferencaCambialFrete, 2), ')', '÷', formatComponent('Quantidade', quantidadeSafe, 4)], 2)
                        : formatCalcDetail(dados.custoUnitarioFinal, ['(', formatComponent('Total NF c/ICMS', dados.vlrTotalNfComIcms ?? dados.vlrTotalNfSemIcms ?? vlrTotalNfSemIcmsVal, 2), '+', formatComponent('Desp. Desembaraço', dados.despesaDesembaraco ?? 0, 2), '+', formatComponent('Dif. Cambial FOB', dados.diferencaCambialFob, 2), '+', formatComponent('Dif. Cambial Frete', dados.diferencaCambialFrete, 2), '-', formatComponent('ICMS reduzido', vlrIcmsReduzidoVal, 2), ')', '÷', formatComponent('Quantidade', quantidadeSafe, 4)], 2)),
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
            
            // Sempre criar o totalizador, mesmo sem linhas
            const tfoot = $('#resultado-totalizadores');
            if (tfoot.length === 0) {
                console.error('Elemento #resultado-totalizadores não encontrado!');
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

                correios: 0,
                li_dta_honor_nix: 0,
                honorarios_nix: 0,
                desp_desenbaraco: 0,
                diferenca_cambial_frete: 0,
                diferenca_cambial_fob: 0,
                opcional_1_valor: 0,
                opcional_2_valor: 0,
                custo_total_final: 0
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
                    Object.keys(totais).forEach(campo => {
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
                console.error('Elemento #resultado-totalizadores não encontrado antes de criar totalizador!');
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
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.outras_taxas_agente, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.liberacao_bl, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.desconsolidacao, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.isps_code, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.handling, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.capatazia, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.afrmm, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.armazenagem_sts, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.frete_dta_sts_ana, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.sda, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.rep_sts, 2)}</td>
        ${getNacionalizacaoAtual() === 'anapolis' ? '<td data-campo="desp_anapolis" style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.desp_anapolis || 0, 2) + '</td><td data-campo="rep_anapolis" style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.rep_anapolis, 2) + '</td><td data-campo="correios" style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.correios, 2) + '</td>' : ''}
        ${getNacionalizacaoAtual() !== 'anapolis' ? '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.armaz_ana, 2) + '</td>' : ''}
        ${getNacionalizacaoAtual() === 'santos' ? '<td data-campo="tx_correcao_lacre" style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.tx_correcao_lacre, 2) + '</td>' : ''}
        ${getNacionalizacaoAtual() !== 'anapolis' ? '<td style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.lavagem_container, 2) + '</td>' : ''}
        ${getNacionalizacaoAtual() !== 'santos' && getNacionalizacaoAtual() !== 'anapolis' ? '<td data-campo="rep_anapolis" style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.rep_anapolis, 2) + '</td><td data-campo="desp_anapolis" style="display: none; font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.desp_anapolis || 0, 2) + '</td><td data-campo="correios" style="font-weight: bold; text-align: right;">' + MoneyUtils.formatMoney(totais.correios, 2) + '</td>' : ''}
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.li_dta_honor_nix, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.honorarios_nix, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.desp_desenbaraco, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(validarDiferencaCambialFrete(totais.diferenca_cambial_frete), 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.diferenca_cambial_fob, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.opcional_1_valor || 0, 2)}</td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.opcional_2_valor || 0, 2)}</td>
        <td></td>
        <td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais.custo_total_final, 2)}</td>
    </tr>`;

            try {
                if (tfoot.length === 0) {
                    console.error('Elemento #resultado-totalizadores não encontrado ao tentar adicionar totalizador!');
                    return;
                }
                // tfoot já foi esvaziado anteriormente, apenas adicionar o conteúdo
                tfoot.append(tr);
            } catch (error) {
                console.error('Erro ao adicionar totalizador:', error);
                console.error('tfoot:', tfoot);
                console.error('tr length:', tr ? tr.length : 'tr é null/undefined');
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
                console.warn(`Cotações inválidas para conversão: ${moedaProcesso} = ${cotacaoMoeda}, USD = ${cotacaoUSD}`);
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
                console.warn(`Cotações inválidas para conversão: ${moedaProcesso} = ${cotacaoMoeda}, USD = ${cotacaoUSD}`);
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
            $('.select2').select2({
                width: '100%'
            });


            $('#service_charges').on('blur change input', function() {

                convertToUSDAndBRL('service_charges');

                setTimeout(function() {
                    recalcularTodaTabela();
                }, 100);
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
                        console.error('Erro ao recalcular tabela:', error);
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
                    recalcularTodaTabela();
                }, 200);
            });
            

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


                setTimeout(recalcularTodaTabela, 200);

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
            const deleteUrl = '/destroy-produto-processo/' + documentId; 

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
            resetDebugStore();
            
            // Inicializar ou limpar valores brutos por linha para garantir precisão máxima
            window.valoresBrutosPorLinha = {};
            
            const rows = $('.linhas-input');
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


                    $(`#peso_liquido_unitario-${rowId}`).val(pesoTotal / (quantidade || 1));
                    $(`#fob_total_usd-${rowId}`).val(MoneyUtils.formatMoney(fobTotal, 7));
                    $(`#fob_total_brl-${rowId}`).val(MoneyUtils.formatMoney(fobTotal * dolar, 7));
                }
            });


            rows.each(function() {
                const rowId = this.id.toString().replace('row-', '');
                if (rowId) {
                    const linha = fobTotaisPorLinha[rowId];

                    const fobTotal = linha.fobTotal;
                    const fobUnitario = linha.fobUnitario;
                    const quantidade = linha.quantidade;
                    const fatorPesoRow = linha.fatorPesoRow;
                    const pesoTotal = linha.pesoTotal;
                    

                    const quantidadeAtual = quantidade || MoneyUtils.parseMoney($(`#quantidade-${rowId}`).val()) || 0;


                    const fatorVlrFob_AX = fobTotal / (fobTotalGeralAtualizado || 1);
                    $(`#fator_valor_fob-${rowId}`).val(MoneyUtils.formatMoney(fatorVlrFob_AX, 6));


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


                    const vlrCrfTotal = fobTotal + freteUsdInt;

                    const vlrCrfUnit = quantidadeAtual > 0 ? vlrCrfTotal / quantidadeAtual : 0;


                    const seguroIntUsdRow = calcularSeguro(fobTotal, fobTotalGeralAtualizado);


                    const acrescimoFreteUsdRow = calcularAcrescimoFrete(fobTotal, fobTotalGeralAtualizado, dolar);
                    const vlrAduaneiroUsd = calcularValorAduaneiro(fobTotal, freteUsdInt, acrescimoFreteUsdRow,
                        seguroIntUsdRow, thcRow, dolar, vlrCrfTotal, serviceChargesRowAtual);

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
                    const diferenca_cambial_fob = dif_cambial_fob_processo > 0 ? (fatorVlrFob_AX * dif_cambial_fob_processo) - (fobTotal * dolar) : 0;

                    const reducaoPercent = MoneyUtils.parseMoney($(`#reducao-${rowId}`).val());
                    const custoUnitarioFinal = MoneyUtils.parseMoney($(`#custo_unitario_final-${rowId}`).val()) || 0;
                    const custoTotalFinal = MoneyUtils.parseMoney($(`#custo_total_final-${rowId}`).val()) || (custoUnitarioFinal * quantidadeAtual);

                    const camposExternos = getCamposExternos();
                    let desp_desenbaraco_parte_1 = 0;
                    for (let campo of camposExternos) {
                        const valorCampo = MoneyUtils.parseMoney($(`#${campo}`).val()) || 0;
                        const valorDistribuido = window.valoresBrutosCamposExternos[campo]?.[rowId] ?? (valorCampo * fatorVlrFob_AX);
                        desp_desenbaraco_parte_1 += valorDistribuido;
                    }
                    const multaDesp = $(`#multa-${rowId}`).val() ? MoneyUtils.parseMoney($(`#multa-${rowId}`).val()) : 0;
                    const vlrAduaneiroBrlDesp = vlrAduaneiroBrl;
                    const txDefLiPercentDesp = $(`#tx_def_li-${rowId}`).val() ? MoneyUtils.parsePercentage($(`#tx_def_li-${rowId}`).val()) : 0;
                    const taxa_def_desp = vlrAduaneiroBrlDesp * txDefLiPercentDesp;
                    const taxa_siscomex_desp = taxaSiscomexUnitaria_BB || 0;
                    desp_desenbaraco_parte_1 += multaDesp + taxa_def_desp + taxa_siscomex_desp;
                    const capatazia_desp = $(`#capatazia-${rowId}`).val() ? MoneyUtils.parseMoney($(`#capatazia-${rowId}`).val()) : 0;
                    const afrmm_desp = $(`#afrmm-${rowId}`).val() ? MoneyUtils.parseMoney($(`#afrmm-${rowId}`).val()) : 0;
                    const honorarios_nix_desp = $(`#honorarios_nix-${rowId}`).val() ? MoneyUtils.parseMoney($(`#honorarios_nix-${rowId}`).val()) : 0;
                    const desp_desenbaraco_parte_2 = multaDesp + taxa_def_desp + taxa_siscomex_desp + capatazia_desp + afrmm_desp + honorarios_nix_desp;
                    const despesaDesembaraco = desp_desenbaraco_parte_1 - desp_desenbaraco_parte_2;

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
                        despesa_aduaneira: despesas,
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
                        tx_def_li: taxa_def_desp,
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
                        frete_dta_sts_ana: MoneyUtils.parseMoney($(`#frete_dta_sts_ana-${rowId}`).val()) || 0,
                        sda: MoneyUtils.parseMoney($(`#sda-${rowId}`).val()) || 0,
                        rep_sts: MoneyUtils.parseMoney($(`#rep_sts-${rowId}`).val()) || 0,
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
                        custo_total_final: custoTotalFinal
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
                        diferenca_cambial_fob
                    });
                }
            });

            atualizarCamposCabecalho();
            atualizarTotaisGlobais(fobTotalGeralAtualizado, dolar); 
            atualizarFatoresFob(); 
            atualizarTotalizadores();
            calcularValoresCPT();
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
                const diferenca_cambial_fob = (fatorVlrFob_AX * dif_cambial_fob_processo) - (fobTotal * dolar);


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
            
            // Só calcular se for Anápolis e processo marítimo
            if (nacionalizacao !== 'anapolis' || tipoProcesso !== 'maritimo') {
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

        function atualizarVisibilidadeNacionalizacao(options = {}) {
            const { recalcular = false } = options;
            const nacionalizacao = getNacionalizacaoAtual();
            const mostrarCamposAnapolis = nacionalizacao !== 'santos';
            const mostrarTxCorrecao = nacionalizacao === 'santos';


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

        function calcularDespesas(rowId, fatorVlrFob_AX, fatorSiscomex, taxaSiscomexUnit, vlrAduaneiroBrl = null) {
            const multa = $(`#multa-${rowId}`).val() ? MoneyUtils.parseMoney($(`#multa-${rowId}`).val()) : 0;

            if (vlrAduaneiroBrl === null) {
                vlrAduaneiroBrl = MoneyUtils.parseMoney($(`#valor_aduaneiro_brl-${rowId}`).val()) || 0;
            }
            const txDefLiPercent = $(`#tx_def_li-${rowId}`).val() ? MoneyUtils.parsePercentage($(`#tx_def_li-${rowId}`).val()) : 0;
            const txDefLi = vlrAduaneiroBrl * txDefLiPercent;
            
            const taxaSiscomex = taxaSiscomexUnit || 0;
            

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
            const nacionalizacao = getNacionalizacaoAtual();
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

            // Debug: console.log apenas para função calcularDespesas e apenas para rowId == 8
            if (rowId == 8) {
                console.log('=== DESP. ADUANEIRA - Row ' + rowId + ' ===');
                console.log('Nacionalização:', nacionalizacao);
                console.log('Multa:', multa);
                console.log('Valor Aduaneiro BRL:', vlrAduaneiroBrl);
                console.log('TX DEF/L.I. %:', txDefLiPercent);
                console.log('TX DEF/L.I. (calculado):', txDefLi);
                console.log('Taxa SISCOMEX:', taxaSiscomex);
                console.log('AFRMM:', afrmm);
                console.log('Armazenagem STS:', armazenagem_sts);
                console.log('Frete STS/GYN:', frete_dta_sts_ana);
                console.log('Honorários NIX:', honorarios_nix);
                console.log('Opcional 1 (compoe:', opcional1Compoe, ', valor:', opcional1Valor, ')');
                console.log('Opcional 2 (compoe:', opcional2Compoe, ', valor:', opcional2Valor, ')');
                console.log('Total DESP. ADUANEIRA:', despesas);
                console.log('==============================');
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


            if ($(`#reducao-${rowId}`).val() && MoneyUtils.parseMoney($(`#reducao-${rowId}`).val()) > 0) {
                reducao = MoneyUtils.parseMoney($(`#reducao-${rowId}`).val());
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
                $(`#vlr_crf_total-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrCrfTotal, 2));
            } else {

                const fobTotal = valores.fobTotal || MoneyUtils.parseMoney($(`#fob_total_usd-${rowId}`).val()) || 0;
                const freteUsd = valores.freteUsdInt || MoneyUtils.parseMoney($(`#frete_usd-${rowId}`).val()) || 0;
                const vlrCrfTotal = fobTotal + freteUsd;
                $(`#vlr_crf_total-${rowId}`).val(MoneyUtils.formatMoney(vlrCrfTotal, 2));
            }
            
            if (valores.vlrCrfUnit !== undefined) {
                $(`#vlr_crf_unit-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrCrfUnit, 2));
            } else {

                const vlrCrfTotal = MoneyUtils.parseMoney($(`#vlr_crf_total-${rowId}`).val()) || 0;
                const quantidade = valores.quantidade || MoneyUtils.parseMoney($(`#quantidade-${rowId}`).val()) || 0;
                const vlrCrfUnit = quantidade > 0 ? vlrCrfTotal / quantidade : 0;
                $(`#vlr_crf_unit-${rowId}`).val(MoneyUtils.formatMoney(vlrCrfUnit, 2));
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
            $(`#despesa_aduaneira-${rowId}`).val(MoneyUtils.formatMoney(valores.despesas, 2));
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

        $('.cabecalhoInputs').on('blur', function() {
            atualizarFatoresFob();
            atualizarCamposCabecalho();
            // Recalcular toda a tabela para atualizar valores brutos usados no totalizador
            // recalcularTodaTabela() já chama atualizarTotalizadores() internamente
            recalcularTodaTabela();
            calcularValoresCPT();
        });
        
        // Listeners para campos opcionais
        $(document).on('change keyup', '.opcional-valor', function() {
            ratearCamposOpcionais();
            recalcularTodaTabela();
        });
        
        $(document).on('change', '#opcional_1_compoe_despesas, #opcional_2_compoe_despesas', function() {
            recalcularTodaTabela();
        });
        
        // Botão para salvar campos do cabeçalho
        $('#btnSalvarCabecalho').on('click', async function() {
            const btn = $(this);
            const originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Salvando...');
            
            try {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                
                // Campos do cabeçalho que devem ser salvos
                let campos = [
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
                    'desp_anapolis',
                    'correios',
                    'li_dta_honor_nix',
                    'honorarios_nix',
                    'diferenca_cambial_frete',
                    'diferenca_cambial_fob',
                    'opcional_1_valor',
                    'opcional_1_descricao',
                    'opcional_1_compoe_despesas',
                    'opcional_2_valor',
                    'opcional_2_descricao',
                    'opcional_2_compoe_despesas'
                ];

                for (let campo of campos) {
                    let valor;
                    if (campo === 'opcional_1_compoe_despesas' || campo === 'opcional_2_compoe_despesas') {
                        valor = $(`#${campo}`).is(':checked') ? '1' : '0';
                    } else if (campo === 'opcional_1_descricao' || campo === 'opcional_2_descricao') {
                        valor = $(`#${campo}`).val() || '';
                    } else {
                        // Para capatazia, ler do campo readonly
                        const $campo = $(`#${campo}`);
                        if ($campo.length) {
                            valor = MoneyUtils.parseMoney($campo.val());
                        } else {
                            valor = 0;
                        }
                    }
                    // Sempre enviar o valor, mesmo que seja 0 ou vazio
                    formData.append(campo, valor !== null && valor !== undefined ? valor : (campo.includes('descricao') ? '' : '0'));
                }
                
                const url = '{{ route("processo.salvar.cabecalho.inputs.maritimo", $processo->id ?? 0) }}';
                
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: data.message || 'Campos do cabeçalho salvos com sucesso!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(data.error || 'Erro ao salvar campos do cabeçalho');
                }
            } catch (error) {
                console.error('Erro ao salvar cabecalhoInputs:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: error.message || 'Erro ao salvar campos do cabeçalho. Tente novamente.',
                });
            } finally {
                btn.prop('disabled', false).html(originalText);
            }
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

            $(`#reducao-${rowId}`).val(MoneyUtils.formatMoney(novoReducao, 8));
        }


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
                    

                    $(`#${campo}-${i}`).val(MoneyUtils.formatMoney(valorFinal, 2));
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
                            
                            $(`#${campo}-${i}`).val(MoneyUtils.formatMoney(valoresPorLinha[i], 2));
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
                


                $(`#${campo}-${ultimaLinha}`).val(MoneyUtils.formatMoneyExato(valorUltimaLinha));
            }
            

            for (let i = 0; i < lengthTable; i++) {
                const fobTotal = MoneyUtils.parseMoney($(`#fob_total_usd-${i}`).val()) || 0;
                const fatorVlrFob_AX = fobTotalGeral > 0 ? (fobTotal / fobTotalGeral) : 0;
                let desp_desenbaraco_parte_1 = 0

                let taxa_siscomex = $(`#taxa_siscomex-${i}`).val() ? MoneyUtils.parseMoney($(`#taxa_siscomex-${i}`).val()) : 0
                let multa = $(`#multa-${i}`).val() ? MoneyUtils.parseMoney($(`#multa-${i}`).val()) : 0

                const vlrAduaneiroBrl = MoneyUtils.parseMoney($(`#valor_aduaneiro_brl-${i}`).val()) || 0;
                const txDefLiPercent = $(`#tx_def_li-${i}`).val() ? MoneyUtils.parsePercentage($(`#tx_def_li-${i}`).val()) : 0;
                let taxa_def = vlrAduaneiroBrl * txDefLiPercent;

                let capatazia = $('#capatazia-' + i).val() ? MoneyUtils.parseMoney($('#capatazia-' + i).val()) : 0
                let afrmm = $('#afrmm-' + i).val() ? MoneyUtils.parseMoney($('#afrmm-' + i).val()) : 0
                let armazenagem_sts = $('#armazenagem_sts-' + i).val() ? MoneyUtils.parseMoney($('#armazenagem_sts-' + i).val()) : 0
                let frete_dta_sts_ana = $('#frete_dta_sts_ana-' + i).val() ? MoneyUtils.parseMoney($('#frete_dta_sts_ana-' + i).val()) : 0
                let honorarios_nix = $('#honorarios_nix-' + i).val() ? MoneyUtils.parseMoney($('#honorarios_nix-' + i).val()) : 0

                const nacionalizacao = getNacionalizacaoAtual();
                
                // Declarar despesa_desembaraco antes dos blocos condicionais
                let despesa_desembaraco = 0;
                
                if (nacionalizacao !== 'santos') {
                    let outras_taxas_agente = $('#outras_taxas_agente-' + i).val() ? MoneyUtils.parseMoney($('#outras_taxas_agente-' + i).val()) : 0
                    let liberacao_bl = $('#liberacao_bl-' + i).val() ? MoneyUtils.parseMoney($('#liberacao_bl-' + i).val()) : 0
                    let desconsolidacao = $('#desconsolidacao-' + i).val() ? MoneyUtils.parseMoney($('#desconsolidacao-' + i).val()) : 0
                    let isps_code = $('#isps_code-' + i).val() ? MoneyUtils.parseMoney($('#isps_code-' + i).val()) : 0
                    let handling = $('#handling-' + i).val() ? MoneyUtils.parseMoney($('#handling-' + i).val()) : 0
                    let sda = $('#sda-' + i).val() ? MoneyUtils.parseMoney($('#sda-' + i).val()) : 0
                    let rep_sts = $('#rep_sts-' + i).val() ? MoneyUtils.parseMoney($('#rep_sts-' + i).val()) : 0
                    let desp_anapolis = $('#desp_anapolis-' + i).val() ? MoneyUtils.parseMoney($('#desp_anapolis-' + i).val()) : 0
                    let rep_anapolis = $('#rep_anapolis-' + i).val() ? MoneyUtils.parseMoney($('#rep_anapolis-' + i).val()) : 0
                    let correios = $('#correios-' + i).val() ? MoneyUtils.parseMoney($('#correios-' + i).val()) : 0
                    let li_dta_honor_nix = $('#li_dta_honor_nix-' + i).val() ? MoneyUtils.parseMoney($('#li_dta_honor_nix-' + i).val()) : 0

                    desp_desenbaraco_parte_1 = multa + taxa_def + taxa_siscomex + outras_taxas_agente + liberacao_bl + 
                        desconsolidacao + isps_code + handling + capatazia + afrmm + armazenagem_sts + 
                        frete_dta_sts_ana + sda + rep_sts + desp_anapolis + rep_anapolis + correios + 
                        li_dta_honor_nix + honorarios_nix;

                    let desp_desenbaraco_parte_2 = taxa_siscomex + capatazia + afrmm + honorarios_nix;
                    despesa_desembaraco = desp_desenbaraco_parte_1 - desp_desenbaraco_parte_2;
                } else {
                    for (let campo of campos) {
                        const valorCampo = MoneyUtils.parseMoney($(`#${campo}`).val()) || 0;
                        const valorDistribuido = window.valoresBrutosCamposExternos[campo]?.[i] ?? (valorCampo * fatorVlrFob_AX);
                        desp_desenbaraco_parte_1 += valorDistribuido;
                    }

                    desp_desenbaraco_parte_1 += multa + taxa_def + taxa_siscomex;

                    let desp_desenbaraco_parte_2 = multa + taxa_def + taxa_siscomex + capatazia + afrmm + honorarios_nix;
                    despesa_desembaraco = desp_desenbaraco_parte_1 - desp_desenbaraco_parte_2;
                }
                const vlrIcmsReduzido = MoneyUtils.parseMoney($(`#valor_icms_reduzido-${i}`).val())
                let qquantidade = parseInt($(`#quantidade-${i}`).val()) || 0
                const vlrTotalNfComIcms = MoneyUtils.parseMoney($(`#valor_total_nf_com_icms_st-${i}`).val())
                let diferenca_cambial_frete = MoneyUtils.parseMoney($(`#diferenca_cambial_frete-${i}`).val());
                diferenca_cambial_frete = validarDiferencaCambialFrete(diferenca_cambial_frete);
                const diferenca_cambial_fob = MoneyUtils.parseMoney($(`#diferenca_cambial_fob-${i}`).val());
                
                // Adicionar campos opcionais se checkbox marcado
                const opcional1Compoe = $('#opcional_1_compoe_despesas').is(':checked');
                const opcional2Compoe = $('#opcional_2_compoe_despesas').is(':checked');
                const opcional1Valor = $(`#opcional_1_valor-${i}`).val() ? MoneyUtils.parseMoney($(`#opcional_1_valor-${i}`).val()) : 0;
                const opcional2Valor = $(`#opcional_2_valor-${i}`).val() ? MoneyUtils.parseMoney($(`#opcional_2_valor-${i}`).val()) : 0;
                
                let despesasAdicionais = 0;
                if (opcional1Compoe) {
                    despesasAdicionais += opcional1Valor;
                }
                if (opcional2Compoe) {
                    despesasAdicionais += opcional2Valor;
                }
                
                const custo_unitario_final = getNacionalizacaoAtual() === 'santos' 
                    ? (vlrTotalNfComIcms + despesa_desembaraco + diferenca_cambial_fob + diferenca_cambial_frete + despesasAdicionais) / qquantidade
                    : ((vlrTotalNfComIcms + despesa_desembaraco + diferenca_cambial_fob + diferenca_cambial_frete + despesasAdicionais) - vlrIcmsReduzido) / qquantidade

                const custo_total_final = custo_unitario_final * qquantidade
                $(`#desp_desenbaraco-${i}`).val(MoneyUtils.formatMoney(despesa_desembaraco, 2))
                $(`#custo_unitario_final-${i}`).val(MoneyUtils.formatMoney(custo_unitario_final, 2))
                $(`#custo_total_final-${i}`).val(MoneyUtils.formatMoney(custo_total_final, 2))
            }


            atualizarTotalizadores();
            calcularValoresCPT();
            
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

            const valores = $('input[name^="produtos["][name$="[adicao]"]')
                .map(function() {
                    return $(this).val();
                })
                .get();


            const unicos = [...new Set(valores.filter(v => v !== ""))];
            const quantidade = unicos.length;


            const valorRegistroDI = 115.67;


            if (quantidade === 0) {
                return valorRegistroDI;
            }



            const faixas = [
                { limite: 2, valor: 38.56, inicio: 0 },      
                { limite: 3, valor: 30.85, inicio: 2 },      
                { limite: 5, valor: 23.14, inicio: 5 },      
                { limite: 10, valor: 15.42, inicio: 10 },   
                { limite: 30, valor: 7.71, inicio: 20 },     
                { limite: Infinity, valor: 3.86, inicio: 50 } 
            ];


            let total = valorRegistroDI;


            faixas.forEach(faixa => {



                let adicoesNaFaixa;
                if (faixa.limite === Infinity) {

                    adicoesNaFaixa = Math.max(quantidade - faixa.inicio, 0);
                } else {

                    adicoesNaFaixa = Math.min(
                        Math.max(quantidade - faixa.inicio, 0),
                        faixa.limite
                    );
                }
                

                if (adicoesNaFaixa > 0) {
                    total += adicoesNaFaixa * faixa.valor;
                }
            });

            return total; 
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


            let moedaFrete = $('#frete_internacional_moeda').val();
            let moedaSeguro = $('#seguro_internacional_moeda').val();
            let moedaAcrescimo = $('#acrescimo_frete_moeda').val();
            let moedaProcesso = 'USD'; 


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
        <td><input data-row="${newIndex}" type="number" class="form-control" name="produtos[${newIndex}][item]" id="item-${newIndex}" value=""></td>
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
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal7" name="produtos[${newIndex}][multa]" id="multa-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control percentage2" name="produtos[${newIndex}][tx_def_li]" id="tx_def_li-${newIndex}" value=""></td>
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
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][li_dta_honor_nix]" id="li_dta_honor_nix-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][honorarios_nix]" id="honorarios_nix-' + newIndex + '" value=""></td>';
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
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][li_dta_honor_nix]" id="li_dta_honor_nix-' + newIndex + '" value=""></td>';
                html += '<td><input type="text" data-row="' + newIndex + '" class=" form-control moneyReal" readonly name="produtos[' + newIndex + '][honorarios_nix]" id="honorarios_nix-' + newIndex + '" value=""></td>';
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
            
            // Chamar totalizador ao carregar a página
            setTimeout(function() {
                if ($('#productsBody tr:not(.separador-adicao)').length > 0) {
                    recalcularTodaTabela();
                } else {
                    atualizarTotalizadores();
                }
                calcularValoresCPT();
            }, 1000);

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
                const adicaoA = parseFloat(a.querySelector('input[name*="[adicao]"]').value) || 0;
                const adicaoB = parseFloat(b.querySelector('input[name*="[adicao]"]').value) || 0;

                if (adicaoA !== adicaoB) {
                    return adicaoA - adicaoB; 
                }

                const itemA = parseFloat(a.querySelector('input[name*="[item]"]').value) || 0;
                const itemB = parseFloat(b.querySelector('input[name*="[item]"]').value) || 0;
                return itemA - itemB; 
            });


            const grupos = {};
            linhas.forEach(linha => {
                const adicao = parseFloat(linha.querySelector('input[name*="[adicao]"]').value) || 0;
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
    </script>
@endsection
