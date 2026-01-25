import { getCalculoCache } from '../core/CalculoCache.js';
import { CalculadoraImpostos } from './CalculadoraImpostos.js';
import { CalculadoraFrete } from './CalculadoraFrete.js';
import { CalculadoraMoedas } from './CalculadoraMoedas.js';
import { DistribuidorValores } from './DistribuidorValores.js';
import { Validador } from '../utils/Validador.js';
import { MoneyUtils } from '../utils/MoneyUtils.js';

/**
 * Orquestrador principal de cálculos do processo marítimo
 * Coordena todos os serviços de cálculo em etapas
 */
export class ProcessoCalculator {
    constructor(store, eventBus, getFactory) {
        this.store = store;
        this.eventBus = eventBus;
        this.getFactory = getFactory;
        this.cache = getCalculoCache();
        this.calculadoraImpostos = new CalculadoraImpostos();
        this.calculadoraFrete = new CalculadoraFrete();
        this.calculadoraMoedas = new CalculadoraMoedas();
        this.distribuidorValores = new DistribuidorValores();
        this.isCalculando = false;
    }

    /**
     * Valida dados antes de calcular
     * @param {Array<Object>} produtos - Array de produtos
     * @returns {Object} - Resultado da validação
     */
    validarDados(produtos) {
        const erros = [];

        produtos.forEach((produto, index) => {
            if (!Validador.validarQuantidade(produto.quantidade)) {
                erros.push(`Produto ${index + 1}: quantidade inválida`);
            }
            if (!Validador.validarPeso(produto.pesoTotal)) {
                erros.push(`Produto ${index + 1}: peso inválido`);
            }
            if (produto.fobUnitario < 0) {
                erros.push(`Produto ${index + 1}: FOB unitário inválido`);
            }
        });

        return {
            valido: erros.length === 0,
            erros
        };
    }

    /**
     * Calcula valores base (FOB, pesos, quantidades)
     * @param {Array<Object>} produtos - Array de produtos
     * @returns {Object} - Valores base calculados
     */
    calcularValoresBase(produtos) {
        const estado = this.store.getState();
        const resultados = {
            fobTotalGeral: 0,
            pesoTotalGeral: 0,
            produtos: []
        };

        produtos.forEach(produto => {
            const quantidade = Validador.validarQuantidade(produto.quantidade);
            const pesoTotal = Validador.validarPeso(produto.pesoTotal);
            const fobUnitario = produto.fobUnitario || 0;
            const fobTotal = fobUnitario * quantidade;
            const pesoLiqUnit = quantidade > 0 ? pesoTotal / quantidade : 0;

            resultados.fobTotalGeral += fobTotal;
            resultados.pesoTotalGeral += pesoTotal;

            resultados.produtos.push({
                rowId: produto.rowId,
                quantidade,
                pesoTotal,
                pesoLiqUnit,
                fobUnitario,
                fobTotal
            });
        });
        return resultados;
    }

    /**
     * Distribui valores do cabeçalho proporcionalmente
     * @param {Object} cabecalho - Valores do cabeçalho
     * @param {Array<Object>} produtos - Array de produtos com valores base
     * @param {string} metodo - Método de distribuição ('fob' ou 'peso')
     * @returns {Object} - Valores distribuídos
     */
    distribuirValoresCabecalho(cabecalho, produtos, metodo = 'fob') {
        const estado = this.store.getState();
        const valoresBrutosCamposExternos = estado.valoresBrutosCamposExternos || {};
        const resultados = {};

        Object.entries(cabecalho).forEach(([campo, valorCampo]) => {
            if (valorCampo === 0 || !valorCampo) {
                return;
            }

            const linhas = produtos.map(p => ({
                rowId: p.rowId,
                fobTotal: p.fobTotal,
                pesoTotal: p.pesoTotal
            }));

            if (metodo === 'fob') {
                this.distribuidorValores.distribuirPorFatorFOB(
                    valorCampo,
                    linhas,
                    valoresBrutosCamposExternos,
                    campo
                );
            } else {
                this.distribuidorValores.distribuirPorPeso(
                    valorCampo,
                    linhas,
                    valoresBrutosCamposExternos,
                    campo
                );
            }
        });

        this.store.setState({ valoresBrutosCamposExternos });
        return valoresBrutosCamposExternos;
    }

