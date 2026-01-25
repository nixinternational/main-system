import { normalizeNumericValue } from './normalizeNumericValue.js';
import { truncateNumber } from './truncateNumber.js';

/**
 * Utilitários para manipulação de valores monetários e porcentagens
 */
export const MoneyUtils = {
    /**
     * Converte uma string de porcentagem para número decimal
     * @param {string|number} value - Valor em porcentagem (ex: "10%" ou "10")
     * @returns {number} - Valor decimal (ex: 0.10)
     */
    parsePercentage: function(value) {
        if (!value || value === "") return 0;

        let stringValue = value.toString().trim();
        stringValue = stringValue.replace(/%/g, '');
        stringValue = stringValue.replace(',', '.');
        stringValue = stringValue.replace(/\s/g, '');

        const parsedValue = parseFloat(stringValue) || 0;
        return parsedValue / 100;
    },

    /**
     * Trunca um valor para um número específico de casas decimais
     * @param {number} value - Valor a ser truncado
     * @param {number} decimals - Número de casas decimais
     * @returns {number} - Valor truncado
     */
    truncate: function(value, decimals = 2) {
        return truncateNumber(value, decimals);
    },

    /**
     * Formata um valor monetário em USD
     * @param {number|string} value - Valor a ser formatado
     * @param {number} decimals - Número de casas decimais
     * @returns {string} - Valor formatado (ex: "1,234.56")
     */
    formatUSD: function(value, decimals = 2) {
        if (value === null || value === undefined) return "0.00";

        let num = typeof value === 'string' ? parseFloat(value.replace(',', '.')) : value;
        let fixedDecimals = num.toFixed(decimals);

        let parts = fixedDecimals.split('.');
        let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        let decimalPart = parts[1] || '00';

        return `${integerPart}.${decimalPart}`;
    },

    /**
     * Formata um valor como porcentagem
     * @param {number|string} value - Valor decimal (ex: 0.10)
     * @param {number} decimals - Número de casas decimais
     * @returns {string} - Valor formatado (ex: "10,00%")
     */
    formatPercentage: function(value, decimals = 2) {
        if (value === null || value === undefined) {
            return this.formatTruncatedNumber(0, decimals) + '%';
        }

        const percentageValue = normalizeNumericValue(value) * 100;
        const truncated = this.truncate(percentageValue, decimals);
        return `${truncated.toLocaleString('pt-BR', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        })}%`;
    },

    /**
     * Converte uma string formatada em valor numérico
     * @param {string|number} value - Valor formatado (ex: "1.234,56")
     * @returns {number} - Valor numérico (ex: 1234.56)
     */
    parseMoney: function(value) {
        if (value === null || value === undefined || value === "") return 0;

        if (typeof value === "number") {
            return value;
        }

        const strValue = value.toString().trim();
        
        // Se tem ponto mas não tem vírgula, pode ser formato USD (ex: "1234.56")
        if (strValue.includes('.') && !strValue.includes(',')) {
            return parseFloat(strValue) || 0;
        }

        // Formato brasileiro: remover pontos (separadores de milhar) e substituir vírgula por ponto
        // Exemplo: "1.345.333,33" -> "1345333.33"
        let cleanValue = strValue
            .replace(/\./g, '')  // Remove todos os pontos (separadores de milhar)
            .replace(/,/g, '.'); // Substitui vírgula (separador decimal) por ponto

        // Remove caracteres não numéricos exceto ponto decimal
        cleanValue = cleanValue.replace(/[^\d.]/g, '');

        return parseFloat(cleanValue) || 0;
    },

    /**
     * Formata um valor monetário em Real (BRL)
     * @param {number|string} value - Valor a ser formatado
     * @param {number} decimals - Número de casas decimais (padrão: 6)
     * @returns {string} - Valor formatado (ex: "1.234,56")
     */
    formatMoney: function(value, decimals = 6) {
        const num = normalizeNumericValue(value);
        if (!isFinite(num)) return '0';
        
        let processedValue;

        if (decimals <= 2) {
            processedValue = Math.round(num * Math.pow(10, decimals)) / Math.pow(10, decimals);
        } else {
            processedValue = this.truncate(num, decimals);
        }
        
        return processedValue.toLocaleString('pt-BR', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    },
    
    /**
     * Formata um valor monetário sem truncar zeros à direita
     * @param {number|string} value - Valor a ser formatado
     * @returns {string} - Valor formatado preservando precisão
     */
    formatMoneyExato: function(value) {
        const num = normalizeNumericValue(value);
        if (!isFinite(num)) return '0';
        
        let str = num.toFixed(20);
        str = str.replace(/\.?0+$/, '');
        
        let parts = str.split('.');
        let integerPart = parts[0];
        let decimalPart = parts[1] || '';
        
        integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        
        if (decimalPart) {
            return `${integerPart},${decimalPart}`;
        }
        return integerPart;
    },

    /**
     * Formata um número truncado
     * @param {number|string} value - Valor a ser formatado
     * @param {number} decimals - Número de casas decimais
     * @returns {string} - Valor formatado
     */
    formatTruncatedNumber: function(value, decimals = 2) {
        const truncated = truncateNumber(value, decimals);
        return truncated.toLocaleString('pt-BR', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    }
};
