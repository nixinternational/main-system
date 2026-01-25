/**
 * Container simples de Injeção de Dependências
 */
export class ContainerDependencias {
    constructor() {
        this.services = {};
        this.singletons = {};
    }

    /**
     * Registra um serviço
     * @param {string} name - Nome do serviço
     * @param {Function|Object} service - Classe ou instância do serviço
     * @param {boolean} singleton - Se deve ser singleton
     */
    register(name, service, singleton = true) {
        this.services[name] = {
            service,
            singleton
        };

        if (singleton && typeof service === 'function') {
            // Para singletons, criar instância imediatamente
            this.singletons[name] = null;
        }
    }

    /**
     * Resolve um serviço
     * @param {string} name - Nome do serviço
     * @returns {*} - Instância do serviço
     */
    resolve(name) {
        if (!this.services[name]) {
            throw new Error(`Serviço ${name} não registrado`);
        }

        const { service, singleton } = this.services[name];

        if (singleton) {
            if (!this.singletons[name]) {
                if (typeof service === 'function') {
                    this.singletons[name] = new service();
                } else {
                    this.singletons[name] = service;
                }
            }
            return this.singletons[name];
        }

        if (typeof service === 'function') {
            return new service();
        }

        return service;
    }

    /**
     * Verifica se um serviço está registrado
     * @param {string} name - Nome do serviço
     * @returns {boolean}
     */
    has(name) {
        return !!this.services[name];
    }

    /**
     * Limpa todos os serviços
     */
    clear() {
        this.services = {};
        this.singletons = {};
    }
}

// Instância singleton
let containerInstance = null;

/**
 * Obtém a instância singleton do Container
 * @returns {ContainerDependencias} - Instância do Container
 */
export function getContainer() {
    if (!containerInstance) {
        containerInstance = new ContainerDependencias();
    }
    return containerInstance;
}
