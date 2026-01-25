import { MoneyUtils } from '../utils/MoneyUtils.js';

/**
 * Serviço para calcular valores CIF (Cost, Insurance and Freight)
 * Aplica-se a Mato Grosso
 */
export class CalculadoraCIF {
    constructor(helperService) {
        this.helperService = helperService;
    }

    /**
     * Calcula valores CIF (USD e BRL)
     * @param {Function} getInputValue - Função para obter valores de input
     * @param {Function} setInputValue - Função para definir valores de input
     * @param {Function} getSelectorValue - Função para obter valor de seletor jQuery
     * @param {string} tipoProcesso - Tipo de processo ('maritimo', etc)
     * @returns {Object|null} - Objeto com valores CIF ou null se não aplicável
     */
    calcularCIF(getInputValue, setInputValue, getSelectorValue, tipoProcesso = 'maritimo') {
        const nacionalizacao = this.helperService.getNacionalizacaoAtual();
        
        // Só calcular se for Mato Grosso e processo marítimo
        if (nacionalizacao !== 'mato_grosso' || tipoProcesso !== 'maritimo') {
            const camposCif = getSelectorValue('#campos-cif-mato-grosso');
            if (camposCif && camposCif.length > 0) {
                camposCif.hide();
            }
            return null;
        }
        
        // Verificar se os campos existem
        const camposCif = getSelectorValue('#campos-cif-mato-grosso');
        if (!camposCif || camposCif.length === 0) {
            return null;
        }
        
        // Mostrar os campos
        camposCif.show();
        
        // Obter valores totais do processo
        const rows = getSelectorValue('#productsBody tr:not(.separador-adicao)');
        
        // Calcular valor total FOB USD
        let valorTotalFobUsd = 0;
        if (rows && rows.length > 0) {
            rows.each(function() {
                const rowId = this.id.replace('row-', '');
                const fobTotalUsd = MoneyUtils.parseMoney(getInputValue(`#fob_total_usd-${rowId}`)) || 0;
                valorTotalFobUsd += fobTotalUsd;
            });
        }
        
        // Obter frete internacional total USD
        const freteInternacionalTotalUsd = MoneyUtils.parseMoney(getInputValue('#frete_internacional_usd')) || 0;
        
        // Obter seguro internacional total USD
        const seguroInternacionalTotalUsd = MoneyUtils.parseMoney(getInputValue('#seguro_internacional_usd')) || 0;
        
        // Obter acréscimo frete dolar
        const acrescimoFreteDolar = MoneyUtils.parseMoney(getInputValue('#acrescimo_frete_usd')) || 0;
        
        // CIF = FOB Total + Frete Internacional + Seguro + Acréscimo Frete
        const valorCifUsd = valorTotalFobUsd + freteInternacionalTotalUsd + seguroInternacionalTotalUsd + acrescimoFreteDolar;
        
        // Obter cotação do dólar do processo
        const cotacoesProcesso = this.helperService.getCotacaoesProcesso();
        let cotacaoDolarProcesso = 1;
        if (cotacoesProcesso && cotacoesProcesso['USD'] && cotacoesProcesso['USD'].venda) {
            cotacaoDolarProcesso = cotacoesProcesso['USD'].venda;
        } else {
            // Tentar obter do campo dolarHoje
            const dolarHoje = getInputValue('#dolarHoje');
            if (dolarHoje) {
                try {
                    const dolarObj = JSON.parse(dolarHoje);
                    if (dolarObj['USD'] && dolarObj['USD'].venda) {
                        cotacaoDolarProcesso = dolarObj['USD'].venda;
                    }
                } catch (e) {
                    // Se não conseguir parsear, usar 1 como padrão
                }
            }
        }
        
        // Calcular Valor CIF BRL
        const valorCifBrl = valorCifUsd * cotacaoDolarProcesso;
        
        // Atualizar campos
        setInputValue('#valor_cif_usd', MoneyUtils.formatMoney(valorCifUsd, 2));
        setInputValue('#valor_cif_brl', MoneyUtils.formatMoney(valorCifBrl, 2));
        
        return {
            valorCifUsd,
            valorCifBrl,
            cotacaoDolarProcesso
        };
    }
}
