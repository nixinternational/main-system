import { normalizeNumericValue } from './normalizeNumericValue.js';

/**
 * Trunca um número para um número específico de casas decimais
 * @param {number} value - Valor a ser truncado
 * @param {number} decimals - Número de casas decimais
 * @returns {number} - Valor truncado
 */
export function truncateNumber(value, decimals = 2) {
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
