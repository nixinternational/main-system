import { MoneyUtils } from '../utils/MoneyUtils.js';

/**
 * Serviço para cálculo de impostos
 */
export class CalculadoraImpostos {
    /**
     * Calcula as alíquotas de impostos para um produto
     * @param {string|number} rowId - ID da linha
     * @param {Function} getInputValue - Função para obter valor de input (jQuery ou DOM)
     * @returns {Object} - Objeto com alíquotas de impostos
     */
    calcularAliquotas(rowId, getInputValue) {
        return {
            ii: getInputValue(`#ii_percent-${rowId}`) 
                ? MoneyUtils.parsePercentage(getInputValue(`#ii_percent-${rowId}`)) 
                : 0,
            ipi: getInputValue(`#ipi_percent-${rowId}`) 
                ? MoneyUtils.parsePercentage(getInputValue(`#ipi_percent-${rowId}`)) 
                : 0,
            pis: getInputValue(`#pis_percent-${rowId}`) 
                ? MoneyUtils.parsePercentage(getInputValue(`#pis_percent-${rowId}`)) 
                : 0,
            cofins: getInputValue(`#cofins_percent-${rowId}`) 
                ? MoneyUtils.parsePercentage(getInputValue(`#cofins_percent-${rowId}`)) 
                : 0,
            icms: getInputValue(`#icms_percent-${rowId}`) 
                ? MoneyUtils.parsePercentage(getInputValue(`#icms_percent-${rowId}`)) 
                : 0
        };
    }

    /**
     * Calcula os valores dos impostos baseados em uma base de cálculo
     * @param {number} base - Base de cálculo
     * @param {Object} aliquotas - Alíquotas dos impostos
     * @param {number} quantidade - Quantidade do produto
     * @returns {Object} - Objeto com valores calculados dos impostos
     */
    calcularValores(base, aliquotas, quantidade = 1) {
        const vlrII = base * aliquotas.ii;
        const bcIpi = base + vlrII;
        const vlrIpi = bcIpi * aliquotas.ipi;
        const bcPisCofins = base;
        const vlrPis = bcPisCofins * aliquotas.pis;
        const vlrCofins = bcPisCofins * aliquotas.cofins;

        return {
            vlrII,
            bcIpi,
            vlrIpi,
            bcPisCofins,
            vlrPis,
            vlrCofins,
            vlrTotalProdutoNf: base + vlrII,
            vlrUnitProdutNf: (base + vlrII) / (quantidade || 1)
        };
    }
}
