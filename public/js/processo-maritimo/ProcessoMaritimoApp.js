import { getStore } from './core/Store.js';
import { getEventBus } from './core/EventBus.js';
import { getFactory } from './strategies/NacionalizacaoFactory.js';
import { ProcessoCalculator } from './services/ProcessoCalculator.js';
import { HelperService } from './services/HelperService.js';
import { CalculadoraDespesas } from './services/CalculadoraDespesas.js';
import { CalculadoraCPT } from './services/CalculadoraCPT.js';
import { CalculadoraCIF } from './services/CalculadoraCIF.js';
import { CalculadoraMatoGrosso } from './services/CalculadoraMatoGrosso.js';
import { CalculadoraMoedas } from './services/CalculadoraMoedas.js';
import { TabelaProdutos } from './components/TabelaProdutos.js';
import { TabelaVirtualizada } from './components/TabelaVirtualizada.js';
import { TotalizadorGlobal } from './components/TotalizadorGlobal.js';
import { VisibilidadeColunas } from './components/VisibilidadeColunas.js';
import { AtualizadorCambial } from './components/AtualizadorCambial.js';
import { AtualizadorMulta } from './components/AtualizadorMulta.js';
import { OrganizadorTabela } from './components/OrganizadorTabela.js';
import { EventHandlers } from './handlers/EventHandlers.js';
import { MoneyUtils } from './utils/MoneyUtils.js';
import { Validador } from './utils/Validador.js';

/**
 * Classe principal do sistema de processos marítimos
 * Orquestra todos os componentes e gerencia o ciclo de vida completo
 */
export class ProcessoMaritimoApp {
    constructor(config = {}) {
        // Inicializar componentes core
        this.store = getStore();
        this.eventBus = getEventBus();
        
        // Inicializar serviços
        this.helperService = new HelperService(this.store);
        this.factory = getFactory(this.store, this.eventBus);
        this.processoCalculator = new ProcessoCalculator(
            this.store,
            this.eventBus,
            () => this.factory
        );
        this.calculadoraDespesas = new CalculadoraDespesas(this.helperService);
        this.calculadoraCPT = new CalculadoraCPT(this.helperService);
        this.calculadoraCIF = new CalculadoraCIF(this.helperService);
        this.calculadoraMatoGrosso = new CalculadoraMatoGrosso(this.helperService);
        this.calculadoraMoedas = new CalculadoraMoedas();
        
        // Inicializar componentes UI
        this.tabelaProdutos = new TabelaProdutos(this.store);
        this.tabelaVirtualizada = new TabelaVirtualizada(
            this.store,
            '#productsBody',
            config.virtualizacao || {
                threshold: 100,
                itemHeight: 50,
                buffer: 5
            }
        );
        this.totalizadorGlobal = new TotalizadorGlobal();
        this.visibilidadeColunas = new VisibilidadeColunas(this.helperService);
        this.atualizadorCambial = new AtualizadorCambial(this.helperService);
        this.atualizadorMulta = new AtualizadorMulta(this.helperService);
        this.organizadorTabela = new OrganizadorTabela();
        
        // Inicializar handlers
        this.eventHandlers = new EventHandlers(this);
        this.eventHandlers.setup();
        
        // Configurar estado inicial
        this.inicializarEstado(config);
        
        // Configurar listeners de eventos do DOM
        this.setupDOMListeners();
    }

    /**
     * Inicializa o estado do store com dados iniciais
     * @param {Object} config - Configuração inicial
     */
    inicializarEstado(config) {
        const estadoInicial = {
            nacionalizacao: config.nacionalizacao || 'santa_catarina',
            moedas: config.moedas || { USD: { venda: 1, compra: 1 } },
            cabecalho: config.cabecalho || {},
            produtos: config.produtos || [],
            valoresBrutosPorLinha: {},
            valoresBrutosCamposExternos: {}
        };

        this.store.setState(estadoInicial);
    }

    /**
     * Configura listeners de eventos do DOM
     */
    setupDOMListeners() {
        if (typeof $ === 'undefined') {
            return;
        }

        // Nacionalização alterada
        $(document).on('change', '#nacionalizacao', (e) => {
            const novaNacionalizacao = $(e.target).val() || 'santa_catarina';
            this.eventBus.emit('nacionalizacao:alterada', {
                nacionalizacao: novaNacionalizacao
            });
        });

        // Campos do cabeçalho alterados
        const camposCabecalho = [
            'frete_internacional',
            'seguro_internacional',
            'acrescimo_frete',
            'service_charges',
            'thc_capatazia',
            'diferenca_cambial_fob',
            'diferenca_cambial_frete',
            'taxa_siscomex'
        ];

        camposCabecalho.forEach(campo => {
            $(document).on('change', `#${campo}`, (e) => {
                const valor = MoneyUtils.parseMoney($(e.target).val()) || 0;
                this.eventBus.emit('cabecalho:alterado', { campo, valor });
            });
        });

        // Cotações alteradas
        $(document).on('change', '#cotacao_frete_internacional, #cotacao_seguro_internacional', (e) => {
            const cotacao = MoneyUtils.parseMoney($(e.target).val()) || 1;
            this.eventBus.emit('moeda:alterada', { moeda: 'USD', valor: cotacao });
        });

        // Campos de produto alterados
        $(document).on('change', '#productsBody input', (e) => {
            const id = $(e.target).attr('id');
            if (!id) return;

            const match = id.match(/^(.+)-(\d+)$/);
            if (!match) return;

            const [, campo, rowId] = match;
            const valor = MoneyUtils.parseMoney($(e.target).val()) || 0;

            this.eventBus.emit('produto:alterado', { rowId, campo, valor });
        });
    }

