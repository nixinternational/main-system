import { MoneyUtils } from '../utils/MoneyUtils.js';

/**
 * Serviço para calcular despesas de desembaraço
 * Suporta diferentes nacionalizações com lógicas específicas
 */
export class CalculadoraDespesas {
    constructor(helperService) {
        this.helperService = helperService;
    }

    /**
     * Calcula despesas de desembaraço para uma linha de produto
     * @param {string} rowId - ID da linha
     * @param {number} fatorVlrFob - Fator de valor FOB
     * @param {number} fatorSiscomex - Fator de taxa Siscomex
     * @param {number} taxaSiscomexUnit - Taxa Siscomex unitária
     * @param {number|null} vlrAduaneiroBrl - Valor aduaneiro em BRL (opcional)
     * @param {string} nacionalizacao - Tipo de nacionalização
     * @param {Function} getInputValue - Função para obter valores de input
     * @param {Function} getCheckboxValue - Função para verificar checkbox
     * @returns {Object} - Objeto com total e componentes das despesas
     */
    calcularDespesas(rowId, fatorVlrFob, fatorSiscomex, taxaSiscomexUnit, vlrAduaneiroBrl = null, nacionalizacao, getInputValue, getCheckboxValue) {
        // Obter multa conforme nacionalização
        let multa = this._obterMulta(rowId, nacionalizacao, getInputValue);
        
        // Obter valor aduaneiro se não fornecido
        if (vlrAduaneiroBrl === null) {
            vlrAduaneiroBrl = MoneyUtils.parseMoney(getInputValue(`#valor_aduaneiro_brl-${rowId}`)) || 0;
        }

        // Calcular TX DEF. LI conforme nacionalização
        let txDefLi = this._calcularTxDefLi(rowId, nacionalizacao, fatorVlrFob, vlrAduaneiroBrl, getInputValue);
        
        const taxaSiscomex = taxaSiscomexUnit || 0;

        // Lógica específica para Santa Catarina
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

        // Obter outros componentes de despesas
        const afrmm = MoneyUtils.parseMoney(getInputValue(`#afrmm-${rowId}`)) || 0;
        const armazenagem_sts = MoneyUtils.parseMoney(getInputValue(`#armazenagem_sts-${rowId}`)) || 0;
        const frete_dta_sts_ana = MoneyUtils.parseMoney(getInputValue(`#frete_dta_sts_ana-${rowId}`)) || 0;
        const honorarios_nix = MoneyUtils.parseMoney(getInputValue(`#honorarios_nix-${rowId}`)) || 0;

        let despesas = multa + txDefLi + taxaSiscomex;

        // Adicionar componentes conforme nacionalização
        if (nacionalizacao === 'santos') {
            despesas += afrmm + honorarios_nix;
        } else if (nacionalizacao === 'anapolis') {
            // Para Anápolis: Multa + tx.def.li + tx sistcomex + afrmm + armazenagem sts + frete sts/gyn + honorarios nix
            despesas += afrmm + armazenagem_sts + frete_dta_sts_ana + honorarios_nix;
        } else {
            despesas += afrmm + armazenagem_sts + frete_dta_sts_ana + honorarios_nix;
        }

        // Adicionar campos opcionais se checkbox marcado
        const opcional1Compoe = getCheckboxValue('#opcional_1_compoe_despesas');
        const opcional2Compoe = getCheckboxValue('#opcional_2_compoe_despesas');
        
        const opcional1Valor = MoneyUtils.parseMoney(getInputValue(`#opcional_1_valor-${rowId}`)) || 0;
        const opcional2Valor = MoneyUtils.parseMoney(getInputValue(`#opcional_2_valor-${rowId}`)) || 0;
        
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
            tipoCalculo: nacionalizacao
        };
    }

    /**
     * Obtém o valor da multa conforme nacionalização
     * @private
     */
    _obterMulta(rowId, nacionalizacao, getInputValue) {
        if (nacionalizacao === 'santa_catarina' && this.helperService) {
            return this.helperService.obterMultaPorAdicaoItemProduto(rowId, getInputValue);
        }
        return MoneyUtils.parseMoney(getInputValue(`#multa-${rowId}`)) || 0;
    }

    /**
     * Calcula TX DEF. LI conforme nacionalização
     * @private
     */
    _calcularTxDefLi(rowId, nacionalizacao, fatorVlrFob, vlrAduaneiroBrl, getInputValue) {
        if (nacionalizacao === 'mato_grosso') {
            // Para Mato Grosso: usar valor rateado do cabeçalho
            const valorCampo = MoneyUtils.parseMoney(getInputValue(`#tx_def_li`)) || 0;
            const valoresBrutosCamposExternos = window.valoresBrutosCamposExternos || {};
            const valorDistribuido = valoresBrutosCamposExternos['tx_def_li'] && 
                valoresBrutosCamposExternos['tx_def_li'][rowId] !== undefined
                ? valoresBrutosCamposExternos['tx_def_li'][rowId]
                : (valorCampo * fatorVlrFob);
            return MoneyUtils.parseMoney(getInputValue(`#tx_def_li-${rowId}`)) || valorDistribuido;
        } else {
            // Para outras nacionalizações: calcular como porcentagem
            const txDefLiPercent = MoneyUtils.parsePercentage(getInputValue(`#tx_def_li-${rowId}`)) || 0;
            return vlrAduaneiroBrl * txDefLiPercent;
        }
    }
}
