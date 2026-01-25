import { DOMUtils } from '../utils/DOMUtils.js';
import { MoneyUtils } from '../utils/MoneyUtils.js';

/**
 * Componente para gerenciar a tabela de produtos
 * Otimiza atualizações DOM usando batch updates e cache de seletores
 */
export class TabelaProdutos {
    constructor(store, containerSelector = '#productsBody') {
        this.store = store;
        this.containerSelector = containerSelector;
        this.selectorCache = new Map();
        this.updateQueue = [];
        this.isUpdating = false;
    }

    /**
     * Atualiza uma linha específica da tabela
     * @param {string|number} rowId - ID da linha
     * @param {Object} dados - Dados a serem atualizados (campo: valor)
     */
    atualizarLinha(rowId, dados) {
        // #region agent log
        fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'TabelaProdutos.js:22',message:'atualizarLinha ENTRY',data:{rowId:rowId,hasDados:!!dados,keysCount:dados?Object.keys(dados).length:0,valoresBrutosExists:!!window.valoresBrutosPorLinha},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
        // #endregion
        if (!rowId) {
            console.warn('TabelaProdutos: rowId não fornecido');
            return;
        }

        // Inicializar valoresBrutosPorLinha se não existir
        if (!window.valoresBrutosPorLinha) {
            window.valoresBrutosPorLinha = {};
        }
        if (!window.valoresBrutosPorLinha[rowId]) {
            window.valoresBrutosPorLinha[rowId] = {};
        }

        Object.entries(dados).forEach(([campo, valor]) => {
            const selector = `#${campo}-${rowId}`;
            const formattedValue = this.formatarValor(campo, valor);
            DOMUtils.setInputValue(selector, formattedValue);
            
            // IMPORTANTE: Armazenar valor bruto (não formatado) para máxima precisão nos totalizadores
            // Garantir que o valor seja um número
            const valorBruto = (typeof valor === 'number' && !isNaN(valor)) ? valor : (parseFloat(valor) || 0);
            window.valoresBrutosPorLinha[rowId][campo] = valorBruto;
            // #region agent log
            if (['valor_ii', 'valor_ipi', 'valor_pis', 'valor_cofins'].includes(campo)) {
                fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'TabelaProdutos.js:47',message:'IMPOSTO sendo armazenado em valoresBrutosPorLinha',data:{rowId:rowId,campo:campo,valorOriginal:valor,valorBruto:valorBruto,isNumber:typeof valorBruto==='number',isNaN:isNaN(valorBruto)},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
            }
            // #endregion
        });

        // Atualizar no store se disponível
        if (this.store && typeof this.store.updateValoresBrutosPorLinha === 'function') {
            this.store.updateValoresBrutosPorLinha(rowId, window.valoresBrutosPorLinha[rowId]);
        }
    }

    /**
     * Atualiza múltiplas linhas em batch
     * @param {Object} updates - Objeto com updates por linha { rowId: { campo: valor } }
     */
    atualizarLinhasBatch(updates) {
        if (this.isUpdating) {
            // Adicionar à fila se já estiver atualizando
            this.updateQueue.push(updates);
            return;
        }

        this.isUpdating = true;
        const fragment = DOMUtils.createFragment();

        // Inicializar valoresBrutosPorLinha se não existir
        if (!window.valoresBrutosPorLinha) {
            window.valoresBrutosPorLinha = {};
        }

        Object.entries(updates).forEach(([rowId, dados]) => {
            if (!window.valoresBrutosPorLinha[rowId]) {
                window.valoresBrutosPorLinha[rowId] = {};
            }

            Object.entries(dados).forEach(([campo, valor]) => {
                const selector = `#${campo}-${rowId}`;
                const formattedValue = this.formatarValor(campo, valor);
                DOMUtils.setInputValue(selector, formattedValue);
                
                // IMPORTANTE: Armazenar valor bruto (não formatado) para máxima precisão nos totalizadores
                const valorBruto = (typeof valor === 'number' && !isNaN(valor)) ? valor : (parseFloat(valor) || 0);
                window.valoresBrutosPorLinha[rowId][campo] = valorBruto;
            });

            // Atualizar no store se disponível
            if (this.store && typeof this.store.updateValoresBrutosPorLinha === 'function') {
                this.store.updateValoresBrutosPorLinha(rowId, window.valoresBrutosPorLinha[rowId]);
            }
        });

        this.isUpdating = false;

        // Processar fila se houver
        if (this.updateQueue.length > 0) {
            const nextUpdate = this.updateQueue.shift();
            setTimeout(() => this.atualizarLinhasBatch(nextUpdate), 0);
        }
    }

    /**
     * Formata um valor baseado no tipo de campo
     * @param {string} campo - Nome do campo
     * @param {*} valor - Valor a ser formatado
     * @returns {string} - Valor formatado
     */
    formatarValor(campo, valor) {
        if (valor === null || valor === undefined || isNaN(valor)) {
            return '';
        }

        // Campos que precisam de precisão especial
        const camposPrecisao7 = ['li_dta_honor_nix', 'honorarios_nix'];
        const camposPrecisao8 = ['fator_valor_fob', 'fator_vlr_fob'];
        const camposPorcentagem = ['mva', 'icms_st', 'mva_mg', 'icms_st_mg', 'ii_percent', 'ipi_percent', 'pis_percent', 'cofins_percent', 'icms_percent', 'reducao'];

        if (camposPrecisao7.includes(campo)) {
            return MoneyUtils.formatMoney(valor, 7);
        }

        if (camposPrecisao8.includes(campo)) {
            return MoneyUtils.formatMoney(valor, 8);
        }

        if (camposPorcentagem.includes(campo)) {
            return MoneyUtils.formatPercentage(valor);
        }

        // Padrão: 2 casas decimais para valores monetários
        return MoneyUtils.formatMoney(valor, 2);
    }

