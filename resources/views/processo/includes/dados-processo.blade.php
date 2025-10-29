   <div class="tab-pane fade active show" id="custom-tabs-two-home" role="tabpanel"
       aria-labelledby="custom-tabs-two-home-tab">
       <form enctype="multipart/form-data" id="formProcesso"
           action="{{ isset($processo) ? route('update.processo', $processo->id) : route('processo.store') }}"
           method="POST">
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
               <div class="col-4">
                   <label for="exampleInputEmail1" class="form-label">Cliente</label>
                   <p>{{$processo->cliente->nome}}</p>
               </div>
               @if (isset($processo))
                   <div class="col-md-4">
                       <label for="processo_codigo_interno" class="form-label">PROCESSO</label>
                       <input value="{{ isset($processo) ? $processo->codigo_interno : '' }}" class=" form-control"
                           name="codigo_interno" id="processo_codigo_interno">
                   </div>
                   <div class="col-md-4">
                       <label for="descricao" class="form-label">Descrição (max 120 caracteres)</label>
                       <input value="{{ isset($processo) ? $processo->descricao : '' }}" class=" form-control"
                           name="descricao" id="descricao">
                   </div>
               @endif

           </div>
           @if (isset($processo))

               @php

               @endphp
               <div class="row mt-3">
                   <div class="col-3">
                       <label for="exampleInputEmail1" class="form-label">Canal</label>
                       <select class="custom-select select2" name="canal">
                           <option value="" selected hidden>Selecione uma opção</option>
                           <option {{ isset($processo) && $processo->canal == 'vermelho' ? 'selected' : '' }}
                               value="vermelho" hidden>Vermelho</option>
                           <option {{ isset($processo) && $processo->canal == 'amarelo' ? 'selected' : '' }}
                               value="amarelo" hidden>Amarelo</option>
                           <option {{ isset($processo) && $processo->canal == 'verde' ? 'selected' : '' }}
                               value="verde" hidden>Verde</option>
                       </select>
                   </div>
                   <div class="col-3">
                       <label for="status" class="form-label">Status</label>
                       <select class="custom-select select2" name="status">
                           <option value="" selected hidden>Selecione uma opção</option>
                           <option {{ isset($processo) && $processo->status == 'andamento' ? 'selected' : '' }}
                               value="andamento" hidden>Em Andamento</option>
                           <option {{ isset($processo) && $processo->status == 'finalizado' ? 'selected' : '' }}
                               value="finalizado" hidden>Finalizado</option>
                           <option {{ isset($processo) && $processo->status == 'prestacao_contas' ? 'selected' : '' }}
                               value="prestacao_contas" hidden>Prestação de Contas</option>
                       </select>
                   </div>
                   <div class="col-lg-3 col-md-6 col-sm-6">
                       <div class="form-group">
                           <label for="credenciamento_radar">Início Processo</label>
                           <input type="date" class=" form-control" id="credenciamento_radar"
                               name="data_desembaraco_inicio"
                               value="{{ old('data_desembaraco_inicio', isset($processo) ? $processo->data_desembaraco_inicio : '') }}">
                       </div>
                   </div>
                   <div class="col-lg-3 col-md-6 col-sm-6">
                       <div class="form-group">
                           <label for="credenciamento_radar">Data Desembaraço</label>
                           <input type="date" class=" form-control" id="credenciamento_radar"
                               name="data_desembaraco_fim"
                               value="{{ old('data_desembaraco_fim', isset($processo) ? $processo->data_desembaraco_fim : '') }}">
                       </div>
                   </div>

               </div>

               <div class="row mt-1">


                   <div class="col-md-2">
                       <label for="thc_capatazia" class="form-label">THC/CAPATAZIA (R$)</label>
                       <input
                           value="{{ isset($processo->thc_capatazia) ? number_format($processo->thc_capatazia, 2, ',', '.') : '' }}"
                           class="form-control moneyReal" name="thc_capatazia" id="thc_capatazia">
                   </div>

                   <div class="col-md-2">
                       <label for="peso_bruto" class="form-label">PESO BRUTO</label>
                       <input type="text"
                           value="{{ isset($processo->peso_bruto) ? number_format($processo->peso_bruto, 4, ',', '.') : '' }}"
                           class="form-control moneyReal" name="peso_bruto" id="peso_bruto">
                   </div>
                   <div class="col-md-2">
                       <label for="peso_bruto" class="form-label">PESO LÍQUIDO</label>
                       <input type="text"
                           value="{{ isset($processo->peso_liquido) ? number_format($processo->peso_liquido, 4, ',', '.') : '' }}"
                           class="form-control moneyReal" readonly>
                   </div>
                   <div class="col-md-2">
                       <label for="multa" class="form-label">MULTA</label>
                       <input type="text"
                           value="{{ isset($processo->multa) ? number_format($processo->multa, 2, ',', '.') : '' }}"
                           class="form-control moneyReal2" name="multa" id="multa">
                   </div>
                   <div class="col-md-2">
                       <label for="multa" class="form-label">QUANTIDADE</label>
                       <input type="text"
                           value="{{ isset($processo->quantidade) ? number_format($processo->quantidade, 4, ',', '.') : '' }}"
                           class="form-control moneyReal" name="quantidade" id="quantidade">
                   </div>
                   <div class="col-md-2">
                       <label for="multa" class="form-label">ESPÉCIE</label>
                       <input type="text" value="{{ isset($processo) ? $processo->especie : '' }}"
                           class="form-control " name="especie" id="especie">
                   </div>

               </div>
               <div class="row">

               </div>

               <div class="" style="margin: 1.5% 0; height:1px; background-color:black; width: 100%"></div>

               <div class="d-flex align-center flex-column">
                   <h4 class="mr-2">Cotações</h4>
                   <div>
                       <button class="btn btn-success " style="margin-right: 10px" type="button"
                           id="atualizarCotacoes">Atualizar
                           cotações para hoje
                       </button>

                   </div>

               </div>
               <div class="my-3">
                   <div class="col-3">
                       <span id="">Data de Cotação: </span>

                       <input type="date" class="form-control" name="data_cotacao" id="data_cotacao"
                           value="{{ isset($processo->data_moeda_frete_internacional) ? $processo->data_moeda_frete_internacional : '' }}"
                           max="{{ date('Y-m-d') }}">
                   </div>
               </div>
               <div class="mt-3 cards-container">
                   <div class="alert  p-3 card-item">
                       <div class="row">
                           <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                               <label for="frete_internacional" class="form-label ">FRETE INTERNACIONAL</label>
                               <input
                                   value="{{ isset($processo->frete_internacional) ? number_format($processo->frete_internacional, 2, ',', '.') : '' }}"
                                   class="form-control moneyReal2" name="frete_internacional"
                                   id="frete_internacional">
                           </div>
                           <div class="col-12 col-sm-6">
                               <label class="">MOEDA</label>
                               <select name="frete_internacional_moeda" id="frete_internacional_moeda"
                                   class="select2 w-100 moedas" aria-label="Moedas BRICS, UE e G20">
                                   <option value="">Selecione um país...</option>
                                   @foreach ($moedasSuportadas as $codigo => $nome)
                                       <option value="{{ $codigo }}"
                                           {{ isset($processo) && $processo->frete_internacional_moeda == $codigo ? 'selected' : '' }}>
                                           {{ $codigo }} - {{ $nome }}
                                       </option>
                                   @endforeach
                               </select>
                           </div>
                       </div>
                       <div class="row mt-2">
                           <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                               <input readonly
                                   value="{{ isset($processo) ? number_format($processo->frete_internacional * ($dolar[$processo->frete_internacional_moeda]['compra'] ?? 0), 2, ',', '.') : '' }}"
                                   class="form-control moneyReal" name="frete_internacional_visualizacao"
                                   id="frete_internacional_visualizacao">
                           </div>
                           <div class="col-12 col-sm-6">
                               <input
                                   value="{{ isset($processo->cotacao_frete_internacional) ? number_format($processo->cotacao_frete_internacional, 4, ',', '.') : '' }}"
                                   class="form-control cotacao" id="cotacao_frete_internacional"
                                   name="cotacao_frete_internacional">
                           </div>
                       </div>
                   </div>

                   <div class="alert  p-3 card-item">
                       <div class="row">
                           <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                               <label for="seguro_internacional" class="form-label ">SEGURO
                                   INTERNACIONAL</label>
                               <input
                                   value="{{ isset($processo->seguro_internacional) ? number_format($processo->seguro_internacional, 2, ',', '.') : '' }}"
                                   class="form-control moneyReal2" name="seguro_internacional"
                                   id="seguro_internacional">
                           </div>
                           <div class="col-12 col-sm-6">
                               <label class="">MOEDA</label>
                               <select name="seguro_internacional_moeda" id="seguro_internacional_moeda"
                                   class="select2 w-100 moedas" aria-label="Moedas BRICS, UE e G20">
                                   <option value="">Selecione um país...</option>
                                   @foreach ($moedasSuportadas as $codigo => $nome)
                                       <option value="{{ $codigo }}"
                                           {{ isset($processo) && $processo->seguro_internacional_moeda == $codigo ? 'selected' : '' }}>
                                           {{ $codigo }} - {{ $nome }}
                                       </option>
                                   @endforeach
                               </select>
                           </div>
                       </div>
                       <div class="row mt-2">
                           <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                               <input readonly
                                   value="{{ isset($processo) ? number_format($processo->seguro_internacional * ($dolar[$processo->seguro_internacional_moeda]['compra'] ?? 0), 2, ',', '.') : '' }}"
                                   class="form-control moneyReal2" name="seguro_internacional_visualizacao"
                                   id="seguro_internacional_visualizacao">
                           </div>
                           <div class="col-12 col-sm-6">
                               <input
                                   value="{{ isset($processo->cotacao_seguro_internacional) ? number_format($processo->cotacao_seguro_internacional, 4, ',', '.') : '' }}"
                                   class="form-control cotacao" id="cotacao_seguro_internacional"
                                   name="cotacao_seguro_internacional">
                           </div>
                       </div>
                   </div>

                   <div class="alert  p-3 card-item">
                       <div class="row">
                           <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                               <label for="acrescimo_frete" class="form-label ">ACRESCIMO DO FRETE</label>
                               <input
                                   value="{{ isset($processo->acrescimo_frete) ? number_format($processo->acrescimo_frete, 2, ',', '.') : '' }}"
                                   class="form-control moneyReal" name="acrescimo_frete" id="acrescimo_frete">
                           </div>
                           <div class="col-12 col-sm-6">
                               <label class="form-label ">MOEDA</label>
                               <select name="acrescimo_frete_moeda" id="acrescimo_frete_moeda"
                                   class="select2 w-100 moedas" aria-label="Moedas BRICS, UE e G20">
                                   <option value="">Selecione um país</option>
                                   @foreach ($moedasSuportadas as $codigo => $nome)
                                       <option value="{{ $codigo }}"
                                           {{ isset($processo) && $processo->acrescimo_frete_moeda == $codigo ? 'selected' : '' }}>
                                           {{ $codigo }} - {{ $nome }}
                                       </option>
                                   @endforeach
                               </select>
                           </div>
                       </div>
                       <div class="row mt-2">
                           <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                               <input readonly
                                   value="{{ isset($processo) ? number_format($processo->acrescimo_frete * ($dolar[$processo->acrescimo_frete_moeda]['compra'] ?? 0), 2, ',', '.') : '' }}"
                                   class="form-control moneyReal" name="acrescimo_frete_visualizacao"
                                   id="acrescimo_frete_visualizacao">
                           </div>
                           <div class="col-12 col-sm-6">
                               <input
                                   value="{{ isset($processo->cotacao_acrescimo_frete) ? number_format($processo->cotacao_acrescimo_frete, 4, ',', '.') : '' }}"
                                   class="form-control cotacao" id="cotacao_acrescimo_frete"
                                   name="cotacao_acrescimo_frete">
                           </div>
                       </div>
                   </div>

                   <div style="" class="alert  p-3 card-item">
                       <div class="row">
                           <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                               <label for="display_cotacao" class="form-label ">COTAÇÃO DO PROCESSO</label>

                               <input
                                   value="{{ isset($processo->cotacao_moeda_processo[$processo->moeda_processo])
                                       ? number_format($processo->cotacao_moeda_processo[$processo->moeda_processo]['venda'], 4, ',', '.')
                                       : '' }}"
                                   class="form-control cotacao" name="display_cotacao" id="display_cotacao">
                               <input type="hidden" id="cotacao_moeda_processo" name="cotacao_moeda_processo"
                                   value="{{ json_encode($processo->cotacao_moeda_processo ?? $dolar) }}">
                           </div>
                           <div class="col-12 col-sm-6">
                               <label class="form-label ">MOEDA</label>
                               <select name="moeda_processo" id="moeda_processo" class="select2 w-100 moedas"
                                   aria-label="Moedas BRICS, UE e G20">
                                   <option value="">Selecione um país</option>
                                   @foreach ($moedasSuportadas as $codigo => $nome)
                                       <option value="{{ $codigo }}"
                                           {{ isset($processo) && $processo->moeda_processo == $codigo ? 'selected' : '' }}>
                                           {{ $codigo }} - {{ $nome }}
                                       </option>
                                   @endforeach
                               </select>
                           </div>
                       </div>
                       <div id="visualizacaoMoedaDolar"
                           class="row mt-2 {{ $processo->moeda_processo == 'USD' || !$processo->moeda_processo ? 'd-none' : '' }}">
                           <div class="col-12">
                               <label for="moeda_processo_usd" class="form-label ">MOEDA EM USD</label>
                               <input disabled class="form-control cotacao"
                                   value="{{ isset($processo->cotacao_moeda_processo[$processo->moeda_processo . '_USD'])
                                       ? number_format($processo->cotacao_moeda_processo[$processo->moeda_processo . '_USD']['venda'], 4, ',', '.')
                                       : '' }}"
                                   name="moeda_processo_usd" id="moeda_processo_usd">
                           </div>
                       </div>
                   </div>
               </div>
               <div class="row">
                   @php
                       $cotacoes = isset($processo->cotacao_moeda_processo)
                           ? (is_array($processo->cotacao_moeda_processo)
                               ? $processo->cotacao_moeda_processo
                               : $processo->cotacao_moeda_processo)
                           : [];
                   @endphp

                   @if (!empty($cotacoes))
                       <div class="col-12">
                           <h5>Editar valores de venda das moedas do dia
                               <span id="cotacao-data-exibicao">
                                   {{ Carbon\Carbon::parse($processo->data_moeda_frete_internacional)->format('d/m/Y') }}
                               </span>
                           </h5>
                           <div class="row" id="cotacoes-moedas-row">
                               @foreach ($cotacoes as $codigo => $cotacao)
                                   <div class="col-md-3 mb-2">
                                       <label class="form-label">{{ $cotacao['nome'] ?? $codigo }}
                                           ({{ $codigo }})
                                       </label>
                                       <input type="hidden" name="cotacao_moeda_processo[{{ $codigo }}][nome]"
                                           value="{{ $cotacao['nome'] ?? $codigo }}">
                                       <input type="text" step="0.0001" min="0"
                                           class="form-control cotacao"
                                           name="cotacao_moeda_processo[{{ $codigo }}][venda]"
                                           value="{{ number_format($cotacao['venda'], 4, ',', '.') }}"
                                           aria-label="Valor de venda para {{ $cotacao['nome'] ?? $codigo }}">
                                   </div>
                               @endforeach
                           </div>
                       </div>
                   @endif
               </div>



               <style>
                   .cards-container {
                       display: flex;
                       flex-wrap: wrap;
                       gap: 15px;
                       width: 100%;
                   }

                   .card-item {
                       flex: 1 1 calc(25% - 15px);
                       background-color: rgb(183, 170, 9, 0.1);
                       min-width: 280px;
                       box-sizing: border-box;
                       display: flex;
                       flex-direction: column;
                   }

                   /* Acima de 1814px - 4 colunas em uma linha */
                   @media (min-width: 1814px) {
                       .card-item {
                           flex: 1 1 calc(25% - 15px);
                       }
                   }

                   /* Abaixo de 1814px - 2x2 (2 colunas) */
                   @media (max-width: 1813px) {
                       .card-item {
                           flex: 1 1 calc(50% - 15px);
                       }
                   }

                   /* Para tablets */
                   @media (max-width: 1199px) {
                       .card-item {
                           flex: 1 1 calc(50% - 15px);
                       }
                   }

                   /* Para tablets pequenos e mobile grande */
                   @media (max-width: 992px) {
                       .card-item {
                           flex: 1 1 calc(50% - 15px);
                           min-width: 250px;
                       }

                       .cards-container {
                           gap: 12px;
                       }
                   }

                   /* Para mobile */
                   @media (max-width: 768px) {
                       .card-item {
                           flex: 1 1 100%;
                           min-width: 100%;
                       }

                       .cards-container {
                           gap: 10px;
                       }
                   }

                   /* Para mobile pequeno */
                   @media (max-width: 576px) {
                       .card-item {
                           padding: 15px 12px !important;
                       }

                       .cards-container {
                           gap: 8px;
                       }

                       .form-control,
                       .select2 {
                           font-size: 14px;
                       }

                       .form-label {
                           font-size: 13px;
                           margin-bottom: 4px;
                       }
                   }

                   /* Melhorias para os selects em mobile */
                   @media (max-width: 576px) {
                       .select2-container .select2-selection--single {
                           height: 38px !important;
                       }

                       .select2-container--default .select2-selection--single .select2-selection__rendered {
                           line-height: 38px !important;
                           font-size: 14px;
                       }

                       .select2-container--default .select2-selection--single .select2-selection__arrow {
                           height: 38px !important;
                       }
                   }

                   /* Garantir que os cards tenham altura consistente */
                   .card-item .alert {
                       flex: 1;
                       display: flex;
                       flex-direction: column;
                   }

                   .card-item .row {
                       flex: 1;
                   }
               </style>

           @endif
           <div class="col-1">
               <button type="submit" class="btn btn-primary mt-3">Salvar</button>
           </div>
       </form>

       <script>
           $(document).ready(function() {
               var today = new Date().toISOString().split('T')[0];
               $('#data_cotacao').attr('max', today);

               $('#data_cotacao').on('change', function() {
                   var selectedDate = $(this).val();
                   if (selectedDate > today) {
                       Swal.fire({
                           icon: 'warning',
                           title: 'Data inválida',
                           text: 'Não é permitido selecionar datas futuras. A data foi ajustada para hoje.',
                           timer: 3000
                       });
                       $(this).val(today);
                   }
               });
           });

           function formatarCampoPorClasse(container, classe, decimais) {
               container.find(classe).each(function() {
                   let val = $(this).val();

                   if (val && val.trim() !== '') {
                       val = val.trim().replace('%', '').trim();
                       if (val.includes(',')) {
                           val = val.replace(/\./g, '').replace(',', '.');
                       } else {
                           val = val.replace(',', '.');
                       }
                       let numero = parseFloat(val);
                       if (!isNaN(numero)) {
                           let formatado = numero.toLocaleString('pt-BR', {
                               minimumFractionDigits: decimais,
                               maximumFractionDigits: decimais
                           });
                           if (classe.includes('percentage')) {
                               formatado += ' %';
                           }
                           $(this).val(formatado);
                       }
                   }
               });
           }

           // Monitoramento específico para o campo thc_capatazia
           function monitorarTHCCapatazia() {
               const campo = $('#thc_capatazia');

               // Monitora mudanças de valor
               campo.on('input change', function(e) {
                   console.log('🚨 THC Capatazia alterado por EVENTO:', e.type);
                   console.log('Valor atual:', this.value);
                   console.trace('Stack trace do evento');
               });

               // Monkey patch do método val()
               const originalVal = $.fn.val;
               $.fn.val = function(value) {
                   if (value !== undefined && this.is('#thc_capatazia')) {
                       console.log('🚨 THC Capatazia alterado via .val()');
                       console.log('Novo valor:', value);
                       console.trace('Stack trace do .val()');
                   }
                   return originalVal.apply(this, arguments);
               };

               // Monitora focos e blurs para debug
               campo.on('focus', function() {
                   console.log('THC Capatazia em foco, valor:', this.value);
               });

               campo.on('blur', function() {
                   console.log('THC Capatazia perdeu foco, valor:', this.value);
               });
           }

           // Executar o monitoramento
           monitorarTHCCapatazia();

           function forcarFormatacaoCamposCards() {

               const container = $('.cards-container');

               formatarCampoPorClasse(container, '.moneyReal', 5);
               formatarCampoPorClasse(container, '.moneyReal2', 2);
               formatarCampoPorClasse(container, '.cotacao', 4);
               formatarCampoPorClasse(container, '.percentage', 7);
               formatarCampoPorClasse(container, '.percentage2', 2);

           }
           $('#data_cotacao').on('blur', function() {
               const $input = $(this);
               const dataCotacao = $input.val();
               const url = "{{ route('cotacao.obter', ['data_cotacao' => ':data_cotacao']) }}".replace(
                   ':data_cotacao', dataCotacao);

               Swal.fire({
                   title: 'Buscando cotações...',
                   text: 'Aguarde enquanto as cotações são atualizadas.',
                   allowOutsideClick: false,
                   allowEscapeKey: false,
                   didOpen: () => {
                       Swal.showLoading();
                   }
               });

               $.ajax({
                   url: url,
                   type: 'GET',
                   dataType: 'json',
                   success: function(response) {
                       const data = response['data'];

                       const moedaFrete = $('#frete_internacional_moeda').val();
                       if (moedaFrete && data[moedaFrete]) {
                           $('#cotacao_frete_internacional').val(MoneyUtils.formatMoney(data[moedaFrete]
                               .venda, 4));
                       }

                       const moedaSeguro = $('#seguro_internacional_moeda').val();
                       if (moedaSeguro && data[moedaSeguro]) {
                           $('#cotacao_seguro_internacional').val(MoneyUtils.formatMoney(data[moedaSeguro]
                               .venda, 4));
                       }

                       const moedaAcrescimo = $('#acrescimo_frete_moeda').val();
                       if (moedaAcrescimo && data[moedaAcrescimo]) {
                           $('#cotacao_acrescimo_frete').val(MoneyUtils.formatMoney(data[moedaAcrescimo]
                               .venda, 4));
                       }

                       setTimeout(() => {
                           forcarFormatacaoCamposCards();

                           $('#cotacao_frete_internacional, #cotacao_seguro_internacional, #cotacao_acrescimo_frete')
                               .trigger('change');
                       }, 100);
                       const moedaProcesso = $('#moeda_processo').val();
                       if (moedaProcesso && data[moedaProcesso]) {
                           $('#cotacao_moeda_processo').val(JSON.stringify(data));
                           $('#display_cotacao').val(MoneyUtils.formatMoney(data[moedaProcesso].venda, 4));
                       }

                       let dataPesquisa = $input.val();
                       if (dataPesquisa) {
                           let partes = dataPesquisa.split('-');
                           if (partes.length === 3) {
                               let exibicao = partes[2] + '/' + partes[1] + '/' + partes[0];
                               $('#cotacao-data-exibicao').text(exibicao);
                           }
                       }

                       let cotacoes = $('#cotacao_moeda_processo').val();
                       if (cotacoes) {
                           try {
                               cotacoes = JSON.parse(cotacoes);
                               let html = '';
                               Object.keys(cotacoes).forEach(function(codigo) {
                                   let cotacao = cotacoes[codigo];
                                   console.log(cotacao);
                                   html += `<div class="col-md-3 mb-2">
                                <label class="form-label">${cotacao.nome ?? codigo} (${codigo})</label>
                                <input type="hidden" name="cotacao_moeda_processo[${codigo}][nome]" 
                                    value="${cotacao.nome ?? codigo}">
                                <input type="text" step="0.0001" min="0" class="form-control cotacao"
                                    name="cotacao_moeda_processo[${codigo}][venda]"
                                    value="${cotacao.venda ?? ''}"
                                    aria-label="Valor de venda para ${cotacao.nome ?? codigo}">
                            </div>`;
                               });
                               $('#cotacoes-moedas-row').html(html);
                           } catch (e) {}
                       }
                       Swal.close();

                       Toast.fire({
                           icon: 'success',
                           title: 'Cotações atualizadas com sucesso!'
                       });
                   },
                   error: function(xhr, status, error) {
                       Swal.fire({
                           icon: 'error',
                           title: 'Erro ao obter cotações',
                           text: error,
                       });
                       console.error('Erro ao obter cotações:', error);
                   }
               });
           });
       </script>
   </div>
