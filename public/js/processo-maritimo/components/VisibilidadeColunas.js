import { DOMUtils } from '../utils/DOMUtils.js';

/**
 * Componente para gerenciar visibilidade de colunas conforme nacionalização
 */
export class VisibilidadeColunas {
    constructor(helperService) {
        this.helperService = helperService;
    }

    /**
     * Atualiza visibilidade de colunas conforme nacionalização
     * @param {Object} options - Opções { recalcular: boolean }
     * @param {Function} calcularValoresCPT - Função para calcular CPT
     * @param {Function} calcularValoresCIF - Função para calcular CIF
     * @param {Function} debouncedRecalcular - Função para recalcular
     */
    atualizarVisibilidadeNacionalizacao(options = {}, calcularValoresCPT, calcularValoresCIF, debouncedRecalcular) {
        const { recalcular = false } = options;
        const nacionalizacao = this.helperService.getNacionalizacaoAtual();
        const mostrarCamposAnapolis = nacionalizacao !== 'santos';
        const mostrarTxCorrecao = nacionalizacao === 'santos' || nacionalizacao === 'santa_catarina';

        // Atualizar visibilidade de headers
        const thTxCorrecao = document.querySelectorAll('th[data-campo="tx_correcao_lacre"]');
        thTxCorrecao.forEach(th => {
            th.style.display = mostrarTxCorrecao ? '' : 'none';
        });

        const thAnapolis = document.querySelectorAll('th[data-campo="rep_anapolis"], th[data-campo="correios"]');
        thAnapolis.forEach(th => {
            th.style.display = mostrarCamposAnapolis ? '' : 'none';
        });

        // Atualizar visibilidade de células
        const tdTxCorrecao = document.querySelectorAll('td[data-campo="tx_correcao_lacre"]');
        tdTxCorrecao.forEach(td => {
            td.style.display = mostrarTxCorrecao ? '' : 'none';
        });

        const tdAnapolis = document.querySelectorAll('td[data-campo="rep_anapolis"], td[data-campo="correios"]');
        tdAnapolis.forEach(td => {
            td.style.display = mostrarCamposAnapolis ? '' : 'none';
        });

        // Toggle colunas por classe
        DOMUtils.toggleColunas('.coluna-anapolis', mostrarCamposAnapolis);
        DOMUtils.toggleColunas('.coluna-tx-correcao-lacre', mostrarTxCorrecao);

        // Limpar campos específicos
        if (mostrarCamposAnapolis) {
            // Limpar campos de Anápolis quando não aplicável
        } else {
            // Limpar campos exclusivos de Anápolis
        }

        // Atualizar valores CPT e CIF
        if (calcularValoresCPT) calcularValoresCPT();
        if (calcularValoresCIF) calcularValoresCIF();

        if (recalcular && debouncedRecalcular) {
            debouncedRecalcular();
        }
    }

    /**
     * Atualiza visibilidade de colunas conforme moeda
     * @param {Object} moedas - Objeto com informações de moedas
     */
    atualizarVisibilidadeColunasMoeda(moedas) {
        // Implementação para mostrar/ocultar colunas conforme moeda selecionada
        // Pode ser expandida conforme necessário
    }

    /**
     * Atualiza títulos de colunas conforme moedas
     * @param {Object} moedas - Objeto com informações de moedas
     */
    atualizarTitulosColunas(moedas) {
        if (typeof $ === 'undefined') return;

        const moedaFrete = $('#frete_internacional_moeda').val();
        const moedaSeguro = $('#seguro_internacional_moeda').val();
        const moedaAcrescimo = $('#acrescimo_frete_moeda').val();
        const moedaProcesso = $('#moeda_processo').val();

        $('th').each(function() {
            let texto = $(this).text();

            if (texto.includes('FRETE INT.') && !texto.includes('USD') && !texto.includes('R$')) {
                if (moedaFrete && moedaFrete !== 'USD') {
                    $(this).text(`FRETE INT.${moedaFrete}`).show();
                } else {
                    $(this).hide();
                }
            }
            if (texto.includes('SEGURO INT.') && !texto.includes('USD') && !texto.includes('R$')) {
                if (moedaSeguro && moedaSeguro !== 'USD') {
                    $(this).text(`SEGURO INT.${moedaSeguro}`).show();
                } else {
                    $(this).hide();
                }
            }
            // Adicionar mais lógica conforme necessário
        });
    }
}