    /**
     * Calcula valores por nacionalização usando Strategy
     * @param {Object} produto - Dados do produto
     * @param {Object} moedas - Cotações de moedas
     * @returns {Object} - Valores calculados
     */
    calcularPorNacionalizacao(produto, moedas) {
        const estado = this.store.getState();
        const estrategia = this.getFactory().get(estado.nacionalizacao);

        // Calcular CFR
        const vlrCrfTotal = estrategia.calcularCFR(produto, moedas);
        const vlrCrfUnit = produto.quantidade > 0 ? vlrCrfTotal / produto.quantidade : 0;

        // IMPORTANTE: Adicionar vlrCrfTotal e valores de campos externos ao produto antes de calcular valor aduaneiro
        const valoresBrutosCamposExternos = estado.valoresBrutosCamposExternos || {};
        const produtoComCFR = {
            ...produto,
            vlrCrfTotal: vlrCrfTotal,
            // Buscar valores distribuídos dos campos externos
            seguroIntUsd: valoresBrutosCamposExternos.seguro_internacional_usd?.[produto.rowId] || 0,
            acrescimoFreteUsd: valoresBrutosCamposExternos.acrescimo_frete_usd?.[produto.rowId] || 0,
            thcUsd: valoresBrutosCamposExternos.thc_usd?.[produto.rowId] || 0
        };
        
        // #region agent log
        fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'ProcessoCalculator.js:152',message:'Produto antes de calcular valor aduaneiro',data:{rowId:produto.rowId,vlrCrfTotal:vlrCrfTotal,seguroIntUsd:produtoComCFR.seguroIntUsd,acrescimoFreteUsd:produtoComCFR.acrescimoFreteUsd,thcUsd:produtoComCFR.thcUsd,valoresBrutosCamposExternosKeys:Object.keys(valoresBrutosCamposExternos)},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
        // #endregion

        // Calcular valor aduaneiro
        const vlrAduaneiroUsd = estrategia.calcularValorAduaneiro(produtoComCFR, moedas);
        const vlrAduaneiroBrl = vlrAduaneiroUsd * (moedas.USD?.venda || 1);
        
        // #region agent log
        fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'ProcessoCalculator.js:147',message:'Valor aduaneiro calculado',data:{rowId:produto.rowId,vlrCrfTotal:vlrCrfTotal,vlrAduaneiroUsd:vlrAduaneiroUsd,vlrAduaneiroBrl:vlrAduaneiroBrl,cotacaoUsd:moedas.USD?.venda},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
        // #endregion

        return {
            vlrCrfTotal,
            vlrCrfUnit,
            vlrAduaneiroUsd,
            vlrAduaneiroBrl
        };
    }

    /**
     * Calcula impostos para um produto
     * @param {Object} produto - Dados do produto
     * @param {Function} getInputValue - Função para obter valores de input
     * @returns {Object} - Impostos calculados
     */
    calcularImpostos(produto, getInputValue) {
        const aliquotas = this.calculadoraImpostos.calcularAliquotas(produto.rowId, getInputValue);
        const baseCalculo = produto.vlrAduaneiroBrl || 0;
        // #region agent log
        fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'ProcessoCalculator.js:165',message:'Calculando impostos',data:{rowId:produto.rowId,baseCalculo:baseCalculo,aliquotas:aliquotas,quantidade:produto.quantidade},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
        // #endregion
        const valores = this.calculadoraImpostos.calcularValores(
            baseCalculo,
            aliquotas,
            produto.quantidade || 1
        );
        // #region agent log
        fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'ProcessoCalculator.js:172',message:'Valores de impostos calculados',data:{rowId:produto.rowId,valores:valores},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
        // #endregion

        return {
            aliquotas,
            valores
        };
    }

    /**
     * Calcula despesas usando Strategy
     * @param {Object} produto - Dados do produto
     * @param {Object} cabecalho - Valores do cabeçalho
     * @returns {Object} - Despesas calculadas
     */
    calcularDespesas(produto, cabecalho) {
        const estado = this.store.getState();
        const estrategia = this.getFactory().get(estado.nacionalizacao);

        // Adicionar valores brutos de campos externos ao produto
        produto.valoresBrutosCamposExternos = estado.valoresBrutosCamposExternos || {};

        return estrategia.calcularDespesas(produto, cabecalho);
    }

    /**
     * Calcula custo final usando Strategy
     * @param {Object} produto - Dados do produto
     * @param {Object} totais - Totais calculados
     * @returns {Object} - Custo final
     */
    calcularCustoFinal(produto, totais) {
        const estado = this.store.getState();
        const estrategia = this.getFactory().get(estado.nacionalizacao);

        return estrategia.calcularCustoFinal(produto, totais);
    }

