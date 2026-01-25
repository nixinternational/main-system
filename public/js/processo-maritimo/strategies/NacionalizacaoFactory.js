import { BaseStrategy } from './BaseStrategy.js';
import { Config } from '../core/Config.js';
import { SantaCatarinaStrategy } from './SantaCatarinaStrategy.js';
import { SantosStrategy } from './SantosStrategy.js';
import { AnapolisStrategy } from './AnapolisStrategy.js';
import { MatoGrossoStrategy } from './MatoGrossoStrategy.js';

/**
 * Factory para criar instâncias de estratégias de nacionalização
 */
export class NacionalizacaoFactory {
    constructor(store, eventBus) {
        this.store = store;
        this.eventBus = eventBus;
        this.strategies = new Map();
        this.strategyClasses = new Map();
        
        // Registrar todas as estratégias disponíveis
        this.registerStrategy(Config.NACIONALIZACOES.SANTA_CATARINA, SantaCatarinaStrategy);
        this.registerStrategy(Config.NACIONALIZACOES.SANTOS, SantosStrategy);
        this.registerStrategy(Config.NACIONALIZACOES.ANAPOLIS, AnapolisStrategy);
        this.registerStrategy(Config.NACIONALIZACOES.MATO_GROSSO, MatoGrossoStrategy);
    }

    /**
     * Obtém a estratégia para um tipo de nacionalização
     * @param {string} tipo - Tipo de nacionalização
     * @returns {BaseStrategy} - Instância da estratégia
     */
    get(tipo) {
        if (this.strategies.has(tipo)) {
            return this.strategies.get(tipo);
        }

        const StrategyClass = this.strategyClasses.get(tipo);

        if (!StrategyClass) {
            // Retornar estratégia padrão se não encontrada
            console.warn(`Estratégia não encontrada para ${tipo}, usando BaseStrategy`);
            return new BaseStrategy(this.store, this.eventBus);
        }

        const strategy = new StrategyClass(this.store, this.eventBus);
        this.strategies.set(tipo, strategy);
        return strategy;
    }

    /**
     * Registra uma estratégia manualmente
     * @param {string} tipo - Tipo de nacionalização
     * @param {Class} StrategyClass - Classe da estratégia
     */
    registerStrategy(tipo, StrategyClass) {
        this.strategyClasses.set(tipo, StrategyClass);
        // Limpar cache se já existir
        if (this.strategies.has(tipo)) {
            this.strategies.delete(tipo);
        }
    }

    /**
     * Registra uma estratégia manualmente
     * @param {string} tipo - Tipo de nacionalização
     * @param {Class} StrategyClass - Classe da estratégia
     */
    register(tipo, StrategyClass) {
        const strategy = new StrategyClass(this.store, this.eventBus);
        this.strategies.set(tipo, strategy);
    }

    /**
     * Limpa o cache de estratégias
     */
    clear() {
        this.strategies.clear();
    }
}

// Instância singleton
let factoryInstance = null;

/**
 * Obtém a instância singleton da Factory
 * @param {Object} store - Instância da Store
 * @param {Object} eventBus - Instância do EventBus
 * @returns {NacionalizacaoFactory} - Instância da Factory
 */
export function getFactory(store, eventBus) {
    if (!factoryInstance) {
        factoryInstance = new NacionalizacaoFactory(store, eventBus);
    }
    return factoryInstance;
}
