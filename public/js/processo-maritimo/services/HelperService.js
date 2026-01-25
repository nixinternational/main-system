import { MoneyUtils } from '../utils/MoneyUtils.js';
import { Validador } from '../utils/Validador.js';

/**
 * Serviço com funções helper migradas do código antigo
 */
export class HelperService {
    constructor(store) {
        this.store = store;
    }

    /**
     * Obtém a nacionalização atual
     * @returns {string} - Tipo de nacionalização
     */
    getNacionalizacaoAtual() {
        const estado = this.store.getState();
        if (estado.nacionalizacao) {
            return estado.nacionalizacao;
        }

        // Fallback para jQuery se disponível
        if (typeof $ !== 'undefined' && $('#nacionalizacao').length > 0) {
            const valor = $('#nacionalizacao').val();
            return valor ? valor.toLowerCase() : 'outros';
        }

        return 'outros';
    }

    /**
     * Obtém cotações do processo
     * @returns {Object} - Objeto com cotações
     */
    getCotacaoesProcesso() {
        const estado = this.store.getState();
        if (estado.moedas && Object.keys(estado.moedas).length > 0) {
            return estado.moedas;
        }

        // Fallback para jQuery
        if (typeof $ !== 'undefined') {
            let cotacaoProcesso = {};
            const cotacaoMoedaProcesso = $('#cotacao_moeda_processo').val();
            
            if (cotacaoMoedaProcesso) {
                try {
                    cotacaoProcesso = JSON.parse(cotacaoMoedaProcesso);
                } catch (e) {
                    console.warn('Erro ao parsear cotações:', e);
                }
            }

            // Se não houver, tentar obter do campo dolarHoje
            if (!cotacaoProcesso.USD) {
                const dolarHoje = $('#dolarHoje').val();
                if (dolarHoje) {
                    cotacaoProcesso.USD = {
                        venda: MoneyUtils.parseMoney(dolarHoje),
                        compra: MoneyUtils.parseMoney(dolarHoje)
                    };
                }
            }

            return cotacaoProcesso;
        }

        return { USD: { venda: 1, compra: 1 } };
    }

    /**
     * Obtém valores base de uma linha (peso, FOB, quantidade)
     * @param {string|number} rowId - ID da linha
     * @returns {Object} - Valores base
     */
    obterValoresBase(rowId) {
        if (typeof $ === 'undefined') {
            return { pesoTotal: 0, fobUnitario: 0, quantidade: 0 };
        }

        // Priorizar valores brutos para máxima precisão
        const valoresBrutos = window.valoresBrutosPorLinha && window.valoresBrutosPorLinha[rowId];
        
        // Obter peso total (priorizar valor bruto)
        let pesoTotal = 0;
        if (valoresBrutos && valoresBrutos.peso_liquido_total !== undefined) {
            pesoTotal = valoresBrutos.peso_liquido_total;
        } else {
            pesoTotal = MoneyUtils.parseMoney($(`#peso_liquido_total-${rowId}`).val()) || 0;
        }

        // Obter quantidade (priorizar valor bruto)
        let quantidade = 0;
        if (valoresBrutos && valoresBrutos.quantidade !== undefined) {
            quantidade = valoresBrutos.quantidade;
        } else {
            quantidade = MoneyUtils.parseMoney($(`#quantidade-${rowId}`).val()) || 0;
        }
        quantidade = Validador.validarQuantidade(quantidade);

        // Obter FOB unitário considerando a moeda do processo
        let fobUnitario = 0;
        const moedaProcesso = $('#moeda_processo').val() || 'USD';
        
        if (moedaProcesso === 'USD') {
            // Campo: fob_unit_usd-${rowId}
            if (valoresBrutos && valoresBrutos.fob_unit_usd !== undefined) {
                fobUnitario = valoresBrutos.fob_unit_usd;
            } else {
                fobUnitario = MoneyUtils.parseMoney($(`#fob_unit_usd-${rowId}`).val()) || 0;
            }
        } else {
            // Campo: fob_unit_moeda_estrangeira-${rowId}
            // Precisa converter para USD
            let fobUnitMoedaEstrangeira = 0;
            if (valoresBrutos && valoresBrutos.fob_unit_moeda_estrangeira !== undefined) {
                fobUnitMoedaEstrangeira = valoresBrutos.fob_unit_moeda_estrangeira;
            } else {
                fobUnitMoedaEstrangeira = MoneyUtils.parseMoney($(`#fob_unit_moeda_estrangeira-${rowId}`).val()) || 0;
            }
            
            // Converter para USD usando a cotação
            const cotacoes = this.getCotacaoesProcesso();
            const cotacaoMoedaProcesso = cotacoes[moedaProcesso]?.venda || 1;
            const cotacaoUSD = cotacoes['USD']?.venda || 1;
            
            if (cotacaoMoedaProcesso > 0 && cotacaoUSD > 0) {
                // Converter: valor na moeda estrangeira * (cotacao_moeda / cotacao_usd)
                fobUnitario = fobUnitMoedaEstrangeira * (cotacaoMoedaProcesso / cotacaoUSD);
            } else {
                fobUnitario = fobUnitMoedaEstrangeira;
            }
        }

        return {
            pesoTotal: Validador.validarPeso(pesoTotal),
            fobUnitario,
            quantidade
        };
    }

