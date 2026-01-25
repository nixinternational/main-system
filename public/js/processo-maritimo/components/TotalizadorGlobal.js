import { MoneyUtils } from '../utils/MoneyUtils.js';
import { DOMUtils } from '../utils/DOMUtils.js';

/**
 * Componente para atualizar totalizadores globais do processo
 */
export class TotalizadorGlobal {
    /**
     * Atualiza totais globais (FOB Total Processo)
     * @param {number} fobTotalGeral - FOB total geral em USD
     * @param {number} dolar - Cotação do dólar
     */
    atualizarTotaisGlobais(fobTotalGeral, dolar) {
        const fobTotalProcesso = document.querySelector('#fobTotalProcesso');
        const fobTotalProcessoReal = document.querySelector('#fobTotalProcessoReal');
        
        if (fobTotalProcesso) {
            fobTotalProcesso.textContent = MoneyUtils.formatMoney(fobTotalGeral);
        }
        
        if (fobTotalProcessoReal) {
            fobTotalProcessoReal.textContent = MoneyUtils.formatMoney(fobTotalGeral * dolar);
        }
    }
}
