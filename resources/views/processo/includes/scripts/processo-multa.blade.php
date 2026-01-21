{{-- ==================== LÓGICA DA TABELA MULTA ==================== --}}
{{-- Este arquivo contém toda a lógica JavaScript para a tabela de multas --}}

        // Classe para salvamento em fases (evitar erro 413)
        class SalvamentoProdutosMultaFases {
            constructor(processoId) {
                this.processoId = processoId;
                this.blocos = [];
                this.totalSalvos = 0;
                this.swalProgress = null;
            }

            coletarProdutosMulta() {
                const produtos = [];
                let rowIndex = 0;

                $('#productsBodyMulta tr.linhas-input').each(function(index) {
                    const produto = {};
                    let hasData = false;
                    
                    const rowId = $(this).attr('id') ? $(this).attr('id').replace('row-multa-', '') : index;

                    $(this).find('input, select, textarea').each(function() {
                        const name = $(this).attr('name');
                        if (name && name.includes('produtos_multa')) {
                            const start = name.indexOf('produtos_multa[') + 15;
                            const end = name.indexOf(']', start);
                            const fieldName = name.substring(end + 2, name.length - 1);

                            if (start !== -1 && end !== -1) {
                                const value = $(this).val();
                                produto[fieldName] = value;
                                if (value && value !== '' && value !== null) {
                                    hasData = true;
                                }
                            }
                        }
                    });

                    if (hasData && produto.produto_id) {
                        produtos.push(produto);
                        rowIndex++;
                    }
                });

                return produtos;
            }

            dividirEmBlocos(array, tamanhoBloco) {
                const blocos = [];
                for (let i = 0; i < array.length; i += tamanhoBloco) {
                    blocos.push(array.slice(i, i + tamanhoBloco));
                }
                return blocos;
            }

            async iniciarProcessoSalvamento() {
                const produtos = this.coletarProdutosMulta();
                this.blocos = this.dividirEmBlocos(produtos, 50);
                this.totalSalvos = 0;

                this.swalProgress = Swal.fire({
                    title: 'Salvando Produtos Multa...',
                    html: this.getHtmlProgresso(0, this.blocos.length, 0),
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    showConfirmButton: false,
                    showCancelButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    for (let i = 0; i < this.blocos.length; i++) {
                        const sucesso = await this.salvarBloco(i);
                        if (!sucesso) {
                            throw new Error(`Erro ao salvar bloco ${i + 1}`);
                        }
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: `Todos os ${this.totalSalvos} produtos multa foram salvos com sucesso.`,
                        confirmButtonText: 'OK'
                    });
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Erro ao salvar produtos multa: ' + error.message,
                        confirmButtonText: 'OK'
                    });
                }
            }

            async salvarBloco(indiceBloco) {
                const blocoProdutos = this.blocos[indiceBloco];

                try {
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('_method', 'PUT');
                    formData.append('tipo_processo', 'maritimo');
                    formData.append('bloco_indice', indiceBloco);
                    formData.append('total_blocos', this.blocos.length);
                    formData.append('salvar_apenas_produtos_multa', 'true');

                    const valorCptUsdMulta = $('#valor_cpt_usd_multa').val();
                    const valorCptBrlMulta = $('#valor_cpt_brl_multa').val();
                    if (valorCptUsdMulta) {
                        formData.append('valor_cpt_usd_multa', MoneyUtils.parseMoney(valorCptUsdMulta) || '');
                    }
                    if (valorCptBrlMulta) {
                        formData.append('valor_cpt_brl_multa', MoneyUtils.parseMoney(valorCptBrlMulta) || '');
                    }

                    blocoProdutos.forEach((produto, index) => {
                        Object.keys(produto).forEach(campo => {
                            if (produto[campo] !== undefined && produto[campo] !== null) {
                                formData.append(`produtos_multa[${index}][${campo}]`, produto[campo]);
                            }
                        });
                    });

                    const response = await fetch(`/processo/${this.processoId}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.success) {
                        this.totalSalvos += blocoProdutos.length;
                        await this.atualizarProgressoSweetAlert(
                            indiceBloco + 1,
                            this.blocos.length,
                            this.totalSalvos
                        );
                        return true;
                    } else {
                        throw new Error(data.message || 'Erro ao salvar bloco');
                    }
                } catch (error) {
                    throw error;
                }
            }

            getHtmlProgresso(blocoAtual, totalBlocos, registrosSalvos) {
                return `
                    <div class="text-center">
                        <p>Processando bloco ${blocoAtual} de ${totalBlocos}</p>
                        <p>Registros salvos: ${registrosSalvos}</p>
                        <div class="progress mt-3" style="height: 25px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" 
                                 style="width: ${(blocoAtual / totalBlocos) * 100}%">
                                ${Math.round((blocoAtual / totalBlocos) * 100)}%
                            </div>
                        </div>
                    </div>
                `;
            }

            async atualizarProgressoSweetAlert(blocoAtual, totalBlocos, registrosSalvos) {
                Swal.update({
                    html: this.getHtmlProgresso(blocoAtual, totalBlocos, registrosSalvos)
                });
            }
        }

        // Inicializar salvamento em fases
        let salvamentoMultaFases = null;
        function inicializarSalvamentoFasesMulta() {
            const processoId = {{ $processo->id ?? 'null' }};
            if (processoId && !salvamentoMultaFases) {
                salvamentoMultaFases = new SalvamentoProdutosMultaFases(processoId);
            }
        }

        $(document).on('click', '#btnSalvarFasesMulta', function() {
            if (!salvamentoMultaFases) {
                inicializarSalvamentoFasesMulta();
            }
            if (salvamentoMultaFases) {
                salvamentoMultaFases.iniciarProcessoSalvamento();
            }
        });

        // Função para copiar valores da tabela produtos para tabela multa
        function copiarValoresProdutosParaMulta(rowId) {
            const produtoId = $(`#produto_multa_id-${rowId}`).val();
            const adicao = $(`#adicao_multa-${rowId}`).val();
            const item = $(`#item_multa-${rowId}`).val();

            if (!produtoId || !adicao || !item) {
                return false; // Retorna false se não tiver dados suficientes
            }

            // Limpar valores não numéricos de adicao e item
            const adicaoLimpa = adicao.toString().replace(/[^0-9]/g, '');
            const itemLimpo = item.toString().replace(/[^0-9]/g, '');

            if (!adicaoLimpa || !itemLimpo) {
                return false;
            }

            // Procurar linha na tabela de produtos com mesmo produto_id, adicao e item
            let linhaProdutoEncontrada = null;
            $('#productsBody tr.linhas-input').each(function() {
                const rowProdId = $(this).attr('id') ? $(this).attr('id').replace('row-', '') : null;
                if (!rowProdId) return;

                const produtoIdProd = $(`#produto_id-${rowProdId}`).val();
                const adicaoProd = $(`#adicao-${rowProdId}`).val();
                const itemProd = $(`#item-${rowProdId}`).val();

                const adicaoProdLimpa = adicaoProd ? adicaoProd.toString().replace(/[^0-9]/g, '') : '';
                const itemProdLimpo = itemProd ? itemProd.toString().replace(/[^0-9]/g, '') : '';

                if (produtoIdProd == produtoId && 
                    adicaoProdLimpa === adicaoLimpa && 
                    itemProdLimpo === itemLimpo) {
                    linhaProdutoEncontrada = rowProdId;
                    return false;
                }
            });

            if (linhaProdutoEncontrada) {
                // Copiar valores apenas se os campos da multa estiverem vazios (0 ou vazio)
                const camposParaCopiar = [
                    { origem: 'frete_usd', destino: 'frete_usd_multa' },
                    { origem: 'frete_brl', destino: 'frete_brl_multa' },
                    { origem: 'acresc_frete_usd', destino: 'acresc_frete_usd_multa' },
                    { origem: 'acresc_frete_brl', destino: 'acresc_frete_brl_multa' },
                    { origem: 'seguro_usd', destino: 'seguro_usd_multa' },
                    { origem: 'seguro_brl', destino: 'seguro_brl_multa' },
                    { origem: 'thc_usd', destino: 'thc_usd_multa' },
                    { origem: 'thc_brl', destino: 'thc_brl_multa' },
                    { origem: 'service_charges', destino: 'service_charges_multa' },
                    { origem: 'service_charges_brl', destino: 'service_charges_brl_multa' }
                ];

                camposParaCopiar.forEach(campo => {
                    const valorOrigem = MoneyUtils.parseMoney($(`#${campo.origem}-${linhaProdutoEncontrada}`).val()) || 0;
                    const valorDestino = MoneyUtils.parseMoney($(`#${campo.destino}-${rowId}`).val()) || 0;

                    // Só copiar se o destino estiver vazio (0 ou vazio)
                    if (valorDestino === 0 || $(`#${campo.destino}-${rowId}`).val() === '' || $(`#${campo.destino}-${rowId}`).val() === null) {
                        $(`#${campo.destino}-${rowId}`).val(MoneyUtils.formatMoney(valorOrigem, 2));
                    }
                });
                return true; // Produto equivalente encontrado
            }
            
            return false; // Produto equivalente não encontrado
        }

        // Variável para controlar avisos já exibidos por linha
        const avisosExibidosMulta = {};

        // Verificar e copiar valores quando produto_id, adicao e item são preenchidos
        function verificarECopiarValoresMulta(rowId, mostrarAviso = true) {
            const produtoId = $(`#produto_multa_id-${rowId}`).val();
            const adicao = $(`#adicao_multa-${rowId}`).val();
            const item = $(`#item_multa-${rowId}`).val();

            if (produtoId && adicao && item) {
                const encontrado = copiarValoresProdutosParaMulta(rowId);
                if (!encontrado && mostrarAviso) {
                    // Criar chave única para esta linha
                    const chaveAviso = `${rowId}-${produtoId}-${adicao}-${item}`;
                    
                    // Só mostrar aviso se ainda não foi exibido para esta combinação
                    if (!avisosExibidosMulta[chaveAviso]) {
                        avisosExibidosMulta[chaveAviso] = true;
                        const produtoNome = $(`#produto_multa_id-${rowId} option:selected`).text() || 'Produto';
                        Swal.fire({
                            icon: 'warning',
                            title: 'Produto equivalente não encontrado',
                            html: `A linha com produto <strong>${produtoNome}</strong>, adição <strong>${adicao}</strong> e item <strong>${item}</strong> não foi encontrada na tabela de produtos.<br><br>Os valores serão calculados automaticamente usando o fator peso/FOB desta linha.`,
                            confirmButtonText: 'OK',
                            timer: 5000,
                            timerProgressBar: true
                        });
                    }
                }
                return encontrado;
            }
            return false;
        }

        // Função para calcular Service Charges da tabela multa
        function calcularServiceChargesMulta(rowId) {
            const serviceChargesBase = MoneyUtils.parseMoney($('#service_charges').val()) || 0;
            const moedaServiceCharges = $('#service_charges_moeda').val();
            let serviceChargesTotalUSD = serviceChargesBase;
            
            // Converter para USD se necessário
            if (moedaServiceCharges && moedaServiceCharges !== 'USD') {
                const cotacoesProcesso = getCotacaoesProcesso();
                const cotacaoServiceCharges = MoneyUtils.parseMoney($('#cotacao_service_charges').val()) || 0;
                const cotacaoUSD = cotacoesProcesso['USD']?.venda ?? 1;
                if (cotacaoServiceCharges > 0 && cotacaoUSD > 0) {
                    const moedaEmUSD = cotacaoServiceCharges / cotacaoUSD;
                    serviceChargesTotalUSD = serviceChargesBase * moedaEmUSD;
                }
            }

            // Calcular fator peso da linha
            const totalPesoLiq = calcularPesoTotalMulta();
            const pesoLinha = MoneyUtils.parseMoney($(`#peso_liquido_total_multa-${rowId}`).val()) || 0;
            const fatorPeso = totalPesoLiq > 0 ? pesoLinha / totalPesoLiq : 0;

            const serviceChargesUSD = serviceChargesTotalUSD * fatorPeso;
            const cotacoes = getCotacaoesProcesso();
            const cotacaoUsd = cotacoes['USD']?.venda || 1;
            const serviceChargesBrl = serviceChargesUSD * cotacaoUsd;

            $(`#service_charges_multa-${rowId}`).val(MoneyUtils.formatMoney(serviceChargesUSD, 2));
            $(`#service_charges_brl_multa-${rowId}`).val(MoneyUtils.formatMoney(serviceChargesBrl, 2));
        }

        // Função para calcular Frete Internacional da tabela multa
        function calcularFreteInternacionalMulta(rowId) {
            const freteInternacionalBase = MoneyUtils.parseMoney($('#frete_internacional').val()) || 0;
            const moedaFrete = $('#frete_internacional_moeda').val();
            let freteInternacionalUSD = freteInternacionalBase;

            // Converter para USD se necessário
            if (moedaFrete && moedaFrete !== 'USD') {
                const cotacoesProcesso = getCotacaoesProcesso();
                const cotacaoFrete = MoneyUtils.parseMoney($('#cotacao_frete_internacional').val()) || 0;
                const cotacaoUSD = cotacoesProcesso['USD']?.venda ?? 1;
                if (cotacaoFrete > 0 && cotacaoUSD > 0) {
                    const moedaEmUSD = cotacaoFrete / cotacaoUSD;
                    freteInternacionalUSD = freteInternacionalBase * moedaEmUSD;
                }
            }

            // Calcular fator peso da linha
            const totalPesoLiq = calcularPesoTotalMulta();
            const pesoLinha = MoneyUtils.parseMoney($(`#peso_liquido_total_multa-${rowId}`).val()) || 0;
            const fatorPeso = totalPesoLiq > 0 ? pesoLinha / totalPesoLiq : 0;

            const freteUSD = freteInternacionalUSD * fatorPeso;
            const cotacoes = getCotacaoesProcesso();
            const cotacaoUsd = cotacoes['USD']?.venda || 1;
            const freteBrl = freteUSD * cotacaoUsd;

            $(`#frete_usd_multa-${rowId}`).val(MoneyUtils.formatMoney(freteUSD, 2));
            $(`#frete_brl_multa-${rowId}`).val(MoneyUtils.formatMoney(freteBrl, 2));
        }

        // Função para calcular Seguro Internacional da tabela multa
        function calcularSeguroInternacionalMulta(rowId) {
            const fobTotalMulta = MoneyUtils.parseMoney($(`#fob_total_usd_multa-${rowId}`).val()) || 0;
            
            // Calcular FOB total geral da tabela multa
            let fobTotalGeralMulta = 0;
            $('#productsBodyMulta tr.linhas-input').each(function() {
                const rowIdMulta = $(this).attr('id') ? $(this).attr('id').replace('row-multa-', '') : null;
                if (rowIdMulta) {
                    fobTotalGeralMulta += MoneyUtils.parseMoney($(`#fob_total_usd_multa-${rowIdMulta}`).val()) || 0;
                }
            });

            if (fobTotalGeralMulta === 0) {
                $(`#seguro_usd_multa-${rowId}`).val(MoneyUtils.formatMoney(0, 2));
                $(`#seguro_brl_multa-${rowId}`).val(MoneyUtils.formatMoney(0, 2));
                return;
            }

            const seguroTotal = MoneyUtils.parseMoney($('#seguro_internacional').val()) || 0;
            const moedaSeguro = $('#seguro_internacional_moeda').val();
            let seguroTotalUSD = seguroTotal;

            // Converter para USD se necessário
            if (moedaSeguro && moedaSeguro !== 'USD') {
                const cotacoesProcesso = getCotacaoesProcesso();
                const cotacaoSeguro = MoneyUtils.parseMoney($('#cotacao_seguro_internacional').val()) || 0;
                const cotacaoUSD = cotacoesProcesso['USD']?.venda ?? 1;
                if (cotacaoSeguro > 0 && cotacaoUSD > 0) {
                    const moedaEmUSD = cotacaoSeguro / cotacaoUSD;
                    seguroTotalUSD = seguroTotal * moedaEmUSD;
                }
            }

            // Proporcional ao FOB
            const seguroUSD = (seguroTotalUSD / fobTotalGeralMulta) * fobTotalMulta;
            const cotacoes = getCotacaoesProcesso();
            const cotacaoUsd = cotacoes['USD']?.venda || 1;
            const seguroBrl = seguroUSD * cotacaoUsd;

            $(`#seguro_usd_multa-${rowId}`).val(MoneyUtils.formatMoney(seguroUSD, 2));
            $(`#seguro_brl_multa-${rowId}`).val(MoneyUtils.formatMoney(seguroBrl, 2));
        }

        // Função para calcular Acréscimo Frete da tabela multa
        function calcularAcrescimoFreteMulta(rowId) {
            const fobTotalMulta = MoneyUtils.parseMoney($(`#fob_total_usd_multa-${rowId}`).val()) || 0;
            
            // Calcular FOB total geral da tabela multa
            let fobTotalGeralMulta = 0;
            $('#productsBodyMulta tr.linhas-input').each(function() {
                const rowIdMulta = $(this).attr('id') ? $(this).attr('id').replace('row-multa-', '') : null;
                if (rowIdMulta) {
                    fobTotalGeralMulta += MoneyUtils.parseMoney($(`#fob_total_usd_multa-${rowIdMulta}`).val()) || 0;
                }
            });

            if (fobTotalGeralMulta === 0) {
                $(`#acresc_frete_usd_multa-${rowId}`).val(MoneyUtils.formatMoney(0, 2));
                $(`#acresc_frete_brl_multa-${rowId}`).val(MoneyUtils.formatMoney(0, 2));
                return;
            }

            const acrescimoFreteBase = MoneyUtils.parseMoney($('#acrescimo_frete').val()) || 0;
            const moedaAcrescimo = $('#acrescimo_frete_moeda').val();
            let acrescimoFreteUSD = acrescimoFreteBase;

            // Converter para USD se necessário
            if (moedaAcrescimo && moedaAcrescimo !== 'USD') {
                const cotacoesProcesso = getCotacaoesProcesso();
                const cotacaoAcrescimo = MoneyUtils.parseMoney($('#cotacao_acrescimo_frete').val()) || 0;
                const cotacaoUSD = cotacoesProcesso['USD']?.venda ?? 1;
                if (cotacaoAcrescimo > 0 && cotacaoUSD > 0) {
                    const moedaEmUSD = cotacaoAcrescimo / cotacaoUSD;
                    acrescimoFreteUSD = acrescimoFreteBase * moedaEmUSD;
                }
            }

            // Proporcional ao FOB
            const acrescFreteUSD = (acrescimoFreteUSD / fobTotalGeralMulta) * fobTotalMulta;
            const cotacoes = getCotacaoesProcesso();
            const cotacaoUsd = cotacoes['USD']?.venda || 1;
            const acrescFreteBrl = acrescFreteUSD * cotacaoUsd;

            $(`#acresc_frete_usd_multa-${rowId}`).val(MoneyUtils.formatMoney(acrescFreteUSD, 2));
            $(`#acresc_frete_brl_multa-${rowId}`).val(MoneyUtils.formatMoney(acrescFreteBrl, 2));
        }

        // Função para calcular THC da tabela multa
        function calcularTHCMulta(rowId) {
            const thcCapataziaBase = MoneyUtils.parseMoney($('#thc_capatazia').val()) || 0;

            // Calcular fator peso da linha
            const totalPesoLiq = calcularPesoTotalMulta();
            const pesoLinha = MoneyUtils.parseMoney($(`#peso_liquido_total_multa-${rowId}`).val()) || 0;
            const fatorPeso = totalPesoLiq > 0 ? pesoLinha / totalPesoLiq : 0;

            const thcBrl = thcCapataziaBase * fatorPeso;
            const cotacoes = getCotacaoesProcesso();
            const cotacaoUsd = cotacoes['USD']?.venda || 1;
            const thcUsd = cotacaoUsd > 0 ? thcBrl / cotacaoUsd : 0;

            $(`#thc_usd_multa-${rowId}`).val(MoneyUtils.formatMoney(thcUsd, 2));
            $(`#thc_brl_multa-${rowId}`).val(MoneyUtils.formatMoney(thcBrl, 2));
        }

        // Event listeners para copiar valores quando campos são preenchidos
        $(document).on('change select2:select', '.selectProductMulta', function() {
            const rowId = $(this).data('row');
            if (rowId !== undefined) {
                // Preencher campos de código, NCM e descrição quando produto é selecionado
                let products = JSON.parse($('#productsClient').val());
                let productObject = products.find(el => el.id == this.value);
                
                if (productObject) {
                    $(`#codigo_multa-${rowId}`).val(productObject.codigo);
                    $(`#ncm_multa-${rowId}`).val(productObject.ncm);
                    $(`#descricao_multa-${rowId}`).val(productObject.descricao || '');
                }
                
                // Verificar e copiar valores de produtos relacionados (sem mostrar aviso ainda, pois pode não ter adição/item)
                const produtoId = $(`#produto_multa_id-${rowId}`).val();
                const adicao = $(`#adicao_multa-${rowId}`).val();
                const item = $(`#item_multa-${rowId}`).val();
                
                // Só verificar se tiver todos os dados
                if (produtoId && adicao && item) {
                    verificarECopiarValoresMulta(rowId, true);
                }
                
                // Recalcular campos da linha
                atualizarCamposMulta(rowId, false);
                
                // Atualizar totalizadores e card de resumo
                setTimeout(function() {
                    calcularValoresCPTMulta();
                    atualizarTotalizadoresMulta();
                    atualizarCardResumoMulta();
                }, 100);
            }
        });

        $(document).on('change blur', '[id^="adicao_multa-"], [id^="item_multa-"]', function() {
            const id = $(this).attr('id');
            const rowId = id.replace(/^(adicao_multa-|item_multa-)/, '');
            
            // Limpar valores não numéricos e formatar como inteiro
            let valor = $(this).val();
            if (valor) {
                valor = valor.toString().replace(/[^0-9]/g, '');
                if (valor) {
                    $(this).val(parseInt(valor, 10));
                } else {
                    $(this).val('');
                }
            }

            if (rowId) {
                const produtoId = $(`#produto_multa_id-${rowId}`).val();
                const adicao = $(`#adicao_multa-${rowId}`).val();
                const item = $(`#item_multa-${rowId}`).val();
                
                // Só verificar se tiver todos os dados
                if (produtoId && adicao && item) {
                    const encontrado = verificarECopiarValoresMulta(rowId, true);
                    if (!encontrado) {
                        // Recalcular campos para preencher com valores calculados
                        atualizarCamposMulta(rowId, false);
                    } else {
                        // Se encontrou, também recalcular para garantir que tudo está atualizado
                        atualizarCamposMulta(rowId, false);
                    }
                }
            }
        });

        // Função para obter valores base da linha multa (similar a obterValoresBase)
        function obterValoresBaseMulta(rowId) {
            let pesoTotal = MoneyUtils.parseMoney($(`#peso_liquido_total_multa-${rowId}`).val()) || 0;
            let quantidade = MoneyUtils.parseMoney($(`#quantidade_multa-${rowId}`).val()) || 0;
            let fobUnitario = MoneyUtils.parseMoney($(`#fob_unit_usd_multa-${rowId}`).val()) || 0;

            if (isNaN(pesoTotal)) pesoTotal = 0;
            if (isNaN(quantidade)) quantidade = 0;
            if (isNaN(fobUnitario)) fobUnitario = 0;

            return {
                pesoTotal: pesoTotal,
                fobUnitario: fobUnitario,
                quantidade: quantidade
            };
        }

        // Função para calcular peso total da tabela multa
        function calcularPesoTotalMulta() {
            let total = 0;
            $('.pesoLiqTotalMulta').each(function() {
                total += MoneyUtils.parseMoney($(this).val()) || 0;
            });
            return total;
        }

        // Função para recalcular fator peso da tabela multa
        function recalcularFatorPesoMulta(totalPeso, currentRowId) {
            let fator = 0;
            $('.pesoLiqTotalMulta').each(function() {
                const rowId = $(this).data('row');
                const valor = MoneyUtils.parseMoney($(this).val()) || 0;
                const fatorLinha = valor / (totalPeso || 1);
                $(`#fator_peso_multa-${rowId}`).val(MoneyUtils.formatMoney(fatorLinha, 8));
                if (rowId == currentRowId) fator = fatorLinha;
            });
            return fator;
        }

        // Função para calcular peso líquido unitário da tabela multa
        function calcularPesoLiquidoUnitarioMulta(rowId) {
            const { pesoTotal, quantidade } = obterValoresBaseMulta(rowId);
            const pesoLiqUnit = quantidade > 0 ? pesoTotal / quantidade : 0;
            $(`#peso_liquido_unitario_multa-${rowId}`).val(MoneyUtils.formatMoney(pesoLiqUnit, 6));
        }

        // Função para calcular FOB total da tabela multa
        function calcularFobTotalMulta(rowId) {
            const { fobUnitario, quantidade } = obterValoresBaseMulta(rowId);
            const fobTotal = fobUnitario * quantidade;
            
            const cotacoes = getCotacaoesProcesso();
            const cotacaoUsd = cotacoes['USD']?.venda || 1;
            const fobTotalBrl = fobTotal * cotacaoUsd;

            $(`#fob_total_usd_multa-${rowId}`).val(MoneyUtils.formatMoney(fobTotal, 7));
            $(`#fob_total_brl_multa-${rowId}`).val(MoneyUtils.formatMoney(fobTotalBrl, 7));
        }

        // Calcular valores CPT da tabela multa
        function calcularValoresCPTMulta() {
            // FOB da tabela multa (isolado)
            let totalFobUsdMulta = 0;
            $('#productsBodyMulta tr.linhas-input').each(function() {
                const rowId = $(this).attr('id') ? $(this).attr('id').replace('row-multa-', '') : null;
                if (!rowId) return;

                const fobTotalUsd = MoneyUtils.parseMoney($(`#fob_total_usd_multa-${rowId}`).val()) || 0;
                totalFobUsdMulta += fobTotalUsd;
            });

            // Valores do cabeçalho (DADOS PROCESSO)
            const freteInternacionalTotalUsd = MoneyUtils.parseMoney($('#frete_internacional_usd').val()) || 0;
            const seguroInternacionalTotalUsd = MoneyUtils.parseMoney($('#seguro_internacional_usd').val()) || 0;
            const acrescimoFreteDolar = MoneyUtils.parseMoney($('#acrescimo_frete_usd').val()) || 0;
            
            // Service Charges USD do cabeçalho
            let valorTotalServiceChargesUsd = 0;
            const moedaServiceCharges = $('#service_charges_moeda').val();
            if (moedaServiceCharges && moedaServiceCharges !== 'USD') {
                // Se a moeda não for USD, somar dos produtos (não da multa)
                $('#productsBody tr:not(.separador-adicao)').each(function() {
                    const rowId = this.id.replace('row-', '');
                    const serviceChargesUsd = MoneyUtils.parseMoney($(`#service_charges-${rowId}`).val()) || 0;
                    valorTotalServiceChargesUsd += serviceChargesUsd;
                });
            } else {
                valorTotalServiceChargesUsd = MoneyUtils.parseMoney($('#service_charges_usd').val()) || 0;
            }

            // CPT = FOB Multa + Frete + Service Charges + Acréscimo + Seguro
            const cptUsd = totalFobUsdMulta + freteInternacionalTotalUsd + valorTotalServiceChargesUsd + acrescimoFreteDolar + seguroInternacionalTotalUsd;
            
            const cotacoes = getCotacaoesProcesso();
            const cotacaoUsd = cotacoes['USD']?.venda || 1;
            const cptBrl = cptUsd * cotacaoUsd;

            $('#valor_cpt_usd_multa').val(MoneyUtils.formatMoney(cptUsd, 2));
            $('#valor_cpt_brl_multa').val(MoneyUtils.formatMoney(cptBrl, 2));
        }

        // Calcular valores CFR da tabela multa (somar, não calcular)
        function calcularValoresCFRMulta(rowId) {
            const fobTotalUsd = MoneyUtils.parseMoney($(`#fob_total_usd_multa-${rowId}`).val()) || 0;
            const freteUsd = MoneyUtils.parseMoney($(`#frete_usd_multa-${rowId}`).val()) || 0;
            const serviceChargesUsd = MoneyUtils.parseMoney($(`#service_charges_multa-${rowId}`).val()) || 0;
            const acrescFreteUsd = MoneyUtils.parseMoney($(`#acresc_frete_usd_multa-${rowId}`).val()) || 0;

            const nacionalizacao = getNacionalizacaoAtual();
            let vlrCrfTotal = 0;

            if (nacionalizacao === 'santa_catarina') {
                vlrCrfTotal = fobTotalUsd + freteUsd + serviceChargesUsd + acrescFreteUsd;
            } else {
                vlrCrfTotal = fobTotalUsd + freteUsd;
            }

            const quantidade = MoneyUtils.parseMoney($(`#quantidade_multa-${rowId}`).val()) || 0;
            const vlrCrfUnit = quantidade > 0 ? vlrCrfTotal / quantidade : 0;

            $(`#vlr_crf_total_multa-${rowId}`).val(MoneyUtils.formatMoney(vlrCrfTotal, 4));
            $(`#vlr_crf_unit_multa-${rowId}`).val(MoneyUtils.formatMoney(vlrCrfUnit, 4));
        }

        // Calcular valor aduaneiro da tabela multa (somar valores existentes)
        function calcularValorAduaneiroMulta(rowId) {
            const vlrCrfTotal = MoneyUtils.parseMoney($(`#vlr_crf_total_multa-${rowId}`).val()) || 0;
            const seguroUsd = MoneyUtils.parseMoney($(`#seguro_usd_multa-${rowId}`).val()) || 0;
            const thcUsd = MoneyUtils.parseMoney($(`#thc_usd_multa-${rowId}`).val()) || 0;

            const vlrAduaneiroUsd = vlrCrfTotal + seguroUsd + thcUsd;
            const cotacoes = getCotacaoesProcesso();
            const cotacaoUsd = cotacoes['USD']?.venda || 1;
            const vlrAduaneiroBrl = vlrAduaneiroUsd * cotacaoUsd;

            $(`#valor_aduaneiro_usd_multa-${rowId}`).val(MoneyUtils.formatMoney(vlrAduaneiroUsd, 2));
            $(`#valor_aduaneiro_brl_multa-${rowId}`).val(MoneyUtils.formatMoney(vlrAduaneiroBrl, 2));
        }

        // Atualizar campos de impostos da tabela multa
        function atualizarCamposMulta(rowId, mostrarAviso = false) {
            // Primeiro, verificar e copiar valores se necessário
            const produtoEquivalenteEncontrado = verificarECopiarValoresMulta(rowId, mostrarAviso);

            // Calcular peso líquido unitário
            calcularPesoLiquidoUnitarioMulta(rowId);

            // Calcular FOB total
            calcularFobTotalMulta(rowId);

            // Calcular fator peso
            const totalPesoLiq = calcularPesoTotalMulta();
            recalcularFatorPesoMulta(totalPesoLiq, rowId);

            // Se não encontrou produto equivalente, calcular os valores automaticamente
            if (!produtoEquivalenteEncontrado) {
                // Verificar se tem os dados mínimos (produto, adição, item)
                const produtoId = $(`#produto_multa_id-${rowId}`).val();
                const adicao = $(`#adicao_multa-${rowId}`).val();
                const item = $(`#item_multa-${rowId}`).val();
                
                if (produtoId && adicao && item) {
                    // Calcular todos os valores usando fator peso/FOB
                    calcularServiceChargesMulta(rowId);
                    calcularFreteInternacionalMulta(rowId);
                    calcularSeguroInternacionalMulta(rowId);
                    calcularAcrescimoFreteMulta(rowId);
                    calcularTHCMulta(rowId);
                }
            } else {
                // Se encontrou produto equivalente, verificar se os campos estão vazios e calcular se necessário
                const freteUsd = MoneyUtils.parseMoney($(`#frete_usd_multa-${rowId}`).val()) || 0;
                const serviceCharges = MoneyUtils.parseMoney($(`#service_charges_multa-${rowId}`).val()) || 0;
                const seguroUsd = MoneyUtils.parseMoney($(`#seguro_usd_multa-${rowId}`).val()) || 0;
                const acrescFreteUsd = MoneyUtils.parseMoney($(`#acresc_frete_usd_multa-${rowId}`).val()) || 0;
                const thcUsd = MoneyUtils.parseMoney($(`#thc_usd_multa-${rowId}`).val()) || 0;

                // Se algum campo estiver vazio, calcular
                if (freteUsd === 0) calcularFreteInternacionalMulta(rowId);
                if (serviceCharges === 0) calcularServiceChargesMulta(rowId);
                if (seguroUsd === 0) calcularSeguroInternacionalMulta(rowId);
                if (acrescFreteUsd === 0) calcularAcrescimoFreteMulta(rowId);
                if (thcUsd === 0) calcularTHCMulta(rowId);
            }

            // Calcular CFR
            calcularValoresCFRMulta(rowId);

            // Calcular valor aduaneiro
            calcularValorAduaneiroMulta(rowId);

            // Obter valores da tabela multa
            const valorAduaneiroBrlMulta = MoneyUtils.parseMoney($(`#valor_aduaneiro_brl_multa-${rowId}`).val()) || 0;
            const iiPercentMulta = MoneyUtils.parsePercentage($(`#ii_percent_multa-${rowId}`).val()) || 0;
            const ipiPercentMulta = MoneyUtils.parsePercentage($(`#ipi_percent_multa-${rowId}`).val()) || 0;
            const pisPercentMulta = MoneyUtils.parsePercentage($(`#pis_percent_multa-${rowId}`).val()) || 0;
            const cofinsPercentMulta = MoneyUtils.parsePercentage($(`#cofins_percent_multa-${rowId}`).val()) || 0;

            // Calcular impostos
            const valorIiMulta = valorAduaneiroBrlMulta * (iiPercentMulta );
            const baseIpiMulta = valorAduaneiroBrlMulta + valorIiMulta;
            const valorIpiMulta = baseIpiMulta * (ipiPercentMulta );
            const basePisCofinsMulta = valorAduaneiroBrlMulta;
            const valorPisMulta = basePisCofinsMulta * (pisPercentMulta );
            const valorCofinsMulta = basePisCofinsMulta * (cofinsPercentMulta);

            // Obter despesa aduaneira
            const despesaAduaneiraMulta = MoneyUtils.parseMoney($(`#despesa_aduaneira_multa-${rowId}`).val()) || 0;

            // Obter percentual ICMS
            const icmsPercentMulta = MoneyUtils.parsePercentage($(`#icms_percent_multa-${rowId}`).val()) || 0;
            const icmsDecimalMulta = icmsPercentMulta;

            // Calcular base ICMS reduzido
            let reducaoMulta = 1;
            if ($(`#reducao_multa-${rowId}`).val() && MoneyUtils.parseMoney($(`#reducao_multa-${rowId}`).val()) > 0) {
                reducaoMulta = MoneyUtils.parseMoney($(`#reducao_multa-${rowId}`).val());
            }

            let baseIcmsReduzidoMulta = 0;
            if (icmsDecimalMulta < 1) {
                baseIcmsReduzidoMulta = (valorAduaneiroBrlMulta + valorIiMulta + valorIpiMulta + valorPisMulta + valorCofinsMulta + despesaAduaneiraMulta) / (1 - icmsDecimalMulta) * reducaoMulta;
            }
            const valorIcmsReduzidoMultaCalculado = baseIcmsReduzidoMulta * icmsDecimalMulta;
            const reducaoFinalMulta = valorAduaneiroBrlMulta > 0 ? valorIcmsReduzidoMultaCalculado / valorAduaneiroBrlMulta : 0;

            // Atualizar campos de impostos
            $(`#valor_ii_multa-${rowId}`).val(MoneyUtils.formatMoney(valorIiMulta, 2));
            $(`#base_ipi_multa-${rowId}`).val(MoneyUtils.formatMoney(baseIpiMulta, 2));
            $(`#valor_ipi_multa-${rowId}`).val(MoneyUtils.formatMoney(valorIpiMulta, 2));
            $(`#base_pis_cofins_multa-${rowId}`).val(MoneyUtils.formatMoney(basePisCofinsMulta, 2));
            $(`#valor_pis_multa-${rowId}`).val(MoneyUtils.formatMoney(valorPisMulta, 2));
            $(`#valor_cofins_multa-${rowId}`).val(MoneyUtils.formatMoney(valorCofinsMulta, 2));
            $(`#despesa_aduaneira_multa-${rowId}`).val(MoneyUtils.formatMoney(despesaAduaneiraMulta, 2));
            // reducao_multa não é preenchido automaticamente - deve ser preenchido manualmente pelo usuário

            // Copiar valores de impostos para campos pos_despesa (mesmos valores dos anteriores)
            $(`#vlr_ii_pos_despesa_multa-${rowId}`).val(MoneyUtils.formatMoney(valorIiMulta, 2));
            $(`#vlr_ipi_pos_despesa_multa-${rowId}`).val(MoneyUtils.formatMoney(valorIpiMulta, 2));
            $(`#vlr_pis_pos_despesa_multa-${rowId}`).val(MoneyUtils.formatMoney(valorPisMulta, 2));
            $(`#vlr_cofins_pos_despesa_multa-${rowId}`).val(MoneyUtils.formatMoney(valorCofinsMulta, 2));

            // Calcular valores de nova_ncm usando as taxas da nova NCM
            const iiNovaNcmPercentMulta = MoneyUtils.parsePercentage($(`#ii_nova_ncm_percent_multa-${rowId}`).val()) || 0;
            const ipiNovaNcmPercentMulta = MoneyUtils.parsePercentage($(`#ipi_nova_ncm_percent_multa-${rowId}`).val()) || 0;
            const pisNovaNcmPercentMulta = MoneyUtils.parsePercentage($(`#pis_nova_ncm_percent_multa-${rowId}`).val()) || 0;
            const cofinsNovaNcmPercentMulta = MoneyUtils.parsePercentage($(`#cofins_nova_ncm_percent_multa-${rowId}`).val()) || 0;

            // Calcular valores usando as taxas da nova NCM
            const valorIiNovaNcmMulta = valorAduaneiroBrlMulta * (iiNovaNcmPercentMulta );
            const baseIpiNovaNcmMulta = valorAduaneiroBrlMulta + valorIiNovaNcmMulta;
            const valorIpiNovaNcmMulta = baseIpiNovaNcmMulta * (ipiNovaNcmPercentMulta );
            const basePisCofinsNovaNcmMulta = valorAduaneiroBrlMulta;
            const valorPisNovaNcmMulta = basePisCofinsNovaNcmMulta * (pisNovaNcmPercentMulta );
            const valorCofinsNovaNcmMulta = basePisCofinsNovaNcmMulta * (cofinsNovaNcmPercentMulta );

            // Atualizar campos de nova_ncm
            $(`#vlr_ii_nova_ncm_multa-${rowId}`).val(MoneyUtils.formatMoney(valorIiNovaNcmMulta, 2));
            $(`#vlr_ipi_nova_ncm_multa-${rowId}`).val(MoneyUtils.formatMoney(valorIpiNovaNcmMulta, 2));
            $(`#vlr_pis_nova_ncm_multa-${rowId}`).val(MoneyUtils.formatMoney(valorPisNovaNcmMulta, 2));
            $(`#vlr_cofins_nova_ncm_multa-${rowId}`).val(MoneyUtils.formatMoney(valorCofinsNovaNcmMulta, 2));

            // Calcular valores de recalc (diferença entre novo e velho: novo - velho)
            const vlrIiRecalc = valorIiNovaNcmMulta - valorIiMulta;
            const vlrIpiRecalc = valorIpiNovaNcmMulta - valorIpiMulta;
            const vlrPisRecalc = valorPisNovaNcmMulta - valorPisMulta;
            const vlrCofinsRecalc = valorCofinsNovaNcmMulta - valorCofinsMulta;

            $(`#vlr_ii_recalc_multa-${rowId}`).val(MoneyUtils.formatMoney(vlrIiRecalc, 2));
            $(`#vlr_ipi_recalc_multa-${rowId}`).val(MoneyUtils.formatMoney(vlrIpiRecalc, 2));
            $(`#vlr_pis_recalc_multa-${rowId}`).val(MoneyUtils.formatMoney(vlrPisRecalc, 2));
            $(`#vlr_cofins_recalc_multa-${rowId}`).val(MoneyUtils.formatMoney(vlrCofinsRecalc, 2));

            // Calcular campos percent_aduaneiro como recalc * 0.375 (não são porcentagens, são valores calculados)
            $(`#ii_percent_aduaneiro_multa-${rowId}`).val(MoneyUtils.formatMoney(vlrIiRecalc * 0.375, 2));
            $(`#ipi_percent_aduaneiro_multa-${rowId}`).val(MoneyUtils.formatMoney(vlrIpiRecalc * 0.375, 2));
            $(`#pis_percent_aduaneiro_multa-${rowId}`).val(MoneyUtils.formatMoney(vlrPisRecalc * 0.375, 2));
            $(`#cofins_percent_aduaneiro_multa-${rowId}`).val(MoneyUtils.formatMoney(vlrCofinsRecalc * 0.375, 2));
        }

        // Calcular valor_aduaneiro_multa por adição (fórmula complexa)
        function calcularValorAduaneiroMultaPorAdicao() {
            // Agrupar linhas por adição
            const linhasPorAdicao = {};
            
            $('#productsBodyMulta tr.linhas-input').each(function() {
                const rowId = $(this).attr('id') ? $(this).attr('id').replace('row-multa-', '') : null;
                if (!rowId) return;

                const adicao = $(`#adicao_multa-${rowId}`).val();
                if (!adicao) return;

                const adicaoLimpa = adicao.toString().replace(/[^0-9]/g, '');
                if (!adicaoLimpa) return;

                if (!linhasPorAdicao[adicaoLimpa]) {
                    linhasPorAdicao[adicaoLimpa] = [];
                }
                linhasPorAdicao[adicaoLimpa].push(rowId);
            });

            // Calcular para cada adição
            Object.keys(linhasPorAdicao).forEach(adicao => {
                const linhas = linhasPorAdicao[adicao];
                
                let somaValorAduaneiroBrl = 0;
                linhas.forEach(rowId => {
                    const valorAduaneiroBrl = MoneyUtils.parseMoney($(`#valor_aduaneiro_brl_multa-${rowId}`).val()) || 0;
                    somaValorAduaneiroBrl += valorAduaneiroBrl;
                });

                const umPorCento = somaValorAduaneiroBrl * 0.01;
                let resultado = umPorCento < 500 ? 500 : umPorCento;

                if (linhas.length > 1) {
                    resultado = resultado / 2;
                }

                linhas.forEach(rowId => {
                    $(`#valor_aduaneiro_multa-${rowId}`).val(MoneyUtils.formatMoney(resultado, 2));
                });
            });
        }

        // Recalcular toda a tabela multa
        function recalcularTodaTabelaMulta() {
            // Primeiro, copiar valores de produtos relacionados
            $('#productsBodyMulta tr.linhas-input').each(function() {
                const rowId = $(this).attr('id') ? $(this).attr('id').replace('row-multa-', '') : null;
                if (rowId !== null) {
                    copiarValoresProdutosParaMulta(rowId);
                }
            });

            // Calcular peso total e recalcular fator peso para todas as linhas
            const totalPesoLiq = calcularPesoTotalMulta();
            $('#productsBodyMulta tr.linhas-input').each(function() {
                const rowId = $(this).attr('id') ? $(this).attr('id').replace('row-multa-', '') : null;
                if (rowId !== null) {
                    calcularPesoLiquidoUnitarioMulta(rowId);
                    calcularFobTotalMulta(rowId);
                    recalcularFatorPesoMulta(totalPesoLiq, rowId);
                }
            });

            calcularValoresCPTMulta();

            // Atualizar campos de impostos e valores aduaneiros
            $('#productsBodyMulta tr.linhas-input').each(function() {
                const rowId = $(this).attr('id') ? $(this).attr('id').replace('row-multa-', '') : null;
                if (rowId !== null) {
                    atualizarCamposMulta(rowId);
                }
            });

            calcularValorAduaneiroMultaPorAdicao();
            atualizarTotalizadoresMulta();
        }

        // Função para atualizar totalizadores da tabela multa
        function atualizarTotalizadoresMulta() {
            const rows = $('#productsBodyMulta tr.linhas-input');
            const tfoot = $('#resultado-totalizadores-multa');
            
            if (tfoot.length === 0) {
                return;
            }

            if (rows.length === 0) {
                tfoot.empty();
                tfoot.append('<tr><td colspan="100" style="text-align: center; font-weight: bold;">Nenhum produto cadastrado</td></tr>');
                return;
            }

            let totais = {
                quantidade: 0, peso_liquido_total: 0, fator_peso: 0,
                fob_total_usd: 0, fob_total_brl: 0, service_charges: 0, service_charges_brl: 0,
                frete_usd: 0, frete_brl: 0, acresc_frete_usd: 0, acresc_frete_brl: 0,
                vlr_crf_unit: 0, vlr_crf_total: 0, seguro_usd: 0, seguro_brl: 0,
                thc_usd: 0, thc_brl: 0, valor_aduaneiro_usd: 0, valor_aduaneiro_brl: 0,
                reducao: 0, valor_ii: 0, base_ipi: 0, valor_ipi: 0, base_pis_cofins: 0,
                valor_pis: 0, valor_cofins: 0, despesa_aduaneira: 0,
                vlr_ii_pos_despesa: 0, vlr_ipi_pos_despesa: 0, vlr_pis_pos_despesa: 0, vlr_cofins_pos_despesa: 0,
                vlr_ii_nova_ncm: 0, vlr_ipi_nova_ncm: 0, vlr_pis_nova_ncm: 0, vlr_cofins_nova_ncm: 0,
                vlr_ii_recalc: 0, vlr_ipi_recalc: 0, vlr_pis_recalc: 0, vlr_cofins_recalc: 0,
                valor_aduaneiro_multa: 0, ii_percent_aduaneiro: 0, ipi_percent_aduaneiro: 0,
                pis_percent_aduaneiro: 0, cofins_percent_aduaneiro: 0
            };

            rows.each(function() {
                const rowId = $(this).attr('id') ? $(this).attr('id').replace('row-multa-', '') : null;
                if (!rowId) return;

                Object.keys(totais).forEach(campo => {
                    const campoId = campo.endsWith('_multa') ? campo : campo + '_multa';
                    const valor = MoneyUtils.parseMoney($(`#${campoId}-${rowId}`).val()) || 0;
                    totais[campo] += valor;
                });
            });

            const ordemColunas = [
                'adicao', 'item', 'codigo', 'ncm', 'quantidade', 'peso_liquido_unitario', 'peso_liquido_total',
                'fator_peso', 'fob_unit_usd', 'fob_total_usd', 'fob_total_brl', 'service_charges', 'service_charges_brl',
                'frete_usd', 'frete_brl', 'acresc_frete_usd', 'acresc_frete_brl', 'vlr_crf_unit', 'vlr_crf_total',
                'seguro_usd', 'seguro_brl', 'thc_usd', 'thc_brl', 'valor_aduaneiro_usd', 'valor_aduaneiro_brl',
                'ii_percent', 'ipi_percent', 'pis_percent', 'cofins_percent', 'icms_percent', 'icms_reduzido_percent',
                'reducao', 'valor_ii', 'base_ipi', 'valor_ipi', 'base_pis_cofins', 'valor_pis', 'valor_cofins',
                'despesa_aduaneira', 'vlr_ii_pos_despesa', 'vlr_ipi_pos_despesa', 'vlr_pis_pos_despesa', 'vlr_cofins_pos_despesa',
                'nova_ncm', 'ii_nova_ncm_percent', 'ipi_nova_ncm_percent', 'pis_nova_ncm_percent', 'cofins_nova_ncm_percent',
                'vlr_ii_nova_ncm', 'vlr_ipi_nova_ncm', 'vlr_pis_nova_ncm', 'vlr_cofins_nova_ncm',
                'vlr_ii_recalc', 'vlr_ipi_recalc', 'vlr_pis_recalc', 'vlr_cofins_recalc',
                'valor_aduaneiro_multa', 'ii_percent_aduaneiro', 'ipi_percent_aduaneiro',
                'pis_percent_aduaneiro', 'cofins_percent_aduaneiro'
            ];

            const camposNaoTotalizaveis = [
                'adicao', 'item', 'codigo', 'ncm', 'peso_liquido_unitario', 'fob_unit_usd', 'ii_percent', 'ipi_percent', 'pis_percent', 'cofins_percent',
                'icms_percent', 'icms_reduzido_percent', 'nova_ncm', 'ii_nova_ncm_percent', 'ipi_nova_ncm_percent',
                'pis_nova_ncm_percent', 'cofins_nova_ncm_percent'
            ];

            tfoot.empty();
            let tr = '<tr>';
            tr += '<td style="text-align: right; font-weight: bold;">TOTAIS:</td>';
            tr += '<td></td>';

            ordemColunas.forEach(campo => {
                if (camposNaoTotalizaveis.includes(campo)) {
                    tr += '<td></td>';
                } else if (totais[campo] !== undefined) {
                    let decimais = 2; // Totalizadores sempre com 2 casas decimais
                    if (campo === 'quantidade') decimais = 0; // Quantidade é inteiro
                    tr += `<td style="font-weight: bold; text-align: right;">${MoneyUtils.formatMoney(totais[campo], decimais)}</td>`;
                } else {
                    tr += '<td></td>';
                }
            });

            tr += '</tr>';
            tfoot.append(tr);
            atualizarCardResumoMulta();
        }

        // Função para atualizar card de resumo multa
        function atualizarCardResumoMulta() {
            const rows = $('#productsBodyMulta tr.linhas-input');
            
            let vlrIiRecalcTotal = 0, vlrIpiRecalcTotal = 0, vlrPisRecalcTotal = 0, vlrCofinsRecalcTotal = 0;
            let valorAduaneiroMultaTotal = 0;
            let iiPercentAduaneiroTotal = 0, ipiPercentAduaneiroTotal = 0, pisPercentAduaneiroTotal = 0, cofinsPercentAduaneiroTotal = 0;

            rows.each(function() {
                const rowId = $(this).attr('id') ? $(this).attr('id').replace('row-multa-', '') : null;
                if (!rowId) return;

                vlrIiRecalcTotal += MoneyUtils.parseMoney($(`#vlr_ii_recalc_multa-${rowId}`).val()) || 0;
                vlrIpiRecalcTotal += MoneyUtils.parseMoney($(`#vlr_ipi_recalc_multa-${rowId}`).val()) || 0;
                vlrPisRecalcTotal += MoneyUtils.parseMoney($(`#vlr_pis_recalc_multa-${rowId}`).val()) || 0;
                vlrCofinsRecalcTotal += MoneyUtils.parseMoney($(`#vlr_cofins_recalc_multa-${rowId}`).val()) || 0;
                valorAduaneiroMultaTotal += MoneyUtils.parseMoney($(`#valor_aduaneiro_multa-${rowId}`).val()) || 0;
                iiPercentAduaneiroTotal += MoneyUtils.parseMoney($(`#ii_percent_aduaneiro_multa-${rowId}`).val()) || 0;
                ipiPercentAduaneiroTotal += MoneyUtils.parseMoney($(`#ipi_percent_aduaneiro_multa-${rowId}`).val()) || 0;
                pisPercentAduaneiroTotal += MoneyUtils.parseMoney($(`#pis_percent_aduaneiro_multa-${rowId}`).val()) || 0;
                cofinsPercentAduaneiroTotal += MoneyUtils.parseMoney($(`#cofins_percent_aduaneiro_multa-${rowId}`).val()) || 0;
            });

            const diferencaImpostos = vlrIiRecalcTotal + vlrIpiRecalcTotal + vlrPisRecalcTotal + vlrCofinsRecalcTotal;
            const multaFiscal711 = valorAduaneiroMultaTotal;
            const multaOficio725 = iiPercentAduaneiroTotal + ipiPercentAduaneiroTotal + pisPercentAduaneiroTotal + cofinsPercentAduaneiroTotal;
            const total = diferencaImpostos + multaFiscal711 + multaOficio725;

            $('#diferenca-impostos-multa').text(MoneyUtils.formatMoney(diferencaImpostos, 2));
            $('#multa-fiscal-711-multa').text(MoneyUtils.formatMoney(multaFiscal711, 2));
            $('#multa-oficio-725-multa').text(MoneyUtils.formatMoney(multaOficio725, 2));
            $('#total-multa').text(MoneyUtils.formatMoney(total, 2));
        }

        // Event listener para botão recalcular
        $(document).on('click', '#btnRecalcularMulta', function() {
            recalcularTodaTabelaMulta();
        });

        // Exclusão via AJAX
        $(document).on('click', '#btnDeleteSelectedProdutosMulta', function() {
            const ids = [];
            $('.select-produto-multa:checked').each(function() {
                const id = $(this).val();
                if (id && /^[0-9]+$/.test(id)) {
                    ids.push(parseInt(id, 10));
                }
            });

            if (ids.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Selecione pelo menos um produto para excluir.', confirmButtonText: 'OK' });
                return;
            }

            Swal.fire({
                title: 'Confirmar exclusão',
                text: `Deseja realmente excluir ${ids.length} produto(s)?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("processo.produtos.multa.batchDelete") }}',
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}', ids: ids },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({ icon: 'success', title: 'Sucesso', text: `${response.deleted_count} produto(s) excluído(s) com sucesso.`, confirmButtonText: 'OK' }).then(() => { location.reload(); });
                            } else {
                                Swal.fire({ icon: 'error', title: 'Erro', text: response.message || 'Erro ao excluir produtos.', confirmButtonText: 'OK' });
                            }
                        },
                        error: function() {
                            Swal.fire({ icon: 'error', title: 'Erro', text: 'Erro ao excluir produtos. Tente novamente.', confirmButtonText: 'OK' });
                        }
                    });
                }
            });
        });

        // Exclusão individual
        $(document).on('click', '.btn-remove-multa', function() {
            const rowId = $(this).data('id');
            const produtoMultaId = $(`#processo_produto_multa_id-${rowId}`).val();

            if (!produtoMultaId || produtoMultaId === '') {
                $(`#row-multa-${rowId}`).remove();
                return;
            }

            Swal.fire({
                title: 'Confirmar exclusão',
                text: 'Deseja realmente excluir este produto?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("processo.produtos.multa.batchDelete") }}',
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}', ids: [parseInt(produtoMultaId, 10)] },
                        success: function(response) {
                            if (response.success) {
                                $(`#row-multa-${rowId}`).remove();
                                Swal.fire({ icon: 'success', title: 'Sucesso', text: 'Produto excluído com sucesso.', confirmButtonText: 'OK' });
                            } else {
                                Swal.fire({ icon: 'error', title: 'Erro', text: response.message || 'Erro ao excluir produto.', confirmButtonText: 'OK' });
                            }
                        },
                        error: function() {
                            Swal.fire({ icon: 'error', title: 'Erro', text: 'Erro ao excluir produto. Tente novamente.', confirmButtonText: 'OK' });
                        }
                    });
                }
            });
        });

        // Função para adicionar nova linha na tabela multa
        $(document).on('click', '.addProductMulta', function() {
            let lengthOptions = $('#productsBodyMulta tr.linhas-input').length;
            let newIndex = lengthOptions;

            let select = `<select data-row="${newIndex}" class="custom-select selectProductMulta w-100 select2" name="produtos_multa[${newIndex}][produto_id]" id="produto_multa_id-${newIndex}">
                <option selected disabled>Selecione uma opção</option>`;
            for (let produto of products) {
                select += `<option value="${produto.id}">${produto.modelo} - ${produto.codigo}</option>`;
            }
            select += '</select>';

            const colunasMulta = [
                {name: 'adicao', type: 'text', class: 'form-control'},
                {name: 'item', type: 'number', class: 'form-control'},
                {name: 'codigo', type: 'text', class: 'form-control', readonly: true},
                {name: 'ncm', type: 'text', class: 'form-control', readonly: true},
                {name: 'descricao', type: 'text', class: 'form-control', readonly: true},
                {name: 'quantidade', type: 'number', class: 'form-control'},
                {name: 'peso_liquido_unitario', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'peso_liquido_total', type: 'text', class: 'form-control moneyReal pesoLiqTotalMulta'},
                {name: 'fator_peso', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'fob_unit_usd', type: 'text', class: 'form-control moneyReal7 fobUnitarioMulta'},
                {name: 'fob_total_usd', type: 'text', class: 'form-control moneyReal7', readonly: true},
                {name: 'fob_total_brl', type: 'text', class: 'form-control moneyReal7', readonly: true},
                {name: 'service_charges', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'service_charges_brl', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'frete_usd', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'frete_brl', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'acresc_frete_usd', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'acresc_frete_brl', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'vlr_crf_unit', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'vlr_crf_total', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'seguro_usd', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'seguro_brl', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'thc_usd', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'thc_brl', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'valor_aduaneiro_usd', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'valor_aduaneiro_brl', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'ii_percent', type: 'text', class: 'form-control percentage2'},
                {name: 'ipi_percent', type: 'text', class: 'form-control percentage2'},
                {name: 'pis_percent', type: 'text', class: 'form-control percentage2'},
                {name: 'cofins_percent', type: 'text', class: 'form-control percentage2'},
                {name: 'icms_percent', type: 'text', class: 'form-control percentage2 icms_percent_multa'},
                {name: 'icms_reduzido_percent', type: 'text', class: 'form-control percentage2 icms_reduzido_percent_multa'},
                {name: 'reducao', type: 'text', class: 'form-control moneyReal8', readonly: true},
                {name: 'valor_ii', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'base_ipi', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'valor_ipi', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'base_pis_cofins', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'valor_pis', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'valor_cofins', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'despesa_aduaneira', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'vlr_ii_pos_despesa', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'vlr_ipi_pos_despesa', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'vlr_pis_pos_despesa', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'vlr_cofins_pos_despesa', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'nova_ncm', type: 'text', class: 'form-control'},
                {name: 'ii_nova_ncm_percent', type: 'text', class: 'form-control percentage2'},
                {name: 'ipi_nova_ncm_percent', type: 'text', class: 'form-control percentage2'},
                {name: 'pis_nova_ncm_percent', type: 'text', class: 'form-control percentage2'},
                {name: 'cofins_nova_ncm_percent', type: 'text', class: 'form-control percentage2'},
                {name: 'vlr_ii_nova_ncm', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'vlr_ipi_nova_ncm', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'vlr_pis_nova_ncm', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'vlr_cofins_nova_ncm', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'vlr_ii_recalc', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'vlr_ipi_recalc', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'vlr_pis_recalc', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'vlr_cofins_recalc', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'valor_aduaneiro_multa', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'ii_percent_aduaneiro', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'ipi_percent_aduaneiro', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'pis_percent_aduaneiro', type: 'text', class: 'form-control moneyReal', readonly: true},
                {name: 'cofins_percent_aduaneiro', type: 'text', class: 'form-control moneyReal', readonly: true}
            ];

            let camposHTML = '';
            colunasMulta.forEach(coluna => {
                const campoId = coluna.name.endsWith('_multa') ? coluna.name : coluna.name + '_multa';
                const readonly = coluna.readonly ? 'readonly' : '';
                const type = coluna.type || 'text';
                camposHTML += `<td><input data-row="${newIndex}" type="${type}" class="${coluna.class}" name="produtos_multa[${newIndex}][${coluna.name}]" id="${campoId}-${newIndex}" value="" ${readonly}></td>`;
            });

            let tr = `<tr class="linhas-input" id="row-multa-${newIndex}">
                <td class="d-flex align-items-center justify-content-center">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-danger btn-sm btn-remove-multa" data-id="${newIndex}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    <input type="checkbox" style="margin-left: 10px" class="select-produto-multa" value="">
                </td>
                <input type="hidden" name="produtos_multa[${newIndex}][processo_produto_multa_id]" id="processo_produto_multa_id-${newIndex}" value="">
                <td>${select}</td>
                ${camposHTML}
            </tr>`;

            $('#productsBodyMulta').append(tr);
            $(`#produto_multa_id-${newIndex}`).select2({ dropdownParent: $('#custom-tabs-three-multa') });
            // Recalcular para preencher os campos automaticamente na nova linha
            setTimeout(function() {
                recalcularTodaTabelaMulta();
                atualizarCardResumoMulta();
                if (getNacionalizacaoAtual() === 'santa_catarina') {
                    atualizarMultaProdutosPorMulta();
                    debouncedRecalcular();
                }
            }, 100);
        });

        // Inicializar quando documento carregar (multa)
        $(document).ready(function() {
            inicializarSalvamentoFasesMulta();
            
            setTimeout(function() {
                calcularValoresCPTMulta();
                
                if ($('#productsBodyMulta tr.linhas-input').length > 0) {
                    atualizarTotalizadoresMulta();
                    atualizarCardResumoMulta();
                }
                if (getNacionalizacaoAtual() === 'santa_catarina') {
                    atualizarMultaProdutosPorMulta();
                    debouncedRecalcular();
                }
            }, 1000);
        });

        // Listener para peso_liquido_total - recalcular peso unitário e fator peso
        $(document).on('change blur keyup input', '[id^="peso_liquido_total_multa-"]', function() {
            const rowId = $(this).data('row');
            if (rowId !== undefined && rowId !== null && rowId !== '') {
                calcularPesoLiquidoUnitarioMulta(rowId);
                const totalPesoLiq = calcularPesoTotalMulta();
                recalcularFatorPesoMulta(totalPesoLiq, rowId);
                atualizarCamposMulta(rowId);
                calcularValorAduaneiroMultaPorAdicao();
                
                setTimeout(function() {
                    calcularValoresCPTMulta();
                    atualizarTotalizadoresMulta();
                    atualizarCardResumoMulta();
                }, 100);
            }
        });

        // Listener para quantidade - recalcular peso unitário, FOB total e fator peso
        $(document).on('change blur keyup input', '[id^="quantidade_multa-"]', function() {
            const rowId = $(this).data('row');
            if (rowId !== undefined && rowId !== null && rowId !== '') {
                calcularPesoLiquidoUnitarioMulta(rowId);
                calcularFobTotalMulta(rowId);
                const totalPesoLiq = calcularPesoTotalMulta();
                recalcularFatorPesoMulta(totalPesoLiq, rowId);
                atualizarCamposMulta(rowId);
                calcularValorAduaneiroMultaPorAdicao();
                
                setTimeout(function() {
                    calcularValoresCPTMulta();
                    atualizarTotalizadoresMulta();
                    atualizarCardResumoMulta();
                }, 100);
            }
        });

        // Listener para fob_unit_usd - recalcular FOB total
        $(document).on('change blur keyup input', '[id^="fob_unit_usd_multa-"]', function() {
            const rowId = $(this).data('row');
            if (rowId !== undefined && rowId !== null && rowId !== '') {
                calcularFobTotalMulta(rowId);
                atualizarCamposMulta(rowId);
                calcularValorAduaneiroMultaPorAdicao();
                
                setTimeout(function() {
                    calcularValoresCPTMulta();
                    atualizarTotalizadoresMulta();
                    atualizarCardResumoMulta();
                }, 100);
            }
        });

        // Atualizar totalizadores quando houver mudanças na tabela multa (para outros campos)
        $(document).on('change blur keyup input', '#productsBodyMulta input, #productsBodyMulta select', function() {
            const rowId = $(this).data('row');
            if (rowId !== undefined && rowId !== null && rowId !== '') {
                // Evitar recalcular duas vezes para campos já tratados acima
                const id = $(this).attr('id') || '';
                if (!id.includes('peso_liquido_total_multa-') && 
                    !id.includes('quantidade_multa-') && 
                    !id.includes('fob_unit_usd_multa-')) {
                    atualizarCamposMulta(rowId);
                    calcularValorAduaneiroMultaPorAdicao();
                }
            }
            setTimeout(function() {
                calcularValoresCPTMulta();
                atualizarTotalizadoresMulta();
                atualizarCardResumoMulta();
                if (getNacionalizacaoAtual() === 'santa_catarina') {
                    atualizarMultaProdutosPorMulta();
                    debouncedRecalcular();
                }
            }, 300);
        });

        // ==================== FIM LÓGICA DA TABELA MULTA ====================
