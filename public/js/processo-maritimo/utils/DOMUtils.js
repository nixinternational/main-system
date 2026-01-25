/**
 * Utilitários para manipulação do DOM
 */
export class DOMUtils {
    /**
     * Obtém o valor de um input jQuery
     * @param {string} selector - Seletor jQuery
     * @returns {string} - Valor do input
     */
    static getInputValue(selector) {
        if (typeof $ !== 'undefined' && $(selector).length > 0) {
            return $(selector).val();
        }
        const element = document.querySelector(selector);
        return element ? element.value : '';
    }

    /**
     * Define o valor de um input jQuery
     * @param {string} selector - Seletor jQuery
     * @param {*} value - Valor a ser definido
     */
    static setInputValue(selector, value) {
        if (typeof $ !== 'undefined') {
            const $element = $(selector);
            // Se for um select com Select2, usar trigger para atualizar corretamente
            if ($element.length && $element.is('select') && $element.data('select2')) {
                $element.val(value).trigger('change.select2');
            } else {
                $element.val(value);
            }
        } else {
            const element = document.querySelector(selector);
            if (element) {
                element.value = value;
            }
        }
    }

    /**
     * Cria um fragmento de documento para atualizações em batch
     * @returns {DocumentFragment} - Fragmento de documento
     */
    static createFragment() {
        return document.createDocumentFragment();
    }

    /**
     * Cache de seletores jQuery
     */
    static selectorCache = new Map();

    /**
     * Obtém um seletor jQuery com cache
     * @param {string} selector - Seletor
     * @returns {jQuery} - Objeto jQuery
     */
    static getCachedSelector(selector) {
        if (!this.selectorCache.has(selector)) {
            if (typeof $ !== 'undefined') {
                this.selectorCache.set(selector, $(selector));
            }
        }
        return this.selectorCache.get(selector);
    }

    /**
     * Limpa o cache de seletores
     */
    static clearCache() {
        this.selectorCache.clear();
    }

    /**
     * Toggle visibilidade de colunas por classe
     * @param {string} selector - Seletor de classe
     * @param {boolean} mostrar - Se deve mostrar ou ocultar
     */
    static toggleColunas(selector, mostrar) {
        if (typeof $ !== 'undefined') {
            $(selector).each(function() {
                $(this).toggle(mostrar);
            });
        } else {
            const elementos = document.querySelectorAll(selector);
            elementos.forEach(el => {
                el.style.display = mostrar ? '' : 'none';
            });
        }
    }
}
