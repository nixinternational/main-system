import { MoneyUtils } from '../utils/MoneyUtils.js';
import { Validador } from '../utils/Validador.js';
import { DOMUtils } from '../utils/DOMUtils.js';

/**
 * Componente para atualizar campos de diferença cambial
 */
export class AtualizadorCambial {
    constructor(helperService) {
        this.helperService = helperService;
    }

    /**
     * Atualiza campos de diferença cambial para todas as linhas
     * @param {Function} getInputValue - Função para obter valores de input
     * @param {Function} setInputValue - Função para definir valores de input
     * @param {Function} getSelectorValue - Função para obter seletor jQuery
     */
    atualizarCamposCambial(getInputValue, setInputValue, getSelectorValue) {
        const campos = this.helperService.getCamposDiferencaCambial();
        const lengthTable = getSelectorValue('.linhas-input').length;
        const totalPesoLiq = this.helperService.calcularPesoTotal();
        const fobTotalGeral = this.helperService.calcularFobTotalGeral();
        
        const cotacaoMoedaProcesso = getInputValue('#cotacao_moeda_processo');
        const dolarHoje = getInputValue('#dolarHoje');
        let moedasObject = {};
        
        try {
            if (cotacaoMoedaProcesso) {
                moedasObject = JSON.parse(cotacaoMoedaProcesso);
            } else if (dolarHoje) {
                moedasObject = JSON.parse(dolarHoje);
            }
        } catch (e) {
            console.warn('Erro ao parsear cotações:', e);
            moedasObject = { USD: { venda: 1 } };
        }
        
        const moedaDolar = moedasObject['USD']?.venda || 1;
        const dolar = MoneyUtils.parseMoney(moedaDolar);
        
        let valorFreteInternacional = MoneyUtils.parseMoney(getInputValue('#frete_internacional')) || 0;
        const moedaFrete = getInputValue('#frete_internacional_moeda');
        let valorFreteInternacionalDolar = 0;

        if (moedaFrete && moedaFrete !== 'USD') {
            const cotacaoesProcesso = this.helperService.getCotacaoesProcesso();
            const cotacaoMoedaFloat = MoneyUtils.parseMoney(getInputValue('#cotacao_frete_internacional')) || 0;
            const cotacaoUSD = cotacaoesProcesso['USD']?.venda ?? 1;
            const moedaEmUSD = cotacaoMoedaFloat / cotacaoUSD;
            valorFreteInternacionalDolar = valorFreteInternacional * moedaEmUSD;
        } else {
            valorFreteInternacionalDolar = valorFreteInternacional;
        }

        for (let rowId = 0; rowId < lengthTable; rowId++) {
            const valoresBase = this.helperService.obterValoresBase(rowId);
            const { pesoTotal, fobUnitario, quantidade } = valoresBase;
            
            const fatorPesoRow = this.helperService.recalcularFatorPeso(totalPesoLiq, rowId);
            const freteUsdInt = valorFreteInternacionalDolar * fatorPesoRow;
            const fobTotal = fobUnitario * quantidade;
            const fatorVlrFob_AX = fobTotalGeral > 0 ? fobTotal / fobTotalGeral : 0;
            
            const dif_cambial_frete_processo = MoneyUtils.parseMoney(getInputValue('#diferenca_cambial_frete')) || 0;
            const dif_cambial_fob_processo = MoneyUtils.parseMoney(getInputValue('#diferenca_cambial_fob')) || 0;
            
            let diferenca_cambial_frete = (freteUsdInt * dif_cambial_frete_processo) - (freteUsdInt * dolar);
            diferenca_cambial_frete = Validador.validarDiferencaCambialFrete(diferenca_cambial_frete);
            
            // Calcular diferenca_cambial_fob conforme nacionalização
            const nacionalizacaoCambial = this.helperService.getNacionalizacaoAtual();
            let diferenca_cambial_fob;
            
            if (nacionalizacaoCambial === 'mato_grosso') {
                // Para Mato Grosso: (diferenca_cambial_fob_cabecalho * fator_vlr_fob) - (fob_total_brl + frete_brl + seguro_brl)
                const fobTotalBrl = fobTotal * dolar;
                const freteBrl = freteUsdInt * dolar;
                // Calcular seguro proporcional ao FOB
                const seguroIntUsdRow = this.helperService.calcularSeguro(fobTotal, fobTotalGeral);
                const seguroBrl = seguroIntUsdRow * dolar;
                diferenca_cambial_fob = (fatorVlrFob_AX * dif_cambial_fob_processo) - (fobTotalBrl + freteBrl + seguroBrl);
            } else {
                diferenca_cambial_fob = (fatorVlrFob_AX * dif_cambial_fob_processo) - (fobTotal * dolar);
            }

            if (diferenca_cambial_frete === 0 || isNaN(diferenca_cambial_frete) || !isFinite(diferenca_cambial_frete) || diferenca_cambial_frete < 0) {
                setInputValue(`#diferenca_cambial_frete-${rowId}`, '');
            } else {
                setInputValue(`#diferenca_cambial_frete-${rowId}`, MoneyUtils.formatMoney(diferenca_cambial_frete, 2));
            }
            
            setInputValue(`#diferenca_cambial_fob-${rowId}`, MoneyUtils.formatMoney(diferenca_cambial_fob, 2));
        }
    }
}
