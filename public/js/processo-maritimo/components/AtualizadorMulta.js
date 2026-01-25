import { MoneyUtils } from '../utils/MoneyUtils.js';
import { DOMUtils } from '../utils/DOMUtils.js';

/**
 * Componente para atualizar campos de multa (Santa Catarina)
 */
export class AtualizadorMulta {
    constructor(helperService) {
        this.helperService = helperService;
    }

    /**
     * Atualiza multa de um produto por adição/item
     * @param {string} rowId - ID da linha
     * @param {Function} getInputValue - Função para obter valores de input
     * @param {Function} setInputValue - Função para definir valores de input
     * @param {Function} setInputProperty - Função para definir propriedades de input
     */
    atualizarMultaProdutoPorMulta(rowId, getInputValue, setInputValue, setInputProperty) {
        const nacionalizacao = this.helperService.getNacionalizacaoAtual();
        if (nacionalizacao !== 'santa_catarina') {
            setInputProperty(`#multa-${rowId}`, 'readonly', false);
            return;
        }

        const multaCalculada = this.helperService.obterMultaPorAdicaoItemProduto(rowId, getInputValue);
        setInputProperty(`#multa-${rowId}`, 'readonly', true);
        setInputValue(`#multa-${rowId}`, MoneyUtils.formatMoney(multaCalculada, 2));
    }

    /**
     * Atualiza multa complementar de um produto
     * @param {string} rowId - ID da linha
     * @param {Function} getInputValue - Função para obter valores de input
     * @param {Function} setInputValue - Função para definir valores de input
     * @param {Function} setInputProperty - Função para definir propriedades de input
     */
    atualizarMultaComplementarProdutoPorMulta(rowId, getInputValue, setInputValue, setInputProperty) {
        const nacionalizacao = this.helperService.getNacionalizacaoAtual();
        if (nacionalizacao !== 'santa_catarina') {
            setInputProperty(`#multa_complem-${rowId}`, 'readonly', false);
            return;
        }

        const valorAduaneiroMulta = this.helperService.obterMultaComplementarPorAdicaoItemProduto(rowId, getInputValue);
        setInputProperty(`#multa_complem-${rowId}`, 'readonly', true);
        setInputValue(`#multa_complem-${rowId}`, MoneyUtils.formatMoney(valorAduaneiroMulta, 2));
    }

    /**
     * Atualiza diferença de impostos de um produto
     * @param {string} rowId - ID da linha
     * @param {Function} getInputValue - Função para obter valores de input
     * @param {Function} setInputValue - Função para definir valores de input
     * @param {Function} setInputProperty - Função para definir propriedades de input
     */
    atualizarDiferencaImpostosProdutoPorMulta(rowId, getInputValue, setInputValue, setInputProperty) {
        const nacionalizacao = this.helperService.getNacionalizacaoAtual();
        if (nacionalizacao !== 'santa_catarina') {
            setInputProperty(`#dif_impostos-${rowId}`, 'readonly', false);
            return;
        }

        const difImpostos = this.helperService.obterDiferencaImpostosPorAdicaoItemProduto(rowId, getInputValue);
        setInputProperty(`#dif_impostos-${rowId}`, 'readonly', true);
        setInputValue(`#dif_impostos-${rowId}`, MoneyUtils.formatMoney(difImpostos, 2));
    }

    /**
     * Atualiza multa para todos os produtos
     * @param {Function} getInputValue - Função para obter valores de input
     * @param {Function} setInputValue - Função para definir valores de input
     * @param {Function} setInputProperty - Função para definir propriedades de input
     * @param {Function} getSelectorValue - Função para obter seletor jQuery
     */
    atualizarMultaProdutosPorMulta(getInputValue, setInputValue, setInputProperty, getSelectorValue) {
        const nacionalizacao = this.helperService.getNacionalizacaoAtual();
        if (nacionalizacao !== 'santa_catarina') {
            return;
        }

        const rows = getSelectorValue('#productsBody tr.linhas-input');
        if (rows && rows.length > 0) {
            rows.each((index, element) => {
                const rowId = element.id ? element.id.toString().replace('row-', '') : null;
                if (rowId) {
                    this.atualizarMultaProdutoPorMulta(rowId, getInputValue, setInputValue, setInputProperty);
                    this.atualizarMultaComplementarProdutoPorMulta(rowId, getInputValue, setInputValue, setInputProperty);
                    this.atualizarDiferencaImpostosProdutoPorMulta(rowId, getInputValue, setInputValue, setInputProperty);
                }
            });
        }
    }
}
