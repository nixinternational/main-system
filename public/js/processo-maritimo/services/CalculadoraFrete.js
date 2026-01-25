import { MoneyUtils } from '../utils/MoneyUtils.js';

/**
 * Serviço para cálculos relacionados a frete
 */
export class CalculadoraFrete {
    /**
     * Calcula frete internacional proporcional ao FOB
     * @param {number} fobTotalLinha - FOB total da linha
     * @param {number} fobTotalGeral - FOB total geral
     * @param {number} freteTotal - Frete total do processo
     * @returns {number} - Frete proporcional da linha
     */
    calcularFreteProporcional(fobTotalLinha, fobTotalGeral, freteTotal) {
        if (fobTotalGeral === 0) {
            return 0;
        }
        const fator = fobTotalLinha / fobTotalGeral;
        return freteTotal * fator;
    }

    /**
     * Calcula frete por peso
     * @param {number} pesoLinha - Peso da linha
     * @param {number} pesoTotal - Peso total
     * @param {number} freteTotal - Frete total do processo
     * @returns {number} - Frete proporcional ao peso
     */
    calcularFretePorPeso(pesoLinha, pesoTotal, freteTotal) {
        if (pesoTotal === 0) {
            return 0;
        }
        const fator = pesoLinha / pesoTotal;
        return freteTotal * fator;
    }

    /**
     * Calcula seguro internacional proporcional ao FOB
     * @param {number} fobTotalLinha - FOB total da linha
     * @param {number} fobTotalGeral - FOB total geral
     * @param {number} seguroTotal - Seguro total do processo
     * @returns {number} - Seguro proporcional da linha
     */
    calcularSeguroProporcional(fobTotalLinha, fobTotalGeral, seguroTotal) {
        if (fobTotalGeral === 0) {
            return 0;
        }
        const fator = fobTotalLinha / fobTotalGeral;
        return seguroTotal * fator;
    }

    /**
     * Calcula acréscimo de frete proporcional ao FOB
     * @param {number} fobTotalLinha - FOB total da linha
     * @param {number} fobTotalGeral - FOB total geral
     * @param {number} acrescimoTotal - Acréscimo total do processo
     * @param {number} cotacaoUSD - Cotação do USD
     * @returns {number} - Acréscimo proporcional da linha
     */
    calcularAcrescimoFrete(fobTotalLinha, fobTotalGeral, acrescimoTotal, cotacaoUSD) {
        if (fobTotalGeral === 0) {
            return 0;
        }
        const fator = fobTotalLinha / fobTotalGeral;
        return acrescimoTotal * fator;
    }

    /**
     * Calcula THC proporcional ao peso
     * @param {number} pesoLinha - Peso da linha
     * @param {number} pesoTotal - Peso total
     * @param {number} thcTotal - THC total do processo
     * @returns {number} - THC proporcional da linha
     */
    calcularTHC(pesoLinha, pesoTotal, thcTotal) {
        if (pesoTotal === 0) {
            return 0;
        }
        const fator = pesoLinha / pesoTotal;
        return thcTotal * fator;
    }
}
