/**
 * Store centralizada para gerenciamento de estado do processo marítimo
 * Implementa padrão Observer para reatividade
 */
export class ProcessoStore {
    constructor() {
        this.state = {
            produtos: [],
            cabecalho: {},
            nacionalizacao: 'santa_catarina',
            moedas: { USD: { venda: 0, compra: 0 } },
            calculos: {},
            totais: {},
            valoresBrutosPorLinha: {},
            valoresBrutosCamposExternos: {}
        };
        this.observers = [];
        this.isUpdating = false;
    }

    /**
     * Obtém o estado atual
     * @returns {Object} - Estado atual
     */
    getState() {
        return { ...this.state };
    }

    /**
     * Atualiza o estado de forma imutável
     * @param {Object} updates - Objeto com as atualizações
     */
    setState(updates) {
        if (this.isUpdating) {
            console.warn('Store está sendo atualizada, aguarde...');
            return;
        }

        this.isUpdating = true;
        const previousState = { ...this.state };
        this.state = {
            ...this.state,
            ...updates
        };

        // Notificar observadores
        this.notifyObservers(previousState, this.state);
        this.isUpdating = false;
    }

    /**
     * Atualiza um produto específico
     * @param {string|number} rowId - ID da linha
     * @param {Object} dados - Dados do produto
     */
    updateProduto(rowId, dados) {
        const produtos = [...this.state.produtos];
        const index = produtos.findIndex(p => p.rowId === rowId);
        
        if (index >= 0) {
            produtos[index] = { ...produtos[index], ...dados };
        } else {
            produtos.push({ rowId, ...dados });
        }

        this.setState({ produtos });
    }

    /**
     * Atualiza um campo do cabeçalho
     * @param {string} campo - Nome do campo
     * @param {*} valor - Valor do campo
     */
    updateCabecalho(campo, valor) {
        this.setState({
            cabecalho: {
                ...this.state.cabecalho,
                [campo]: valor
            }
        });
    }

    /**
     * Atualiza valores brutos por linha (para precisão)
     * @param {string|number} rowId - ID da linha
     * @param {Object} valores - Valores brutos
     */
    updateValoresBrutosPorLinha(rowId, valores) {
        this.setState({
            valoresBrutosPorLinha: {
                ...this.state.valoresBrutosPorLinha,
                [rowId]: {
                    ...this.state.valoresBrutosPorLinha[rowId],
                    ...valores
                }
            }
        });
    }

    /**
     * Atualiza valores brutos de campos externos (cabeçalho inputs)
     * @param {string} campo - Nome do campo
     * @param {number} linha - Índice da linha
     * @param {number} valor - Valor bruto
     */
    updateValoresBrutosCamposExternos(campo, linha, valor) {
        if (!this.state.valoresBrutosCamposExternos[campo]) {
            this.state.valoresBrutosCamposExternos[campo] = [];
        }
        this.state.valoresBrutosCamposExternos[campo][linha] = valor;
        
        // Forçar notificação
        this.setState({
            valoresBrutosCamposExternos: { ...this.state.valoresBrutosCamposExternos }
        });
    }

    /**
     * Inscreve um observador para mudanças no estado
     * @param {Function} callback - Função callback
     * @returns {Function} - Função para desinscrever
     */
    subscribe(callback) {
        this.observers.push(callback);
        
        return () => {
            this.observers = this.observers.filter(obs => obs !== callback);
        };
    }

    /**
     * Notifica todos os observadores
     * @param {Object} previousState - Estado anterior
     * @param {Object} currentState - Estado atual
     */
    notifyObservers(previousState, currentState) {
        this.observers.forEach(observer => {
            try {
                observer(previousState, currentState);
            } catch (error) {
                console.error('Erro ao notificar observador:', error);
            }
        });
    }

    /**
     * Reseta o estado para valores iniciais
     */
    reset() {
        this.state = {
            produtos: [],
            cabecalho: {},
            nacionalizacao: 'santa_catarina',
            moedas: { USD: { venda: 0, compra: 0 } },
            calculos: {},
            totais: {},
            valoresBrutosPorLinha: {},
            valoresBrutosCamposExternos: {}
        };
        this.notifyObservers({}, this.state);
    }
}

// Instância singleton
let storeInstance = null;

/**
 * Obtém a instância singleton da Store
 * @returns {ProcessoStore} - Instância da Store
 */
export function getStore() {
    if (!storeInstance) {
        storeInstance = new ProcessoStore();
    }
    return storeInstance;
}
