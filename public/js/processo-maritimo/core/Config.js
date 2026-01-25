/**
 * Configurações e constantes do sistema de processos marítimos
 */
export const Config = {
    // Tipos de nacionalização suportados
    NACIONALIZACOES: {
        SANTA_CATARINA: 'santa_catarina',
        SANTOS: 'santos',
        ANAPOLIS: 'anapolis',
        MATO_GROSSO: 'mato_grosso',
        OUTROS: 'outros'
    },

    // Casas decimais padrão para diferentes tipos de valores
    DECIMAIS: {
        MOEDA: 2,
        MOEDA_PRECISA: 6,
        MOEDA_MUITO_PRECISA: 8,
        PORCENTAGEM: 2,
        PESO: 4,
        FATOR_FOB: 8
    },

    // Campos que precisam de precisão especial (7 casas decimais)
    CAMPOS_PRECISAO_7: ['li_dta_honor_nix', 'honorarios_nix'],

    // Delay padrão para debouncing (ms)
    DEBOUNCE_DELAY: 300,

    // Delay para recálculo automático após carregamento (ms)
    RECALCULO_DELAY: 1500,

    // Seletores DOM comuns
    SELECTORS: {
        PRODUCTS_BODY: '#productsBody',
        PRODUCTS_TABLE: '.table-products',
        LINHAS_INPUT: '.linhas-input',
        CABECALHO_INPUTS: '.cabecalhoInputs'
    }
};
