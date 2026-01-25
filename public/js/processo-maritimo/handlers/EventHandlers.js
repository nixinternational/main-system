import { MoneyUtils } from '../utils/MoneyUtils.js';
import { HelperService } from '../services/HelperService.js';

/**
 * Handlers de eventos para o sistema de processos marítimos
 * Centraliza toda a lógica de resposta a eventos
 */
export class EventHandlers {
    constructor(app) {
        this.app = app;
        this.helperService = new HelperService(app.store);
    }

    /**
     * Configura todos os handlers de eventos
     */
    setup() {
        const { eventBus } = this.app;

        // NÃO inicializar Select2 aqui - será inicializado no form-maritimo.blade.php
        // após o DOM estar completamente carregado

        // Produto adicionado
        eventBus.on('produto:adicionado', (dados) => {
            this.handleProdutoAdicionado(dados);
        });

        // Produto removido
        eventBus.on('produto:removido', (dados) => {
            this.handleProdutoRemovido(dados);
        });

        // Produto alterado
        eventBus.on('produto:alterado', (dados) => {
            this.handleProdutoAlterado(dados);
        }, { debounce: 300 });

        // Cabeçalho alterado
        eventBus.on('cabecalho:alterado', (dados) => {
            this.handleCabecalhoAlterado(dados);
        }, { debounce: 300 });

        // Nacionalização alterada
        eventBus.on('nacionalizacao:alterada', (dados) => {
            this.handleNacionalizacaoAlterada(dados);
        });

        // Moeda alterada
        eventBus.on('moeda:alterada', (dados) => {
            this.handleMoedaAlterada(dados);
        }, { debounce: 300 });

        // Recalcular tabela
        eventBus.on('recalcular:tabela', () => {
            this.handleRecalcularTabela();
        }, { debounce: 100 });

        // Salvar processo
        eventBus.on('salvar:processo', (dados) => {
            this.handleSalvarProcesso(dados);
        });

        // Configurar handlers jQuery diretamente (para compatibilidade durante migração)
        this.setupJQueryHandlers();
    }

    /**
     * Configura handlers jQuery diretamente
     * Esta função será removida quando toda a lógica estiver migrada para eventos
     */
    setupJQueryHandlers() {
        if (typeof $ === 'undefined') {
            return;
        }

        // Handlers de formatação de inputs
        this.setupFormatacaoHandlers();
        
        // Handlers de campos do cabeçalho
        this.setupCabecalhoHandlers();
        
        // Handlers de campos de produto
        this.setupProdutoHandlers();
        
        // Handlers de nacionalização
        this.setupNacionalizacaoHandlers();
        
        // Handlers de botões
        this.setupBotoesHandlers();
        
        // Handlers de formulário
        this.setupFormularioHandlers();
        
        // Handler para inicializar Select2 em elementos adicionados dinamicamente
        this.setupSelect2DynamicHandler();
    }
    
    /**
     * Configura handler para inicializar Select2 em elementos adicionados dinamicamente
     */
    setupSelect2DynamicHandler() {
        if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
            return;
        }

