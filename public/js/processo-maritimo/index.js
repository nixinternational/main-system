/**
 * Ponto de entrada principal do sistema de processos marítimos
 * Orquestra a inicialização de todos os módulos
 */

import { getStore } from './core/Store.js';
import { getEventBus } from './core/EventBus.js';
import { getContainer } from './core/ContainerDependencias.js';
import { getCalculoCache } from './core/CalculoCache.js';
import { Config } from './core/Config.js';
import { getFactory } from './strategies/NacionalizacaoFactory.js';
import { CalculadoraImpostos } from './services/CalculadoraImpostos.js';
import { CalculadoraFrete } from './services/CalculadoraFrete.js';
import { CalculadoraMoedas } from './services/CalculadoraMoedas.js';
import { CalculadoraDespesas } from './services/CalculadoraDespesas.js';
import { CalculadoraCPT } from './services/CalculadoraCPT.js';
import { CalculadoraCIF } from './services/CalculadoraCIF.js';
import { CalculadoraMatoGrosso } from './services/CalculadoraMatoGrosso.js';
import { DistribuidorValores } from './services/DistribuidorValores.js';
import { ProcessoCalculator } from './services/ProcessoCalculator.js';
import { HelperService } from './services/HelperService.js';
import { TabelaProdutos } from './components/TabelaProdutos.js';
import { TabelaVirtualizada } from './components/TabelaVirtualizada.js';
import { TotalizadorGlobal } from './components/TotalizadorGlobal.js';
import { VisibilidadeColunas } from './components/VisibilidadeColunas.js';
import { AtualizadorCambial } from './components/AtualizadorCambial.js';
import { AtualizadorMulta } from './components/AtualizadorMulta.js';
import { OrganizadorTabela } from './components/OrganizadorTabela.js';
import { ProcessoMaritimoApp } from './ProcessoMaritimoApp.js';
import { MoneyUtils } from './utils/MoneyUtils.js';
import { DOMUtils } from './utils/DOMUtils.js';
import { Validador } from './utils/Validador.js';
import { Formatador } from './utils/Formatador.js';
import { LimpezaCampos } from './utils/LimpezaCampos.js';

// Exportar instâncias singleton para uso global
export const store = getStore();
export const eventBus = getEventBus();
export const container = getContainer();
export const calculoCache = getCalculoCache();

// Exportar serviços e utilitários
export {
    Config,
    MoneyUtils,
    DOMUtils,
    Validador,
    Formatador,
    LimpezaCampos,
    CalculadoraImpostos,
    CalculadoraFrete,
    CalculadoraMoedas,
    CalculadoraDespesas,
    CalculadoraCPT,
    CalculadoraCIF,
    CalculadoraMatoGrosso,
    DistribuidorValores,
    ProcessoCalculator,
    HelperService,
    TabelaProdutos,
    TabelaVirtualizada,
    TotalizadorGlobal,
    VisibilidadeColunas,
    AtualizadorCambial,
    AtualizadorMulta,
    OrganizadorTabela,
    ProcessoMaritimoApp,
    getFactory,
    getCalculoCache
};

/**
 * Inicializa o sistema de processos marítimos
 * @param {Object} config - Opções de inicialização
 * @returns {ProcessoMaritimoApp} - Instância da aplicação
 */
export function init(config = {}) {
    const app = new ProcessoMaritimoApp(config);
    
    // Inicializar virtualização
    app.initVirtualizacao();
    
    return app;
}

/**
 * Limpa recursos e reseta o sistema
 */
export function destroy() {
    store.reset();
    eventBus.clearDebounce();
    eventBus.off();
    container.clear();
}

// Auto-inicialização desabilitada - a inicialização é feita manualmente no Blade
// Isso evita conflitos e garante que o DOM esteja completamente pronto
// if (typeof document !== 'undefined') {
//     if (document.readyState === 'loading') {
//         document.addEventListener('DOMContentLoaded', () => {
//             init();
//         });
//     } else {
//         init();
//     }
// }
