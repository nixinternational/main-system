/**
 * Classe base abstrata para estratégias de nacionalização
 * Define a interface que todas as estratégias devem implementar
 */
export class BaseStrategy {
    constructor(store, eventBus) {
        this.store = store;
        this.eventBus = eventBus;
    }

    /**
     * Calcula impostos para um produto
     * @param {Object} produto - Dados do produto
     * @param {Object} moedas - Objeto com cotações de moedas
     * @returns {Object} - Objeto com impostos calculados
     */
    calcularImpostos(produto, moedas) {
        throw new Error('Método calcularImpostos deve ser implementado');
    }

    /**
     * Calcula despesas para um produto
     * @param {Object} produto - Dados do produto
     * @param {Object} cabecalho - Valores dos campos do cabeçalho
     * @returns {Object} - Objeto com despesas calculadas
     */
    calcularDespesas(produto, cabecalho) {
        throw new Error('Método calcularDespesas deve ser implementado');
    }

    /**
     * Calcula o custo final para um produto
     * @param {Object} produto - Dados do produto
     * @param {Object} totais - Totais calculados
     * @returns {Object} - Objeto com custos finais
     */
    calcularCustoFinal(produto, totais) {
        throw new Error('Método calcularCustoFinal deve ser implementado');
    }

    /**
     * Retorna as colunas visíveis para esta nacionalização
     * @returns {Array<string>} - Array com nomes das colunas
     */
    getColunasVisiveis() {
        throw new Error('Método getColunasVisiveis deve ser implementado');
    }

    /**
     * Retorna os campos do cabeçalho para esta nacionalização
     * @returns {Array<string>} - Array com nomes dos campos
     */
    getCamposCabecalho() {
        throw new Error('Método getCamposCabecalho deve ser implementado');
    }

    /**
     * Calcula o valor CFR para um produto
     * @param {Object} produto - Dados do produto
     * @param {Object} moedas - Objeto com cotações de moedas
     * @returns {number} - Valor CFR
     */
    calcularCFR(produto, moedas) {
        throw new Error('Método calcularCFR deve ser implementado');
    }

    /**
     * Calcula o valor aduaneiro para um produto
     * @param {Object} produto - Dados do produto
     * @param {Object} moedas - Objeto com cotações de moedas
     * @returns {number} - Valor aduaneiro
     */
    calcularValorAduaneiro(produto, moedas) {
        throw new Error('Método calcularValorAduaneiro deve ser implementado');
    }

    /**
     * Calcula a base de ICMS sem redução
     * @param {Object} produto - Dados do produto
     * @param {Object} impostos - Impostos calculados
     * @param {number} despesas - Despesas calculadas
     * @returns {number} - Base de ICMS sem redução
     */
    calcularBcIcmsSemReducao(produto, impostos, despesas) {
        throw new Error('Método calcularBcIcmsSemReducao deve ser implementado');
    }

    /**
     * Calcula a base de ICMS reduzido
     * @param {Object} produto - Dados do produto
     * @param {Object} impostos - Impostos calculados
     * @param {number} despesas - Despesas calculadas
     * @returns {number} - Base de ICMS reduzido
     */
    calcularBcIcmsReduzido(produto, impostos, despesas) {
        throw new Error('Método calcularBcIcmsReduzido deve ser implementado');
    }

    /**
     * Calcula a despesa de desembaraço
     * @param {Object} produto - Dados do produto
     * @param {Object} cabecalho - Valores dos campos do cabeçalho
     * @returns {number} - Despesa de desembaraço
     */
    calcularDespesaDesembaraco(produto, cabecalho) {
        throw new Error('Método calcularDespesaDesembaraco deve ser implementado');
    }
}