    /**
     * Obtém o valor de um campo de uma linha
     * @param {string|number} rowId - ID da linha
     * @param {string} campo - Nome do campo
     * @returns {number} - Valor parseado
     */
    obterValor(rowId, campo) {
        const selector = `#${campo}-${rowId}`;
        const valor = DOMUtils.getInputValue(selector);
        return MoneyUtils.parseMoney(valor);
    }

    /**
     * Obtém todos os valores de uma linha
     * @param {string|number} rowId - ID da linha
     * @param {Array<string>} campos - Array com nomes dos campos
     * @returns {Object} - Objeto com valores parseados
     */
    obterValoresLinha(rowId, campos) {
        const valores = {};
        campos.forEach(campo => {
            valores[campo] = this.obterValor(rowId, campo);
        });
        return valores;
    }

    /**
     * Atualiza os totalizadores da tabela
     * @param {Object} totais - Objeto com totais a serem atualizados
     */
    atualizarTotais(totais) {
        Object.entries(totais).forEach(([campo, valor]) => {
            // Totalizadores podem estar em tfoot ou em elementos específicos
            const selector = `[data-campo="${campo}"]`;
            const formattedValue = this.formatarValor(campo, valor);
            
            // Tentar atualizar via data-campo
            const elementos = document.querySelectorAll(selector);
            elementos.forEach(el => {
                if (el.tagName === 'TD' || el.tagName === 'TH') {
                    el.textContent = formattedValue;
                } else {
                    el.value = formattedValue;
                }
            });

            // Fallback: tentar por ID se não encontrou
            if (elementos.length === 0) {
                const selectorId = `#total-${campo}`;
                DOMUtils.setInputValue(selectorId, formattedValue);
            }
        });
    }

    /**
     * Atualiza campos FOB de uma linha
     * @param {string|number} rowId - ID da linha
     * @param {Object} valores - Valores { fobUnitario, fobTotal, dolar, fobUnitarioMoedaEstrangeira }
     * @param {Function} getInputValue - Função para obter valores de input
     * @param {Function} setInputValue - Função para definir valores de input
     * @param {Function} isFocused - Função para verificar se input está focado
     */
    atualizarCamposFOB(rowId, valores, getInputValue, setInputValue, isFocused) {
        const moedaProcesso = getInputValue('#moeda_processo');
        const quantidade = MoneyUtils.parseMoney(getInputValue(`#quantidade-${rowId}`)) || 0;

        if (isNaN(valores.fobUnitario)) valores.fobUnitario = 0;
        if (isNaN(valores.fobTotal)) valores.fobTotal = 0;
        if (isNaN(valores.dolar)) valores.dolar = 1;

        const fobTotalUSD = valores.fobTotal;
        const fobTotalBRL = valores.fobTotal * valores.dolar;

        if (moedaProcesso && moedaProcesso !== 'USD') {
            const fobTotalMoedaEstrangeira = valores.fobUnitarioMoedaEstrangeira * quantidade;
            setInputValue(`#fob_total_moeda_estrangeira-${rowId}`, MoneyUtils.formatMoney(fobTotalMoedaEstrangeira, 7));
        } else {
            const campoFobUsd = `#fob_unit_usd-${rowId}`;
            if (!isFocused || !isFocused(campoFobUsd)) {
                setInputValue(campoFobUsd, MoneyUtils.formatMoney(valores.fobUnitario, 7));
            }
        }

        setInputValue(`#fob_total_usd-${rowId}`, MoneyUtils.formatMoney(fobTotalUSD, 7));
        setInputValue(`#fob_total_brl-${rowId}`, MoneyUtils.formatMoney(fobTotalBRL, 7));
    }

    /**
     * Atualiza peso líquido total no cabeçalho
     * @param {number} pesoTotal - Peso total
     * @param {Function} setInputValue - Função para definir valores de input
     */
    atualizarPesoLiquidoTotal(pesoTotal, setInputValue) {
        setInputValue('#peso_liquido', MoneyUtils.formatMoney(pesoTotal, 4));
    }

