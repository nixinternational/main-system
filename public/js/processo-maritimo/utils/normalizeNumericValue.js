/**
 * Normaliza um valor numérico de string para número
 * @param {*} value - Valor a ser normalizado
 * @returns {number} - Valor numérico normalizado
 */
export function normalizeNumericValue(value) {
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