    /**
     * Prepara produtos da tabela para cálculo
     * @returns {Array<Object>} - Array de produtos
     */
    prepararProdutos() {
        if (typeof $ === 'undefined') {
            return [];
        }

        const produtos = [];
        const rows = $('#productsBody .linhas-input');

        rows.each((index, element) => {
            const rowId = element.id ? element.id.toString().replace('row-', '') : null;
            if (!rowId || rowId.includes('multa')) {
                return;
            }

            const valores = this.helperService.obterValoresBase(rowId);
            produtos.push({
                rowId,
                ...valores
            });
        });

        return produtos;
    }

    /**
     * Prepara valores do cabeçalho
     * @returns {Object} - Valores do cabeçalho
     */
    prepararCabecalho() {
        if (typeof $ === 'undefined') {
            return {};
        }

        const cabecalho = {};
        const campos = [
            'frete_internacional',
            'seguro_internacional',
            'acrescimo_frete',
            'service_charges',
            'thc_capatazia',
            'diferenca_cambial_fob',
            'diferenca_cambial_frete',
            'taxa_siscomex'
        ];

        campos.forEach(campo => {
            const valor = MoneyUtils.parseMoney($(`#${campo}`).val()) || 0;
            if (valor > 0) {
                cabecalho[campo] = valor;
            }
        });

        return cabecalho;
    }

    /**
     * Prepara cotações de moedas
     * @returns {Object} - Cotações
     */
    prepararMoedas() {
        const cotacoes = this.helperService.getCotacaoesProcesso();
        const dolar = cotacoes['USD']?.venda || 1;

        return {
            USD: { venda: dolar, compra: dolar },
            ...cotacoes
        };
    }

    /**
     * Função para obter valores de input
     * @param {string} selector - Seletor jQuery
     * @returns {string} - Valor do input
     */
    getInputValue(selector) {
        if (typeof $ !== 'undefined') {
            return $(selector).val() || '';
        }
        const element = document.querySelector(selector);
        return element ? element.value : '';
    }

    /**
     * Recalcula toda a tabela
     */
    recalcularTabela() {
        try {
            const produtos = this.prepararProdutos();
            console.log(produtos)
            console.log(this.prepararCabecalho())
            console.log(this.prepararMoedas())
            const cabecalho = this.prepararCabecalho();
            const moedas = this.prepararMoedas();

            // Atualizar Store
            this.store.setState({
                moedas,
                cabecalho
            });

            // Calcular
            const resultado = this.processoCalculator.recalcularTodaTabela(
                produtos,
                cabecalho,
                moedas,
                (selector) => this.getInputValue(selector)
            );

            if (resultado && !resultado.erro) {
                // Atualizar DOM
                this.atualizarDOM(resultado);

                // Atualizar totalizadores
                this.atualizarTotalizadores();

                // Calcular valores CPT e CIF
                this.calcularValoresCPT();
                this.calcularValoresCIF();

                // Atualizar multa produtos (Santa Catarina)
                this.atualizarMultaProdutosPorMulta();

                // Emitir evento de cálculo concluído
                this.eventBus.emit('tabela:recalculada', resultado);
            } else if (resultado && resultado.erro) {
                console.error('Erros no cálculo:', resultado.erro);
                this.eventBus.emit('tabela:erro', resultado.erro);
            }
        } catch (error) {
            console.error('Erro ao recalcular tabela:', error);
            this.eventBus.emit('tabela:erro', [error.message]);
        }
    }

    /**
     * Atualiza totalizadores da tabela
     */
    atualizarTotalizadores() {
        if (this.tabelaProdutos && typeof $ !== 'undefined') {
            this.tabelaProdutos.atualizarTotalizadores({
                helperService: this.helperService,
                getInputValue: (selector) => $(selector).val(),
                setInputValue: (selector, value) => $(selector).val(value),
                getSelectorValue: (selector) => $(selector),
                validarDiferencaCambialFrete: (valor) => Validador.validarDiferencaCambialFrete(valor)
            });
        }
    }