    /**
     * Atualiza totalizadores completos da tabela (versão completa)
     * Esta é uma versão simplificada - a versão completa será migrada gradualmente
     * @param {Object} options - Opções { helperService, getInputValue, setInputValue, getSelectorValue, validarDiferencaCambialFrete }
     */
    atualizarTotalizadores(options = {}) {
        // #region agent log
        fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'TabelaProdutos.js:235',message:'atualizarTotalizadores ENTRY',data:{valoresBrutosExists:!!window.valoresBrutosPorLinha,valoresBrutosKeys:window.valoresBrutosPorLinha?Object.keys(window.valoresBrutosPorLinha).length:0,valoresBrutosCamposExternosExists:!!window.valoresBrutosCamposExternos},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'E'})}).catch(()=>{});
        // #endregion
        const {
            helperService,
            getInputValue,
            setInputValue,
            getSelectorValue,
            validarDiferencaCambialFrete
        } = options;

        if (typeof $ === 'undefined') {
            console.warn('TabelaProdutos: jQuery não disponível para atualizarTotalizadores');
            return;
        }

        const rows = $('#productsBody tr:not(.separador-adicao)');
        const tfoot = $('#resultado-totalizadores');
        
        // #region agent log
        fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'TabelaProdutos.js:250',message:'Linhas encontradas',data:{rowsCount:rows.length,tfootExists:tfoot.length>0},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'E'})}).catch(()=>{});
        // #endregion
        
        if (tfoot.length === 0) {
            return;
        }
        
        if (rows.length === 0) {
            tfoot.empty();
            tfoot.append('<tr><td colspan="100" style="text-align: center; font-weight: bold;">Nenhum produto cadastrado</td></tr>');
            return;
        }

        // Inicializar totais (estrutura completa será implementada)
        let totais = this._inicializarTotais();
        
        // IMPORTANTE: Para campos distribuídos do cabeçalho, usar valor original em vez de somar
        // Lista de campos que são distribuídos do cabeçalho
        const camposDistribuidos = [
            'outras_taxas_agente', 'liberacao_bl', 'desconsolidacao', 'isps_code',
            'handling', 'capatazia', 'afrmm', 'armazenagem_sts', 'armazenagem_porto',
            'frete_dta_sts_ana', 'frete_sts_cgb', 'frete_rodoviario', 'dif_frete_rodoviario',
            'diarias', 'sda', 'rep_sts', 'rep_porto', 'rep_cgb', 'armaz_cgb', 'armaz_ana',
            'lavagem_container', 'rep_anapolis', 'correios', 'demurrage', 'desp_anapolis',
            'tx_correcao_lacre', 'li_dta_honor_nix', 'honorarios_nix'
        ];
        
        // Preencher totais com valores originais do cabeçalho quando disponíveis
        // Mapeamento de campos distribuídos para inputs do cabeçalho
        const mapeamentoCamposCabecalho = {
            'outras_taxas_agente': 'outras_taxas_agente',
            'liberacao_bl': 'liberacao_bl',
            'desconsolidacao': 'desconsolidacao',
            'isps_code': 'isps_code',
            'handling': 'handling',
            'capatazia': 'capatazia',
            'afrmm': 'afrmm',
            'armazenagem_sts': 'armazenagem_sts',
            'armazenagem_porto': 'armazenagem_porto',
            'frete_dta_sts_ana': 'frete_dta_sts_ana',
            'frete_sts_cgb': 'frete_sts_cgb',
            'frete_rodoviario': 'frete_rodoviario',
            'dif_frete_rodoviario': 'dif_frete_rodoviario',
            'diarias': 'diarias',
            'sda': 'sda',
            'rep_sts': 'rep_sts',
            'rep_porto': 'rep_porto',
            'rep_cgb': 'rep_cgb',
            'armaz_cgb': 'armaz_cgb',
            'armaz_ana': 'armaz_ana',
            'lavagem_container': 'lavagem_container',
            'rep_anapolis': 'rep_anapolis',
            'correios': 'correios',
            'demurrage': 'demurrage',
            'desp_anapolis': 'desp_anapolis',
            'tx_correcao_lacre': 'tx_correcao_lacre',
            'li_dta_honor_nix': 'li_dta_honor_nix',
            'honorarios_nix': 'honorarios_nix'
        };
        
        camposDistribuidos.forEach(campo => {
            // 1. Tentar valor original armazenado
            if (window.valoresBrutosCamposExternos && 
                window.valoresBrutosCamposExternos[campo] && 
                window.valoresBrutosCamposExternos[campo]._totalOriginal !== undefined) {
                totais[campo] = window.valoresBrutosCamposExternos[campo]._totalOriginal;
                // #region agent log
                fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'TabelaProdutos.js:326',message:'Usando valor original armazenado',data:{campo:campo,valorOriginal:totais[campo]},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
                // #endregion
            } 
            // 2. Tentar ler diretamente do input do cabeçalho
            else if (mapeamentoCamposCabecalho[campo]) {
                const inputCabecalho = $(`#${mapeamentoCamposCabecalho[campo]}`);
                if (inputCabecalho.length > 0) {
                    const valorInput = inputCabecalho.val();
                    const valorOriginal = valorInput ? MoneyUtils.parseMoney(valorInput) : undefined;
                    if (valorOriginal !== undefined && valorOriginal > 0) {
                        totais[campo] = valorOriginal;
                        // #region agent log
                        fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'TabelaProdutos.js:338',message:'Lendo valor original do cabeçalho',data:{campo:campo,inputCabecalho:mapeamentoCamposCabecalho[campo],valorInput:valorInput,valorOriginal:valorOriginal},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
                        // #endregion
                    }
                }
            }
        });
        
        // Calcular totais das linhas
        const moedaServiceCharges = $('#service_charges_moeda').val();
        let fatorPesoSum = 0;
        let fatorValorFobSum = 0;
        let fatorTxSiscomexSum = 0;

        rows.each(function() {
            const rowId = this.id.replace('row-', '');
            const valoresBrutos = window.valoresBrutosPorLinha && window.valoresBrutosPorLinha[rowId];
            // #region agent log
            fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'TabelaProdutos.js:273',message:'Processando linha para totalizadores',data:{rowId:rowId,rowIdOriginal:this.id,hasValoresBrutos:!!valoresBrutos,valoresBrutosKeys:valoresBrutos?Object.keys(valoresBrutos).length:0,windowValoresBrutosExists:!!window.valoresBrutosPorLinha,windowValoresBrutosKeys:window.valoresBrutosPorLinha?Object.keys(window.valoresBrutosPorLinha).length:0},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'B'})}).catch(()=>{});
            // #endregion
            
            // Sempre tentar ler valores, priorizando valores brutos mas fazendo fallback para inputs
            Object.keys(totais).forEach(campo => {
                let valor = 0;
                let valorEncontrado = false;
                
                // 1. Tentar valores brutos primeiro
                if (valoresBrutos && valoresBrutos[campo] !== undefined) {
                    valor = valoresBrutos[campo];
                    valorEncontrado = true;
                    // #region agent log
                    if (['valor_ii', 'valor_ipi', 'valor_pis', 'valor_cofins'].includes(campo)) {
                        fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'TabelaProdutos.js:297',message:'IMPOSTO encontrado em valoresBrutos',data:{rowId:rowId,campo:campo,valor:valor,source:'valoresBrutos'},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
                    }
                    // #endregion
                }
                // 2. Tentar valores brutos de campos externos
                else if (window.valoresBrutosCamposExternos && window.valoresBrutosCamposExternos[campo] && 
                         window.valoresBrutosCamposExternos[campo][rowId] !== undefined) {
                    valor = window.valoresBrutosCamposExternos[campo][rowId];
                    valorEncontrado = true;
                    // #region agent log
                    fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'TabelaProdutos.js:288',message:'Valor encontrado em valoresBrutosCamposExternos',data:{rowId:rowId,campo:campo,valor:valor,source:'valoresBrutosCamposExternos'},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
                    // #endregion
                }
                // 3. Fallback: ler do input
                else {
                    const elemento = $(`#${campo}-${rowId}`);
                    if (elemento.length > 0) {
                        const valorInput = elemento.val();
                        valor = MoneyUtils.parseMoney(valorInput) || 0;
                        valorEncontrado = true;
                        // #region agent log
                        if (['valor_ii', 'valor_ipi', 'valor_pis', 'valor_cofins'].includes(campo)) {
                            fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'TabelaProdutos.js:317',message:'IMPOSTO lido do input',data:{rowId:rowId,campo:campo,valor:valor,valorInput:valorInput,selector:`#${campo}-${rowId}`,source:'input'},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'D'})}).catch(()=>{});
                        }
                        // #endregion
                    } else {
                        // #region agent log
                        if (['valor_ii', 'valor_ipi', 'valor_pis', 'valor_cofins'].includes(campo)) {
                            fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'TabelaProdutos.js:323',message:'IMPOSTO campo não encontrado',data:{rowId:rowId,campo:campo,selector:`#${campo}-${rowId}`,elementoLength:elemento.length},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'D'})}).catch(()=>{});
                        }
                        // #endregion
                    }
                }
                
                // Aplicar validações e somar
                if (valorEncontrado) {
                    // IMPORTANTE: Não somar campos distribuídos se já foram preenchidos com valor original
                    // Mas se o valor original não existe, somar normalmente
                    if (camposDistribuidos.includes(campo)) {
                        // Verificar se o valor original foi definido
                        const valorOriginalDefinido = window.valoresBrutosCamposExternos && 
                            window.valoresBrutosCamposExternos[campo] && 
                            window.valoresBrutosCamposExternos[campo]._totalOriginal !== undefined;
                        if (valorOriginalDefinido) {
                            // Campo já foi preenchido com valor original, pular soma
                            return;
                        }
                        // Se não tem valor original, continuar somando normalmente
                    }
                    
                    // Tratamento especial para campos específicos
                    const nacionalizacao = helperService ? helperService.getNacionalizacaoAtual() : 'outros';
                    const isSantaCatarina = nacionalizacao === 'santa_catarina';
                    
                    // Campos calculados especiais (não usar valor encontrado acima)
                    if (isSantaCatarina && campo === 'multa_complem') {
                        if (helperService) {
                            valor = helperService.obterMultaComplementarPorAdicaoItemProduto(rowId, getInputValue) || 0;
                        } else {
                            valor = 0;
                        }
                    } else if (isSantaCatarina && campo === 'dif_impostos') {
                        if (helperService) {
                            valor = helperService.obterDiferencaImpostosPorAdicaoItemProduto(rowId, getInputValue) || 0;
                        } else {
                            valor = 0;
                        }
                    } else if (campo === 'service_charges' && moedaServiceCharges && moedaServiceCharges !== 'USD') {
                        // Para service_charges em moeda estrangeira, usar campo específico
                        const elemento = $(`#service_charges_moeda_estrangeira-${rowId}`);
                        if (elemento.length > 0) {
                            valor = MoneyUtils.parseMoney(elemento.val()) || 0;
                        } else {
                            valor = 0;
                        }
                    } else if (campo === 'service_charges_brl') {
                        // service_charges_brl NÃO deve ser somado das linhas
                        // Ele é calculado a partir do service_charges_usd do cabeçalho
                        // Pular soma - será calculado no totalizador a partir do cabeçalho
                        // #region agent log
                        fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'TabelaProdutos.js:449',message:'service_charges_brl - pulando soma (será calculado do cabeçalho)',data:{rowId:rowId,valorEncontrado:valorEncontrado,valor:valor},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
                        // #endregion
                        return;
                    }
                    
                    if (campo === 'diferenca_cambial_frete' && validarDiferencaCambialFrete) {
                        valor = validarDiferencaCambialFrete(valor);
                    }
                    // Garantir que o valor seja um número válido
                    valor = (typeof valor === 'number' && !isNaN(valor)) ? valor : 0;
                    totais[campo] += valor;
                    // #region agent log
                    if (['valor_ii', 'valor_ipi', 'valor_pis', 'valor_cofins'].includes(campo)) {
                        fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'TabelaProdutos.js:363',message:'IMPOSTO sendo somado ao total',data:{rowId:rowId,campo:campo,valor:valor,totalAntes:totais[campo]-valor,totalDepois:totais[campo]},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
                    }
                    // #endregion
                }
            });

            // Usar valores brutos para fatores quando disponíveis
            if (valoresBrutos) {
                let fatorPeso = valoresBrutos.fator_peso !== undefined ? valoresBrutos.fator_peso : (MoneyUtils.parseMoney($(`#fator_peso-${rowId}`).val()) || 0);
                let fatorValorFob = valoresBrutos.fator_valor_fob !== undefined ? valoresBrutos.fator_valor_fob : (MoneyUtils.parseMoney($(`#fator_valor_fob-${rowId}`).val()) || 0);
                let fatorTxSiscomex = valoresBrutos.fator_tx_siscomex !== undefined ? valoresBrutos.fator_tx_siscomex : (MoneyUtils.parseMoney($(`#fator_tx_siscomex-${rowId}`).val()) || 0);
                
                // Garantir que os valores sejam números válidos
                fatorPeso = (typeof fatorPeso === 'number' && !isNaN(fatorPeso)) ? fatorPeso : 0;
                fatorValorFob = (typeof fatorValorFob === 'number' && !isNaN(fatorValorFob)) ? fatorValorFob : 0;
                fatorTxSiscomex = (typeof fatorTxSiscomex === 'number' && !isNaN(fatorTxSiscomex)) ? fatorTxSiscomex : 0;
                
                fatorPesoSum += fatorPeso;
                fatorValorFobSum += fatorValorFob;
                fatorTxSiscomexSum += fatorTxSiscomex;
            } else {
                let fatorPeso = MoneyUtils.parseMoney($(`#fator_peso-${rowId}`).val()) || 0;
                let fatorValorFob = MoneyUtils.parseMoney($(`#fator_valor_fob-${rowId}`).val()) || 0;
                let fatorTxSiscomex = MoneyUtils.parseMoney($(`#fator_tx_siscomex-${rowId}`).val()) || 0;
                
                // Garantir que os valores sejam números válidos
                fatorPeso = (typeof fatorPeso === 'number' && !isNaN(fatorPeso)) ? fatorPeso : 0;
                fatorValorFob = (typeof fatorValorFob === 'number' && !isNaN(fatorValorFob)) ? fatorValorFob : 0;
                fatorTxSiscomex = (typeof fatorTxSiscomex === 'number' && !isNaN(fatorTxSiscomex)) ? fatorTxSiscomex : 0;
                
                fatorPesoSum += fatorPeso;
                fatorValorFobSum += fatorValorFob;
                fatorTxSiscomexSum += fatorTxSiscomex;
            }
        });

        // Atualizar peso líquido total
        if (helperService) {
            const pesoLiquidoTotal = helperService.calcularPesoTotal();
            this.atualizarPesoLiquidoTotal(pesoLiquidoTotal, setInputValue);
        }

        // #region agent log
        // Log de todos os totais zerados para identificar problemas
        const totaisZerados = {};
        Object.keys(totais).forEach(key => {
            if (totais[key] === 0) {
                totaisZerados[key] = 0;
            }
        });
        const totaisNaoZerados = {};
        Object.keys(totais).forEach(key => {
            if (totais[key] !== 0) {
                totaisNaoZerados[key] = totais[key];
            }
        });
        fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'TabelaProdutos.js:403',message:'Totais calculados - Zerados vs Não Zerados',data:{totaisZeradosCount:Object.keys(totaisZerados).length,totaisZeradosKeys:Object.keys(totaisZerados).slice(0,20),totaisNaoZeradosCount:Object.keys(totaisNaoZerados).length,totaisNaoZeradosSample:Object.fromEntries(Object.entries(totaisNaoZerados).slice(0,15))},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'E'})}).catch(()=>{});
        // #endregion

        // Renderizar totalizadores no tfoot
        tfoot.empty();
        const tr = this._renderizarTotalizadores(totais, rows, fatorPesoSum, fatorValorFobSum, fatorTxSiscomexSum, helperService, validarDiferencaCambialFrete);
        tfoot.append(tr);
    }

    /**
     * Inicializa objeto de totais
     * @private
     */
    _inicializarTotais() {
        return {
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
    }

    /**
     * Renderiza HTML dos totalizadores
     * @private
     */
    _renderizarTotalizadores(totais, rows, fatorPesoSum, fatorValorFobSum, fatorTxSiscomexSum, helperService, validarDiferencaCambialFrete) {
        if (typeof $ === 'undefined') {
            return '<tr><td colspan="100">Erro: jQuery não disponível</td></tr>';
        }

        // Função auxiliar para garantir valor válido antes de formatar
        const formatarValorSeguro = (valor, decimais = 2) => {
            const valorNum = (typeof valor === 'number' && !isNaN(valor)) ? valor : 0;
            return MoneyUtils.formatMoney(valorNum, decimais);
        };

        const nacionalizacao = helperService ? helperService.getNacionalizacaoAtual() : 'outros';
        const moedaProcesso = $('#moeda_processo').val() || 'USD';
        const moedaFrete = $('#frete_internacional_moeda').val() || 'USD';
        const moedaSeguro = $('#seguro_internacional_moeda').val() || 'USD';
        const moedaAcrescimo = $('#acrescimo_frete_moeda').val() || 'USD';
        const moedaServiceCharges = $('#service_charges_moeda').val() || 'USD';

        // Iniciar linha de totais
        let tr = '<tr><td colspan="7" style="text-align: right; font-weight: bold;">TOTAIS:</td>';

        // QUANTIDADE
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.quantidade, 2)}</td>`;

        // PESO LIQ. UNIT (vazio - é unitário)
        tr += '<td></td>';

        // PESO LIQ TOTAL
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.peso_liquido_total, 2)}</td>`;

        // FATOR PESO
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(fatorPesoSum, 8)}</td>`;

        // COLUNAS FOB
        if (moedaProcesso !== 'USD') {
            // FOB UNIT MOEDA (vazio - é unitário)
            tr += '<td></td>';
            // VLR TOTALFOB MOEDA
            let totalFobMoeda = 0;
            rows.each(function() {
                const rowId = this.id.replace('row-', '');
                // Priorizar valores brutos para máxima precisão
                const valoresBrutos = window.valoresBrutosPorLinha && window.valoresBrutosPorLinha[rowId];
                let valor = 0;
                if (valoresBrutos && valoresBrutos.fob_total_moeda_estrangeira !== undefined) {
                    valor = valoresBrutos.fob_total_moeda_estrangeira;
                } else {
                    valor = MoneyUtils.parseMoney($(`#fob_total_moeda_estrangeira-${rowId}`).val()) || 0;
                }
                // Garantir que o valor seja um número válido
                valor = (typeof valor === 'number' && !isNaN(valor)) ? valor : 0;
                totalFobMoeda += valor;
            });
            tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totalFobMoeda, 2)}</td>`;
        } else {
            // FOB UNIT USD (vazio - é unitário)
            tr += '<td></td>';
        }

        // VLR TOTALFOB USD
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.fob_total_usd, 2)}</td>`;

        // VLR TOTALFOB R$
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.fob_total_brl, 2)}</td>`;

        // COLUNAS FRETE
        if (moedaFrete !== 'USD') {
            let totalFreteMoeda = 0;
            rows.each(function() {
                const rowId = this.id.replace('row-', '');
                // Priorizar valores brutos para máxima precisão
                const valoresBrutos = window.valoresBrutosPorLinha && window.valoresBrutosPorLinha[rowId];
                let valor = 0;
                if (valoresBrutos && valoresBrutos.frete_moeda_estrangeira !== undefined) {
                    valor = valoresBrutos.frete_moeda_estrangeira;
                } else {
                    valor = MoneyUtils.parseMoney($(`#frete_moeda_estrangeira-${rowId}`).val()) || 0;
                }
                // Garantir que o valor seja um número válido
                valor = (typeof valor === 'number' && !isNaN(valor)) ? valor : 0;
                totalFreteMoeda += valor;
            });
            tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totalFreteMoeda, 2)}</td>`;
        }

        // FRETE INT.USD - usar valor original do cabeçalho se disponível, senão usar valor totalizado
        // Tentar primeiro com sufixo _usd, depois sem sufixo, depois ler do input do cabeçalho
        let freteUsdOriginal = window.valoresBrutosCamposExternos?.frete_internacional_usd?._totalOriginal || 
                               window.valoresBrutosCamposExternos?.frete_internacional?._totalOriginal;
        if (freteUsdOriginal === undefined) {
            // Ler diretamente do input do cabeçalho
            const freteUsdInput = $('#frete_internacional_usd').val();
            freteUsdOriginal = freteUsdInput ? MoneyUtils.parseMoney(freteUsdInput) : undefined;
        }
        const freteUsdFinal = freteUsdOriginal !== undefined ? freteUsdOriginal : totais.frete_usd;
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(freteUsdFinal, 2)}</td>`;

        // FRETE INT.R$ - usar valor original do cabeçalho se disponível, senão usar valor totalizado
        let freteBrlOriginal = window.valoresBrutosCamposExternos?.frete_internacional_brl?._totalOriginal || 
                                window.valoresBrutosCamposExternos?.frete_internacional?._totalOriginal;
        if (freteBrlOriginal === undefined) {
            // Ler diretamente do input do cabeçalho
            const freteBrlInput = $('#frete_internacional_brl').val();
            freteBrlOriginal = freteBrlInput ? MoneyUtils.parseMoney(freteBrlInput) : undefined;
        }
        const freteBrlFinal = freteBrlOriginal !== undefined ? freteBrlOriginal : totais.frete_brl;
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(freteBrlFinal, 2)}</td>`;

        // VLR CFR UNIT (vazio - é unitário)
        tr += '<td></td>';

        // VLR CFR TOTAL
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.vlr_crf_total, 2)}</td>`;

        // COLUNAS SERVICE CHARGES
        if (moedaServiceCharges !== 'USD') {
            let totalServiceChargesMoeda = 0;
            rows.each(function() {
                const rowId = this.id.replace('row-', '');
                // Priorizar valores brutos para máxima precisão
                const valoresBrutos = window.valoresBrutosPorLinha && window.valoresBrutosPorLinha[rowId];
                let valor = 0;
                if (valoresBrutos && valoresBrutos.service_charges_moeda_estrangeira !== undefined) {
                    valor = valoresBrutos.service_charges_moeda_estrangeira;
                } else {
                    valor = MoneyUtils.parseMoney($(`#service_charges_moeda_estrangeira-${rowId}`).val()) || 0;
                }
                // Garantir que o valor seja um número válido
                valor = (typeof valor === 'number' && !isNaN(valor)) ? valor : 0;
                totalServiceChargesMoeda += valor;
            });
            tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totalServiceChargesMoeda, 2)}</td>`;
        }

        // SERVICE CHARGES USD - usar valor original do cabeçalho se disponível
        let serviceChargesUsdOriginal = window.valoresBrutosCamposExternos?.service_charges_usd?._totalOriginal || 
                                         window.valoresBrutosCamposExternos?.service_charges?._totalOriginal;
        if (serviceChargesUsdOriginal === undefined) {
            // Ler diretamente do input do cabeçalho
            serviceChargesUsdOriginal = MoneyUtils.parseMoney($('#service_charges_usd').val()) || undefined;
        }
        const serviceChargesUsdFinal = serviceChargesUsdOriginal !== undefined ? serviceChargesUsdOriginal : totais.service_charges;
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(serviceChargesUsdFinal, 2)}</td>`;

        // SERVICE CHARGES R$ - usar valor original do cabeçalho se disponível
        let serviceChargesBrlOriginal = window.valoresBrutosCamposExternos?.service_charges_brl?._totalOriginal || 
                                         window.valoresBrutosCamposExternos?.service_charges?._totalOriginal;
        if (serviceChargesBrlOriginal === undefined) {
            // Ler diretamente do input do cabeçalho
            const serviceChargesBrlInput = $('#service_charges_brl').val();
            serviceChargesBrlOriginal = serviceChargesBrlInput ? MoneyUtils.parseMoney(serviceChargesBrlInput) : undefined;
            // #region agent log
            fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'TabelaProdutos.js:777',message:'Lendo service_charges_brl do cabeçalho',data:{serviceChargesBrlInput:serviceChargesBrlInput,serviceChargesBrlOriginal:serviceChargesBrlOriginal,totaisServiceChargesBrl:totais.service_charges_brl,serviceChargesUsdFinal:serviceChargesUsdFinal},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
            // #endregion
        }
        // Se ainda não encontrou, calcular a partir do USD usando a cotação
        if (serviceChargesBrlOriginal === undefined && serviceChargesUsdFinal > 0) {
            const cotacoes = helperService ? helperService.getCotacaoesProcesso() : null;
            const cotacaoUSD = cotacoes && cotacoes['USD'] ? cotacoes['USD'].venda : 1;
            serviceChargesBrlOriginal = serviceChargesUsdFinal * cotacaoUSD;
            // #region agent log
            fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'TabelaProdutos.js:785',message:'Calculando service_charges_brl a partir do USD',data:{serviceChargesUsdFinal:serviceChargesUsdFinal,cotacaoUSD:cotacaoUSD,serviceChargesBrlOriginal:serviceChargesBrlOriginal},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
            // #endregion
        }
        const serviceChargesBrlFinal = serviceChargesBrlOriginal !== undefined ? serviceChargesBrlOriginal : totais.service_charges_brl;
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(serviceChargesBrlFinal, 2)}</td>`;

        // COLUNAS ACRÉSCIMO
        if (moedaAcrescimo !== 'USD') {
            let totalAcrescimoMoeda = 0;
            rows.each(function() {
                const rowId = this.id.replace('row-', '');
                // Priorizar valores brutos para máxima precisão
                const valoresBrutos = window.valoresBrutosPorLinha && window.valoresBrutosPorLinha[rowId];
                let valor = 0;
                if (valoresBrutos && valoresBrutos.acrescimo_moeda_estrangeira !== undefined) {
                    valor = valoresBrutos.acrescimo_moeda_estrangeira;
                } else {
                    valor = MoneyUtils.parseMoney($(`#acrescimo_moeda_estrangeira-${rowId}`).val()) || 0;
                }
                // Garantir que o valor seja um número válido
                valor = (typeof valor === 'number' && !isNaN(valor)) ? valor : 0;
                totalAcrescimoMoeda += valor;
            });
            tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totalAcrescimoMoeda, 2)}</td>`;
        }

        // ACRESC. FRETE USD - usar valor original do cabeçalho se disponível
        let acrescFreteUsdOriginal = window.valoresBrutosCamposExternos?.acrescimo_frete_usd?._totalOriginal || 
                                     window.valoresBrutosCamposExternos?.acrescimo_frete?._totalOriginal;
        if (acrescFreteUsdOriginal === undefined) {
            // Ler diretamente do input do cabeçalho
            acrescFreteUsdOriginal = MoneyUtils.parseMoney($('#acrescimo_frete_usd').val()) || undefined;
        }
        const acrescFreteUsdFinal = acrescFreteUsdOriginal !== undefined ? acrescFreteUsdOriginal : totais.acresc_frete_usd;
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(acrescFreteUsdFinal, 2)}</td>`;

        // ACRESC. FRETE R$ - usar valor original do cabeçalho se disponível
        let acrescFreteBrlOriginal = window.valoresBrutosCamposExternos?.acrescimo_frete_brl?._totalOriginal || 
                                      window.valoresBrutosCamposExternos?.acrescimo_frete?._totalOriginal;
        if (acrescFreteBrlOriginal === undefined) {
            // Ler diretamente do input do cabeçalho
            acrescFreteBrlOriginal = MoneyUtils.parseMoney($('#acrescimo_frete_brl').val()) || undefined;
        }
        const acrescFreteBrlFinal = acrescFreteBrlOriginal !== undefined ? acrescFreteBrlOriginal : totais.acresc_frete_brl;
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(acrescFreteBrlFinal, 2)}</td>`;

        // COLUNAS SEGURO
        if (moedaSeguro !== 'USD') {
            let totalSeguroMoeda = 0;
            rows.each(function() {
                const rowId = this.id.replace('row-', '');
                // Priorizar valores brutos para máxima precisão
                const valoresBrutos = window.valoresBrutosPorLinha && window.valoresBrutosPorLinha[rowId];
                let valor = 0;
                if (valoresBrutos && valoresBrutos.seguro_moeda_estrangeira !== undefined) {
                    valor = valoresBrutos.seguro_moeda_estrangeira;
                } else {
                    valor = MoneyUtils.parseMoney($(`#seguro_moeda_estrangeira-${rowId}`).val()) || 0;
                }
                // Garantir que o valor seja um número válido
                valor = (typeof valor === 'number' && !isNaN(valor)) ? valor : 0;
                totalSeguroMoeda += valor;
            });
            tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totalSeguroMoeda, 2)}</td>`;
        }

        // SEGURO INT.USD - usar valor original do cabeçalho se disponível
        let seguroUsdOriginal = window.valoresBrutosCamposExternos?.seguro_internacional_usd?._totalOriginal || 
                                 window.valoresBrutosCamposExternos?.seguro_internacional?._totalOriginal;
        if (seguroUsdOriginal === undefined) {
            // Ler diretamente do input do cabeçalho
            seguroUsdOriginal = MoneyUtils.parseMoney($('#seguro_internacional_usd').val()) || undefined;
        }
        const seguroUsdFinal = seguroUsdOriginal !== undefined ? seguroUsdOriginal : totais.seguro_usd;
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(seguroUsdFinal, 2)}</td>`;

        // SEGURO INT.R$ - usar valor original do cabeçalho se disponível
        let seguroBrlOriginal = window.valoresBrutosCamposExternos?.seguro_internacional_brl?._totalOriginal || 
                                 window.valoresBrutosCamposExternos?.seguro_internacional?._totalOriginal;
        if (seguroBrlOriginal === undefined) {
            // Ler diretamente do input do cabeçalho
            seguroBrlOriginal = MoneyUtils.parseMoney($('#seguro_internacional_brl').val()) || undefined;
        }
        const seguroBrlFinal = seguroBrlOriginal !== undefined ? seguroBrlOriginal : totais.seguro_brl;
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(seguroBrlFinal, 2)}</td>`;

        // THC USD
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.thc_usd, 2)}</td>`;

        // THC R$
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.thc_brl, 2)}</td>`;

        // VLR ADUANEIRO USD
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.valor_aduaneiro_usd, 2)}</td>`;

        // VLR ADUANEIRO R$
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.valor_aduaneiro_brl, 2)}</td>`;

        // II, IPI, PIS, COFINS, ICMS, ICMS REDUZIDO (percentuais - vazios)
        tr += '<td></td><td></td><td></td><td></td><td></td><td></td>';

        // REDUÇÃO (vazio - é unitário)
        tr += '<td></td>';

        // VLR II
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.valor_ii, 2)}</td>`;

        // BC IPI
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.base_ipi, 2)}</td>`;

        // VLR IPI
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.valor_ipi, 2)}</td>`;

        // BC PIS/COFINS
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.base_pis_cofins, 2)}</td>`;

        // VLR PIS
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.valor_pis, 2)}</td>`;

        // VLR COFINS
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.valor_cofins, 2)}</td>`;

        // DESP. ADUANEIRA
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.despesa_aduaneira, 2)}</td>`;

        // BC ICMS S/REDUÇÃO
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.base_icms_sem_reducao, 2)}</td>`;

        // VLR ICMS S/RED.
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.valor_icms_sem_reducao, 2)}</td>`;

        // BC ICMS REDUZIDO
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.base_icms_reduzido, 2)}</td>`;

        // VLR ICMS REDUZ.
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.valor_icms_reduzido, 2)}</td>`;

        // VLR UNIT PROD. NF (vazio - é unitário)
        tr += '<td></td>';

        // VLR TOTAL PROD. NF
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.valor_total_nf, 2)}</td>`;

        // VLR TOTAL NF S/ICMS ST
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.valor_total_nf_sem_icms_st, 2)}</td>`;

        // BC ICMS-ST
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.base_icms_st, 2)}</td>`;

        // MVA, ICMS-ST (percentuais - vazios)
        tr += '<td></td><td></td>';

        // VLR ICMS-ST
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.valor_icms_st, 2)}</td>`;

        // VLR TOTAL NF C/ICMS-ST
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.valor_total_nf_com_icms_st, 2)}</td>`;

        // FATOR VLR FOB
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(fatorValorFobSum, 8)}</td>`;

        // FATOR TX SISCOMEX
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(fatorTxSiscomexSum, 7)}</td>`;

        // MULTA
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.multa, 2)}</td>`;

        // TX DEF. LI (vazio - é percentual ou calculado, não é totalizado)
        // Para Mato Grosso, é um valor calculado por linha, não somado
        tr += '<td></td>';

        // TAXA SISCOMEX
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.taxa_siscomex, 2)}</td>`;

        // Campos específicos por nacionalização
        if (nacionalizacao === 'santa_catarina') {
            // MULTA COMPLEM
            tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.multa_complem, 2)}</td>`;
            // DIF IMPOSTOS
            tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.dif_impostos, 2)}</td>`;
        }

        // Campos comuns (ordem varia por nacionalização)
        const camposComuns = this._obterCamposPorNacionalizacao(nacionalizacao);
        camposComuns.forEach(campo => {
            if (totais[campo] !== undefined) {
                const decimais = (campo === 'li_dta_honor_nix' || campo === 'honorarios_nix') ? 7 : 2;
                tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais[campo], decimais)}</td>`;
            } else {
                tr += '<td></td>';
            }
        });

        // DESP. DESEMBARAÇO
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.desp_desenbaraco, 2)}</td>`;

        // DIF. CAMBIAL FRETE
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.diferenca_cambial_frete, 2)}</td>`;

        // DIF.CAMBIAL FOB
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.diferenca_cambial_fob, 2)}</td>`;

        // OPCIONAL 1
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.opcional_1_valor, 2)}</td>`;

        // OPCIONAL 2
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.opcional_2_valor, 2)}</td>`;

        // CUSTO UNIT FINAL (vazio - é unitário)
        tr += '<td></td>';

        // CUSTO TOTAL FINAL
        tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.custo_total_final, 2)}</td>`;

        // Colunas específicas do Mato Grosso
        if (nacionalizacao === 'mato_grosso') {
            tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.dez_porcento, 2)}</td>`;
            tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.custo_com_margem, 2)}</td>`;
            tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.vlr_ipi_mg, 2)}</td>`;
            tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.vlr_icms_mg, 2)}</td>`;
            tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.pis_mg, 2)}</td>`;
            tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.cofins_mg, 2)}</td>`;
            tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.custo_total_final_credito, 2)}</td>`;
            // CUSTO UNIT CREDITO (vazio - é unitário)
            tr += '<td></td>';
            tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.bc_icms_st_mg, 2)}</td>`;
            // MVA, ICMS-ST (percentuais - vazios)
            tr += '<td></td><td></td>';
            tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.vlr_icms_st_mg, 2)}</td>`;
            tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.custo_total_c_icms_st, 2)}</td>`;
            // CUSTO UNIT C/ICMS ST (vazio - é unitário)
            tr += '<td></td>';
            tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.exportador_mg, 2)}</td>`;
            tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.tributos_mg, 2)}</td>`;
            tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.despesas_mg, 2)}</td>`;
            tr += `<td style="font-weight: bold; text-align: right;">${formatarValorSeguro(totais.total_pago_mg, 2)}</td>`;
            // PERCENTUAL S/FOB (percentual - vazio)
            tr += '<td></td>';
        }

        tr += '</tr>';
        return tr;
    }

    /**
     * Obtém lista de campos externos por nacionalização
     * @private
     */
    _obterCamposPorNacionalizacao(nacionalizacao) {
        if (nacionalizacao === 'anapolis') {
            return [
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
        } else if (nacionalizacao === 'santos') {
            return [
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
                'tx_correcao_lacre',
                'li_dta_honor_nix',
                'honorarios_nix'
            ];
        } else if (nacionalizacao === 'santa_catarina') {
            return [
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
        } else if (nacionalizacao === 'mato_grosso') {
            return [
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
                'li_dta_honor_nix',
                'honorarios_nix'
            ];
        } else {
            return [
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
                'correios',
                'li_dta_honor_nix',
                'honorarios_nix'
            ];
        }
    }

    /**
     * Limpa o cache de seletores
     */
    limparCache() {
        this.selectorCache.clear();
        DOMUtils.clearCache();
    }
}