        // Observer para detectar novos elementos Select2 adicionados ao DOM
        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === 1) { // Element node
                            // Verificar se o próprio nó é um select com classe select2
                            if (node.tagName === 'SELECT' && node.classList && node.classList.contains('select2')) {
                                setTimeout(() => {
                                    this.inicializarSelect2Elemento($(node));
                                }, 50);
                            }
                            // Verificar se há selects com classe select2 dentro do nó
                            if (node.querySelectorAll) {
                                const selects = node.querySelectorAll('select.select2');
                                selects.forEach((select) => {
                                    setTimeout(() => {
                                        this.inicializarSelect2Elemento($(select));
                                    }, 50);
                                });
                            }
                        }
                    });
                });
            });

            // Observar mudanças no DOM
            if (document.body) {
                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            }
        }

        // Fallback: usar event delegation para elementos adicionados via jQuery
        $(document).on('DOMNodeInserted', 'select.select2', (e) => {
            const $select = $(e.target);
            if (!$select.data('select2')) {
                this.inicializarSelect2Elemento($select);
            }
        });
    }
    
    /**
     * Inicializa Select2 em um elemento específico
     * @param {jQuery} $select - Elemento jQuery do select
     */
    inicializarSelect2Elemento($select) {
        if (!$select || !$select.length || $select.data('select2')) {
            return;
        }

        // IMPORTANTE: Capturar valor ANTES de qualquer manipulação
        let valorSelecionado = null;
        
        // Buscar todas as opções com selected (ignorando disabled)
        const allOptions = $select.find('option');
        let selectedOption = null;
        
        allOptions.each(function() {
            const $option = $(this);
            if ($option.attr('selected') && !$option.prop('disabled')) {
                const val = $option.val();
                // Ignorar valores vazios ou "Selecione uma opção"
                if (val && val !== '' && val !== null && val !== undefined) {
                    selectedOption = $option;
                    return false; // break
                }
            }
        });
        
        if (selectedOption && selectedOption.length > 0) {
            valorSelecionado = selectedOption.val();
        }
        
        // Se não encontrou, verificar o valor atual do select
        if (!valorSelecionado || valorSelecionado === '' || valorSelecionado === null) {
            const currentVal = $select.val();
            // Ignorar se for a primeira opção disabled
            if (currentVal && currentVal !== '' && currentVal !== null) {
                valorSelecionado = currentVal;
            }
        }

        const config = {
            width: '100%',
            language: {
                noResults: function() {
                    return "Nenhum resultado encontrado";
                },
                searching: function() {
                    return "Buscando...";
                }
            }
        };

        // Se estiver dentro de um modal ou tab, definir dropdownParent
        const modalParent = $select.closest('.modal');
        const tabParent = $select.closest('.tab-pane');
        if (modalParent.length) {
            config.dropdownParent = modalParent;
        } else if (tabParent.length) {
            config.dropdownParent = tabParent;
        }

        // Inicializar Select2
        $select.select2(config);

        // Restaurar valor selecionado após inicialização
        if (valorSelecionado && valorSelecionado !== '' && valorSelecionado !== null && valorSelecionado !== undefined) {
            requestAnimationFrame(() => {
                $select.val(valorSelecionado).trigger('change.select2');
            });
        }
    }

    /**
     * Configura handlers de formatação
     */
    setupFormatacaoHandlers() {
        if (typeof $ === 'undefined') return;

        // Handlers de blur para formatação
        $('.moneyReal').on('blur', (e) => {
            if ($(e.target).hasClass('cabecalhoInputs')) {
                return;
            }
            const val = $(e.target).val();
            if (val && val.trim() !== '') {
                const numero = this.normalizeNumericValue(val);
                $(e.target).val(this.formatTruncatedNumber(numero, 5));
            } else {
                $(e.target).val('');
            }
        });

        $('.moneyReal2').on('blur', (e) => {
            const val = $(e.target).val();
            if (val && val.trim() !== '') {
                const numero = this.normalizeNumericValue(val);
                $(e.target).val(this.formatTruncatedNumber(numero, 2));
            } else {
                $(e.target).val('');
            }
        });

        $('.cotacao').on('blur', (e) => {
            const val = $(e.target).val();
            if (val && val.trim() !== '') {
                const numero = this.normalizeNumericValue(val);
                $(e.target).val(this.formatTruncatedNumber(numero, 4));
            } else {
                $(e.target).val('');
            }
        });

        $('.moneyReal7').on('blur', (e) => {
            if ($(e.target).hasClass('cabecalhoInputs')) {
                return;
            }
            const val = $(e.target).val();
            if (val && val.trim() !== '') {
                const numero = this.normalizeNumericValue(val);
                $(e.target).val(this.formatTruncatedNumber(numero, 7));
            } else {
                $(e.target).val('');
            }
        });

        $('.moneyReal8').on('blur', (e) => {
            const val = $(e.target).val();
            if (val && val.trim() !== '') {
                const numero = this.normalizeNumericValue(val);
                $(e.target).val(this.formatTruncatedNumber(numero, 8));
            } else {
                $(e.target).val('');
            }
        });

        $('.percentage').on('blur', (e) => {
            const val = $(e.target).val();
            if (val && val.trim() !== '') {
                const numero = this.normalizeNumericValue(val.replace('%', ''));
                $(e.target).val(`${this.formatTruncatedNumber(numero, 7)} %`);
            } else {
                $(e.target).val('');
            }
        });

        $('.percentage2').on('blur', (e) => {
            const val = $(e.target).val();
            if (val && val.trim() !== '') {
                const numero = this.normalizeNumericValue(val.replace('%', ''));
                $(e.target).val(`${this.formatTruncatedNumber(numero, 2)} %`);
            } else {
                $(e.target).val('');
            }
        });
    }

    /**
     * Configura handlers de campos do cabeçalho
     */
    setupCabecalhoHandlers() {
        if (typeof $ === 'undefined') return;

        // Handler para service_charges
        $('#service_charges').on('blur change input', () => {
            if (this.app.calculadoraMoedas && this.app.helperService) {
                const cotacoes = this.app.helperService.getCotacaoesProcesso();
                this.app.calculadoraMoedas.convertToUSDAndBRL(
                    'service_charges',
                    (selector) => $(selector).val(),
                    (selector, value) => $(selector).val(value),
                    cotacoes
                );
            }
            this.agendarRecalculo(150);
        });

        // Handlers de moedas
        $('#frete_internacional_moeda, #seguro_internacional_moeda, #acrescimo_frete_moeda, #service_charges_moeda, #moeda_processo')
            .on('change', () => {
                setTimeout(() => {
                    if (this.app.visibilidadeColunas) {
                        this.app.visibilidadeColunas.atualizarVisibilidadeColunasMoeda();
                        this.app.visibilidadeColunas.atualizarTitulosColunas();
                    }
                }, 100);
            });
    }

    /**
     * Configura handlers de campos de produto
     */
    setupProdutoHandlers() {
        if (typeof $ === 'undefined') return;

        // Handler para diferença cambial
        $(document).on('change blur', '.difCambial', () => {
            this.debouncedAtualizarCambial();
        });

        // Handler para MVA e ICMS-ST Mato Grosso
        $(document).on('change blur keyup', '#productsBody input[id^="mva_mg-"], #productsBody input[id^="icms_st_mg-"]', (e) => {
            const nacionalizacao = this.helperService.getNacionalizacaoAtual();
            if (nacionalizacao === 'mato_grosso') {
                const rowId = $(e.target).data('row');
                if (rowId !== undefined && rowId !== null && rowId !== '') {
                    setTimeout(() => {
                        this.app.recalcularTabela();
                    }, 100);
                }
            }
        });

        // Handler para atualizar valores brutos quando campos são editados manualmente
        // Lista de campos que devem ser armazenados em valoresBrutosPorLinha
        const camposParaValoresBrutos = [
            'frete_usd', 'frete_brl', 'frete_moeda_estrangeira',
            'seguro_usd', 'seguro_brl', 'seguro_moeda_estrangeira',
            'acresc_frete_usd', 'acresc_frete_brl', 'acrescimo_moeda_estrangeira',
            'service_charges', 'service_charges_brl', 'service_charges_moeda_estrangeira',
            'fob_total_usd', 'fob_total_brl', 'fob_total_moeda_estrangeira',
            'fob_unit_usd', 'fob_unit_moeda_estrangeira',
            'quantidade', 'peso_liquido_total', 'peso_liquido_unitario',
            'fator_peso', 'fator_valor_fob', 'fator_tx_siscomex',
            'valor_ii', 'valor_ipi', 'valor_pis', 'valor_cofins',
            'base_ipi', 'base_pis_cofins', 'base_icms_sem_reducao', 'base_icms_reduzido',
            'valor_icms_sem_reducao', 'valor_icms_reduzido',
            'valor_total_nf', 'valor_total_nf_sem_icms_st', 'valor_total_nf_com_icms_st',
            'base_icms_st', 'valor_icms_st',
            'thc_usd', 'thc_brl',
            'valor_aduaneiro_usd', 'valor_aduaneiro_brl',
            'vlr_crf_unit', 'vlr_crf_total',
            'desp_desenbaraco', 'diferenca_cambial_frete', 'diferenca_cambial_fob',
            'custo_unitario_final', 'custo_total_final',
            'opcional_1_valor', 'opcional_2_valor'
        ];

        // Criar seletor para todos os campos
        const seletores = camposParaValoresBrutos.map(campo => `#productsBody input[id^="${campo}-"]`).join(', ');
        
        // Handler para capturar valor durante digitação (input) - antes da formatação
        $(document).on('input', seletores, (e) => {
            const $input = $(e.target);
            const campoId = $input.attr('id');
            if (!campoId) return;

            // Extrair nome do campo e rowId do ID (formato: campo-rowId)
            const match = campoId.match(/^(.+?)-(\d+)$/);
            if (match) {
                const campo = match[1];
                const rowId = match[2];
                
                // Obter valor bruto do input (pode estar em formato parcial durante digitação)
                const valorInput = $input.val();
                const valorBruto = MoneyUtils.parseMoney(valorInput) || 0;
                
                // Inicializar valoresBrutosPorLinha se não existir
                if (!window.valoresBrutosPorLinha) {
                    window.valoresBrutosPorLinha = {};
                }
                if (!window.valoresBrutosPorLinha[rowId]) {
                    window.valoresBrutosPorLinha[rowId] = {};
                }
                
                // Armazenar valor bruto com máxima precisão
                window.valoresBrutosPorLinha[rowId][campo] = valorBruto;
                // #region agent log
                fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'EventHandlers.js:415',message:'Valor bruto armazenado via input',data:{rowId:rowId,campo:campo,valorBruto:valorBruto,valorInput:valorInput},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
                // #endregion
            }
        });
        
        // Handler para capturar valor final após formatação (blur/change)
        $(document).on('change blur', seletores, (e) => {
            const $input = $(e.target);
            const campoId = $input.attr('id');
            if (!campoId) return;

            // Extrair nome do campo e rowId do ID (formato: campo-rowId)
            const match = campoId.match(/^(.+?)-(\d+)$/);
            if (match) {
                const campo = match[1];
                const rowId = match[2];
                
                // Obter valor bruto após formatação
                const valorBruto = MoneyUtils.parseMoney($input.val()) || 0;
                
                // Inicializar valoresBrutosPorLinha se não existir
                if (!window.valoresBrutosPorLinha) {
                    window.valoresBrutosPorLinha = {};
                    // #region agent log
                    fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'EventHandlers.js:436',message:'valoresBrutosPorLinha inicializado como {}',data:{rowId:rowId,campo:campo},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'C'})}).catch(()=>{});
                    // #endregion
                }
                if (!window.valoresBrutosPorLinha[rowId]) {
                    window.valoresBrutosPorLinha[rowId] = {};
                }
                
                // Armazenar valor bruto com máxima precisão
                window.valoresBrutosPorLinha[rowId][campo] = valorBruto;
                // #region agent log
                fetch('http://127.0.0.1:7242/ingest/110fafe9-38f5-4b75-8965-0b46efd90519',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'EventHandlers.js:443',message:'Valor bruto armazenado via change/blur',data:{rowId:rowId,campo:campo,valorBruto:valorBruto},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'A'})}).catch(()=>{});
                // #endregion
                
                // Atualizar no store se disponível
                if (this.app && this.app.store) {
                    this.app.store.updateValoresBrutosPorLinha(rowId, { [campo]: valorBruto });
                }
            }
        });
    }

    /**
     * Configura handlers de nacionalização
     */
    setupNacionalizacaoHandlers() {
        if (typeof $ === 'undefined') return;

        $(document).on('focusin', '#nacionalizacao', function() {
            $(this).data('valor-anterior', $(this).val());
        });

        $(document).on('change', '#nacionalizacao', (e) => {
            this.processarMudancaNacionalizacao(e.target);
        });
    }

    /**
     * Configura handlers de botões
     */
    setupBotoesHandlers() {
        if (typeof $ === 'undefined') return;

        // Handler para botão recalcular
        $('#recalcularTabela').on('click', () => {
            const btn = $('#recalcularTabela');
            const originalText = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin"></i> Calculando...').prop('disabled', true);

            setTimeout(() => {
                try {
                    this.app.recalcularTabela();
                    if (typeof Toast !== 'undefined') {
                        Toast.fire({
                            icon: 'success',
                            title: 'Tabela recalculada com sucesso!'
                        });
                    }
                } catch (error) {
                    if (typeof Toast !== 'undefined') {
                        Toast.fire({
                            icon: 'error',
                            title: 'Erro ao recalcular tabela'
                        });
                    }
                } finally {
                    btn.html(originalText).prop('disabled', false);
                }
            }, 100);
        });

        // Handler para remover linha
        $(document).on('click', '.removeLine', (e) => {
            this.handleRemoverLinha(e);
        });
    }

    /**
     * Configura handlers de formulário
     */
    setupFormularioHandlers() {
        if (typeof $ === 'undefined') return;

        $('form').on('submit', (e) => {
            const pesoLiquidoTotal = this.helperService.calcularPesoTotal();
            $('#peso_liquido').val(MoneyUtils.formatMoney(pesoLiquidoTotal, 4));
            
            $('.percentage').each(function() {
                let originalValue = $(this).val();
                let unformattedValue = originalValue
                    .replace(/\./g, '')
                    .replace(',', '.')
                    .replace('%', '');
                $(this).val(unformattedValue);
            });
        });
    }

    /**
     * Processa mudança de nacionalização
     */
    processarMudancaNacionalizacao(element) {
        const valorAnterior = $(element).data('valor-anterior');
        const valorAtual = $(element).val();
        
        if (valorAnterior === valorAtual) {
            return;
        }

        // Atualizar visibilidade
        if (this.app.visibilidadeColunas) {
            this.app.visibilidadeColunas.atualizarVisibilidadeNacionalizacao(
                { recalcular: true },
                () => this.app.calculadoraCPT?.calcularCPT(),
                () => this.app.calculadoraCIF?.calcularCIF(),
                () => this.agendarRecalculo(300)
            );
        }

        // Recalcular tabela
        this.agendarRecalculo(300);
    }

    /**
     * Handler para remover linha
     */
    handleRemoverLinha(e) {
        if (typeof Swal === 'undefined') {
            // Fallback se SweetAlert não estiver disponível
            if (confirm('Você tem certeza que deseja excluir este registro?')) {
                const id = e.currentTarget.dataset.id;
                $(`#row-${id}`).remove();
                setTimeout(() => {
                    if (this.app.atualizadorMulta) {
                        this.app.atualizadorMulta.atualizarMultaProdutosPorMulta();
                    }
                    if (this.app.tabelaProdutos) {
                        this.app.tabelaProdutos.atualizarTotalizadores();
                    }
                }, 100);
            }
            return;
        }

        Swal.fire({
            title: 'Você tem certeza que deseja excluir este registro?',
            text: 'Esta ação não poderá ser desfeita!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const id = e.currentTarget.dataset.id;
                $(`#row-${id}`).remove();
                setTimeout(() => {
                    if (this.app.atualizadorMulta) {
                        this.app.atualizadorMulta.atualizarMultaProdutosPorMulta();
                    }
                    if (this.app.tabelaProdutos) {
                        this.app.tabelaProdutos.atualizarTotalizadores();
                    }
                }, 100);
            } else {
                if (typeof Toast !== 'undefined') {
                    Toast.fire({
                        icon: 'info',
                        title: 'Ação cancelada'
                    });
                }
            }
        });
    }

    /**
     * Debounce para atualizar cambial
     */
    debouncedAtualizarCambial() {
        if (this.atualizarCambialTimeout) {
            clearTimeout(this.atualizarCambialTimeout);
        }
        this.atualizarCambialTimeout = setTimeout(() => {
            if (this.app.atualizadorFatoresFob) {
                this.app.atualizadorFatoresFob.atualizarFatoresFob();
            }
            if (this.app.atualizadorCambial) {
                this.app.atualizadorCambial.atualizarCamposCambial();
            }
            if (this.app.tabelaProdutos) {
                this.app.tabelaProdutos.atualizarTotalizadores();
            }
        }, 200);
    }

    /**
     * Agenda recálculo
     */
    agendarRecalculo(delay = 150) {
        if (this.recalculoTimeout) {
            clearTimeout(this.recalculoTimeout);
        }
        this.recalculoTimeout = setTimeout(() => {
            this.app.recalcularTabela();
        }, delay);
    }

    /**
     * Normaliza valor numérico
     */
    normalizeNumericValue(value) {
        if (!value) return '';
        return value.toString().replace(/[^\d,.-]/g, '').replace(',', '.');
    }

    /**
     * Formata número truncado
     */
    formatTruncatedNumber(value, decimals) {
        const num = parseFloat(value) || 0;
        const factor = Math.pow(10, decimals);
        const truncated = Math.floor(num * factor) / factor;
        return truncated.toFixed(decimals).replace('.', ',');
    }

    /**
     * Inicializa Select2 em todos os elementos com classe .select2
     */
    inicializarSelect2() {
        if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
            return;
        }

        // Flag global para evitar múltiplas inicializações simultâneas
        if (window._select2Inicializando) {
            return;
        }
        window._select2Inicializando = true;

        // Aguardar para garantir que o DOM está pronto e valores estão definidos
        setTimeout(() => {
            $('.select2').each(function() {
                const $select = $(this);
                
                // Verificar se já está inicializado - se estiver, NÃO fazer nada
                if ($select.data('select2')) {
                    return;
                }

                // IMPORTANTE: Capturar valor ANTES de qualquer manipulação
                // Verificar o atributo selected diretamente no HTML
                let valorSelecionado = null;
                
                // Primeiro, verificar se há option com selected (ignorando disabled)
                // Buscar todas as opções com selected, mas filtrar as disabled manualmente
                const allOptions = $select.find('option');
                let selectedOption = null;
                
                allOptions.each(function() {
                    const $option = $(this);
                    if ($option.attr('selected') && !$option.prop('disabled')) {
                        const val = $option.val();
                        // Ignorar valores vazios ou "Selecione uma opção"
                        if (val && val !== '' && val !== null && val !== undefined) {
                            selectedOption = $option;
                            return false; // break
                        }
                    }
                });
                
                if (selectedOption && selectedOption.length > 0) {
                    valorSelecionado = selectedOption.val();
                }
                
                // Se não encontrou, verificar o valor atual do select
                if (!valorSelecionado || valorSelecionado === '' || valorSelecionado === null) {
                    const currentVal = $select.val();
                    // Ignorar se for a primeira opção disabled
                    if (currentVal && currentVal !== '' && currentVal !== null) {
                        valorSelecionado = currentVal;
                    }
                }

                // Configuração padrão
                const config = {
                    width: '100%',
                    language: {
                        noResults: function() {
                            return "Nenhum resultado encontrado";
                        },
                        searching: function() {
                            return "Buscando...";
                        }
                    }
                };

                // Se estiver dentro de um modal ou tab, definir dropdownParent
                const modalParent = $select.closest('.modal');
                const tabParent = $select.closest('.tab-pane');
                if (modalParent.length) {
                    config.dropdownParent = modalParent;
                } else if (tabParent.length) {
                    config.dropdownParent = tabParent;
                }

                // Inicializar Select2
                $select.select2(config);

                // Restaurar valor selecionado IMEDIATAMENTE após inicialização
                // Sem setTimeout adicional para evitar perda de valores
                if (valorSelecionado && valorSelecionado !== '' && valorSelecionado !== null && valorSelecionado !== undefined) {
                    // Usar requestAnimationFrame para garantir que o Select2 está pronto
                    requestAnimationFrame(() => {
                        $select.val(valorSelecionado).trigger('change.select2');
                    });
                }
            });
            
            // Liberar flag após inicialização
            window._select2Inicializando = false;
        }, 100);
    }

    /**
     * Handler: Produto adicionado
     */
    handleProdutoAdicionado(dados) {
        const { rowId } = dados;
        
        // Inicializar Select2 no novo select de produto
        if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
            setTimeout(() => {
                const $select = $(`#produto_id-${rowId}`);
                if ($select.length && !$select.data('select2')) {
                    // IMPORTANTE: Capturar valor ANTES de qualquer manipulação
                    let valorSelecionado = null;
                    
                    // Buscar todas as opções com selected (ignorando disabled)
                    const allOptions = $select.find('option');
                    let selectedOption = null;
                    
                    allOptions.each(function() {
                        const $option = $(this);
                        if ($option.attr('selected') && !$option.prop('disabled')) {
                            const val = $option.val();
                            // Ignorar valores vazios ou "Selecione uma opção"
                            if (val && val !== '' && val !== null && val !== undefined) {
                                selectedOption = $option;
                                return false; // break
                            }
                        }
                    });
                    
                    if (selectedOption && selectedOption.length > 0) {
                        valorSelecionado = selectedOption.val();
                    }
                    
                    // Se não encontrou, verificar o valor atual do select
                    if (!valorSelecionado || valorSelecionado === '' || valorSelecionado === null) {
                        const currentVal = $select.val();
                        // Ignorar se for a primeira opção disabled
                        if (currentVal && currentVal !== '' && currentVal !== null) {
                            valorSelecionado = currentVal;
                        }
                    }

                    const config = {
                        width: '100%',
                        language: {
                            noResults: function() {
                                return "Nenhum resultado encontrado";
                            },
                            searching: function() {
                                return "Buscando...";
                            }
                        }
                    };

                    // Se estiver dentro de um modal ou tab, definir dropdownParent
                    const tabParent = $select.closest('.tab-pane');
                    if (tabParent.length) {
                        config.dropdownParent = tabParent;
                    }

                    // Inicializar Select2
                    $select.select2(config);

                    // Restaurar valor selecionado após inicialização
                    if (valorSelecionado && valorSelecionado !== '' && valorSelecionado !== null && valorSelecionado !== undefined) {
                        requestAnimationFrame(() => {
                            $select.val(valorSelecionado).trigger('change.select2');
                        });
                    }
                }
            }, 50);
        }
        
        // Recalcular tabela após adicionar produto
        setTimeout(() => {
            this.app.recalcularTabela();
        }, 100);
    }

    /**
     * Handler: Produto removido
     */
    handleProdutoRemovido(dados) {
        const { rowId } = dados;
        
        // Remover do store
        const estado = this.app.store.getState();
        const produtos = (estado.produtos || []).filter(p => p.rowId !== rowId);
        this.app.store.setState({ produtos });

        // Recalcular tabela
        setTimeout(() => {
            this.app.recalcularTabela();
        }, 100);
    }

    /**
     * Handler: Produto alterado
     */
    handleProdutoAlterado(dados) {
        const { rowId, campo, valor } = dados;
        
        // Atualizar no store
        const estado = this.app.store.getState();
        const produtos = estado.produtos || [];
        const produtoIndex = produtos.findIndex(p => p.rowId === rowId);
        
        if (produtoIndex >= 0) {
            produtos[produtoIndex][campo] = valor;
            this.app.store.setState({ produtos });
        }

        // Recalcular tabela
        this.app.recalcularTabela();
    }

    /**
     * Handler: Cabeçalho alterado
     */
    handleCabecalhoAlterado(dados) {
        const { campo, valor } = dados;
        
        // Atualizar no store
        const estado = this.app.store.getState();
        const cabecalho = estado.cabecalho || {};
        cabecalho[campo] = valor;
        this.app.store.setState({ cabecalho });

        // Recalcular tabela
        this.app.recalcularTabela();
    }

    /**
     * Handler: Nacionalização alterada
     */
    handleNacionalizacaoAlterada(dados) {
        const { nacionalizacao } = dados;
        
        // Atualizar no store
        this.app.store.setState({ nacionalizacao });

        // Emitir evento para atualizar UI
        this.app.eventBus.emit('ui:atualizar-colunas', { nacionalizacao });

        // Recalcular tabela
        this.app.recalcularTabela();
    }

    /**
     * Handler: Moeda alterada
     */
    handleMoedaAlterada(dados) {
        const { moeda, valor } = dados;
        
        // Atualizar no store
        const estado = this.app.store.getState();
        const moedas = estado.moedas || {};
        if (!moedas[moeda]) {
            moedas[moeda] = {};
        }
        moedas[moeda].venda = valor;
        this.app.store.setState({ moedas });

        // Recalcular tabela
        this.app.recalcularTabela();
    }

    /**
     * Handler: Recalcular tabela
     */
    handleRecalcularTabela() {
        this.app.recalcularTabela();
    }

    /**
     * Handler: Salvar processo
     */
    async handleSalvarProcesso(dados) {
        const estado = this.app.store.getState();
        
        // Preparar dados para salvar
        const dadosParaSalvar = {
            produtos: estado.produtos || [],
            cabecalho: estado.cabecalho || {},
            moedas: estado.moedas || {},
            nacionalizacao: estado.nacionalizacao
        };

        // Emitir evento de salvamento iniciado
        this.app.eventBus.emit('salvar:iniciado', dadosParaSalvar);

        try {
            // Aqui seria feita a chamada AJAX para salvar
            // Por enquanto, apenas emite evento de sucesso
            this.app.eventBus.emit('salvar:sucesso', dadosParaSalvar);
        } catch (error) {
            this.app.eventBus.emit('salvar:erro', { error, dados: dadosParaSalvar });
        }
    }
}
