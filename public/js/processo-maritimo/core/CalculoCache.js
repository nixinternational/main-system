/**
 * Cache de cálculos para evitar reprocessamento
 * Usa Map com chave baseada em hash das dependências
 */
export class CalculoCache {
    constructor() {
        this.cache = new Map();
        this.dependencias = new Map(); // Mapeia chave -> array de dependências
        this.invalidacoes = new Set(); // Set de chaves invalidadas
    }

    /**
     * Gera uma chave de cache baseada nas dependências
     * @param {string} tipo - Tipo de cálculo
     * @param {Object} dependencias - Objeto com dependências
     * @returns {string} - Chave do cache
     */
    gerarChave(tipo, dependencias) {
        const dependenciasStr = JSON.stringify(dependencias, Object.keys(dependencias).sort());
        return `${tipo}:${this.hash(dependenciasStr)}`;
    }

    /**
     * Gera hash simples de uma string
     * @param {string} str - String a ser hasheada
     * @returns {string} - Hash
     */
    hash(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            const char = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash; // Convert to 32bit integer
        }
        return hash.toString(36);
    }

    /**
     * Obtém um valor do cache
     * @param {string} chave - Chave do cache
     * @returns {*} - Valor em cache ou undefined
     */
    obter(chave) {
        if (this.invalidacoes.has(chave)) {
            return undefined;
        }
        return this.cache.get(chave);
    }

    /**
     * Armazena um valor no cache
     * @param {string} chave - Chave do cache
     * @param {*} valor - Valor a ser armazenado
     * @param {Array<string>} dependencias - Array de dependências (ex: ['fob_total', 'quantidade'])
     */
    armazenar(chave, valor, dependencias = []) {
        this.cache.set(chave, valor);
        this.dependencias.set(chave, dependencias);
        this.invalidacoes.delete(chave);
    }

    /**
     * Invalida cache baseado em uma dependência
     * @param {string} dependencia - Nome da dependência (ex: 'fob_total')
     */
    invalidarPorDependencia(dependencia) {
        this.dependencias.forEach((deps, chave) => {
            if (deps.includes(dependencia)) {
                this.invalidacoes.add(chave);
            }
        });
    }

    /**
     * Invalida uma chave específica
     * @param {string} chave - Chave a ser invalidada
     */
    invalidar(chave) {
        this.invalidacoes.add(chave);
    }

    /**
     * Limpa todo o cache
     */
    limpar() {
        this.cache.clear();
        this.dependencias.clear();
        this.invalidacoes.clear();
    }

    /**
     * Obtém estatísticas do cache
     * @returns {Object} - Estatísticas
     */
    getEstatisticas() {
        return {
            total: this.cache.size,
            invalidadas: this.invalidacoes.size,
            validas: this.cache.size - this.invalidacoes.size
        };
    }
}

// Instância singleton
let cacheInstance = null;

/**
 * Obtém a instância singleton do CalculoCache
 * @returns {CalculoCache} - Instância do cache
 */
export function getCalculoCache() {
    if (!cacheInstance) {
        cacheInstance = new CalculoCache();
    }
    return cacheInstance;
}
