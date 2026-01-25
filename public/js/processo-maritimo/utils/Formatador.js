import { MoneyUtils } from './MoneyUtils.js';

/**
 * Utilitários para formatação de valores
 */
export class Formatador {
    /**
     * Limpa número removendo formatação
     * @param {string|number} valor - Valor a ser limpo
     * @returns {string} - Número limpo (apenas dígitos)
     */
    static limparNumero(valor) {
        if (valor === null || valor === undefined) {
            return '';
        }
        return valor.toString().replace(/[^0-9]/g, '');
    }

    /**
     * Atualiza valor real em um span
     * @param {string} inputId - ID do input
     * @param {string} spanId - ID do span
     * @param {boolean} automatic - Se deve atualizar automaticamente
     */
    static updateValorReal(inputId, spanId, automatic = true) {
        if (typeof $ === 'undefined') return;
        
        const valor = MoneyUtils.parseMoney($(inputId).val()) || 0;
        const cotacao = MoneyUtils.parseMoney($('#cotacao_frete_internacional').val()) || 1;
        const valorReal = valor * cotacao;
        
        if (automatic) {
            $(spanId).text(MoneyUtils.formatMoney(valorReal, 2));
        }
    }

    /**
     * Atualiza valor de cotação em um span
     * @param {string} inputId - ID do input
     * @param {string} spanId - ID do span
     */
    static updateValorCotacao(inputId, spanId) {
        if (typeof $ === 'undefined') return;
        
        const valor = MoneyUtils.parseMoney($(inputId).val()) || 0;
        $(spanId).text(MoneyUtils.formatMoney(valor, 4));
    }

    /**
     * Atualiza símbolo de moeda em um input
     * @param {string} inputId - ID do input
     */
    static updateCurrencySymbol(inputId) {
        if (typeof $ === 'undefined') return;
        
        const selectId = inputId.replace('_usd', '_moeda').replace('_brl', '_moeda');
        const codigoMoeda = $(selectId).val() || 'USD';
        const simbolo = this.getCurrencySymbol(codigoMoeda);
        
        // Atualizar símbolo conforme necessário
        // Implementação pode ser expandida
    }

    /**
     * Obtém símbolo de moeda
     * @param {string} codigoMoeda - Código da moeda (USD, EUR, etc)
     * @returns {string} - Símbolo da moeda
     */
    static getCurrencySymbol(codigoMoeda) {
        const simbolos = {
            'USD': '$',
            'EUR': '€',
            'GBP': '£',
            'BRL': 'R$',
            'CNY': '¥',
            'JPY': '¥'
        };
        return simbolos[codigoMoeda] || codigoMoeda;
    }
}
