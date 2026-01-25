@extends('layouts.app')
@section('title', isset($processo) ? '' : 'Cadastrar Processo')

@section('content')
    @php
        $nacionalizacaoAtual = $processo->nacionalizacao ?? 'outros';
    @endphp
    {{-- CSS extraído para arquivos separados --}}
    <link rel="stylesheet" href="{{ asset('css/processo-maritimo/modal-preview.css') }}">
    <link rel="stylesheet" href="{{ asset('css/processo-maritimo/tabela-produtos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/processo-maritimo/formulario-cabecalho.css') }}">
    <link rel="stylesheet" href="{{ asset('css/processo-maritimo/utilitarios.css') }}">

    {{-- Expor MoneyUtils globalmente ANTES do código do processo-multa (compatibilidade) --}}
    <script type="module">
        import { MoneyUtils } from '{{ asset('js/processo-maritimo/utils/MoneyUtils.js') }}';
        window.MoneyUtils = MoneyUtils;
    </script>

    {{-- Sistema JavaScript Modular - Nova Arquitetura --}}
    <script type="module">
        import { init, MoneyUtils } from '{{ asset('js/processo-maritimo/index.js') }}';

        // Inicializar o sistema com dados do processo
        const nacionalizacaoAtual = '{{ $processo->nacionalizacao ?? 'santa_catarina' }}';
        
        // Obter cotações se disponíveis
        let moedasIniciais = { USD: { venda: 1, compra: 1 } };
        try {
            const cotacaoFrete = document.querySelector('#cotacao_frete_internacional');
            if (cotacaoFrete && cotacaoFrete.value) {
                moedasIniciais.USD.venda = parseFloat(cotacaoFrete.value.replace(',', '.')) || 1;
            }
        } catch (e) {
            console.warn('Não foi possível obter cotação inicial:', e);
        }

        // Inicializar aplicação principal
        const app = init({
            nacionalizacao: nacionalizacaoAtual,
            moedas: moedasIniciais
        });

        // Expor para uso global
        window.ProcessoMaritimo = app;
        
        // Garantir que MoneyUtils está exposto globalmente (pode já estar do script anterior)
        if (!window.MoneyUtils) {
            window.MoneyUtils = MoneyUtils;
        }

        // Recalcular tabela após carregar página
        if (typeof $ !== 'undefined') {
            $(document).ready(function() {
                setTimeout(() => {
                    app.recalcularTabela();
                }, 1500);
            });
        }

        console.log('Sistema de Processos Marítimos inicializado com sucesso');
    </script>

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
        <input type="hidden" name="dolarHoje" id="dolarHoje" value="{{ json_encode($dolar) }}">
        <input type="hidden" id="processoAlterado" name="processoAlterado" value="0">
    @endif
    <form id="delete-form" method="POST" action="{{ route('documento.cliente.destroy', 'document_id') }}"
        style="display:none;">
        @method('DELETE')
        @csrf
    </form>
    {{-- Inicialização JavaScript - Toda lógica migrada para arquitetura modular --}}
    <script>
        // Variáveis globais necessárias para compatibilidade
        let reordenando = false;
        let products = [];
        let debugStore = {};
        let debugGlobals = {};
        
        // Inicializar products se disponível
        if (typeof $ !== 'undefined' && $('#productsClient').length > 0) {
            try {
                products = JSON.parse($('#productsClient').val() || '[]');
            } catch (e) {
                console.warn('Erro ao parsear productsClient:', e);
                products = [];
            }
        }

        // Expor products globalmente para compatibilidade
        window.products = products;

        {{-- Include de funções de debug e formatação --}}
        @include('processo.includes.scripts.processo-debug')

        {{-- Wrapper para compatibilidade - delega para ProcessoMaritimoApp --}}
        function atualizarTotalizadores() {
            if (window.ProcessoMaritimo && typeof window.ProcessoMaritimo.atualizarTotalizadores === 'function') {
                window.ProcessoMaritimo.atualizarTotalizadores();
            }
        }

        {{-- Funções removidas - migradas para arquitetura modular --}}
        {{-- Todas as funções JavaScript foram movidas para: --}}
        {{-- - services/ (CalculadoraDespesas, CalculadoraCPT, CalculadoraCIF, CalculadoraMatoGrosso, etc) --}}
        {{-- - components/ (TabelaProdutos, TotalizadorGlobal, VisibilidadeColunas, etc) --}}
        {{-- - handlers/EventHandlers.js (todos os event handlers) --}}
        {{-- - utils/ (Formatador, LimpezaCampos, DOMUtils) --}}
        
        {{-- Todas as funções JavaScript foram migradas para a arquitetura modular --}}
        {{-- Ver: public/js/processo-maritimo/ para os módulos --}}
        
        // Wrappers de compatibilidade - delegam para ProcessoMaritimoApp
        function recalcularTodaTabela() {
            if (window.ProcessoMaritimo && typeof window.ProcessoMaritimo.recalcularTabela === 'function') {
                window.ProcessoMaritimo.recalcularTabela();
            } else {
                console.warn('ProcessoMaritimo não inicializado. Aguarde...');
            }
        }

        function getNacionalizacaoAtual() {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.helperService) {
                return window.ProcessoMaritimo.helperService.getNacionalizacaoAtual();
            }
            if (typeof $ !== 'undefined' && $('#nacionalizacao').length > 0) {
                const valor = $('#nacionalizacao').val();
                return valor ? valor.toLowerCase() : 'outros';
            }
            return 'outros';
        }

        function getCotacaoesProcesso() {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.helperService) {
                return window.ProcessoMaritimo.helperService.getCotacaoesProcesso();
            }
            let cotacaoProcesso = {};
            if (typeof $ !== 'undefined') {
                if (!$('#cotacao_moeda_processo').val()) {
                    $('#cotacao_moeda_processo').val($('#dolarHoje').val());
                    cotacaoProcesso = JSON.parse($('#dolarHoje').val());
                } else {
                    cotacaoProcesso = JSON.parse($('#cotacao_moeda_processo').val());
                }
            }
            return cotacaoProcesso;
        }

        function calcularImpostos(rowId, base) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.processoCalculator) {
                // Delegar para ProcessoCalculator
                return window.ProcessoMaritimo.processoCalculator.calcularImpostos(rowId, base);
            }
            // Fallback básico
            return { ii: 0, ipi: 0, pis: 0, cofins: 0, icms: 0 };
        }

        function calcularDespesas(rowId, fatorVlrFob_AX, fatorSiscomex, taxaSiscomexUnit, vlrAduaneiroBrl = null) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.calculadoraDespesas) {
                const nacionalizacao = getNacionalizacaoAtual();
                return window.ProcessoMaritimo.calculadoraDespesas.calcularDespesas(
                    rowId, fatorVlrFob_AX, fatorSiscomex, taxaSiscomexUnit, vlrAduaneiroBrl, nacionalizacao,
                    (selector) => $(selector).val(),
                    (selector) => $(selector).is(':checked')
                );
            }
            return { total: 0, componentes: {}, tipoCalculo: 'outros' };
        }

        function calcularBcIcmsSemReducao(vlrAduaneiroBrl, impostos, despesas) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.processoCalculator) {
                // Delegar para ProcessoCalculator
                return window.ProcessoMaritimo.processoCalculator.calcularBcIcmsSemReducao(vlrAduaneiroBrl, impostos, despesas);
            }
            return 0;
        }

        function calcularBcIcmsReduzido(rowId, base, impostos, despesas) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.processoCalculator) {
                // Delegar para ProcessoCalculator
                return window.ProcessoMaritimo.processoCalculator.calcularBcIcmsReduzido(rowId, base, impostos, despesas);
            }
            return 0;
        }

        function calcularTotais(base, impostos, despesas, quantidade, vlrIcmsReduzido, rowId) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.processoCalculator) {
                // Delegar para ProcessoCalculator
                return window.ProcessoMaritimo.processoCalculator.calcularTotais(base, impostos, despesas, quantidade, vlrIcmsReduzido, rowId);
            }
            return {};
        }

        function calcularValoresCPT() {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.calcularValoresCPT) {
                window.ProcessoMaritimo.calcularValoresCPT();
            }
        }

        function calcularValoresCIF() {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.calcularValoresCIF) {
                window.ProcessoMaritimo.calcularValoresCIF();
            }
        }

        function atualizarMultaProdutosPorMulta() {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.atualizarMultaProdutosPorMulta) {
                window.ProcessoMaritimo.atualizarMultaProdutosPorMulta();
            }
        }

        function obterValoresBase(rowId) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.helperService) {
                return window.ProcessoMaritimo.helperService.obterValoresBase(rowId);
            }
            return { pesoTotal: 0, fobUnitario: 0, quantidade: 0 };
        }

        function calcularPesoTotal() {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.helperService) {
                return window.ProcessoMaritimo.helperService.calcularPesoTotal();
            }
            return 0;
        }

        function calcularFobTotalGeral() {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.helperService) {
                return window.ProcessoMaritimo.helperService.calcularFobTotalGeral();
            }
            return 0;
        }

        function recalcularFatorPeso(totalPeso, currentRowId) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.helperService) {
                return window.ProcessoMaritimo.helperService.recalcularFatorPeso(totalPeso, currentRowId);
            }
            return 0;
        }

        function obterMultaPorAdicaoItemProduto(rowId) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.helperService) {
                return window.ProcessoMaritimo.helperService.obterMultaPorAdicaoItemProduto(rowId, (selector) => $(selector).val());
            }
            return 0;
        }

        function obterMultaComplementarPorAdicaoItemProduto(rowId) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.helperService) {
                return window.ProcessoMaritimo.helperService.obterMultaComplementarPorAdicaoItemProduto(rowId, (selector) => $(selector).val());
            }
            return 0;
        }

        function obterDiferencaImpostosPorAdicaoItemProduto(rowId) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.helperService) {
                return window.ProcessoMaritimo.helperService.obterDiferencaImpostosPorAdicaoItemProduto(rowId, (selector) => $(selector).val());
            }
            return 0;
        }

        function getCamposExternos() {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.helperService) {
                return window.ProcessoMaritimo.helperService.getCamposExternos();
            }
            return [];
        }

        function getCamposDiferencaCambial() {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.helperService) {
                return window.ProcessoMaritimo.helperService.getCamposDiferencaCambial();
            }
            return ['diferenca_cambial_fob', 'diferenca_cambial_frete'];
        }

        function validarDiferencaCambialFrete(valor) {
            if (window.Validador) {
                return window.Validador.validarDiferencaCambialFrete(valor);
            }
            return valor >= 0 ? valor : 0;
        }

        function atualizarCamposCambial() {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.atualizadorCambial) {
                window.ProcessoMaritimo.atualizadorCambial.atualizarCamposCambial(
                    (selector) => $(selector).val(),
                    (selector, value) => $(selector).val(value),
                    (selector) => $(selector)
                );
            }
        }

        function atualizarVisibilidadeNacionalizacao(options = {}) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.visibilidadeColunas) {
                window.ProcessoMaritimo.visibilidadeColunas.atualizarVisibilidadeNacionalizacao(
                    options,
                    () => calcularValoresCPT(),
                    () => calcularValoresCIF(),
                    () => {
                        if (window.ProcessoMaritimo && window.ProcessoMaritimo.recalcularTabela) {
                            setTimeout(() => window.ProcessoMaritimo.recalcularTabela(), 300);
                        }
                    }
                );
            }
        }

        function atualizarVisibilidadeColunasMoeda() {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.visibilidadeColunas) {
                const cotacoes = getCotacaoesProcesso();
                window.ProcessoMaritimo.visibilidadeColunas.atualizarVisibilidadeColunasMoeda(cotacoes);
            }
        }

        function atualizarTitulosColunas(moedaFrete, moedaSeguro, moedaAcrescimo, moedaProcesso) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.visibilidadeColunas) {
                const cotacoes = getCotacaoesProcesso();
                window.ProcessoMaritimo.visibilidadeColunas.atualizarTitulosColunas(cotacoes);
            }
        }

        function atualizarPesoLiquidoTotal(pesoTotal) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.tabelaProdutos) {
                window.ProcessoMaritimo.tabelaProdutos.atualizarPesoLiquidoTotal(pesoTotal, (selector, value) => $(selector).val(value));
            }
        }

        function atualizarTotaisGlobais(fobTotalGeral, dolar) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.totalizadorGlobal) {
                window.ProcessoMaritimo.totalizadorGlobal.atualizarTotaisGlobais(fobTotalGeral, dolar);
            }
        }

        function calcularSeguro(fobTotal, fobGeral) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.helperService) {
                return window.ProcessoMaritimo.helperService.calcularSeguro(fobTotal, fobGeral);
            }
            return 0;
        }

        function calcularAcrescimoFrete(fobTotal, fobGeral, dolar) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.helperService) {
                return window.ProcessoMaritimo.helperService.calcularAcrescimoFrete(fobTotal, fobGeral, dolar);
            }
            return 0;
        }

        function calcularTaxaSiscomex() {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.helperService) {
                return window.ProcessoMaritimo.helperService.calcularTaxaSiscomex();
            }
            return 0;
        }

        function obterValorProcessoUSD(valorSelector, moedaSelector, cotacaoSelector) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.helperService) {
                return window.ProcessoMaritimo.helperService.obterValorProcessoUSD(valorSelector, moedaSelector, cotacaoSelector);
            }
            return 0;
        }

        function converterMoedaProcessoParaUSD(valor, moedaProcesso) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.calculadoraMoedas) {
                const cotacoes = getCotacaoesProcesso();
                return window.ProcessoMaritimo.calculadoraMoedas.converterParaUSD(valor, moedaProcesso, cotacoes);
            }
            return valor;
        }

        function converterUSDParaMoedaProcesso(valor, moedaProcesso) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.calculadoraMoedas) {
                const cotacoes = getCotacaoesProcesso();
                return window.ProcessoMaritimo.calculadoraMoedas.converterDeUSD(valor, moedaProcesso, cotacoes);
            }
            return valor;
        }

        function convertToUSDAndBRL(inputId) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.calculadoraMoedas) {
                const cotacoes = getCotacaoesProcesso();
                window.ProcessoMaritimo.calculadoraMoedas.convertToUSDAndBRL(
                    inputId,
                    (selector) => $(selector).val(),
                    (selector, value) => $(selector).val(value),
                    cotacoes
                );
            }
        }

        function atualizarCamposFOB(rowId, valores) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.tabelaProdutos) {
                window.ProcessoMaritimo.tabelaProdutos.atualizarCamposFOB(
                    rowId, valores,
                    (selector) => $(selector).val(),
                    (selector, value) => $(selector).val(value),
                    (selector) => {
                        const el = $(selector);
                        return el.length > 0 && el.is(':focus');
                    }
                );
            }
        }

        function limparNumero(valor) {
            if (window.Formatador) {
                return window.Formatador.limparNumero(valor);
            }
            if (valor === null || valor === undefined) {
                return '';
            }
            return valor.toString().replace(/[^0-9]/g, '');
        }

        function limparCamposEspecificos(campos) {
            if (window.LimpezaCampos) {
                window.LimpezaCampos.limparCamposEspecificos(campos, (selector, value) => $(selector).val(value));
            }
        }

        function toggleColunas(selector, mostrar) {
            if (window.DOMUtils) {
                window.DOMUtils.toggleColunas(selector, mostrar);
            } else if (typeof $ !== 'undefined') {
                $(selector).each(function() {
                    $(this).toggle(mostrar);
                });
            }
        }

        function reordenarLinhas() {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.organizadorTabela) {
                window.ProcessoMaritimo.organizadorTabela.reordenarLinhas((selector) => {
                    $(selector).trigger('change');
                });
            } else if (typeof $ !== 'undefined') {
                if (reordenando) return;
                reordenando = true;
                // Implementação básica de fallback
                $('#productsBody input:not([name*="[adicao]"])').trigger('change');
                $('#productsBody select').trigger('change');
                reordenando = false;
            }
        }

        function adicionarSeparadoresAdicao() {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.organizadorTabela) {
                window.ProcessoMaritimo.organizadorTabela.adicionarSeparadoresAdicao();
            }
        }

        function calcularValorAduaneiro(fob, frete, acrescimo, seguro, thc, dolar, vlrCrfTotal = 0, serviceCharges = 0) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.processoCalculator) {
                // Delegar para ProcessoCalculator
                return window.ProcessoMaritimo.processoCalculator.calcularValorAduaneiro(fob, frete, acrescimo, seguro, thc, dolar, vlrCrfTotal, serviceCharges);
            }
            return 0;
        }

        function calcularColunasExportadorTributosDespesas(rowId) {
            if (window.ProcessoMaritimo && window.ProcessoMaritimo.calculadoraMatoGrosso) {
                const valoresBrutos = window.valoresBrutosPorLinha && window.valoresBrutosPorLinha[rowId] ? window.valoresBrutosPorLinha[rowId] : {};
                window.ProcessoMaritimo.calculadoraMatoGrosso.calcularColunasExportadorTributosDespesas(
                    rowId, valoresBrutos,
                    (selector) => $(selector).val(),
                    (selector, value) => $(selector).val(value)
                );
            }
        }

        // Inicializar sistema após DOM estar pronto
        if (typeof $ !== 'undefined') {
            $(document).ready(function() {
                // Aguardar inicialização do ProcessoMaritimo
                const aguardarInicializacao = setInterval(() => {
                    if (window.ProcessoMaritimo) {
                        clearInterval(aguardarInicializacao);
                        
                        // Reordenar linhas inicialmente
                        if (window.ProcessoMaritimo.organizadorTabela) {
                            window.ProcessoMaritimo.organizadorTabela.adicionarSeparadoresAdicao();
                        }
                        
                        // Configurar handlers de reordenação
                        $(document).on('change', 'input[name*="[adicao]"]', reordenarLinhas);
                        $(document).on('click', '.btn-reordenar', reordenarLinhas);
                        
                        // Recalcular tabela após carregar
                        setTimeout(() => {
                            if (window.ProcessoMaritimo.recalcularTabela) {
                                window.ProcessoMaritimo.recalcularTabela();
                            }
                        }, 1500);
                    }
                }, 100);
                
                // Timeout de segurança
                setTimeout(() => {
                    clearInterval(aguardarInicializacao);
                }, 10000);
            });
        }

        {{-- Código JavaScript antigo removido - migrado para arquitetura modular --}}
        {{-- Funções como atualizarTotalizadores, getCotacaoesProcesso, atualizarVisibilidadeColunasMoeda, --}}
        {{-- convertToUSDAndBRL, MoneyUtils, etc. foram todas migradas para os módulos em public/js/processo-maritimo/ --}}

        {{-- Inicialização e handlers finais --}}
        if (typeof $ !== 'undefined') {
            $(document).ready(function() {
                // Aguardar inicialização do ProcessoMaritimo
                const aguardarInicializacao = setInterval(() => {
                    if (window.ProcessoMaritimo) {
                        clearInterval(aguardarInicializacao);
                        
                        // Reordenar linhas inicialmente
                        if (window.ProcessoMaritimo.organizadorTabela) {
                            window.ProcessoMaritimo.organizadorTabela.adicionarSeparadoresAdicao();
                        }
                        
                        // Configurar handlers de reordenação
                        $(document).on('change', 'input[name*="[adicao]"]', reordenarLinhas);
                        $(document).on('click', '.btn-reordenar', reordenarLinhas);
                        
                        // Inicializar Select2 em todos os elementos
                        // IMPORTANTE: Inicializar apenas uma vez, após DOM estar pronto
                        setTimeout(() => {
                            if (window.ProcessoMaritimo.eventHandlers && typeof window.ProcessoMaritimo.eventHandlers.inicializarSelect2 === 'function') {
                                window.ProcessoMaritimo.eventHandlers.inicializarSelect2();
                            } else if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                                // Fallback: inicializar Select2 diretamente
                                $('.select2').each(function() {
                                    const $select = $(this);
                                    if (!$select.data('select2')) {
                                        // IMPORTANTE: Capturar valor ANTES de qualquer manipulação
                                        let valorSelecionado = null;
                                        
                                        // Buscar todas as opções com selected (ignorando disabled)
                                        const allOptions = $select.find('option');
                                        let selectedOption = null;
                                        
                                        allOptions.each(function() {
                                            const $option = $(this);
                                            if ($option.attr('selected') && !$option.prop('disabled')) {
                                                const val = $option.val();
                                                // Ignorar valores vazios ou "Selecione uma opção"
                                                if (val && val !== '' && val !== null && val !== undefined) {
                                                    selectedOption = $option;
                                                    return false; // break
                                                }
                                            }
                                        });
                                        
                                        if (selectedOption && selectedOption.length > 0) {
                                            valorSelecionado = selectedOption.val();
                                        }
                                        
                                        // Se não encontrou, verificar o valor atual do select
                                        if (!valorSelecionado || valorSelecionado === '' || valorSelecionado === null) {
                                            const currentVal = $select.val();
                                            // Ignorar se for a primeira opção disabled
                                            if (currentVal && currentVal !== '' && currentVal !== null) {
                                                valorSelecionado = currentVal;
                                            }
                                        }

                                        const config = {
                                            width: '100%',
                                            language: {
                                                noResults: function() {
                                                    return "Nenhum resultado encontrado";
                                                },
                                                searching: function() {
                                                    return "Buscando...";
                                                }
                                            }
                                        };
                                        const modalParent = $select.closest('.modal');
                                        const tabParent = $select.closest('.tab-pane');
                                        if (modalParent.length) {
                                            config.dropdownParent = modalParent;
                                        } else if (tabParent.length) {
                                            config.dropdownParent = tabParent;
                                        }

                                        // Inicializar Select2
                                        $select.select2(config);

                                        // Restaurar valor IMEDIATAMENTE após inicialização
                                        if (valorSelecionado && valorSelecionado !== '' && valorSelecionado !== null && valorSelecionado !== undefined) {
                                            requestAnimationFrame(() => {
                                                $select.val(valorSelecionado).trigger('change.select2');
                                            });
                                        }
                                    }
                                });
                            }
                        }, 200);
                        
                        // Recalcular tabela após carregar
                        setTimeout(() => {
                            if (window.ProcessoMaritimo.recalcularTabela) {
                                window.ProcessoMaritimo.recalcularTabela();
                            }
                        }, 1500);
                    }
                }, 100);
                
                // Timeout de segurança
                setTimeout(() => {
                    clearInterval(aguardarInicializacao);
                }, 10000);
            });
        }

        {{-- Função debouncedRecalcular para compatibilidade com código do processo-multa --}}
        let debouncedRecalcularTimeout = null;
        function debouncedRecalcular() {
            if (debouncedRecalcularTimeout) {
                clearTimeout(debouncedRecalcularTimeout);
            }
            debouncedRecalcularTimeout = setTimeout(() => {
                if (window.ProcessoMaritimo && typeof window.ProcessoMaritimo.recalcularTabela === 'function') {
                    window.ProcessoMaritimo.recalcularTabela();
                } else if (typeof recalcularTodaTabela === 'function') {
                    recalcularTodaTabela();
                }
            }, 300);
        }

        {{-- Include de lógica da tabela Multa --}}
        @include('processo.includes.scripts.processo-multa')
    </script>
@endsection
