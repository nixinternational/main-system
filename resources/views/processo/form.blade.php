@extends('layouts.app')
@section('title', isset($cliente) ? '' : 'Cadastrar Processo')


@section('content')
    <style>
        /* Aumentar a altura do modal */
        .modal-dialog.modal-xl {
            max-width: 90%;
            height: 80vh;
            /* Aumenta a altura do modal */
            margin: 30px auto;
            /* Ajusta a margem para o modal */
        }

        .modal-content {
            height: 100%;
            /* Garante que o conteúdo ocupe toda a altura do modal */
        }

        .modal-body {
            height: 100%;
            /* Faz com que o corpo do modal ocupe toda a altura disponível */
            overflow-y: auto;
            /* Permite rolagem se o conteúdo for maior */
        }

        /* Ajusta o iframe */
        #pdf-iframe {
            width: 100%;
            height: 80vh;
            /* Aumenta a altura do iframe */
            border: none;
            /* Remove a borda do iframe */
        }

        /* Ajusta a imagem */
        #imagePreview {
            width: 100%;
            max-height: 80vh;
            /* Limita a altura da imagem */
            object-fit: contain;
            /* Faz com que a imagem se ajuste ao contêiner sem distorção */
            display: none;
            /* Inicialmente escondido */
        }

        /* Ajusta a visibilidade do texto de descrição */
        #doc-text {
            display: none;
            /* Inicialmente escondido */
        }

        th {
            min-width: 200px;
        }
    </style>
    <style>
        .dados-container {
            display: flex;
            gap: 40px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin-bottom: 30px;
            margin-top: 30px
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .dados-basicos th,
        .dados-basicos td {
            border: 1px solid #000;
            padding: 5px 8px;
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
            margin-bottom: 5px;
        }

        .info-complementares .bold {
            font-weight: bold;
        }
    </style>
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
                                aria-selected="false">Informações Cadastrais</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-two-tabContent">
                        <div class="tab-pane fade active show" id="custom-tabs-two-home" role="tabpanel"
                            aria-labelledby="custom-tabs-two-home-tab">
                            <small class="text-danger d-none" id="avisoProcessoAlterado">Processo alterado, pressione o
                                botão salvar para persistir as alterações</small>
                            <form enctype="multipart/form-data"
                                action="{{ isset($processo) ? route('processo.update', $processo->id) : route('processo.store') }}"
                                method="POST">
                                @csrf
                                @if (isset($processo))
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-4">
                                        <label for="exampleInputEmail1" class="form-label">Cliente</label>

                                        <select {{ isset($processo) ? 'disabled' : '' }} class="custom-select select2"
                                            name="cliente_id">
                                            <option selected disabled>Selecione uma opção</option>
                                            @foreach ($clientes as $cliente)
                                                <option
                                                    {{ isset($processo) && $processo->cliente_id == $cliente->id ? 'selected' : '' }}
                                                    value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <button type="submit" class="btn btn-primary mt-3">Salvar</button>

                                    </div>
                                </div>

                                <div class="dados-container">
                                    <!-- DADOS BÁSICOS -->
                                    <table class="dados-basicos">
                                        <thead>
                                            <tr>
                                                <th colspan="3">DADOS BÁSICOS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>DI</td>
                                                <td colspan="2" class="highlight">24/2387113-8</td>
                                            </tr>
                                            <tr>
                                                <td>PROCESSO</td>
                                                <td class="highlight">I9IIMP-008/24</td>
                                                <td class="highlight">REAL</td>
                                            </tr>
                                            <tr>
                                                <td>VALOR FOB</td>
                                                <td class="highlight">USD <span id="fobTotalProcesso"></span></td>
                                                <td class="highlight">R$ <span id="fobTotalProcessoReal"></span></td>
                                            </tr>
                                            <tr>
                                                <td>FRETE INTERNACIONAL</td>
                                                <td class="highlight">

                                                    <input
                                                        value="{{ isset($processo) ? $processo->frete_internacional : '' }}"
                                                        class="form-control" name="frete_internacional"
                                                        id="frete_internacional">
                                                </td>
                                                <td class="highlight">R$ <span id="frete_internacional_real">{{ isset($processo) ? $processo->frete_internacional * $dolar : '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td>SEGURO INTERNACIONAL</td>
                                                <td class="highlight"> <input
                                                        value="{{ isset($processo) ? $processo->seguro_internacional : '' }}"
                                                        class="form-control" name="seguro_internacional"
                                                        id="seguro_internacional"></td>
                                                <td class="highlight">R$ <span id="seguro_internacional_real">{{ isset($processo) ? $processo->seguro_internacional * $dolar : '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td>ACRESCIMO DO FRETE</td>
                                                <td class="highlight"> <input class="form-control"
                                                        value="{{ isset($processo) ? $processo->acrescimo_frete : '' }}"
                                                        name="acrescimo_frete" id="acrescimo_frete"></td>
                                                <td class="highlight">R$ <span id="acrescimo_frete_real">{{ isset($processo) ? $processo->acrescimo_frete * $dolar : '' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td>VALOR CIF</td>
                                                <td class="highlight">USD 83.185,00</td>
                                                <td class="highlight">R$ 480.817,62</td>
                                            </tr>
                                            <tr>
                                                <td>TAXA DO DOLAR</td>
                                                <td class="highlight"> {{$dolar}}</td>
                                                <td class="highlight">DOLAR</td>
                                            </tr>
                                            <tr>
                                                <td>IUAN RENMIMBI</td>
                                                <td class="highlight">-</td>
                                                <td></td>
                                            </tr>
                                            <tr class="green-highlight">
                                                <td>THC/CAPATAZIA</td>
                                                <td>R$ 1.250,00</td>
                                                <td>USD 216,26</td>
                                            </tr>
                                            <tr>
                                                <td>PESO BRUTO</td>
                                                <td colspan="2"> <input class="form-control" name="peso_bruto"
                                                        value="{{ isset($processo) ? $processo->peso_bruto : '' }}"
                                                        id="peso_bruto">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>PESO LIQUIDO</td>
                                                <td colspan="2">14.046,9700</td>
                                            </tr>
                                            <tr>
                                                <td>MULTA</td>
                                                <td colspan="2" class="highlight">R$ -</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- INFORMAÇÕES COMPLEMENTARES -->
                                    <div class="info-complementares">
                                        <h4>INFORMAÇÕES COMPLEMENTARES</h4>

                                        <div class="linha">
                                            <span class="bold">DESCRIÇÃO DA MERCADORIA:</span><br>
                                            Conforme DI 24/2387113-8, desembaraçada em 31/10/2024
                                        </div>

                                        <div class="linha"><span class="bold">II:</span> R$ 59.247,82</div>
                                        <div class="linha"><span class="bold">IPI:</span> R$ 5.977,14</div>
                                        <div class="linha"><span class="bold">PIS:</span> R$ 10.961,58</div>
                                        <div class="linha"><span class="bold">COFINS:</span> R$ 54.764,78</div>
                                        <div class="linha"><span class="bold">Desp. Aduanei:</span> R$ 25.668,48</div>

                                        <br>

                                        <div class="linha"><span class="bold">QUANTIDADE:</span> 411</div>
                                        <div class="linha"><span class="bold">ESPECIE:</span> VOLUMES</div>
                                        <div class="linha"><span class="bold">PESO BRUTO:</span> 14.621,4500</div>
                                        <div class="linha"><span class="bold">PESO LIQUIDO:</span> 14.046,9700</div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary mb-2 addProduct">Adicionar Produto</button>
                                <div style="overflow-x: auto; width: 100%;">
                                    <table class="table table-bordered table-striped" style="min-width: 3000px;">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>PRODUTO</th>
                                                <th>Descrição</th>
                                                <th>ADIÇÃO</th>
                                                <th>ITEM</th>
                                                <th>CODIGO</th>
                                                <th>NCM</th>
                                                <th>QUANTD</th>
                                                <th>PESO LID. UNIT</th>
                                                <th>PESO LIQ TOTAL</th>
                                                <th>FATOR PESO</th>
                                                <th>FOB UNIT USD</th>
                                                <th>FOB TOTAL USD</th>
                                                <th>VLR TOTALFOB R$</th>
                                                <th>FRETE INT.USD</th>
                                                <th>FRETE INT.R$</th>
                                                <th>SEGURO INT.USD</th>
                                                <th>SEGURO INT.R$</th>
                                                <th>ACRESC. FRETE USD</th>
                                                <th>ACRESC. FRETE R$</th>
                                                <th>THC USD</th>
                                                <th>THC R$</th>
                                                <th>VLR ADUANEIRO USD</th>
                                                <th>VLR ADUANEIRO R$</th>
                                                <th>II</th>
                                                <th>IPI</th>
                                                <th>PIS</th>
                                                <th>COFINS</th>
                                                <th>ICMS</th>
                                                <th>ICMS REDUZIDO</th>
                                                <th>REDUÇÃO</th>
                                                <th>VLR II</th>
                                                <th>BC IPI</th>
                                                <th>VLR IPI</th>
                                                <th>BC PIS/COFINS</th>
                                                <th>VLR PIS</th>
                                                <th>VLR COFINS</th>
                                                <th>DESP. ADUANEIRA</th>
                                                <th>BC ICMS S/REDUÇÃO</th>
                                                <th>VLR ICMS S/RED.</th>
                                                <th>BC ICMS REDUZIDO</th>
                                                <th>VLR ICMS REDUZ.</th>
                                                <th>VLR UNIT PROD. NF</th>
                                                <th>VLR TOTAL PROD. NF</th>
                                                <th>VLR TOTAL NF S/ICMS ST</th>
                                                <th>BC ICMS-ST</th>
                                                <th>MVA</th>
                                                <th>ICMS-ST</th>
                                                <th>VLR ICMS-ST</th>
                                                <th>VLR TOTAL NF C/ICMS-ST</th>
                                                <th>FATOR VLR FOB</th>
                                                <th>FATOR TX SISCOMEX</th>
                                                <th>MULTA</th>
                                                <th>TX DEF. LI</th>
                                                <th>TAXA SISCOMEX</th>
                                                <th>OUTRAS TX AGENTE</th>
                                                <th>LIBERAÇÃO BL</th>
                                                <th>DESCONS.</th>
                                                <th>ISPS CODE</th>
                                                <th>HANDLING</th>
                                                <th>CAPATAZIA</th>
                                                <th>AFRMM</th>
                                                <th>ARMAZENAGEM STS</th>
                                                <th>FRETE DTA STS/ANA</th>
                                                <th>S.D.A</th>
                                                <th>REP.STS</th>
                                                <th>ARMAZ. ANA</th>
                                                <th>LAVAGEM CONT</th>
                                                <th>REP. ANAPOLIS</th>
                                                <th>LI+DTA+HONOR.NIX</th>
                                                <th>HONORÁRIOS NIX</th>
                                                <th>DESP. DESEMBARAÇO</th>
                                                <th>DIF. CAMBIAL FRETE</th>
                                                <th>DIF.CAMBIAL FOB</th>
                                                <th>CUSTO UNIT FINAL</th>
                                                <th>CUSTO TOTAL FINAL</th>
                                            </tr>
                                        </thead>
                                        <tbody id="productsBody">
                                            <!-- Dados -->
                                        </tbody>
                                    </table>
                                </div>
                            </form>

                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
    <input type="hidden" name="productsClient" id="productsClient" value="{{ $productsClient }}">
    <input type="hidden" name="dolarHoje" id="dolarHoje" value="{{ $dolar }}">
    <input type="hidden" id="processoAlterado" name="processoAlterado" value="0">
    <script>
        $('#frete_internacional, #seguro_internacional, #acrescimo_frete').trigger('change');

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            customClass: {
                popup: 'colored-toast',
            },
            animation: true,
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
        });

        function downloadDocument(url) {
            const link = document.createElement('a');
            link.href = url;
            link.download = url.substring(url.lastIndexOf('/') + 1); // Obtém o nome do arquivo a partir da URL

            link.click();

            Toast.fire({
                icon: 'success',
                title: 'Download iniciado...'
            });
        }
        $(document).on('change', 'input, select, textarea', function() {
            $('#avisoProcessoAlterado').removeClass('d-none')
            let rowId = $(this).data('row')
            if (rowId) {
                console.log('tem coisa pra mudar')
                let pesoTotal = parseFloat($(`#peso-total-input-${rowId}`).val()) || 0;
                let fobUnitario = parseFloat($(`#fob-unit-input-${rowId}`).val()) || 0;
                let quantidade = parseFloat($(`#quantd-input-${rowId}`).val()) || 0;


                let pesoLiqUnit = pesoTotal / (quantidade || 1); // evita divisão por 0
                let fobTotal = fobUnitario * quantidade;
                let totalPesoLiq = 0;
                $('.pesoLiqTotal').each(function() {
                    let valor = parseFloat($(this).val()) || 0;
                    totalPesoLiq += valor;
                });
                console.log(totalPesoLiq)
                // Recalcular fatorPeso de todas as linhas
                $('.pesoLiqTotal').each(function() {
                    const rowId = $(this).data('row');
                    const pesoTotalLinha = parseFloat($(this).val()) || 0;
                    const fatorPeso = pesoTotalLinha / (totalPesoLiq ||
                        1); // totalPesoLiq já calculado acima

                    $(`#fator-peso-${rowId}`).text(fatorPeso.toFixed(6));
                });

                let fobTotalGeral = 0;

                $('.fobUnitario').each(function() {
                    const rowId = $(this).data('row');
                    const unitario = parseFloat($(this).val()) || 0;
                    const qtd = parseInt($(`#quantd-input-${rowId}`).val()) || 0;

                    fobTotalGeral += unitario * qtd;
                });
                let dolar = $('#dolarHoje').val()
                $('#fobTotalProcesso').text(fobTotalGeral ?? 0)
                $('#fobTotalProcessoReal').text(fobTotalGeral * dolar)


                const valor = 'teste'
                $(`#peso-unit-${rowId}`).text(pesoLiqUnit);
                // $(`#fator-peso-${rowId}`).text(fatorPeso);
                $(`#fob-total-${rowId}`).text(fobTotal);
                $(`#fob-total-brl-${rowId}`).text(valor);
                $(`#frete-usd-${rowId}`).text(valor);
                $(`#frete-brl-${rowId}`).text(valor);
                $(`#seguro-usd-${rowId}`).text(valor);
                $(`#seguro-brl-${rowId}`).text(valor);
                $(`#acresc-frete-usd-${rowId}`).text(valor);
                $(`#acresc-frete-brl-${rowId}`).text(valor);
                $(`#thc-usd-${rowId}`).text(valor);
                $(`#thc-brl-${rowId}`).text(valor);
                $(`#vlr-adu-usd-${rowId}`).text(valor);
                $(`#vlr-adu-brl-${rowId}`).text(valor);
                $(`#ii-${rowId}`).text(valor);
                $(`#ipi-${rowId}`).text(valor);
                $(`#pis-${rowId}`).text(valor);
                $(`#cofins-${rowId}`).text(valor);
                $(`#icms-${rowId}`).text(valor);
                $(`#icms-reduzido-${rowId}`).text(valor);
                $(`#reducao-${rowId}`).text(valor);
                $(`#vlr-ii-${rowId}`).text(valor);
                $(`#bc-ipi-${rowId}`).text(valor);
                $(`#vlr-ipi-${rowId}`).text(valor);
                $(`#bc-pis-cofins-${rowId}`).text(valor);
                $(`#vlr-pis-${rowId}`).text(valor);
                $(`#vlr-cofins-${rowId}`).text(valor);
                $(`#desp-adu-${rowId}`).text(valor);
                $(`#bc-icms-sr-${rowId}`).text(valor);
                $(`#vlr-icms-sr-${rowId}`).text(valor);
                $(`#bc-icms-red-${rowId}`).text(valor);
                $(`#vlr-icms-red-${rowId}`).text(valor);
                $(`#vlr-unit-nf-${rowId}`).text(valor);
                $(`#vlr-total-nf-${rowId}`).text(valor);
                $(`#vlr-total-nf-sicms-${rowId}`).text(valor);
                $(`#bc-icms-st-${rowId}`).text(valor);
                $(`#mva-${rowId}`).text(valor);
                $(`#icms-st-${rowId}`).text(valor);
                $(`#vlr-icms-st-${rowId}`).text(valor);
                $(`#vlr-total-nf-cicms-${rowId}`).text(valor);
                $(`#fator-vlr-fob-${rowId}`).text(valor);
                $(`#fator-tx-siscomex-${rowId}`).text(valor);
                $(`#multa-${rowId}`).text(valor);
                $(`#tx-def-li-${rowId}`).text(valor);
                $(`#tx-siscomex-${rowId}`).text(valor);
                $(`#outras-tx-${rowId}`).text(valor);
                $(`#liberacao-bl-${rowId}`).text(valor);
                $(`#descons-${rowId}`).text(valor);
                $(`#isps-${rowId}`).text(valor);
                $(`#handling-${rowId}`).text(valor);
                $(`#capatazia-${rowId}`).text(valor);
                $(`#afrmm-${rowId}`).text(valor);
                $(`#armazenagem-sts-${rowId}`).text(valor);
                $(`#frete-dta-${rowId}`).text(valor);
                $(`#sda-${rowId}`).text(valor);
                $(`#rep-sts-${rowId}`).text(valor);
                $(`#armaz-ana-${rowId}`).text(valor);
                $(`#lavagem-cont-${rowId}`).text(valor);
                $(`#rep-anapolis-${rowId}`).text(valor);
                $(`#li-dta-honor-${rowId}`).text(valor);
                $(`#honorarios-nix-${rowId}`).text(valor);
                $(`#desp-desenb-${rowId}`).text(valor);
                $(`#dif-camb-frete-${rowId}`).text(valor);
                $(`#dif-camb-fob-${rowId}`).text(valor);
                $(`#custo-unit-final-${rowId}`).text(valor);
                $(`#custo-total-final-${rowId}`).text(valor);

            }
        });
        $(document).on('change', '.selectProduct', function() {
            let products = JSON.parse($('#productsClient').val());
            let productObject = products.find(el => el.id == this.value);

            let rowId = $(this).data('row');

            if (productObject) {
                $(`#codigo-${rowId}`).text(productObject.codigo);
                $(`#ncm-${rowId}`).text(productObject.ncm);
                $(`#descricao-produto-${rowId}`).text(productObject.descricao);

                $(`#quantd-input-${rowId}`).val(0)
            }
        });
        $(document).on('change', '#frete_internacional, #seguro_internacional, #acrescimo_frete', function() {
            let dolar = parseFloat($('#dolarHoje').val()) || 0;

            const updateValorReal = (inputId, spanId) => {
                let valor = parseFloat($(`#${inputId}`).val()) || 0;
                let convertido = valor * dolar;
                $(`#${spanId}`).text(convertido.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            };

            updateValorReal('frete_internacional', 'frete_internacional_real');
            updateValorReal('seguro_internacional', 'seguro_internacional_real');
            updateValorReal('acrescimo_frete', 'acrescimo_frete_real');
        });

        $(document).on('click', '.addProduct', function() {
            let products = JSON.parse($('#productsClient').val());
            let lengthOptions = $('#productsBody tr').length + 1;

            let select =
                `<select data-row="${lengthOptions}" class="custom-select selectProduct select2" name="produtos[]">
                                                                <option selected disabled>Selecione uma opção</option>

                    `;
            for (let produto of products) {
                select += `<option value="${produto.id}">${produto.modelo} - ${produto.codigo}</option>`;
            }
            select += '</select>';

            let tr = `<tr>
        <th id="produto-${lengthOptions}">${select}</th>
        <th id="descricao-produto-${lengthOptions}"></th>
        <td id="adicao-${lengthOptions}">    <input  data-row="${lengthOptions}" class="form-control" name="adicoes[]"value=""id="adicao-input-${lengthOptions}"></td>
        <td id="item-${lengthOptions}">${lengthOptions}</td>
        <td id="codigo-${lengthOptions}"></td>
        <td id="ncm-${lengthOptions}"></td>
        <td id="quantd-${lengthOptions}">
            <input  data-row="${lengthOptions}" class="form-control" name="quantidades[]"value=""id="quantd-input-${lengthOptions}">
        </td>
        <td id="peso-unit-${lengthOptions}"></td>
        <td id="peso-total-${lengthOptions}"><input  data-row="${lengthOptions}" class="form-control pesoLiqTotal" name="pesoTotal[]"value=""id="peso-total-input-${lengthOptions}"></td>
        <td id="fator-peso-${lengthOptions}"></td>
        <td id="fob-unit-${lengthOptions}"><input  data-row="${lengthOptions}" class="form-control fobUnitario" name="fobUnitario[]"value=""id="fob-unit-input-${lengthOptions}"></td>
        <td id="fob-total-${lengthOptions}"></td>
        <td id="fob-total-brl-${lengthOptions}"></td>
        <td id="frete-usd-${lengthOptions}"></td>
        <td id="frete-brl-${lengthOptions}"></td>
        <td id="seguro-usd-${lengthOptions}"></td>
        <td id="seguro-brl-${lengthOptions}"></td>
        <td id="acresc-frete-usd-${lengthOptions}"></td>
        <td id="acresc-frete-brl-${lengthOptions}"></td>
        <td id="thc-usd-${lengthOptions}"></td>
        <td id="thc-brl-${lengthOptions}"></td>
        <td id="vlr-adu-usd-${lengthOptions}"></td>
        <td id="vlr-adu-brl-${lengthOptions}"></td>
        <td id="ii-${lengthOptions}"></td>
        <td id="ipi-${lengthOptions}"></td>
        <td id="pis-${lengthOptions}"></td>
        <td id="cofins-${lengthOptions}"></td>
        <td id="icms-${lengthOptions}"></td>
        <td id="icms-reduzido-${lengthOptions}"></td>
        <td id="reducao-${lengthOptions}"></td>
        <td id="vlr-ii-${lengthOptions}"></td>
        <td id="bc-ipi-${lengthOptions}"></td>
        <td id="vlr-ipi-${lengthOptions}"></td>
        <td id="bc-pis-cofins-${lengthOptions}"></td>
        <td id="vlr-pis-${lengthOptions}"></td>
        <td id="vlr-cofins-${lengthOptions}"></td>
        <td id="desp-adu-${lengthOptions}"></td>
        <td id="bc-icms-sr-${lengthOptions}"></td>
        <td id="vlr-icms-sr-${lengthOptions}"></td>
        <td id="bc-icms-red-${lengthOptions}"></td>
        <td id="vlr-icms-red-${lengthOptions}"></td>
        <td id="vlr-unit-nf-${lengthOptions}"></td>
        <td id="vlr-total-nf-${lengthOptions}"></td>
        <td id="vlr-total-nf-sicms-${lengthOptions}"></td>
        <td id="bc-icms-st-${lengthOptions}"></td>
        <td id="mva-${lengthOptions}"></td>
        <td id="icms-st-${lengthOptions}"></td>
        <td id="vlr-icms-st-${lengthOptions}"></td>
        <td id="vlr-total-nf-cicms-${lengthOptions}"></td>
        <td id="fator-vlr-fob-${lengthOptions}"></td>
        <td id="fator-tx-siscomex-${lengthOptions}"></td>
        <td id="multa-${lengthOptions}"></td>
        <td id="tx-def-li-${lengthOptions}"></td>
        <td id="tx-siscomex-${lengthOptions}"></td>
        <td id="outras-tx-${lengthOptions}"></td>
        <td id="liberacao-bl-${lengthOptions}"></td>
        <td id="descons-${lengthOptions}"></td>
        <td id="isps-${lengthOptions}"></td>
        <td id="handling-${lengthOptions}"></td>
        <td id="capatazia-${lengthOptions}"></td>
        <td id="afrmm-${lengthOptions}"></td>
        <td id="armazenagem-sts-${lengthOptions}"></td>
        <td id="frete-dta-${lengthOptions}"></td>
        <td id="sda-${lengthOptions}"></td>
        <td id="rep-sts-${lengthOptions}"></td>
        <td id="armaz-ana-${lengthOptions}"></td>
        <td id="lavagem-cont-${lengthOptions}"></td>
        <td id="rep-anapolis-${lengthOptions}"></td>
        <td id="li-dta-honor-${lengthOptions}"></td>
        <td id="honorarios-nix-${lengthOptions}"></td>
        <td id="desp-desenb-${lengthOptions}"></td>
        <td id="dif-camb-frete-${lengthOptions}"></td>
        <td id="dif-camb-fob-${lengthOptions}"></td>
        <td id="custo-unit-final-${lengthOptions}"></td>
        <td id="custo-total-final-${lengthOptions}"></td>
    </tr>`;

            $('#productsBody').append(tr);
        });
    </script>


@endsection