    /**
     * Consolida totais de todos os produtos
     * @param {Array<Object>} produtosCalculados - Array de produtos com cálculos
     * @returns {Object} - Totais consolidados
     */
    consolidarTotais(produtosCalculados) {
        const totais = {
            fobTotalGeral: 0,
            pesoTotalGeral: 0,
            vlrAduaneiroTotal: 0,
            impostosTotal: 0,
            despesasTotal: 0,
            custoTotalFinal: 0
        };

        produtosCalculados.forEach(produto => {
            totais.fobTotalGeral += produto.fobTotal || 0;
            totais.pesoTotalGeral += produto.pesoTotal || 0;
            totais.vlrAduaneiroTotal += produto.vlrAduaneiroBrl || 0;
            totais.impostosTotal += (produto.impostos?.valores?.vlrII || 0) +
                                   (produto.impostos?.valores?.vlrIPI || 0) +
                                   (produto.impostos?.valores?.vlrPIS || 0) +
                                   (produto.impostos?.valores?.vlrCOFINS || 0);
            totais.despesasTotal += produto.despesas?.total || 0;
            totais.custoTotalFinal += produto.custoFinal?.custoTotalFinal || 0;
        });

        return totais;
    }

    /**
     * Recalcula toda a tabela de produtos
     * @param {Array<Object>} produtos - Array de produtos
     * @param {Object} cabecalho - Valores do cabeçalho
     * @param {Object} moedas - Cotações de moedas
     * @param {Function} getInputValue - Função para obter valores de input
     * @returns {Object} - Resultado completo dos cálculos
     */
    recalcularTodaTabela(produtos, cabecalho, moedas, getInputValue) {
        if (this.isCalculando) {
            console.warn('Cálculo já em andamento, aguarde...');
            return null;
        }

        this.isCalculando = true;

        try {
            // 1. Validação
            const validacao = this.validarDados(produtos);
            if (!validacao.valido) {
                console.error('Erros de validação:', validacao.erros);
                return { erro: validacao.erros };
            }

            // 2. Calcular valores base
            const valoresBase = this.calcularValoresBase(produtos);

            // 3. Distribuir valores do cabeçalho
            this.distribuirValoresCabecalho(cabecalho, valoresBase.produtos);

            // 4. Calcular cada produto
            const produtosCalculados = valoresBase.produtos.map(produto => {
                // Calcular por nacionalização
                const valoresNacionalizacao = this.calcularPorNacionalizacao(
                    { ...produto, ...moedas },
                    moedas
                );

                // IMPORTANTE: Adicionar valor aduaneiro ao produto antes de calcular impostos
                const produtoComValorAduaneiro = {
                    ...produto,
                    vlrAduaneiroBrl: valoresNacionalizacao.vlrAduaneiroBrl
                };
                
                // #region agent log
                fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'ProcessoCalculator.js:290',message:'Produto com valor aduaneiro antes de calcular impostos',data:{rowId:produto.rowId,vlrAduaneiroBrl:produtoComValorAduaneiro.vlrAduaneiroBrl,valoresNacionalizacao:valoresNacionalizacao},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
                // #endregion

                // Calcular impostos usando o valor aduaneiro calculado
                const impostos = this.calcularImpostos(produtoComValorAduaneiro, getInputValue);

                // Calcular despesas
                const despesas = this.calcularDespesas(produto, cabecalho);

                // Calcular totais
                const totais = {
                    vlrTotalNfComIcms: valoresNacionalizacao.vlrAduaneiroBrl + 
                                      impostos.valores.vlrII + 
                                      impostos.valores.vlrIPI
                };

                // Calcular custo final
                const custoFinal = this.calcularCustoFinal(
                    {
                        ...produto,
                        ...valoresNacionalizacao,
                        despesaDesembaraco: despesas.despesaDesembaraco
                    },
                    totais
                );

                return {
                    ...produto,
                    ...valoresNacionalizacao,
                    impostos,
                    despesas,
                    custoFinal
                };
            });

            // 5. Consolidar totais
            const totais = this.consolidarTotais(produtosCalculados);

            return {
                produtos: produtosCalculados,
                totais,
                valoresBase
            };
        } finally {
            this.isCalculando = false;
        }
    }
}
