import { DOMUtils } from './DOMUtils.js';

/**
 * Utilitários para limpeza de campos
 */
export class LimpezaCampos {
    /**
     * Limpa campos específicos
     * @param {Array<string>} campos - Array de nomes de campos a serem limpos
     * @param {Function} setInputValue - Função para definir valores de input
     */
    static limparCamposEspecificos(campos, setInputValue) {
        if (!campos || campos.length === 0) return;

        if (typeof $ !== 'undefined') {
            campos.forEach(campo => {
                $(`#${campo}`).val('');
                $(`[id^="${campo}-"]`).val('');
            });
        } else {
            campos.forEach(campo => {
                const elemento = document.querySelector(`#${campo}`);
                if (elemento) elemento.value = '';
                
                const elementos = document.querySelectorAll(`[id^="${campo}-"]`);
                elementos.forEach(el => el.value = '');
            });
        }
    }
}
