@extends('layouts.app')
@section('title', isset($processo) ? '' : 'Cadastrar Processo')

@section('content')
    <style>
        [class^="col"] {
            display: flex !important;
            flex-direction: column !important;
        }

        .modal-dialog.modal-xl {
            max-width: 90%;
            height: 80vh;
            margin: 30px auto;
        }

        .modal-content {
            height: 100%;
        }

        .modal-body {
            height: 100%;
            overflow-y: auto;
        }

        #pdf-iframe {
            width: 100%;
            height: 80vh;
            border: none;
        }

        #imagePreview {
            width: 100%;
            max-height: 80vh;
            object-fit: contain;
            display: none;
        }

        #doc-text {
            display: none;
        }

        th {
            min-width: 200px;
            font-size: 12px;
        }

        tr {
            min-height: 300px;

        }

        .table-products td {
            /* text-align: center; */
            vertical-align: middle;
        }

        /* Garante que a coluna fixa tenha o mesmo estilo que as outras */
        table thead th:first-child,
        table tbody td:first-child {
            position: sticky;
            left: 0;
            z-index: 10;
            background-color: #f8f9fa;
            color: white;
            text-align: center
        }

        .table-dados-complementares td:first-child,
        .table-dados-basicos td:first-child {
            color: black !important;
        }

        table thead th:first-child {
            /* z-index: 20; */
            background-color: #212529;
            color: white
        }

        .table-products th {
            background-color: #212529;
            color: white
        }

        .middleRow th {
            background-color: transparent;
        }

        .middleRowInputTh {
            background-color: #ffff99 !important;
        }

        /* Estilo para o botão de remover */
        .btn-remove {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        /* Ajuste para o sticky funcionar corretamente */
        table tbody tr {
            position: relative;
        }

        .dados-container {
            display: flex;
            gap: 40px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin-bottom: 30px;

        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .dados-basicos th,
        .dados-basicos td {
            border: 1px solid #000;
            /* padding: 5px 8px; */
        }

        .dados-basicos th {
            background-color: #d9d9d9;
            text-align: left;
        }

        .highlight {
            background-color: #fef7a1;
        }

        .green-highlight {
            background-color: #d4f4d4;
        }

        .info-complementares {
            max-width: 400px;
        }

        .info-complementares h4 {
            background-color: #d9d9d9;
            padding: 5px;
            border: 1px solid #000;
            margin-bottom: 10px;
        }

        .info-complementares .linha {
            /* margin-bottom: 5px; */
        }

        .info-complementares .bold {
            font-weight: bold;
        }
    </style>
    <form enctype="multipart/form-data"
        action="{{ isset($processo) ? route('processo.update', $processo->id) : route('processo.store') }}" method="POST">
        @csrf
        @if (isset($processo))
            @method('PUT')
        @endif
        @php
            if (isset($processo)) {
                $moedasSuportadas += [
                    'ARS' => 'Peso argentino',
                    'CNY' => 'Yuan chinês',
                    'HKD' => 'Dólar de Hong Kong',
                    'MXN' => 'Peso mexicano',
                    'NZD' => 'Dólar neozelandês',
                    'SGD' => 'Dólar de Singapura',
                    'ZAR' => 'Rand sul-africano',
                    'AED' => 'Dirham dos Emirados Árabes',
                    'INR' => 'Rúpia indiana',
                    'RUB' => 'Rublo russo',
                    'TRY' => 'Lira turca',
                    'KRW' => 'Won sul-coreano',
                ];
            }
        @endphp
        <div class="row">
            <div class="col-12 shadow-lg px-0">
                <div class="card w-100 card-primary card-tabs">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                            <li class="pt-2 px-3">
                                <h3 class="card-title text-dark font-weight-bold" style="">
                                    {{ $processo->codigo_interno ?? 'Novo Processo' }}</h3>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" id="custom-tabs-two-home-tab" data-toggle="pill"
                                    href="#custom-tabs-two-home" role="tab" aria-controls="custom-tabs-two-home"
                                    aria-selected="false">Dados Processo</a>
                            </li>
                            @if (isset($processo))
                                <li class="nav-item">
                                    <a class="nav-link " id="custom-tabs-three-home-tab" data-toggle="pill"
                                        href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home"
                                        aria-selected="false">Produtos</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link " id="custom-tabs-four-home-tab" data-toggle="pill"
                                        href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home"
                                        aria-selected="false">Esboço da NF</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div id="avisoProcessoAlterado" class="alert d-none alert-warning" role="alert">
                                Processo alterado, pressione o botão salvar para persistir as alterações</div>

                            @include('processo.includes.dados-processo')


                            @if (isset($processo))
                                @include('processo.includes.tabela-produtos')
                                <div class="tab-pane fade" id="custom-tabs-four-home"
                                    aria-labelledby="custom-tabs-four-home-tab" role="tabpanel">
                                    <iframe src="{{ route('processo.esboco.pdf', $processo->id) }}" width="100%"
                                        height="800px" frameborder="0"></iframe>
                                </div>
                            @endif
                        </div>


                        <div class="col-1">
                            <button type="submit" class="btn btn-primary mt-3">Salvar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @if (isset($productsClient))
        <input type="hidden" name="productsClient" id="productsClient" value="{{ $productsClient }}">
        <input type="hidden" name="dolarHoje" id="dolarHoje" value="{{ json_encode($dolar) }}">
        <input type="hidden" id="processoAlterado" name="processoAlterado" value="0">
    @endif
    <form id="delete-form" method="POST" action="{{ route('documento.cliente.destroy', 'document_id') }}"
        style="display:none;">
        @method('DELETE')
        @csrf
    </form>
    <script>
        let reordenando = false;
        let products = JSON.parse($('#productsClient').val());

        $(document).ready(function() {
            reordenarLinhas();
            $('.moneyReal').on('blur', function() {
                let val = $(this).val();
                if (val) {
                    val = val.trim().replace('%', '').trim();
                    if (val.includes(',')) {
                        val = val.replace(/\./g, '').replace(',', '.');
                    } else {
                        val = val.replace(',', '.');
                    }
                    let numero = parseFloat(val);
                    if (!isNaN(numero)) {
                        let formatado = numero.toLocaleString('pt-BR', {
                            minimumFractionDigits: 5,
                            maximumFractionDigits: 5
                        });
                        $(this).val(formatado);
                    } else {
                        $(this).val('');
                    }
                } else {
                    $(this).val('');
                }
            });
            // Dinheiro com 7 casas decimais
            $('.moneyReal2').on('blur', function() {
                let val = $(this).val();
                if (val) {
                    val = val.trim().replace('%', '').trim();
                    if (val.includes(',')) {
                        val = val.replace(/\./g, '').replace(',', '.');
                    } else {
                        val = val.replace(',', '.');
                    }
                    let numero = parseFloat(val);
                    if (!isNaN(numero)) {
                        let formatado = numero.toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                        $(this).val(formatado);
                    } else {
                        $(this).val('');
                    }
                } else {
                    $(this).val('');
                }
            });
            $('.cotacao').on('blur', function() {
                let val = $(this).val();
                if (val) {
                    val = val.trim().replace('%', '').trim();
                    if (val.includes(',')) {
                        val = val.replace(/\./g, '').replace(',', '.');
                    } else {
                        val = val.replace(',', '.');
                    }
                    let numero = parseFloat(val);
                    if (!isNaN(numero)) {
                        let formatado = numero.toLocaleString('pt-BR', {
                            minimumFractionDigits: 4,
                            maximumFractionDigits: 4
                        });
                        $(this).val(formatado);
                    } else {
                        $(this).val('');
                    }
                } else {
                    $(this).val('');
                }
            });

            // Dinheiro com 7 casas decimais
            $('.moneyReal7').on('blur', function() {
                let val = $(this).val();
                if (val) {
                    val = val.trim().replace('%', '').trim();
                    if (val.includes(',')) {
                        val = val.replace(/\./g, '').replace(',', '.');
                    } else {
                        val = val.replace(',', '.');
                    }
                    let numero = parseFloat(val);
                    if (!isNaN(numero)) {
                        let formatado = numero.toLocaleString('pt-BR', {
                            minimumFractionDigits: 7,
                            maximumFractionDigits: 7
                        });
                        $(this).val(formatado);
                    } else {
                        $(this).val('');
                    }
                } else {
                    $(this).val('');
                }
            });

            // Percentuais (7 casas decimais igual migration)
            $('.percentage').on('blur', function() {
                let val = $(this).val();
                if (val) {
                    val = val.trim().replace('%', '').trim();
                    if (val.includes(',')) {
                        val = val.replace(/\./g, '').replace(',', '.');
                    } else {
                        val = val.replace(',', '.');
                    }
                    let numero = parseFloat(val);
                    if (!isNaN(numero)) {
                        let formatado = numero.toLocaleString('pt-BR', {
                            minimumFractionDigits: 7,
                            maximumFractionDigits: 7
                        });
                        $(this).val(formatado + ' %');
                    } else {
                        $(this).val('');
                    }
                } else {
                    $(this).val('');
                }
            });
            $('.percentage2').on('blur', function() {
                let val = $(this).val();
                if (val) {
                    val = val.trim().replace('%', '').trim();
                    if (val.includes(',')) {
                        val = val.replace(/\./g, '').replace(',', '.');
                    } else {
                        val = val.replace(',', '.');
                    }
                    let numero = parseFloat(val);
                    if (!isNaN(numero)) {
                        let formatado = numero.toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                        $(this).val(formatado + ' %');
                    } else {
                        $(this).val('');
                    }
                } else {
                    $(this).val('');
                }
            });
            $('.select2').select2({
                width: '100%'
            });

            $('form').on('submit', function(e) {
                // Remover máscaras de todos os campos com moneyReal
                // $('.moneyReal').each(function() {
                //     // Salvar o valor original sem formatação
                //     let originalValue = $(this).val();

                //     // Remover pontos de milhar e substituir vírgula por ponto
                //     let unformattedValue = originalValue
                //         .replace(/\./g, '')
                //         .replace(',', '.');

                //     // Atualizar o valor do campo sem formatação
                //     $(this).val(unformattedValue);
                // });

                // Opcional: fazer o mesmo para campos de porcentagem se necessário
                $('.percentage').each(function() {
                    let originalValue = $(this).val();
                    let unformattedValue = originalValue
                        .replace(/\./g, '')
                        .replace(',', '.')
                        .replace('%', '');
                    $(this).val(unformattedValue);
                });
            });

            // Reaplicar as máscaras após o submit (caso a página não recarregue)
            $(document).ajaxComplete(function() {
                $('.moneyReal').mask('#.##0,00000', {
                    reverse: true,
                    placeholder: "",
                    maxlength: false
                });

                $('.percentage').mask('#.##0,00000 %', {
                    reverse: true,
                    placeholder: "",
                    maxlength: false
                });
            });
             $('#recalcularTabela').on('click', function() {
        // Mostrar indicador de carregamento
        const btn = $(this);
        const originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i> Calculando...').prop('disabled', true);
        
        // Pequeno delay para permitir a atualização visual do botão
        setTimeout(function() {
            try {
                recalcularTodaTabela();
                Toast.fire({
                    icon: 'success',
                    title: 'Tabela recalculada com sucesso!'
                });
            } catch (error) {
                console.error('Erro ao recalcular tabela:', error);
                Toast.fire({
                    icon: 'error',
                    title: 'Erro ao recalcular tabela'
                });
            } finally {
                // Restaurar o botão
                btn.html(originalText).prop('disabled', false);
            }
        }, 100);
    });

        });

        function updateValorReal(inputId, spanId, automatic = true) {
            let dolar = JSON.parse($('#dolarHoje').val());

            let valor = MoneyUtils.parseMoney($(`#${inputId}`).val());
            let codigoMoeda = $(`#${inputId}_moeda`).val()

            if (codigoMoeda && dolar[codigoMoeda] && automatic) {
                let convertido = valor * (dolar[codigoMoeda].venda);
                $(`#${spanId}`).val(MoneyUtils.formatMoney(convertido, 2));
            } else if (codigoMoeda && dolar[codigoMoeda] && !automatic) {
                let taxa = MoneyUtils.parseMoney($(`#cotacao_${inputId}`).val().replace(',', '.'))
                let convertido = valor * taxa;
                $(`#${spanId}`).val(MoneyUtils.formatMoney(convertido, 4));
            } else {
                $(`#${spanId}`).val(0);
            }
        };

        function updateValorCotacao(inputId, spanId) {
            let dolar = JSON.parse($('#dolarHoje').val());

            let valor = MoneyUtils.parseMoney($(`#${inputId}`).val());
            let codigoMoeda = $(`#${inputId}_moeda`).val()
            let nome = $(`#${inputId}_moeda option:selected`).text();
            $(`#description_moeda_${inputId}`).text(`Taxa: ${nome}`)


            if (codigoMoeda && dolar[codigoMoeda]) {
                let convertido = dolar[codigoMoeda].venda;
                $(`#${spanId}`).val(MoneyUtils.formatMoney(convertido, 4));

                const data = new Date(dolar[codigoMoeda].data);

                const formatada = data.getFullYear() + '-' +
                    String(data.getMonth() + 1).padStart(2, '0') + '-' +
                    String(data.getDate() + 1).padStart(2, '0');

                $(`#data_moeda_${inputId}`).val(formatada);

            } else {
                $(`#${spanId}`).val(0);
                $(`#data_moeda_${inputId}`).val('');
            }
        };
        const initialInputs = new Set(
            Array.from(document.querySelectorAll('#productsBody input, #productsBody select, #productsBody textarea'))
        );

        setTimeout(function() {
            $(document).on('change', '.selectProduct', function(e) {
                let products = JSON.parse($('#productsClient').val());
                console.log(this)
                let productObject = products.find(el => el.id == this.value);
                let rowId = $(this).data('row');

                if (productObject) {
                    $(`#codigo-${rowId}`).val(productObject.codigo);
                    $(`#ncm-${rowId}`).val(productObject.ncm);
                    $(`#descricao-${rowId}`).val(productObject.descricao);
                }
            });
            $(document).on('keyup',
                '#productsBody input, #productsBody select, #productsBody textarea, #productsBody .form-control',
                function(e) {
                    console.log(!initialInputs.has(e.target))
                    if (!initialInputs.has(e.target)) {
                        return; // Ignora inputs criados dinamicamente
                    }
                    $('#avisoProcessoAlterado').removeClass('d-none');
                    const rowId = $(this).data('row');

                    console.log('alterado')
                    if ($(`#produto_id-${rowId}`).val()) {
                        try {
                            const nome = $(this).attr('name');
                            const camposExternos = getCamposExternos();
                            // Verifica se é um campo externo (sem rowId)
                            if (camposExternos.includes(nome)) {
                                // Campo externo alterado - recalcular todas as linhas
                                recalcularTodaTabela();
                            } else if (rowId != null) {
                                // Campo normal - calcular apenas a linha afetada
                                const {
                                    pesoTotal,
                                    fobUnitario,
                                    quantidade
                                } = obterValoresBase(rowId);

                                const pesoLiqUnit = pesoTotal / (quantidade || 1);
                                const fobTotal = fobUnitario * quantidade;
                                const totalPesoLiq = calcularPesoTotal();
                                const fatorPesoRow = recalcularFatorPeso(totalPesoLiq, rowId);

                                const fobTotalGeral = calcularFobTotalGeral();
                                const moedasOBject = JSON.parse($('#dolarHoje').val())
                                const moedaDolar = moedasOBject['USD'].venda ?? $(
                                    `#cotacao_frete_internacional`).val().replace(',', '.')

                                const dolar = MoneyUtils.parseMoney(moedaDolar);
                            
                                atualizarFatoresFob();
                                atualizarTotaisGlobais(fobTotalGeral, dolar);

                                const freteUsdInt = MoneyUtils.parseMoney($('#frete_internacional').val()) *
                                    fatorPesoRow;
                                const thc_capataziaBase = MoneyUtils.parseMoney($('#thc_capatazia').val());
                                const thcRow = thc_capataziaBase * fatorPesoRow;
                                const seguroIntUsdRow = calcularSeguro(fobTotal, fobTotalGeral);
                                const acrescimoFreteUsdRow = calcularAcrescimoFrete(fobTotal, fobTotalGeral,
                                    dolar);

                                const vlrAduaneiroUsd = calcularValorAduaneiro(fobTotal, freteUsdInt,
                                    acrescimoFreteUsdRow,
                                    seguroIntUsdRow, thcRow, dolar);
                                const vlrAduaneiroBrl = vlrAduaneiroUsd * dolar;

                                const impostos = calcularImpostos(rowId, vlrAduaneiroBrl);
                                const taxaSisComex = calcularTaxaSiscomex($('#productsBody tr').length);
                                const fatorTaxaSiscomex_AY = taxaSisComex / ((fobTotal) * dolar);

                                const taxaSisComexUnitaria_BB = fatorTaxaSiscomex_AY * (fobUnitario * dolar);
                                const fatorVlrFob_AX = fobTotal / fobTotalGeral;

                                const despesas = calcularDespesas(rowId, fatorVlrFob_AX, fatorTaxaSiscomex_AY,
                                    (taxaSisComexUnitaria_BB ?? 0));
                                const bcIcmsSReducao = calcularBcIcmsSemReducao(vlrAduaneiroBrl, impostos,
                                    despesas);
                                const vlrIcmsSReducao = bcIcmsSReducao * impostos.icms;
                                const bcImcsReduzido = calcularBcIcmsReduzido(rowId, vlrAduaneiroBrl, impostos,
                                    despesas);
                                const vlrIcmsReduzido = bcIcmsSReducao * impostos.icms;
                                const totais = calcularTotais(vlrAduaneiroBrl, impostos, despesas, quantidade,
                                    vlrIcmsReduzido,
                                    rowId);

                                const diferenca_cambial_frete = 0;
                                const diferenca_cambial_fob = 0;
                                const mva = $(`#mva-${rowId}`).val() ? MoneyUtils.parsePercentage($(
                                    `#mva-${rowId}`).val()) : 0;
                                let base_icms_st = 0;
                                let vlrIcmsSt = 0;
                                if (mva) {
                                    base_icms_st = totais.vlrTotalNfSemIcms * (1 + mva)
                                    const icms_st = $(`#icms_st-${rowId}`).val() ? MoneyUtils
                                        .parsePercentage($(
                                            `#icms_st-${rowId}`).val()) : 0;

                                    if (icms_st) {
                                        console.log([
                                            base_icms_st, icms_st, vlrIcmsReduzido
                                        ])
                                        vlrIcmsSt = (base_icms_st * icms_st) - vlrIcmsReduzido;
                                    }
                                }
                                console.log(fobTotal)
                                atualizarCampos(rowId, {
                                    pesoLiqUnit,
                                    fobTotal,
                                    dolar,
                                    freteUsdInt,
                                    seguroIntUsdRow,
                                    acrescimoFreteUsdRow,
                                    thcRow,
                                    vlrAduaneiroUsd,
                                    vlrAduaneiroBrl,
                                    impostos,
                                    despesas,
                                    bcIcmsSReducao,
                                    vlrIcmsSReducao,
                                    bcImcsReduzido,
                                    vlrIcmsReduzido,
                                    totais,
                                    fatorVlrFob_AX,
                                    fatorTaxaSiscomex_AY,
                                    taxaSisComex,
                                    vlrIcmsSt,
                                    base_icms_st
                                });

                                atualizarCamposCabecalho();
                            }
                        } catch (error) {
                            console.log(error);
                        }
                    }

                });




            $('.moedas').on('select2:select', function(e) {
                updateValorReal('frete_internacional', 'frete_internacional_visualizacao');
                updateValorReal('seguro_internacional', 'seguro_internacional_visualizacao');
                updateValorReal('acrescimo_frete', 'acrescimo_frete_visualizacao');
                updateValorCotacao('frete_internacional', 'cotacao_frete_internacional');
                updateValorCotacao('seguro_internacional', 'cotacao_seguro_internacional');
                updateValorCotacao('acrescimo_frete', 'cotacao_acrescimo_frete');
            });
            $('#frete_internacional, #seguro_internacional, #acrescimo_frete').trigger('change');
            $(document).on('change', '#frete_internacional, #seguro_internacional, #acrescimo_frete', function() {

                updateValorReal('frete_internacional', 'frete_internacional_visualizacao');
                updateValorReal('seguro_internacional', 'seguro_internacional_visualizacao');
                updateValorReal('acrescimo_frete', 'acrescimo_frete_visualizacao');
            });

            $(document).on('change', '.cotacao', function() {
                updateValorReal('frete_internacional', 'frete_internacional_visualizacao', false);
                updateValorReal('seguro_internacional', 'seguro_internacional_visualizacao', false);
                updateValorReal('acrescimo_frete', 'acrescimo_frete_visualizacao', false);
            })

        }, 1000);

        $('#atualizarCotacoes').on('click', function() {
            updateValorReal('frete_internacional', 'frete_internacional_visualizacao');
            updateValorReal('seguro_internacional', 'seguro_internacional_visualizacao');
            updateValorReal('acrescimo_frete', 'acrescimo_frete_visualizacao');
            updateValorCotacao('frete_internacional', 'cotacao_frete_internacional');
            updateValorCotacao('seguro_internacional', 'cotacao_seguro_internacional');
            updateValorCotacao('acrescimo_frete', 'cotacao_acrescimo_frete');
        })

        function showDeleteConfirmation(documentId) {
            const deleteUrl = '/destroy-produto-processo/' + documentId; // Ajuste a URL conforme necessário

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
                    $('#delete-form').attr('action', deleteUrl);

                    $('#delete-form').submit();
                } else {
                    Toast.fire({
                        icon: 'info',
                        title: 'Ação cancelada'
                    });
                }
            });
        }
        const MoneyUtils = {


            parsePercentage: function(value) {
                if (!value || value === "") return 0;

                // Converter para string e remover espaços
                let stringValue = value.toString().trim();

                // Remover o símbolo de porcentagem se existir
                stringValue = stringValue.replace(/%/g, '');

                // Substituir vírgula por ponto para decimal
                stringValue = stringValue.replace(',', '.');

                // Remover espaços extras que possam ter sobrado
                stringValue = stringValue.replace(/\s/g, '');

                // Converter para float e dividir por 100 para obter a fração
                const parsedValue = parseFloat(stringValue) || 0;

                return parsedValue / 100;
            },


            formatUSD: function(value, decimals = 2) {
                if (value === null || value === undefined) return "0.00";

                let num = typeof value === 'string' ? parseFloat(value.replace(',', '.')) : value;
                let fixedDecimals = num.toFixed(decimals);

                let parts = fixedDecimals.split('.');
                let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                let decimalPart = parts[1] || '00';

                return `${integerPart}.${decimalPart}`;
            },

            formatPercentage: function(value, decimals = 2) {
                if (value === null || value === undefined) return "0,00%";

                // Multiplicar por 100 para obter a porcentagem
                const percentageValue = (typeof value === 'string' ?
                    parseFloat(value.replace(',', '.')) : value) * 100;

                let fixedDecimals = percentageValue.toFixed(decimals);

                let parts = fixedDecimals.split('.');
                let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                let decimalPart = parts[1] || '00';

                return `${integerPart},${decimalPart}%`;
            },
            parseMoney: function(value) {
                if (value === null || value === undefined || value === "") return 0;

                // Se já for número, retorna direto
                if (typeof value === "number") {
                    return value;
                }

                // Se for string, trata a formatação
                let cleanValue = value.toString()
                    .replace(/\./g, '') // Remove todos os pontos
                    .replace(/,/g, '.'); // Substitui vírgula por ponto

                // Remover caracteres não numéricos exceto ponto decimal
                cleanValue = cleanValue.replace(/[^\d.]/g, '');

                return parseFloat(cleanValue) || 0;
            },

            formatMoney: function(value, decimals = 6) {
                if (value === null || value === undefined) return "0,000000";
                let num = typeof value === 'string' ?
                    parseFloat(value.replace(',', '.')) : value;

                // Formatar com 6 casas decimais para exibição
                return num.toLocaleString('pt-BR', {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals
                });
            }
        };

        function atualizarFatoresFob() {
            let fobTotalGeral = 0;
            const fobTotaisPorLinha = {};

            $('.fobUnitario').each(function() {
                const rowId = $(this).data('row');
                const unitario = MoneyUtils.parseMoney($(this).val());
                const qtd = MoneyUtils.parseMoney($(`#quantidade-${rowId}`).val());
                const fobTotal = unitario * qtd;

                fobTotaisPorLinha[rowId] = fobTotal;
                fobTotalGeral += fobTotal;
            });

            for (const rowId in fobTotaisPorLinha) {
                const fobLinha = fobTotaisPorLinha[rowId];
                const fatorFob = fobLinha / (fobTotalGeral || 1);

                $(`#fator_vlr_fob-${rowId}`).val(fatorFob.toFixed(6));
                const camposExternos = getCamposExternos();

                for (let campo of camposExternos) {
                    const campoEl = $(`#${campo}-${rowId}`);
                    const valorOriginal = MoneyUtils.parseMoney(campoEl.val());
                    const valorComFator = valorOriginal * fatorFob;

                    campoEl.val(valorComFator.toFixed(6));
                }
            }

            $('#fobTotalProcesso').text(MoneyUtils.formatMoney(fobTotalGeral));

            const moedasOBject = JSON.parse($('#dolarHoje').val())
            const moedaDolar = moedasOBject['USD'].venda ?? $(`#cotacao_frete_internacional`).val().replace(',', '.')
            const dolar = MoneyUtils.parseMoney(moedaDolar);
            $('#fobTotalProcessoReal').text(MoneyUtils.formatMoney(fobTotalGeral * dolar));
        }

        function recalcularTodaTabela() {
            const rows = $('#productsBody tr');
            const moedasOBject = JSON.parse($('#dolarHoje').val());
            const moedaDolar = moedasOBject['USD'].venda ?? $(`#cotacao_frete_internacional`).val().replace(',', '.')
            const dolar = MoneyUtils.parseMoney(moedaDolar);
            const totalPesoLiq = calcularPesoTotal();
            const fobTotalGeral = calcularFobTotalGeral();
            const taxaSisComex = calcularTaxaSiscomex(rows.length);

            rows.each(function() {
                const rowId = $(this).find('input').first().data('row');
                const {
                    pesoTotal,
                    fobUnitario,
                    quantidade
                } = obterValoresBase(rowId);

                const fatorPesoRow = recalcularFatorPeso(totalPesoLiq, rowId);
                const fobTotal = fobUnitario * quantidade;
                const fatorVlrFob_AX = fobTotal / (fobTotalGeral || 1);

                $(`#peso_liquido_unitario-${rowId}`).val(pesoTotal / (quantidade || 1));
                $(`#fob_total_usd-${rowId}`).val(MoneyUtils.formatMoney(fobTotal));
                $(`#fob_total_brl-${rowId}`).val(MoneyUtils.formatMoney(fobTotal * dolar));
                $(`#fator_valor_fob-${rowId}`).val(MoneyUtils.formatMoney(fatorVlrFob_AX));
            });

            // Segunda passada: calcular impostos e despesas
            rows.each(function() {
                const rowId = $(this).find('select').first().data('row');
                const {
                    fobUnitario,
                    quantidade
                } = obterValoresBase(rowId);
                const fobTotal = fobUnitario * quantidade;
                const fatorPesoRow = MoneyUtils.parseMoney($(`#fator_peso-${rowId}`).val()) || 0;
                const fatorVlrFob_AX = parseFloat($(`#fator_valor_fob-${rowId}`).val()) || 0;

                const freteUsdInt = MoneyUtils.parseMoney($('#frete_internacional').val()) * fatorPesoRow;
                console.log(freteUsdInt)
                const thc_capataziaBase = MoneyUtils.parseMoney($('#thc_capatazia').val());
                const thcRow = thc_capataziaBase * fatorPesoRow;
                const seguroIntUsdRow = calcularSeguro(fobTotal, fobTotalGeral);
                console.log(fobTotal, fobTotalGeral)
                const acrescimoFreteUsdRow = calcularAcrescimoFrete(fobTotal, fobTotalGeral, dolar);

                const vlrAduaneiroUsd = calcularValorAduaneiro(fobTotal, freteUsdInt, acrescimoFreteUsdRow,
                    seguroIntUsdRow, thcRow, dolar);
                const vlrAduaneiroBrl = vlrAduaneiroUsd * dolar;

                const impostos = calcularImpostos(rowId, vlrAduaneiroBrl);
                const fatorTaxaSiscomex_AY = taxaSisComex / ((fobTotal) * dolar);
                const taxaSisComexUnitaria_BB = fatorTaxaSiscomex_AY * (fobUnitario * dolar);

                const despesas = calcularDespesas(rowId, fatorVlrFob_AX, fatorTaxaSiscomex_AY,
                    (taxaSisComexUnitaria_BB ?? 0));
                const bcIcmsSReducao = calcularBcIcmsSemReducao(vlrAduaneiroBrl, impostos, despesas);
                const vlrIcmsSReducao = bcIcmsSReducao * impostos.icms;
                const bcImcsReduzido = calcularBcIcmsReduzido(rowId, vlrAduaneiroBrl, impostos, despesas);
                const vlrIcmsReduzido = bcIcmsSReducao * impostos.icms;
                const totais = calcularTotais(vlrAduaneiroBrl, impostos, despesas, quantidade, vlrIcmsReduzido,
                    rowId);
                const mva = $(`#mva-${rowId}`).val() ? MoneyUtils.parsePercentage($(`#mva-${rowId}`).val()) : 0;
                let base_icms_st = 0;
                let vlrIcmsSt = 0;
                if (mva) {
                    base_icms_st = totais.vlrTotalNfSemIcms * (1 + mva)
                    const icms_st = $(`#icms_st_percent-${rowId}`).val() ? MoneyUtils.parsePercentage($(
                        `#icms_st_percent-${rowId}`).val()) : 0;
                    if (icms_st) {
                        vlrIcmsSt = (base_icms_st * icms_st) - vlrIcmsReduzido;
                    }
                }

                atualizarCampos(rowId, {
                    pesoLiqUnit: MoneyUtils.parseMoney($(`#peso_liquido_unitario-${rowId}`).val()),
                    fobTotal,
                    dolar,
                    freteUsdInt,
                    seguroIntUsdRow,
                    acrescimoFreteUsdRow,
                    thcRow,
                    vlrAduaneiroUsd,
                    vlrAduaneiroBrl,
                    impostos,
                    despesas,
                    bcIcmsSReducao,
                    vlrIcmsSReducao,
                    bcImcsReduzido,
                    vlrIcmsReduzido,
                    totais,
                    fatorVlrFob_AX,
                    fatorTaxaSiscomex_AY,
                    taxaSisComex,
                    vlrIcmsSt,
                    base_icms_st
                });
            });

            atualizarCamposCabecalho();
            atualizarTotaisGlobais(fobTotalGeral, dolar);
        }

        function getCamposExternos() {
            return [
                'outras_taxas_agente', 'liberacao_bl', 'desconsolidacao', 'isps_code', 'handling', 'capatazia',
                'afrmm', 'armazenagem_sts', 'frete_dta_sts_ana', 'sda', 'rep_sts', 'armaz_ana',
                'lavagem_container', 'rep_anapolis', 'li_dta_honor_nix', 'honorarios_nix'
            ];
        }

        function obterValoresBase(rowId) {
            return {
                pesoTotal: MoneyUtils.parseMoney($(`#peso_liquido_total-${rowId}`).val()),
                fobUnitario: MoneyUtils.parseMoney($(`#fob_unit_usd-${rowId}`).val()),
                quantidade: parseFloat($(`#quantidade-${rowId}`).val()) || 0
            };
        }

        function calcularPesoTotal() {
            let total = 0;
            $('.pesoLiqTotal').each(function() {
                total += MoneyUtils.parseMoney($(this).val());
            });
            return total;
        }


        function recalcularFatorPeso(totalPeso, currentRowId) {
            let fator = 0;
            $('.pesoLiqTotal').each(function() {
                const rowId = $(this).data('row');
                const valor = MoneyUtils.parseMoney($(this).val());
                const fatorLinha = valor / (totalPeso || 1);
                $(`#fator_peso-${rowId}`).val(fatorLinha.toFixed(6));
                if (rowId == currentRowId) fator = fatorLinha;
            });
            return fator;
        }

        function calcularFobTotalGeral() {
            let total = 0;
            $('.fobUnitario').each(function() {
                const rowId = $(this).data('row');
                const unit = MoneyUtils.parseMoney($(this).val());
                const qtd = MoneyUtils.parseMoney($(`#quantidade-${rowId}`).val());
                total += unit * qtd;
            });
            return total;
        }

        function atualizarTotaisGlobais(fobTotalGeral, dolar) {
            $('#fobTotalProcesso').text(MoneyUtils.formatMoney(fobTotalGeral));
            $('#fobTotalProcessoReal').text(MoneyUtils.formatMoney(fobTotalGeral * dolar));
        }

        function calcularSeguro(fobTotal, fobGeral) {
            if (fobGeral == 0) {
                return 0
            }
            const total = MoneyUtils.parseMoney($('#seguro_internacional').val());
            return (total / fobGeral) * fobTotal;
        }

        function calcularAcrescimoFrete(fobTotal, fobGeral, dolar) {
            const base = MoneyUtils.parseMoney($('#acrescimo_frete').val());
            return (base / (fobGeral * dolar)) * fobTotal * dolar;
        }

        function calcularValorAduaneiro(fob, frete, acrescimo, seguro, thc, dolar) {
            // Função para validar e converter valores
            const parseSafe = (value, defaultValue = 0) => {
                if (value === null || value === undefined) return defaultValue;
                const num = Number(value);
                return isNaN(num) ? defaultValue : num;
            };

            const safeFob = parseSafe(fob);
            const safeFrete = parseSafe(frete);
            const safeAcrescimo = parseSafe(acrescimo);
            const safeSeguro = parseSafe(seguro);
            const safeThc = parseSafe(thc);
            const safeDolar = parseSafe(dolar, 1); // Default 1 para evitar divisão por zero

            return safeFob + safeFrete + safeAcrescimo + safeSeguro +
                (safeThc / (safeDolar || 1)); // Garantir que não divida por zero
        }

        function calcularImpostos(rowId, base) {
            return {
                ii: $(`#ii_percent-${rowId}`).val() ? MoneyUtils.parsePercentage($(`#ii_percent-${rowId}`).val()) / 100 : 0,
                ipi: $(`#ipi_percent-${rowId}`).val() ? MoneyUtils.parsePercentage($(`#ipi_percent-${rowId}`).val()) / 100 :
                    0,
                pis: $(`#pis_percent-${rowId}`).val() ? MoneyUtils.parsePercentage($(`#pis_percent-${rowId}`).val()) / 100 :
                    0,
                cofins: $(`#cofins_percent-${rowId}`).val() ? MoneyUtils.parsePercentage($(`#cofins_percent-${rowId}`)
                    .val()) / 100 : 0,
                icms: $(`#icms_percent-${rowId}`).val() ? MoneyUtils.parsePercentage($(`#icms_percent-${rowId}`).val()) /
                    100 : 0
            };
        }

        function calcularDespesas(rowId, fatorVlrFob_AX, fatorSiscomex, taxaSiscomexUnit) {
            const multa = $(`#multa-${rowId}`).val() ? parseFloat($(`#multa-${rowId}`).val()) : 0;
            const txDefLi = $(`#tx_def_li-${rowId}`).val() ? parseFloat($(`#tx_def_li-${rowId}`).val()) : 0;
            const capatazia = $(`#capatazia-${rowId}`).val() ? parseFloat($(`#capatazia-${rowId}`).val()) : 0
            const afrmm = $('#afrmm-' + rowId).val() ? parseFloat($('#afrmm-' + rowId).val()) : 0
            let armazenagem_sts = $('#armazenagem_sts-' + rowId).val() ? parseFloat($('#armazenagem_sts-' + rowId).val()) :
                0
            let frete_dta_sts_ana = $('#frete_dta_sts_ana-' + rowId).val() ? parseFloat($('#frete_dta_sts_ana-' + rowId)
                    .val()) :
                0
            let honorarios_nix = $('#honorarios_nix-' + rowId).val() ? parseFloat($('#honorarios_nix-' + rowId).val()) : 0

            return multa + txDefLi + calcularTaxaSiscomex($('#productsBody tr').length) + capatazia + taxaSiscomexUnit +
                afrmm + armazenagem_sts + frete_dta_sts_ana + honorarios_nix;
        }

        function calcularBcIcmsSemReducao(base, impostos, despesas) {
            return (base + base * impostos.ii + base * impostos.ipi + base * impostos.pis + base * impostos.cofins +
                despesas) / (1 - impostos.icms);
        }

        function calcularBcIcmsReduzido(rowId, base, impostos, despesas) {
            const reducao = $(`#reducao-${rowId}`).val() ? parseFloat($(`#reducao-${rowId}`).val()) : 1;
            return (base + base * impostos.ii + base * impostos.ipi + base * impostos.pis + base * impostos.cofins +
                despesas) / reducao;
        }

        function calcularTotais(base, impostos, despesas, quantidade, vlrIcmsReduzido, rowId) {
            const vlrII = base * impostos.ii;
            const bcIpi = base + vlrII;
            const vlrIpi = bcIpi * impostos.ipi;
            const bcPisCofins = base;
            const vlrPis = bcPisCofins * impostos.pis;
            const vlrCofins = bcPisCofins * impostos.cofins;
            const vlrTotalProdutoNf = base + vlrII;
            const vlrUnitProdutNf = vlrTotalProdutoNf / (quantidade || 1);
            const vlrTotalNfSemIcms = vlrTotalProdutoNf + vlrIpi + vlrPis + vlrCofins + despesas + vlrIcmsReduzido;
            const vlrTotalNfComIcms = vlrTotalNfSemIcms + ($(`#valor_icms_st-${rowId}`).val() ? MoneyUtils.parseMoney($(
                `#valor_icms_st-${rowId}`).val()) : 0);
            return {
                vlrTotalProdutoNf: vlrTotalProdutoNf ?? 0,
                vlrUnitProdutNf: vlrUnitProdutNf ?? 0,
                vlrTotalNfSemIcms: vlrTotalNfSemIcms ?? 0,
                vlrTotalNfComIcms: vlrTotalNfComIcms ?? 0
            };
        }

        function atualizarCampos(rowId, valores) {
            $(`#peso_liquido_unitario-${rowId}`).val(valores.pesoLiqUnit);
            $(`#fob_total_usd-${rowId}`).val(MoneyUtils.formatMoney(valores.fobTotal));
            console.log([valores.fobTotal, valores.dolar])
            $(`#fob_total_brl-${rowId}`).val(MoneyUtils.formatMoney(valores.fobTotal * valores.dolar));
            $(`#frete_usd-${rowId}`).val(MoneyUtils.formatMoney(valores.freteUsdInt));
            $(`#frete_brl-${rowId}`).val(MoneyUtils.formatMoney(valores.freteUsdInt * valores.dolar));
            $(`#seguro_usd-${rowId}`).val(MoneyUtils.formatMoney(valores.seguroIntUsdRow));
            $(`#seguro_brl-${rowId}`).val(MoneyUtils.formatMoney(valores.seguroIntUsdRow * valores.dolar));
            $(`#acresc_frete_usd-${rowId}`).val(MoneyUtils.formatMoney(valores.acrescimoFreteUsdRow));
            $(`#acresc_frete_brl-${rowId}`).val(MoneyUtils.formatMoney(valores.acrescimoFreteUsdRow * valores.dolar));
            $(`#thc_usd-${rowId}`).val(MoneyUtils.formatMoney(valores.thcRow / valores.dolar));
            $(`#thc_brl-${rowId}`).val(MoneyUtils.formatMoney(valores.thcRow));
            $(`#valor_aduaneiro_usd-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrAduaneiroUsd));
            $(`#valor_aduaneiro_brl-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrAduaneiroBrl));
            $(`#valor_ii-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrAduaneiroBrl * valores.impostos.ii));
            $(`#base_ipi-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrAduaneiroBrl + valores.vlrAduaneiroBrl * valores
                .impostos.ii));
            $(`#valor_ipi-${rowId}`).val(MoneyUtils.formatMoney((valores.vlrAduaneiroBrl + valores.vlrAduaneiroBrl * valores
                .impostos.ii) * valores.impostos.ipi));
            $(`#base_pis_cofins-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrAduaneiroBrl));
            $(`#valor_pis-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrAduaneiroBrl * valores.impostos.pis));
            $(`#valor_cofins-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrAduaneiroBrl * valores.impostos.cofins));
            $(`#despesa_aduaneira-${rowId}`).val(MoneyUtils.formatMoney(valores.despesas));
            $(`#base_icms_sem_reducao-${rowId}`).val(MoneyUtils.formatMoney(valores.bcIcmsSReducao));
            $(`#valor_icms_sem_reducao-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrIcmsSReducao));
            $(`#base_icms_reduzido-${rowId}`).val(MoneyUtils.formatMoney(valores.bcImcsReduzido));
            $(`#valor_icms_reduzido-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrIcmsReduzido));
            $(`#valor_unit_nf-${rowId}`).val(MoneyUtils.formatMoney(valores.totais.vlrUnitProdutNf));
            $(`#valor_total_nf-${rowId}`).val(MoneyUtils.formatMoney(valores.totais.vlrTotalProdutoNf));
            $(`#valor_total_nf_sem_icms_st-${rowId}`).val(MoneyUtils.formatMoney(valores.totais.vlrTotalNfSemIcms));
            $(`#valor_icms_st--${rowId}`).val(MoneyUtils.formatMoney(valores.totais.vlrIcmsSt));
            $(`#valor_total_nf_com_icms_st-${rowId}`).val(MoneyUtils.formatMoney(valores.totais.vlrTotalNfComIcms));

            $(`#base_icms_st-${rowId}`).val(MoneyUtils.formatMoney(valores.base_icms_st));
            $(`#valor_icms_st-${rowId}`).val(MoneyUtils.formatMoney(valores.vlrIcmsSt));


            atualizarFatoresFob()
            // $(`#fator_valor_fob-${rowId}`).val(MoneyUtils.formatMoney(valores.fatorVlrFob_AX));
            // $(`#fator_tx_siscomex-${rowId}`).val(valores.fatorTaxaSiscomex_AY);
            // $(`#taxa_siscomex-${rowId}`).val(MoneyUtils.formatMoney(valores.taxaSisComex));
        }

        function atualizarFatoresFob() {
            const moedasOBject = JSON.parse($('#dolarHoje').val())
            const moedaDolar = moedasOBject['USD'].venda ?? $(`#cotacao_frete_internacional`).val().replace(',', '.')
            const dolar = MoneyUtils.parseMoney(moedaDolar);
            const taxaSiscomex = calcularTaxaSiscomex($('#productsBody tr').length);

            const dadosFob = []; // cada item = { rowId, fobTotal }

            $('.fobUnitario').each(function() {
                const rowId = $(this).data('row');
                const fobUnit = MoneyUtils.parseMoney($(this).val());
                const qtd = parseInt($(`#quantidade-${rowId}`).val()) || 0;
                const fobTotal = fobUnit * qtd;

                dadosFob.push({
                    rowId,
                    fobTotal
                });
            });

            const fobTotalGeral = dadosFob.reduce((acc, linha) => acc + linha.fobTotal, 0);

            for (const linha of dadosFob) {
                const {
                    rowId,
                    fobTotal
                } = linha;

                const fatorVlrFob_AX = fobTotal / (fobTotalGeral || 1);
                const fatorTaxaSiscomex_AY = taxaSiscomex / ((fobTotal * dolar) || 1);
                const taxaSisComexUnitaria_BB = fatorTaxaSiscomex_AY * fobTotal * dolar;

                // Preenche os campos correspondentes
                $(`#fator_valor_fob-${rowId}`).val(MoneyUtils.formatMoney(fatorVlrFob_AX));
                $(`#fator_tx_siscomex-${rowId}`).val(MoneyUtils.formatMoney(fatorTaxaSiscomex_AY));
                $(`#taxa_siscomex-${rowId}`).val(MoneyUtils.formatMoney(taxaSisComexUnitaria_BB));
                atualizarCamposCabecalho()
            }
        }

        function atualizarCamposCabecalho() {
            const campos = getCamposExternos()
            const lengthTable = $('#productsBody tr').length

            // Primeiro calcula o FOB total geral
            let fobTotalGeral = 0
            for (let i = 0; i < lengthTable; i++) {
                const fobUnit = MoneyUtils.parseMoney($(`#fob_unit_usd-${i}`).val())
                const qtd = parseInt($(`#quantidade-${i}`).val()) || 0
                fobTotalGeral += fobUnit * qtd
            }

            // Para cada linha da tabela
            for (let i = 0; i < lengthTable; i++) {
                const fobUnit = MoneyUtils.parseMoney($(`#fob_unit_usd-${i}`).val())
                const qtd = parseInt($(`#quantidade-${i}`).val()) || 0
                const fobTotal = fobUnit * qtd
                const fatorVlrFob_AX = fobTotalGeral ? (fobTotal / fobTotalGeral) : 0
                let desp_desenbaraco_parte_1 = 0

                for (let campo of campos) {
                    const valorCampo = MoneyUtils.parseMoney($(`#${campo}`).val()) || 0
                    const valorDistribuido = MoneyUtils.formatMoney(valorCampo * fatorVlrFob_AX)
                    desp_desenbaraco_parte_1 += valorDistribuido;
                    $(`#${campo}-${i}`).val(valorDistribuido)
                }
                let taxa_siscomex = $(`#taxa_siscomex-${i}`).val() ? MoneyUtils.parseMoney($(`#taxa_siscomex-${i}`).val()) :
                    0
                let multa = $(`#multa-${i}`).val() ? MoneyUtils.parseMoney($(`#multa-${i}`).val()) : 0
                let taxa_def = $(`#tx_def_li-${i}`).val() ? MoneyUtils.parseMoney($(`#tx_def_li-${i}`).val()) : 0

                desp_desenbaraco_parte_1 += multa + taxa_def + taxa_siscomex;

                let capatazia = $('#capatazia-' + i).val() ? parseFloat($('#capatazia-' + i).val()) : 0
                let afrmm = $('#afrmm-' + i).val() ? parseFloat($('#afrmm-' + i).val()) : 0
                let armazenagem_sts = $('#armazenagem_sts-' + i).val() ? parseFloat($('#armazenagem_sts-' + i).val()) : 0
                let frete_dta_sts_ana = $('#frete_dta_sts_ana-' + i).val() ? parseFloat($('#frete_dta_sts_ana-' + i)
                    .val()) : 0
                let honorarios_nix = $('#honorarios_nix-' + i).val() ? parseFloat($('#honorarios_nix-' + i).val()) : 0


                let desp_desenbaraco_parte_2 = multa + taxa_def + taxa_siscomex + capatazia + afrmm + armazenagem_sts +
                    frete_dta_sts_ana + honorarios_nix

                let despesa_desembaraco = desp_desenbaraco_parte_1 - desp_desenbaraco_parte_2
                const vlrIcmsReduzido = MoneyUtils.parseMoney($(`#valor_icms_reduzido-${i}`).val())
                let qquantidade = parseInt($(`#quantidade-${i}`).val()) || 0
                const vlrTotalNfComIcms = MoneyUtils.parseMoney($(`#valor_total_nf_com_icms_st-${i}`).val())
                const custo_unitario_final = ((vlrTotalNfComIcms + despesa_desembaraco + 0 + 0) - vlrIcmsReduzido) /
                    qquantidade
                const custo_total_final = custo_unitario_final * qquantidade
                $(`#desp_desenbaraco-${i}`).val(MoneyUtils.formatMoney(despesa_desembaraco))
                $(`#custo_unitario_final-${i}`).val(MoneyUtils.formatMoney(custo_unitario_final))
                $(`#custo_total_final-${i}`).val(MoneyUtils.formatMoney(custo_total_final))
            }
        }
        $(document).on('click', '.removeLine', function() {
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
                    const id = this.dataset.id
                    $(`#row-${id}`).remove();
                } else {
                    Toast.fire({
                        icon: 'info',
                        title: 'Ação cancelada'
                    });
                }
            });
        })

        function calcularTaxaSiscomex(quantidade) {
            const valorRegistroDI = 115.67;

            const faixas = [{
                    max: 5,
                    adicional: 38.56
                },
                {
                    max: 3,
                    adicional: 30.85
                },
                {
                    max: 5,
                    adicional: 30.85
                },
                {
                    max: 7,
                    adicional: 23.14
                },
                {
                    max: 9,
                    adicional: 23.14
                },
                {
                    max: 10,
                    adicional: 23.14
                },
                {
                    max: 20,
                    adicional: 15.42
                },
                {
                    max: 50,
                    adicional: 7.71
                },
                {
                    max: Infinity,
                    adicional: 3.86
                },
            ];

            const faixa = faixas.find(f => quantidade <= f.max);
            const total = valorRegistroDI + faixa.adicional;

            return parseFloat(total.toFixed(2));
        }

        $(document).on('click', '.addProduct', function() {
            let lengthOptions = $('#productsBody tr').length;
            let newIndex = lengthOptions;

            let select = `<select required data-row="${newIndex}" class="custom-select selectProduct select2" name="produtos[${newIndex}][produto_id]" id="produto_id-${newIndex}">
            <option selected disabled>Selecione uma opção</option>`;

            for (let produto of products) {
                select += `<option value="${produto.id}">${produto.modelo} - ${produto.codigo}</option>`;
            }
            select += '</select>';

            let tr = `<tr id="row-${newIndex}" >
        <!-- Coluna fixa de ações -->
        <td style="position: sticky; left: 0; z-index: 5; background-color: white;">
            <button type="button" class="btn btn-danger removeLine btn-sm btn-remove" data-id="${newIndex}">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
        
        <input type="hidden" name="produtos[${newIndex}][processo_produto_id]" id="processo_produto_id-${newIndex}" value="">
        
        <td>${select}</td>
        <td><input data-row="${newIndex}" type="text" step="1" class=" form-control" name="produtos[${newIndex}][descricao]" id="descricao-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control " name="produtos[${newIndex}][adicao]" id="adicao-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="number" class="form-control" name="produtos[${newIndex}][item]"  id="item-${newIndex}" value=""></td>
        <td><input type="text" class=" form-control " readonly name="produtos[${newIndex}][codigo]" id="codigo-${newIndex}" value=""></td>
        <td><input type="text" class=" form-control " readonly name="produtos[${newIndex}][ncm]" id="ncm-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text"  class=" form-control moneyReal" name="produtos[${newIndex}][quantidade]" id="quantidade-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][peso_liquido_unitario]" id="peso_liquido_unitario-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal pesoLiqTotal" name="produtos[${newIndex}][peso_liquido_total]" id="peso_liquido_total-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][fator_peso]" id="fator_peso-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class="form-control moneyReal fobUnitario" name="produtos[${newIndex}][fob_unit_usd]" id="fob_unit_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][fob_total_usd]" id="fob_total_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][fob_total_brl]" id="fob_total_brl-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][frete_usd]" id="frete_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][frete_brl]" id="frete_brl-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][seguro_usd]" id="seguro_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][seguro_brl]" id="seguro_brl-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][acresc_frete_usd]" id="acresc_frete_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][acresc_frete_brl]" id="acresc_frete_brl-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][thc_usd]" id="thc_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][thc_brl]" id="thc_brl-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_aduaneiro_usd]" id="valor_aduaneiro_usd-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_aduaneiro_brl]" id="valor_aduaneiro_brl-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control percentage2" name="produtos[${newIndex}][ii_percent]" id="ii_percent-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control percentage2" name="produtos[${newIndex}][ipi_percent]" id="ipi_percent-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control percentage2" name="produtos[${newIndex}][pis_percent]" id="pis_percent-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control percentage2" name="produtos[${newIndex}][cofins_percent]" id="cofins_percent-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control percentage2" name="produtos[${newIndex}][icms_percent]" id="icms_percent-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control percentage2" readonly name="produtos[${newIndex}][icms_reduzido_percent]" id="icms_reduzido_percent-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][reducao]" id="reducao-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_ii]" id="valor_ii-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][base_ipi]" id="base_ipi-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_ipi]" id="valor_ipi-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][base_pis_cofins]" id="base_pis_cofins-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_pis]" id="valor_pis-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_cofins]" id="valor_cofins-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][despesa_aduaneira]" id="despesa_aduaneira-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][base_icms_sem_reducao]" id="base_icms_sem_reducao-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_icms_sem_reducao]" id="valor_icms_sem_reducao-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][base_icms_reduzido]" id="base_icms_reduzido-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_icms_reduzido]" id="valor_icms_reduzido-${newIndex}" value=""></td>
        <td><input data-row="${newIndex}" type="text" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_unit_nf]" id="valor_unit_nf-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_total_nf]" id="valor_total_nf-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_total_nf_sem_icms_st]" id="valor_total_nf_sem_icms_st-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][base_icms_st]" id="base_icms_st-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control percentage" name="produtos[${newIndex}][mva]" id="mva-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control percentage" name="produtos[${newIndex}][icms_st]" id="icms_st-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_icms_st]" id="valor_icms_st-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][valor_total_nf_com_icms_st]" id="valor_total_nf_com_icms_st-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][fator_valor_fob]" id="fator_valor_fob-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][fator_tx_siscomex]" id="fator_tx_siscomex-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" name="produtos[${newIndex}][multa]" id="multa-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" name="produtos[${newIndex}][tx_def_li]" id="tx_def_li-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][taxa_siscomex]" id="taxa_siscomex-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][outras_taxas_agente]" id="outras_taxas_agente-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][liberacao_bl]" id="liberacao_bl-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][desconsolidacao]" id="desconsolidacao-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][isps_code]" id="isps_code-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][handling]" id="handling-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][capatazia]" id="capatazia-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][afrmm]" id="afrmm-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][armazenagem_sts]" id="armazenagem_sts-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][frete_dta_sts_ana]" id="frete_dta_sts_ana-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][sda]" id="sda-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][rep_sts]" id="rep_sts-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][armaz_ana]" id="armaz_ana-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][lavagem_container]" id="lavagem_container-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][rep_anapolis]" id="rep_anapolis-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][li_dta_honor_nix]" id="li_dta_honor_nix-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][honorarios_nix]" id="honorarios_nix-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][desp_desenbaraco]" id="desp_desenbaraco-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][diferenca_cambial_frete]" id="diferenca_cambial_frete-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][diferenca_cambial_fob]" id="diferenca_cambial_fob-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][custo_unitario_final]" id="custo_unitario_final-${newIndex}" value=""></td>
        <td><input type="text" data-row="${newIndex}" class=" form-control moneyReal" readonly name="produtos[${newIndex}][custo_total_final]" id="custo_total_final-${newIndex}" value=""></td>
    </tr>`;

            $('#productsBody').append(tr);

            $('.select2').select2({
                width: '100%'
            });
            $('input[data-row="' + newIndex + '"], select[data-row="' + newIndex + '"]').trigger('change');
            $(`#row-${newIndex} input, #row-${newIndex} select, #row-${newIndex} textarea`).each(function() {
                initialInputs.add(this); // Adiciona cada novo elemento ao Set
            });
        });

        $(document).ready(function($) {

            var activeTab = localStorage.getItem('activeTab_processo');
            if (activeTab) {
                $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
            }

        })
        $('.nav-link').on('click', function(e) {
            var currentTab = $(e.target).attr('href');
            localStorage.setItem('activeTab_processo', currentTab);
        });

     function adicionarSeparadoresAdicao() {
    const tbody = document.getElementById('productsBody');
    const linhas = Array.from(tbody.querySelectorAll('tr:not(.separador-adicao)'));

    // Primeiro remove todos os separadores existentes
    document.querySelectorAll('.separador-adicao').forEach(el => el.remove());

    if (linhas.length === 0) return;

    // Ordenar as linhas por adição e depois por item
    linhas.sort((a, b) => {
        const adicaoA = parseFloat(a.querySelector('input[name*="[adicao]"]').value) || 0;
        const adicaoB = parseFloat(b.querySelector('input[name*="[adicao]"]').value) || 0;

        if (adicaoA !== adicaoB) {
            return adicaoA - adicaoB; // primeiro pela adição
        }

        const itemA = parseFloat(a.querySelector('input[name*="[item]"]').value) || 0;
        const itemB = parseFloat(b.querySelector('input[name*="[item]"]').value) || 0;
        return itemA - itemB; // depois pelo item
    });

    // Agrupar linhas por adição
    const grupos = {};
    linhas.forEach(linha => {
        const adicao = parseFloat(linha.querySelector('input[name*="[adicao]"]').value) || 0;
        if (!grupos[adicao]) grupos[adicao] = [];
        grupos[adicao].push(linha);
    });

    // Limpar o tbody
    while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild);
    }

    // Adicionar linhas com separadores
    Object.keys(grupos).sort((a, b) => a - b).forEach((adicao, index) => {
        if (index > 0) {
            const separador = document.createElement('tr');
            separador.className = 'separador-adicao';
            separador.innerHTML =
                `<td colspan="100" style="background-color: #B7AA09 !important;border-top: 20px dashed #999; height: 10px;"></td>`;
            tbody.appendChild(separador);
        }

        grupos[adicao].forEach(linha => {
            tbody.appendChild(linha);
        });
    });
}



        // Atualizar a função reordenarLinhas para usar os separadores
        function reordenarLinhas() {
            // Se já estiver reordenando, ignora
            if (reordenando) return;

            reordenando = true;

            adicionarSeparadoresAdicao();

            // Disparar eventos de mudança para recalcular valores, exceto para o campo de adição
            $('input:not([name*="[adicao]"]), select').trigger('change');

            reordenando = false;
        }

        // Adicionar CSS para os separadores
        const style = document.createElement('style');
        style.textContent = `
    .separador-adicao td {
        background-color: #f8f9fa !important;
        border-top: 2px dashed #aaa !important;
        border-bottom: none !important;
        height: 10px;
    }
    .separador-adicao:hover {
        background-color: transparent !important;
    }
`;
        document.head.appendChild(style);

        $(document).on('change', 'input[name*="[adicao]"]', reordenarLinhas);
        $(document).on('click', '.btn-reordenar', reordenarLinhas);
    </script>
@endsection
