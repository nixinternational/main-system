import { DOMUtils } from '../utils/DOMUtils.js';
import { MoneyUtils } from '../utils/MoneyUtils.js';

/**
 * Componente de tabela virtualizada para performance
 * Renderiza apenas itens visíveis + buffer para scroll suave
 */
export class TabelaVirtualizada {
    constructor(store, containerSelector = '#productsBody', options = {}) {
        this.store = store;
        this.containerSelector = containerSelector;
        this.options = {
            itemHeight: options.itemHeight || 50, // Altura estimada de cada linha
            buffer: options.buffer || 5, // Número de itens extras a renderizar
            threshold: options.threshold || 100, // Número mínimo de itens para ativar virtualização
            ...options
        };
        this.container = null;
        this.visibleItems = [];
        this.scrollTop = 0;
        this.totalHeight = 0;
        this.isVirtualized = false;
    }

    /**
     * Inicializa a virtualização
     */
    init() {
        if (typeof document === 'undefined') {
            return;
        }

        this.container = document.querySelector(this.containerSelector);
        if (!this.container) {
            console.warn(`Container ${this.containerSelector} não encontrado`);
            return;
        }

        // Verificar se precisa virtualizar
        const totalItems = this.getTotalItems();
        if (totalItems < this.options.threshold) {
            this.isVirtualized = false;
            return;
        }

        this.isVirtualized = true;
        this.setupScrollListener();
        this.update();
    }

    /**
     * Obtém o número total de itens
     * @returns {number} - Total de itens
     */
    getTotalItems() {
        if (!this.container) {
            return 0;
        }
        return this.container.querySelectorAll('tr.linhas-input').length;
    }

    /**
     * Configura listener de scroll
     */
    setupScrollListener() {
        if (!this.container) {
            return;
        }

        if (!this.container) {
            return;
        }

        const scrollParent = this.container.closest('.table-products-container') || 
                           this.container.parentElement;

        if (scrollParent && scrollParent.addEventListener) {
            scrollParent.addEventListener('scroll', () => {
                this.handleScroll();
            }, { passive: true });
        }
    }

    /**
     * Manipula evento de scroll
     */
    handleScroll() {
        if (!this.isVirtualized) {
            return;
        }

        if (!this.container) {
            return;
        }

        const scrollParent = this.container.closest('.table-products-container') || 
                           this.container.parentElement;
        
        if (scrollParent && scrollParent.scrollTop !== undefined) {
            this.scrollTop = scrollParent.scrollTop;
            this.update();
        }
    }

    /**
     * Calcula quais itens devem ser visíveis
     * @returns {Object} - Objeto com índices de início e fim
     */
    calculateVisibleRange() {
        if (!this.container) {
            return { start: 0, end: 0 };
        }

        const containerHeight = this.container.clientHeight || 500;
        const startIndex = Math.floor(this.scrollTop / this.options.itemHeight);
        const endIndex = Math.ceil((this.scrollTop + containerHeight) / this.options.itemHeight);

        return {
            start: Math.max(0, startIndex - this.options.buffer),
            end: Math.min(this.getTotalItems(), endIndex + this.options.buffer)
        };
    }

    /**
     * Atualiza a renderização baseado no scroll
     */
    update() {
        if (!this.isVirtualized || !this.container) {
            return;
        }

        const { start, end } = this.calculateVisibleRange();
        const items = Array.from(this.container.querySelectorAll('tr.linhas-input'));

        // Ocultar itens fora do range visível
        items.forEach((item, index) => {
            if (index < start || index > end) {
                if (item.style.display !== 'none') {
                    item.style.display = 'none';
                }
            } else {
                if (item.style.display === 'none') {
                    item.style.display = '';
                }
            }
        });

        // Atualizar altura total do container para manter scrollbar correta
        this.totalHeight = this.getTotalItems() * this.options.itemHeight;
        if (this.container.style.minHeight !== `${this.totalHeight}px`) {
            this.container.style.minHeight = `${this.totalHeight}px`;
        }
    }

    /**
     * Renderiza uma linha específica
     * @param {number} index - Índice da linha
     * @param {Object} dados - Dados da linha
     */
    renderRow(index, dados) {
        // Esta função seria chamada quando necessário renderizar uma linha
        // Por enquanto, apenas atualiza a visibilidade
        this.update();
    }

    /**
     * Atualiza todas as linhas visíveis
     * @param {Array<Object>} dados - Array de dados por linha
     */
    updateVisibleRows(dados) {
        if (!this.isVirtualized) {
            return;
        }

        const { start, end } = this.calculateVisibleRange();
        const items = Array.from(this.container.querySelectorAll('tr.linhas-input'));

        items.forEach((item, index) => {
            if (index >= start && index <= end && dados[index]) {
                // Atualizar dados da linha se necessário
                // Implementação específica dependeria da estrutura HTML
            }
        });
    }

    /**
     * Desativa virtualização
     */
    destroy() {
        if (!this.container) {
            return;
        }

        // Mostrar todos os itens
        const items = this.container.querySelectorAll('tr.linhas-input');
        items.forEach(item => {
            item.style.display = '';
        });

        this.container.style.minHeight = '';
        this.isVirtualized = false;
    }

    /**
     * Verifica se a virtualização está ativa
     * @returns {boolean}
     */
    isActive() {
        return this.isVirtualized;
    }
}
