import { MoneyUtils } from '../utils/MoneyUtils.js';

/**
 * Serviço para cálculos relacionados a moedas e conversões
 */
export class CalculadoraMoedas {
    /**
     * Converte um valor de uma moeda para USD
     * @param {number} valor - Valor a ser convertido
     * @param {string} moedaOrigem - Moeda de origem
     * @param {Object} cotações - Objeto com cotações de moedas
     * @returns {number} - Valor convertido para USD
     */
    converterParaUSD(valor, moedaOrigem, cotacoes) {
        if (moedaOrigem === 'USD') {
            return valor;
        }

        const cotacaoOrigem = cotacoes[moedaOrigem]?.venda || 1;
        const cotacaoUSD = cotacoes['USD']?.venda || 1;

        if (cotacaoOrigem === 0 || cotacaoUSD === 0) {
            return 0;
        }

        const moedaEmUSD = cotacaoOrigem / cotacaoUSD;
        return valor * moedaEmUSD;
    }

    /**
     * Converte um valor de USD para uma moeda de destino
     * @param {number} valorUSD - Valor em USD
     * @param {string} moedaDestino - Moeda de destino
     * @param {Object} cotacoes - Objeto com cotações de moedas
     * @returns {number} - Valor convertido
     */
    converterDeUSD(valorUSD, moedaDestino, cotacoes) {
        if (moedaDestino === 'USD') {
            return valorUSD;
        }

        const cotacaoDestino = cotacoes[moedaDestino]?.venda || 1;
        const cotacaoUSD = cotacoes['USD']?.venda || 1;

        if (cotacaoUSD === 0) {
            return 0;
        }

        return valorUSD * cotacaoDestino;
    }

    /**
     * Calcula diferença cambial para FOB
     * @param {number} fatorVlrFob - Fator de valor FOB
     * @param {number} difCambialFobProcesso - Diferença cambial FOB do processo
     * @param {number} fobTotal - FOB total da linha
     * @param {number} cotacaoUSD - Cotação do USD
     * @returns {number} - Diferença cambial FOB
     */
    calcularDiferencaCambialFOB(fatorVlrFob, difCambialFobProcesso, fobTotal, cotacaoUSD) {
        const fobTotalBrl = fobTotal * cotacaoUSD;
        return (fatorVlrFob * difCambialFobProcesso) - fobTotalBrl;
    }

    /**
     * Calcula diferença cambial para frete
     * @param {number} fatorPeso - Fator de peso
     * @param {number} difCambialFreteProcesso - Diferença cambial frete do processo
     * @param {number} freteUsd - Frete em USD
     * @param {number} cotacaoUSD - Cotação do USD
     * @returns {number} - Diferença cambial frete
     */
    calcularDiferencaCambialFrete(fatorPeso, difCambialFreteProcesso, freteUsd, cotacaoUSD) {
        const freteBrl = freteUsd * cotacaoUSD;
        return (fatorPeso * difCambialFreteProcesso) - freteBrl;
    }

    /**
     * Converte valor para USD e BRL
     * @param {string} inputId - ID do input base
     * @param {Function} getInputValue - Função para obter valores de input
     * @param {Function} setInputValue - Função para definir valores de input
     * @param {Object} cotacoes - Cotações de moedas
     * @returns {Object} - { valorUSD, valorBRL }
     */
    convertToUSDAndBRL(inputId, getInputValue, setInputValue, cotacoes) {
        const valor = MoneyUtils.parseMoney(getInputValue(`#${inputId}`)) || 0;
        const codigoMoeda = getInputValue(`#${inputId}_moeda`);
        
        let cotacaoMoeda = MoneyUtils.parseMoney(getInputValue(`#cotacao_${inputId}`));
        
        if (!cotacaoMoeda || cotacaoMoeda === 0) {
            cotacaoMoeda = (cotacoes && cotacoes[codigoMoeda]) ? cotacoes[codigoMoeda].venda : 0;
        }

        if (!codigoMoeda) {
            setInputValue(`#${inputId}_usd`, '');
            setInputValue(`#${inputId}_brl`, '');
            return { valorUSD: 0, valorBRL: 0 };
        }

        if (valor === 0 || !valor) {
            setInputValue(`#${inputId}_usd`, '0,0000000');
            setInputValue(`#${inputId}_brl`, '0,0000000');
            return { valorUSD: 0, valorBRL: 0 };
        }

        let valorUSD = 0;
        let valorBRL = 0;
        const cotacaoUSD = (cotacoes && cotacoes['USD']) ? cotacoes['USD'].venda : 1;

        if (codigoMoeda === 'USD') {
            valorUSD = valor;
            valorBRL = valor * cotacaoUSD;
        } else {
            if (!cotacaoMoeda || cotacaoMoeda === 0) {
                setInputValue(`#${inputId}_usd`, '');
                setInputValue(`#${inputId}_brl`, '');
                return { valorUSD: 0, valorBRL: 0 };
            }

            valorBRL = valor * cotacaoMoeda;
            
            if (cotacaoUSD > 0) {
                valorUSD = valorBRL / cotacaoUSD;
            }
        }

        setInputValue(`#${inputId}_usd`, MoneyUtils.formatMoney(valorUSD, 2));
        setInputValue(`#${inputId}_brl`, MoneyUtils.formatMoney(valorBRL, 2));
        
        return { valorUSD, valorBRL };
    }
}