    /**
     * Calcula peso total de todas as linhas
     * @returns {number} - Peso total
     */
    calcularPesoTotal() {
        if (typeof $ === 'undefined') {
            return 0;
        }

        let pesoTotal = 0;
        $('#productsBody .linhas-input').each(function() {
            const rowId = this.id ? this.id.replace('row-', '') : null;
            if (rowId && !rowId.includes('multa')) {
                const peso = MoneyUtils.parseMoney($(`#peso_liquido_total-${rowId}`).val()) || 0;
                pesoTotal += peso;
            }
        });

        return pesoTotal;
    }

    /**
     * Calcula fator de peso para uma linha
     * @param {number} pesoTotalGeral - Peso total geral
     * @param {string|number} rowId - ID da linha
     * @returns {number} - Fator de peso
     */
    recalcularFatorPeso(pesoTotalGeral, rowId) {
        if (pesoTotalGeral === 0) {
            return 0;
        }

        const valores = this.obterValoresBase(rowId);
        return valores.pesoTotal / pesoTotalGeral;
    }

    /**
     * Calcula taxa Siscomex do processo
     * @returns {number} - Taxa Siscomex total
     */
    calcularTaxaSiscomex() {
        if (typeof $ === 'undefined') {
            return 0;
        }

        // Implementação simplificada - pode ser expandida conforme necessário
        const taxaSiscomex = MoneyUtils.parseMoney($('#taxa_siscomex').val()) || 0;
        return taxaSiscomex;
    }

    /**
     * Obtém valor de um campo do processo em USD
     * @param {string} inputSelector - Seletor do input de valor
     * @param {string} moedaSelector - Seletor do select de moeda
     * @param {string} cotacaoSelector - Seletor do input de cotação
     * @returns {number} - Valor em USD
     */
    obterValorProcessoUSD(inputSelector, moedaSelector, cotacaoSelector) {
        if (typeof $ === 'undefined') {
            return 0;
        }

        const valor = MoneyUtils.parseMoney($(inputSelector).val()) || 0;
        const moeda = $(moedaSelector).val();
        const cotacoes = this.getCotacaoesProcesso();

        if (moeda === 'USD') {
            return valor;
        }

        const cotacaoMoeda = MoneyUtils.parseMoney($(cotacaoSelector).val()) || 0;
        const cotacaoUSD = cotacoes['USD']?.venda || 1;

        if (cotacaoMoeda > 0 && cotacaoUSD > 0) {
            const moedaEmUSD = cotacaoMoeda / cotacaoUSD;
            return valor * moedaEmUSD;
        }

        return valor;
    }

    /**
     * Calcula seguro proporcional ao FOB
     * @param {number} fobTotalLinha - FOB total da linha
     * @param {number} fobTotalGeral - FOB total geral
     * @returns {number} - Seguro proporcional
     */
    calcularSeguro(fobTotalLinha, fobTotalGeral) {
        if (fobTotalGeral === 0) {
            return 0;
        }

        const seguroTotal = this.obterValorProcessoUSD(
            '#seguro_internacional',
            '#seguro_internacional_moeda',
            '#cotacao_seguro_internacional'
        );

        const fator = fobTotalLinha / fobTotalGeral;
        return seguroTotal * fator;
    }

