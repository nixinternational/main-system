/**
 * Event Bus para comunicação desacoplada entre módulos
 * Implementa pub/sub com debouncing e fila de eventos
 */
export class EventBus {
    constructor() {
        this.events = {};
        this.debounceTimers = {};
        this.eventQueue = [];
        this.processingQueue = false;
    }

    /**
     * Registra um listener para um evento
     * @param {string} event - Nome do evento
     * @param {Function} callback - Função callback
     * @param {Object} options - Opções (debounce, priority)
     * @returns {Function} - Função para remover o listener
     */
    on(event, callback, options = {}) {
        if (!this.events[event]) {
            this.events[event] = [];
        }

        const listener = {
            callback,
            debounce: options.debounce || 0,
            priority: options.priority || 0,
            once: options.once || false
        };

        this.events[event].push(listener);
        this.events[event].sort((a, b) => b.priority - a.priority);

        // Retorna função para remover o listener
        return () => {
            this.events[event] = this.events[event].filter(l => l !== listener);
        };
    }

    /**
     * Registra um listener que executa apenas uma vez
     * @param {string} event - Nome do evento
     * @param {Function} callback - Função callback
     * @param {Object} options - Opções
     */
    once(event, callback, options = {}) {
        return this.on(event, callback, { ...options, once: true });
    }

    /**
     * Emite um evento
     * @param {string} event - Nome do evento
     * @param {*} data - Dados do evento
     * @param {Object} options - Opções (immediate, queue)
     */
    emit(event, data, options = {}) {
        if (options.queue && !options.immediate) {
            this.eventQueue.push({ event, data, options });
            this.processQueue();
            return;
        }

        if (!this.events[event]) {
            return;
        }

        const listeners = [...this.events[event]];

        listeners.forEach(listener => {
            if (listener.debounce > 0 && !options.immediate) {
                this.debounceEmit(event, listener, data);
            } else {
                this.executeListener(listener, data, event);
            }
        });
    }

    /**
     * Executa um listener com debounce
     * @param {string} event - Nome do evento
     * @param {Object} listener - Listener
     * @param {*} data - Dados
     */
    debounceEmit(event, listener, data) {
        const key = `${event}_${listener.callback.name || 'anonymous'}`;
        
        if (this.debounceTimers[key]) {
            clearTimeout(this.debounceTimers[key]);
        }

        this.debounceTimers[key] = setTimeout(() => {
            this.executeListener(listener, data, event);
            delete this.debounceTimers[key];
        }, listener.debounce);
    }

    /**
     * Executa um listener
     * @param {Object} listener - Listener
     * @param {*} data - Dados
     * @param {string} event - Nome do evento
     */
    executeListener(listener, data, event) {
        try {
            listener.callback(data, event);
            
            if (listener.once) {
                this.events[event] = this.events[event].filter(l => l !== listener);
            }
        } catch (error) {
            console.error(`Erro ao executar listener do evento ${event}:`, error);
        }
    }

    /**
     * Processa a fila de eventos
     */
    processQueue() {
        if (this.processingQueue || this.eventQueue.length === 0) {
            return;
        }

        this.processingQueue = true;

        while (this.eventQueue.length > 0) {
            const { event, data, options } = this.eventQueue.shift();
            this.emit(event, data, { ...options, immediate: true });
        }

        this.processingQueue = false;
    }

    /**
     * Remove todos os listeners de um evento
     * @param {string} event - Nome do evento
     */
    off(event) {
        if (event) {
            delete this.events[event];
        } else {
            this.events = {};
        }
    }

    /**
     * Limpa todos os timers de debounce
     */
    clearDebounce() {
        Object.values(this.debounceTimers).forEach(timer => clearTimeout(timer));
        this.debounceTimers = {};
    }
}

// Instância singleton
let eventBusInstance = null;

/**
 * Obtém a instância singleton do EventBus
 * @returns {EventBus} - Instância do EventBus
 */
export function getEventBus() {
    if (!eventBusInstance) {
        eventBusInstance = new EventBus();
    }
    return eventBusInstance;
}
