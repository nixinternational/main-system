import { MoneyUtils } from '../utils/MoneyUtils.js';

/**
 * Serviço para calcular valores CPT (Carriage Paid To)
 * Aplica-se a Anápolis e Santa Catarina
 */
export class CalculadoraCPT {
    constructor(helperService) {
        this.helperService = helperService;
    }

    /**
     * Calcula valores CPT (USD e BRL)
     * @param {Function} getInputValue - Função para obter valores de input
     * @param {Function} setInputValue - Função para definir valores de input
     * @param {Function} getSelectorValue - Função para obter valor de seletor jQuery
     * @param {string} tipoProcesso - Tipo de processo ('maritimo', etc)
     * @returns {Object|null} - Objeto com valores CPT ou null se não aplicável
     */
    calcularCPT(getInputValue, setInputValue, getSelectorValue, tipoProcesso = 'maritimo') {
        const nacionalizacao = this.helperService.getNacionalizacaoAtual();
        
        // Só calcular se for Anápolis ou Santa Catarina e processo marítimo
        if ((nacionalizacao !== 'anapolis' && nacionalizacao !== 'santa_catarina') || tipoProcesso !== 'maritimo') {
            const camposCpt = getSelectorValue('#campos-cpt-anapolis');
            if (camposCpt && camposCpt.length > 0) {
                camposCpt.hide();
            }
            return null;
        }
        
        // Verificar se os campos existem
        const camposCpt = getSelectorValue('#campos-cpt-anapolis');
        if (!camposCpt || camposCpt.length === 0) {
            return null;
        }
        
        // Mostrar os campos
        camposCpt.show();
        
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
        
        // Calcular valor total Service Charges USD
        let valorTotalServiceChargesUsd = 0;
        const moedaServiceCharges = getInputValue('#service_charges_moeda');
        if (moedaServiceCharges && moedaServiceCharges !== 'USD') {
            if (rows && rows.length > 0) {
                rows.each(function() {
                    const rowId = this.id.replace('row-', '');
                    const serviceChargesUsd = MoneyUtils.parseMoney(getInputValue(`#service_charges-${rowId}`)) || 0;
                    valorTotalServiceChargesUsd += serviceChargesUsd;
                });
            }
        } else {
            valorTotalServiceChargesUsd = MoneyUtils.parseMoney(getInputValue('#service_charges_usd')) || 0;
        }
        
        // Obter frete internacional total USD
        const freteInternacionalTotalUsd = MoneyUtils.parseMoney(getInputValue('#frete_internacional_usd')) || 0;
        
        // Obter seguro internacional total USD
        const seguroInternacionalTotalUsd = MoneyUtils.parseMoney(getInputValue('#seguro_internacional_usd')) || 0;
        
        // Obter acréscimo frete dolar
        const acrescimoFreteDolar = MoneyUtils.parseMoney(getInputValue('#acrescimo_frete_usd')) || 0;
        
        // Calcular CPT USD
        const valorCptUsd = valorTotalFobUsd + valorTotalServiceChargesUsd + freteInternacionalTotalUsd + 
                          seguroInternacionalTotalUsd + acrescimoFreteDolar;
        
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
        
        // Calcular Valor CPT BRL
        const valorCptBrl = valorCptUsd * cotacaoDolarProcesso;
        
        // Atualizar campos
        setInputValue('#valor_cpt_usd', MoneyUtils.formatMoney(valorCptUsd, 2));
        setInputValue('#valor_cpt_brl', MoneyUtils.formatMoney(valorCptBrl, 2));
        
        return {
            valorCptUsd,
            valorCptBrl,
            cotacaoDolarProcesso
        };
    }
}