    /**
     * Calcula acréscimo de frete proporcional ao FOB
     * @param {number} fobTotalLinha - FOB total da linha
     * @param {number} fobTotalGeral - FOB total geral
     * @param {number} cotacaoUSD - Cotação do USD
     * @returns {number} - Acréscimo de frete
     */
    calcularAcrescimoFrete(fobTotalLinha, fobTotalGeral, cotacaoUSD) {
        if (fobTotalGeral === 0) {
            return 0;
        }

        const acrescimoTotal = this.obterValorProcessoUSD(
            '#acrescimo_frete',
            '#acrescimo_frete_moeda',
            '#cotacao_acrescimo_frete'
        );

        const fator = fobTotalLinha / fobTotalGeral;
        return acrescimoTotal * fator;
    }

    /**
     * Calcula FOB total geral de todas as linhas
     * @returns {number} - FOB total geral
     */
    calcularFobTotalGeral() {
        if (typeof $ === 'undefined') {
            return 0;
        }

        let total = 0;
        $('[id^="fob_total_usd-"]').each(function() {
            total += MoneyUtils.parseMoney($(this).val()) || 0;
        });
        return total || 0;
    }

    /**
     * Obtém campos externos conforme nacionalização
     * @returns {Array<string>} - Array de nomes de campos
     */
    getCamposExternos() {
        const nacionalizacao = this.getNacionalizacaoAtual();
        
        // Constantes de campos externos
        const CAMPOS_EXCLUSIVOS_ANAPOLIS = ['rep_anapolis', 'correios'];
        
        const CAMPOS_EXTERNOS_BASE = [
            'outras_taxas_agente', 'liberacao_bl', 'desconsolidacao', 'isps_code',
            'handling', 'capatazia', 'afrmm', 'armazenagem_sts', 'frete_sts_cgb',
            'diarias', 'sda', 'rep_sts', 'armaz_cgb', 'rep_cgb', 'demurrage',
            'li_dta_honor_nix', 'honorarios_nix'
        ];

        const CAMPOS_EXTERNOS_ANAPOLIS = [
            'outras_taxas_agente', 'liberacao_bl', 'desconsolidacao', 'isps_code',
            'handling', 'capatazia', 'afrmm', 'armazenagem_sts', 'frete_dta_sts_ana',
            'sda', 'rep_sts', 'desp_anapolis', 'rep_anapolis', 'correios',
            'li_dta_honor_nix', 'honorarios_nix'
        ];

        const CAMPOS_EXTERNOS_SANTA_CATARINA = [
            'outras_taxas_agente', 'liberacao_bl', 'desconsolidacao', 'isps_code',
            'handling', 'capatazia', 'afrmm', 'armazenagem_porto', 'frete_rodoviario',
            'dif_frete_rodoviario', 'sda', 'rep_porto', 'tx_correcao_lacre',
            'li_dta_honor_nix', 'honorarios_nix'
        ];

        const CAMPOS_EXTERNOS_MATO_GROSSO = [
            'outras_taxas_agente', 'liberacao_bl', 'desconsolidacao', 'isps_code',
            'handling', 'capatazia', 'afrmm', 'armazenagem_sts', 'frete_sts_cgb',
            'diarias', 'sda', 'rep_sts', 'armaz_cgb', 'rep_cgb', 'demurrage',
            'li_dta_honor_nix', 'honorarios_nix'
        ];

        const CAMPO_CORRECAO_LACRE = 'tx_correcao_lacre';

        if (nacionalizacao === 'anapolis') {
            return CAMPOS_EXTERNOS_ANAPOLIS;
        }
        
        if (nacionalizacao === 'santa_catarina') {
            return CAMPOS_EXTERNOS_SANTA_CATARINA;
        }
        
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

    /**
     * Obtém campos de diferença cambial
     * @returns {Array<string>} - Array de nomes de campos
     */
    getCamposDiferencaCambial() {
        return ['diferenca_cambial_fob', 'diferenca_cambial_frete'];
    }

    /**
     * Obtém multa por adição e item do produto (Santa Catarina)
     * @param {string} rowId - ID da linha
     * @param {Function} getInputValue - Função para obter valores de input
     * @returns {number} - Valor da multa
     */
    obterMultaPorAdicaoItemProduto(rowId, getInputValue) {
        if (typeof $ === 'undefined') {
            return 0;
        }

        const adicao = this._limparNumero(getInputValue(`#adicao-${rowId}`));
        const item = this._limparNumero(getInputValue(`#item-${rowId}`));
        if (!adicao || !item) {
            return 0;
        }

        let multaEncontrada = 0;
        $('#productsBodyMulta tr.linhas-input').each(function() {
            const multaRowId = $(this).attr('id') ? $(this).attr('id').replace('row-multa-', '') : null;
            if (!multaRowId) return;

            const adicaoMulta = this._limparNumero(getInputValue(`#adicao_multa-${multaRowId}`));
            const itemMulta = this._limparNumero(getInputValue(`#item_multa-${multaRowId}`));

            if (adicaoMulta === adicao && itemMulta === item) {
                const ii = MoneyUtils.parseMoney(getInputValue(`#ii_percent_aduaneiro_multa-${multaRowId}`)) || 0;
                const ipi = MoneyUtils.parseMoney(getInputValue(`#ipi_percent_aduaneiro_multa-${multaRowId}`)) || 0;
                const pis = MoneyUtils.parseMoney(getInputValue(`#pis_percent_aduaneiro_multa-${multaRowId}`)) || 0;
                const cofins = MoneyUtils.parseMoney(getInputValue(`#cofins_percent_aduaneiro_multa-${multaRowId}`)) || 0;
                multaEncontrada = ii + ipi + pis + cofins;
                return false;
            }
        }.bind(this));

        return multaEncontrada;
    }

    /**
     * Obtém multa complementar por adição e item do produto (Santa Catarina)
     * @param {string} rowId - ID da linha
     * @param {Function} getInputValue - Função para obter valores de input
     * @returns {number} - Valor da multa complementar
     */
    obterMultaComplementarPorAdicaoItemProduto(rowId, getInputValue) {
        if (typeof $ === 'undefined') {
            return 0;
        }

        const adicao = this._limparNumero(getInputValue(`#adicao-${rowId}`));
        const item = this._limparNumero(getInputValue(`#item-${rowId}`));
        if (!adicao || !item) {
            return 0;
        }

        let valorAduaneiroMultaEncontrado = 0;
        $('#productsBodyMulta tr.linhas-input').each(function() {
            const multaRowId = $(this).attr('id') ? $(this).attr('id').replace('row-multa-', '') : null;
            if (!multaRowId) return;

            const adicaoMulta = this._limparNumero(getInputValue(`#adicao_multa-${multaRowId}`));
            const itemMulta = this._limparNumero(getInputValue(`#item_multa-${multaRowId}`));

            if (adicaoMulta === adicao && itemMulta === item) {
                valorAduaneiroMultaEncontrado = MoneyUtils.parseMoney(getInputValue(`#valor_aduaneiro_multa-${multaRowId}`)) || 0;
                return false;
            }
        }.bind(this));

        return valorAduaneiroMultaEncontrado;
    }

    /**
     * Obtém diferença de impostos por adição e item do produto (Santa Catarina)
     * @param {string} rowId - ID da linha
     * @param {Function} getInputValue - Função para obter valores de input
     * @returns {number} - Valor da diferença de impostos
     */
    obterDiferencaImpostosPorAdicaoItemProduto(rowId, getInputValue) {
        if (typeof $ === 'undefined') {
            return 0;
        }

        const adicao = this._limparNumero(getInputValue(`#adicao-${rowId}`));
        const item = this._limparNumero(getInputValue(`#item-${rowId}`));
        if (!adicao || !item) {
            return 0;
        }

        let somaRecalc = 0;
        $('#productsBodyMulta tr.linhas-input').each(function() {
            const multaRowId = $(this).attr('id') ? $(this).attr('id').replace('row-multa-', '') : null;
            if (!multaRowId) return;

            const adicaoMulta = this._limparNumero(getInputValue(`#adicao_multa-${multaRowId}`));
            const itemMulta = this._limparNumero(getInputValue(`#item_multa-${multaRowId}`));

            if (adicaoMulta === adicao && itemMulta === item) {
                const iiRecalc = MoneyUtils.parseMoney(getInputValue(`#vlr_ii_recalc_multa-${multaRowId}`)) || 0;
                const ipiRecalc = MoneyUtils.parseMoney(getInputValue(`#vlr_ipi_recalc_multa-${multaRowId}`)) || 0;
                const pisRecalc = MoneyUtils.parseMoney(getInputValue(`#vlr_pis_recalc_multa-${multaRowId}`)) || 0;
                const cofinsRecalc = MoneyUtils.parseMoney(getInputValue(`#vlr_cofins_recalc_multa-${multaRowId}`)) || 0;
                somaRecalc += iiRecalc + ipiRecalc + pisRecalc + cofinsRecalc;
            }
        }.bind(this));

        return somaRecalc;
    }

    /**
     * Limpa número removendo formatação
     * @private
     */
    _limparNumero(valor) {
        if (!valor) return '';
        return valor.toString().replace(/[^\d]/g, '');
    }
}