    /**
     * Calcula valores CPT
     */
    calcularValoresCPT() {
        if (this.calculadoraCPT && typeof $ !== 'undefined') {
            const tipoProcesso = $('body').data('tipo-processo') || 'maritimo';
            this.calculadoraCPT.calcularCPT(
                (selector) => $(selector).val(),
                (selector, value) => $(selector).val(value),
                (selector) => $(selector),
                tipoProcesso
            );
        }
    }

    /**
     * Calcula valores CIF
     */
    calcularValoresCIF() {
        if (this.calculadoraCIF && typeof $ !== 'undefined') {
            const tipoProcesso = $('body').data('tipo-processo') || 'maritimo';
            this.calculadoraCIF.calcularCIF(
                (selector) => $(selector).val(),
                (selector, value) => $(selector).val(value),
                (selector) => $(selector),
                tipoProcesso
            );
        }
    }

    /**
     * Atualiza multa produtos (Santa Catarina)
     */
    atualizarMultaProdutosPorMulta() {
        if (this.atualizadorMulta && typeof $ !== 'undefined') {
            this.atualizadorMulta.atualizarMultaProdutosPorMulta(
                (selector) => $(selector).val(),
                (selector, value) => $(selector).val(value),
                (selector, prop, value) => $(selector).prop(prop, value),
                (selector) => $(selector)
            );
        }
    }

    /**
     * Atualiza o DOM com os resultados do cálculo
     * @param {Object} resultado - Resultado do cálculo
     */
    atualizarDOM(resultado) {
        const { produtos, totais } = resultado;

        // Atualizar cada produto
        produtos.forEach(produto => {
            const updates = {
                fob_total_usd: produto.fobTotal,
                fob_total_brl: produto.fobTotal * (resultado.valoresBase?.fobTotalGeral ? 
                    (this.store.getState().moedas.USD?.venda || 1) : 1),
                peso_liquido_unitario: produto.pesoLiqUnit,
                fator_valor_fob: produto.fatorVlrFob || 
                    (produto.fobTotal / (resultado.valoresBase?.fobTotalGeral || 1))
            };

            // Adicionar valores de nacionalização
            if (produto.valoresNacionalizacao) {
                Object.assign(updates, {
                    vlr_crf_total: produto.valoresNacionalizacao.vlrCrfTotal,
                    vlr_crf_unit: produto.valoresNacionalizacao.vlrCrfUnit,
                    valor_aduaneiro_usd: produto.valoresNacionalizacao.vlrAduaneiroUsd,
                    valor_aduaneiro_brl: produto.valoresNacionalizacao.vlrAduaneiroBrl
                });
            }

            // Adicionar impostos
            if (produto.impostos?.valores) {
                const impostosUpdates = {
                    valor_ii: produto.impostos.valores.vlrII || 0,
                    valor_ipi: produto.impostos.valores.vlrIpi || 0,
                    valor_pis: produto.impostos.valores.vlrPis || 0,
                    valor_cofins: produto.impostos.valores.vlrCofins || 0
                };
                Object.assign(updates, impostosUpdates);
                // #region agent log
                fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'ProcessoMaritimoApp.js:374',message:'Impostos sendo adicionados ao update',data:{rowId:produto.rowId,impostosUpdates:impostosUpdates,impostosValores:produto.impostos.valores},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
                // #endregion
            } else {
                // #region agent log
                fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'ProcessoMaritimoApp.js:380',message:'Impostos NÃO encontrados no produto',data:{rowId:produto.rowId,hasImpostos:!!produto.impostos,hasValores:!!produto.impostos?.valores},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
                // #endregion
            }

            // Adicionar despesas
            if (produto.despesas) {
                Object.assign(updates, {
                    despesa_desembaraco: produto.despesas.despesaDesembaraco || produto.despesas.total
                });
            }

            // Adicionar custo final
            if (produto.custoFinal) {
                Object.assign(updates, {
                    custo_unitario_final: produto.custoFinal.custoUnitarioFinal,
                    custo_total_final: produto.custoFinal.custoTotalFinal
                });
            }

            this.tabelaProdutos.atualizarLinha(produto.rowId, updates);
        });

        // Atualizar totalizadores
        if (totais) {
            this.tabelaProdutos.atualizarTotais(totais);
        }
    }

    /**
     * Inicializa a virtualização
     */
    initVirtualizacao() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => this.tabelaVirtualizada.init(), 500);
            });
        } else {
            setTimeout(() => this.tabelaVirtualizada.init(), 500);
        }
    }

    /**
     * Destrói a aplicação e limpa recursos
     */
    destroy() {
        // Limpar event listeners
        this.eventBus.off('*');
        
        // Limpar cache
        this.tabelaProdutos.limparCache();
        
        // Destruir virtualização
        if (this.tabelaVirtualizada.isActive()) {
            this.tabelaVirtualizada.destroy();
        }
    }
}
