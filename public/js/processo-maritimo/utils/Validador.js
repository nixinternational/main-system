/**
 * Utilitários para validação de dados
 */
export class Validador {
    /**
     * Valida se um valor é um número válido
     * @param {*} value - Valor a ser validado
     * @returns {boolean} - True se for válido
     */
    static isNumeroValido(value) {
        if (value === null || value === undefined) {
            return false;
        }
        const num = typeof value === 'number' ? value : parseFloat(value);
        return !isNaN(num) && isFinite(num) && num >= 0;
    }

    /**
     * Valida diferença cambial (deve ser >= 0)
     * @param {number} valor - Valor a ser validado
     * @returns {number} - Valor validado (0 se inválido)
     */
    static validarDiferencaCambial(valor) {
        if (valor === null || valor === undefined || isNaN(valor) || !isFinite(valor) || valor < 0) {
            return 0;
        }
        return valor;
    }

    /**
     * Valida quantidade (deve ser > 0)
     * @param {number} quantidade - Quantidade a ser validada
     * @returns {number} - Quantidade validada
     */
    static validarQuantidade(quantidade) {
        if (!this.isNumeroValido(quantidade) || quantidade <= 0) {
            return 1; // Retorna 1 como padrão para evitar divisão por zero
        }
        return quantidade;
    }

    /**
     * Valida peso (deve ser >= 0)
     * @param {number} peso - Peso a ser validado
     * @returns {number} - Peso validado
     */
    static validarPeso(peso) {
        if (!this.isNumeroValido(peso) || peso < 0) {
            return 0;
        }
        return peso;
    }

    /**
     * Valida porcentagem (deve estar entre 0 e 1)
     * @param {number} porcentagem - Porcentagem a ser validada
     * @returns {number} - Porcentagem validada
     */
    static validarPorcentagem(porcentagem) {
        if (!this.isNumeroValido(porcentagem)) {
            return 0;
        }
        // Se for maior que 1, assume que está em formato de porcentagem (ex: 10 para 10%)
        if (porcentagem > 1) {
            return porcentagem / 100;
        }
        return Math.max(0, Math.min(1, porcentagem));
    }

    /**
     * Valida diferença cambial de frete (deve ser >= 0)
     * @param {number} valor - Valor a ser validado
     * @returns {number} - Valor validado (0 se inválido)
     */
    static validarDiferencaCambialFrete(valor) {
        if (valor === null || valor === undefined || isNaN(valor) || !isFinite(valor) || valor < 0) {
            return 0;
        }
        return valor;
    }
}
