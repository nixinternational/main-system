import { MoneyUtils } from '../utils/MoneyUtils.js';

/**
 * Serviço para calcular colunas específicas de Mato Grosso
 * EXPORTADOR, TRIBUTOS, DESPESAS, TOTAL PAGO, PERCENTUAL S/FOB
 */
export class CalculadoraMatoGrosso {
    constructor(helperService) {
        this.helperService = helperService;
    }

    /**
     * Calcula colunas: EXPORTADOR, TRIBUTOS, DESPESAS, TOTAL PAGO, PERCENTUAL S/FOB
     * @param {string} rowId - ID da linha
     * @param {Object} valoresBrutos - Valores brutos da linha
     * @param {Function} getInputValue - Função para obter valores de input
     * @param {Function} setInputValue - Função para definir valores de input
     */
    calcularColunasExportadorTributosDespesas(rowId, valoresBrutos, getInputValue, setInputValue) {
        const nacionalizacao = this.helperService.getNacionalizacaoAtual();
        if (nacionalizacao !== 'mato_grosso') {
            return;
        }

        // 1. EXPORTADOR = DIF CAMBIAL FOB (cabecalho) × FATOR VLR FOB
        const diferencaCambialFobCabecalho = MoneyUtils.parseMoney(getInputValue('#diferenca_cambial_fob')) || 0;
        const fatorValorFob = MoneyUtils.parseMoney(getInputValue(`#fator_valor_fob-${rowId}`)) || 0;
        const exportador = diferencaCambialFobCabecalho * fatorValorFob;

        // 2. TRIBUTOS = VLR II + VLR IPI + VLR PIS + VLR COFINS + vlr_icms_st_mg
        const valorII = valoresBrutos.valor_ii || MoneyUtils.parseMoney(getInputValue(`#valor_ii-${rowId}`)) || 0;
        const valorIPI = valoresBrutos.valor_ipi || MoneyUtils.parseMoney(getInputValue(`#valor_ipi-${rowId}`)) || 0;
        const valorPIS = valoresBrutos.valor_pis || MoneyUtils.parseMoney(getInputValue(`#valor_pis-${rowId}`)) || 0;
        const valorCOFINS = valoresBrutos.valor_cofins || MoneyUtils.parseMoney(getInputValue(`#valor_cofins-${rowId}`)) || 0;
        const vlrIcmsStMg = valoresBrutos.vlr_icms_st_mg || 0;
        const tributos = valorII + valorIPI + valorPIS + valorCOFINS + vlrIcmsStMg;

        // 3. DESPESAS = SOMA de todos os campos de despesas
        const valoresBrutosCamposExternos = window.valoresBrutosCamposExternos || {};
        
        const multa = valoresBrutos.multa || MoneyUtils.parseMoney(getInputValue(`#multa-${rowId}`)) || 0;
        const txDefLi = this._obterValorCampoExterno('tx_def_li', rowId, valoresBrutosCamposExternos, getInputValue);
        const taxaSiscomex = valoresBrutos.taxa_siscomex || MoneyUtils.parseMoney(getInputValue(`#taxa_siscomex-${rowId}`)) || 0;
        const outrasTaxasAgente = this._obterValorCampoExterno('outras_taxas_agente', rowId, valoresBrutosCamposExternos, getInputValue);
        const liberacaoBl = this._obterValorCampoExterno('liberacao_bl', rowId, valoresBrutosCamposExternos, getInputValue);
        const desconsolidacao = this._obterValorCampoExterno('desconsolidacao', rowId, valoresBrutosCamposExternos, getInputValue);
        const ispsCode = this._obterValorCampoExterno('isps_code', rowId, valoresBrutosCamposExternos, getInputValue);
        const handling = this._obterValorCampoExterno('handling', rowId, valoresBrutosCamposExternos, getInputValue);
        const capatazia = this._obterValorCampoExterno('capatazia', rowId, valoresBrutosCamposExternos, getInputValue);
        const afrmm = this._obterValorCampoExterno('afrmm', rowId, valoresBrutosCamposExternos, getInputValue);
        const armazenagemSts = this._obterValorCampoExterno('armazenagem_sts', rowId, valoresBrutosCamposExternos, getInputValue);
        const freteStsCgb = this._obterValorCampoExterno('frete_sts_cgb', rowId, valoresBrutosCamposExternos, getInputValue);
        const diarias = this._obterValorCampoExterno('diarias', rowId, valoresBrutosCamposExternos, getInputValue);
        const sda = this._obterValorCampoExterno('sda', rowId, valoresBrutosCamposExternos, getInputValue);
        const repSts = this._obterValorCampoExterno('rep_sts', rowId, valoresBrutosCamposExternos, getInputValue);
        const armazCgb = this._obterValorCampoExterno('armaz_cgb', rowId, valoresBrutosCamposExternos, getInputValue);
        const repCgb = this._obterValorCampoExterno('rep_cgb', rowId, valoresBrutosCamposExternos, getInputValue);
        const demurrage = this._obterValorCampoExterno('demurrage', rowId, valoresBrutosCamposExternos, getInputValue);
        const liDtaHonorNix = this._obterValorCampoExterno('li_dta_honor_nix', rowId, valoresBrutosCamposExternos, getInputValue);
        const honorariosNix = this._obterValorCampoExterno('honorarios_nix', rowId, valoresBrutosCamposExternos, getInputValue);

        const despesas = multa + txDefLi + taxaSiscomex + outrasTaxasAgente + liberacaoBl + desconsolidacao +
            ispsCode + handling + capatazia + afrmm + armazenagemSts + freteStsCgb + diarias + sda +
            repSts + armazCgb + repCgb + demurrage + liDtaHonorNix + honorariosNix;

        // 4. TOTAL PAGO = EXPORTADOR + TRIBUTOS + DESPESAS
        const totalPago = exportador + tributos + despesas;

        // 5. PERCENTUAL S/FOB = (TOTAL PAGO / VLR TOTAL FOB R$ LINHA) / 100
        const fobTotalBrl = valoresBrutos.fob_total_brl || MoneyUtils.parseMoney(getInputValue(`#fob_total_brl-${rowId}`)) || 0;
        let percentualSFob = 0;
        if (fobTotalBrl > 0) {
            percentualSFob = (totalPago / fobTotalBrl) / 100;
        }

        // Armazenar valores brutos
        if (!window.valoresBrutosPorLinha) {
            window.valoresBrutosPorLinha = {};
        }
        if (!window.valoresBrutosPorLinha[rowId]) {
            window.valoresBrutosPorLinha[rowId] = {};
        }
        window.valoresBrutosPorLinha[rowId].exportador_mg = exportador;
        window.valoresBrutosPorLinha[rowId].tributos_mg = tributos;
        window.valoresBrutosPorLinha[rowId].despesas_mg = despesas;
        window.valoresBrutosPorLinha[rowId].total_pago_mg = totalPago;
        window.valoresBrutosPorLinha[rowId].percentual_s_fob_mg = percentualSFob;
        
        // Atualizar campos diretamente após calcular
        setInputValue(`#exportador_mg-${rowId}`, MoneyUtils.formatMoney(exportador, 2));
        setInputValue(`#tributos_mg-${rowId}`, MoneyUtils.formatMoney(tributos, 2));
        setInputValue(`#despesas_mg-${rowId}`, MoneyUtils.formatMoney(despesas, 2));
        setInputValue(`#total_pago_mg-${rowId}`, MoneyUtils.formatMoney(totalPago, 2));
        setInputValue(`#percentual_s_fob_mg-${rowId}`, MoneyUtils.formatPercentage(percentualSFob));
    }

    /**
     * Obtém valor de campo externo (prioriza valor distribuído)
     * @private
     */
    _obterValorCampoExterno(campo, rowId, valoresBrutosCamposExternos, getInputValue) {
        if (valoresBrutosCamposExternos[campo] && valoresBrutosCamposExternos[campo][rowId] !== undefined) {
            return valoresBrutosCamposExternos[campo][rowId];
        }
        return MoneyUtils.parseMoney(getInputValue(`#${campo}-${rowId}`)) || 0;
    }
}
